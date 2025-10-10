<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivitySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivitySessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function create(Activity $activity)
{
    if ($activity->user_id !== Auth::id()) {
        abort(403);
    }

    $intensityLevels = Activity::getIntensityLevels();
    
    // Vérifier si la méthode existe avant de l'appeler
    $difficultyLevels = method_exists(ActivitySession::class, 'getDifficultyLevels') 
        ? ActivitySession::getDifficultyLevels() 
        : [];
        
    $lastSession = method_exists($activity, 'lastSession') ? $activity->lastSession() : null;

    return view('activity-sessions.create', compact('activity', 'intensityLevels', 'difficultyLevels', 'lastSession'));
}

 

    public function store(Request $request, Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'session_date' => 'required|date|before_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'duration' => 'required|integer|min:1|max:1440',
            'distance' => 'nullable|numeric|min:0|max:999.99',
            'calories' => 'nullable|integer|min:0|max:9999',
            'intensity' => 'required|in:faible,modere,intense',
            'difficulty' => 'nullable|in:tres_facile,facile,normal,difficile,tres_difficile',
            'rating' => 'nullable|numeric|min:1|max:5|regex:/^\d(\.\d)?$/',
            'session_notes' => 'nullable|string|max:1000',
            'heart_rate' => 'nullable|integer|min:40|max:220',
            'weather' => 'nullable|string|max:100',
            'mood' => 'nullable|string|max:50'
        ]);

        // Vérifier qu'il n'y a pas déjà une session à cette date
        $existingSession = $activity->sessions()
            ->whereDate('session_date', $validated['session_date'])
            ->first();

        if ($existingSession) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['session_date' => 'Une session existe déjà pour cette date.']);
        }

        // Préparer les données de session
        $sessionData = [];
        if ($request->filled('heart_rate')) {
            $sessionData['heart_rate'] = $request->heart_rate;
        }
        if ($request->filled('weather')) {
            $sessionData['weather'] = $request->weather;
        }
        if ($request->filled('mood')) {
            $sessionData['mood'] = $request->mood;
        }

        // Calcul automatique des calories si non fourni
        if (!$validated['calories'] && $validated['duration']) {
            $validated['calories'] = $this->calculateCalories($activity->type, $validated['duration'], $validated['intensity']);
        }

        // Créer la session
        $session = $activity->sessions()->create([
            'user_id' => Auth::id(),
            'session_date' => $validated['session_date'],
            'start_time' => $validated['start_time'],
            'duration' => $validated['duration'],
            'distance' => $validated['distance'],
            'calories' => $validated['calories'],
            'intensity' => $validated['intensity'],
            'difficulty' => $validated['difficulty'],
            'rating' => $validated['rating'],
            'session_notes' => $validated['session_notes'],
            'session_data' => $sessionData
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Session du ' . $session->session_date->format('d/m/Y') . ' ajoutée avec succès !');
    }

    private function calculateCalories($type, $duration, $intensity)
    {
        $caloriesPerMinute = [
            'course' => ['faible' => 8, 'modere' => 12, 'intense' => 16],
            'marche' => ['faible' => 4, 'modere' => 6, 'intense' => 8],
            'velo' => ['faible' => 6, 'modere' => 10, 'intense' => 14],
            'fitness' => ['faible' => 5, 'modere' => 8, 'intense' => 12]
        ];

        return $duration * ($caloriesPerMinute[$type][$intensity] ?? 8);
    }
}