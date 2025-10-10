<?php
// app/Http/Controllers/YogaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\YogaSession;
use App\Models\YogaPose;
use App\Models\YogaUserStats;
use Carbon\Carbon;

class YogaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function practice()
    {
        $user = Auth::user();
        $stats = YogaUserStats::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_points' => 0,
                'total_sessions' => 0,
                'total_duration' => 0,
                'current_streak' => 0,
                'best_streak' => 0,
                'level' => 1,
                'pose_mastery' => []
            ]
        );

        return view('yoga.practice', compact('stats'));
    }

    public function startSession(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Créer une nouvelle session
            $session = YogaSession::create([
                'user_id' => $user->id,
                'start_time' => now(),
                'poses_data' => [],
                'is_completed' => false
            ]);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'message' => 'Session démarrée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du démarrage de la session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detectPose(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|string',
                'session_id' => 'required|exists:yoga_sessions,id'
            ]);

            $user = Auth::user();
            $session = YogaSession::findOrFail($request->session_id);

            // Vérifier que la session appartient à l'utilisateur et est active
            if ($session->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session non autorisée'
                ], 403);
            }

            // Vérifier que la session n'est pas terminée
            if ($session->is_completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session déjà terminée'
                ], 400);
            }

            // Envoyer l'image à l'API Python
            $response = Http::timeout(5)->post('http://yoga-ai:5000/detect_pose', [
                'image' => $request->image,
                'user_id' => $user->id
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Enregistrer la pose détectée si correcte
                if ($data['is_correct'] && $data['pose_name'] !== 'Unknown Pose') {
                    $this->recordPoseDetection($user->id, $session->id, $data);
                }

                return response()->json([
                    'success' => true,
                    'pose_name' => $data['pose_name'],
                    'is_correct' => $data['is_correct'],
                    'points' => $data['points'] ?? 0
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur de communication avec le serveur IA'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function endSession(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|exists:yoga_sessions,id'
            ]);

            $user = Auth::user();
            $session = YogaSession::findOrFail($request->session_id);

            if ($session->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session non autorisée'
                ], 403);
            }

            // Vérifier que la session n'est pas déjà terminée
            if ($session->is_completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session déjà terminée'
                ], 400);
            }

            // Calculer la durée et les points totaux
            $endTime = now();
            $startTime = Carbon::parse($session->start_time);
            $duration = $startTime->diffInSeconds($endTime); // Durée en secondes positives
            $totalPoints = $session->poses->sum('points_earned');

            // Mettre à jour la session
            $session->update([
                'end_time' => $endTime,
                'duration' => $duration,
                'total_points' => $totalPoints,
                'is_completed' => true
            ]);

            // Mettre à jour les statistiques utilisateur
            $this->updateUserStats($user->id, $duration, $totalPoints);

            return response()->json([
                'success' => true,
                'duration' => $duration,
                'total_points' => $totalPoints,
                'formatted_duration' => $this->formatDuration($duration),
                'message' => 'Session terminée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStats()
    {
        try {
            $user = Auth::user();
            $stats = YogaUserStats::where('user_id', $user->id)->first();

            if (!$stats) {
                $stats = YogaUserStats::create([
                    'user_id' => $user->id,
                    'total_points' => 0,
                    'total_sessions' => 0,
                    'total_duration' => 0,
                    'current_streak' => 0,
                    'best_streak' => 0,
                    'level' => 1,
                    'pose_mastery' => []
                ]);
            }

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_points' => $stats->total_points,
                    'total_sessions' => $stats->total_sessions,
                    'total_duration' => $stats->total_duration,
                    'current_streak' => $stats->current_streak,
                    'level' => $stats->level,
                    'pose_mastery' => $stats->pose_mastery ?? []
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    private function recordPoseDetection($userId, $sessionId, $data)
    {
        $pose = YogaPose::firstOrCreate([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'pose_name' => $data['pose_name']
        ], [
            'correct_count' => 0,
            'total_attempts' => 0,
            'points_earned' => 0,
            'detected_at' => now()
        ]);

        $pose->increment('correct_count');
        $pose->increment('total_attempts');
        $pose->increment('points_earned', $data['points'] ?? 1);
        
        // Calculer le pourcentage de précision
        $pose->accuracy_percentage = ($pose->correct_count / $pose->total_attempts) * 100;
        $pose->save();
    }

    private function updateUserStats($userId, $duration, $points)
    {
        $stats = YogaUserStats::where('user_id', $userId)->first();
        
        if ($stats) {
            $stats->increment('total_points', $points);
            $stats->increment('total_sessions');
            $stats->increment('total_duration', $duration);
            
            // Calculer le streak
            $today = Carbon::today();
            $lastPractice = $stats->last_practice_date;
            
            if ($lastPractice && $lastPractice->diffInDays($today) == 1) {
                $stats->increment('current_streak');
            } elseif (!$lastPractice || $lastPractice->diffInDays($today) > 1) {
                $stats->current_streak = 1;
            }
            
            if ($stats->current_streak > $stats->best_streak) {
                $stats->best_streak = $stats->current_streak;
            }
            
            $stats->last_practice_date = $today;
            $stats->level = 1 + intval($stats->total_points / 1000); // 1 niveau par 1000 points
            
            $stats->save();
        }
    }

    private function formatDuration($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }
}