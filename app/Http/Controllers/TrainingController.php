<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\User;

class TrainingController extends Controller
{
    public function startTraining(Request $request)
    {
        $exerciseData = $request->validate([
            'exercise_id' => 'required|integer',
            'exercise_name' => 'required|string',
            'exercise_type' => 'required|string',
            'recommended_duration' => 'required|integer',
            'difficulty_level' => 'required|integer',
            'calories_per_minute' => 'required|numeric'
        ]);

        $user = Auth::user();
        
        return view('exercises.training-session', [
            'exercise' => $exerciseData,
            'user' => $user
        ]);
    }

    public function completeTraining(Request $request)
    {
        $data = $request->validate([
            'exercise_name' => 'required|string',
            'exercise_type' => 'required|string',
            'duration_minutes' => 'required|integer',
            'calories_burned' => 'required|numeric',
            'difficulty_level' => 'required|integer'
        ]);

        $user = Auth::user();

        // Créer une nouvelle activité
        $activity = Activity::create([
            'user_id' => $user->id,
            'activity_name' => $data['exercise_name'],
            'activity_type' => $data['exercise_type'],
            'duration_minutes' => $data['duration_minutes'],
            'calories_burned' => $data['calories_burned'],
            'notes' => "Entraînement IA - Difficulté: {$data['difficulty_level']}/10",
            'activity_date' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entraînement terminé avec succès !',
            'activity_id' => $activity->id,
            'total_calories' => $data['calories_burned'],
            'duration' => $data['duration_minutes']
        ]);
    }
}