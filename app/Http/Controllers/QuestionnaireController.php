<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Questionnaire;
use App\Models\QuestionnaireSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class QuestionnaireController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Vérifier si c'est lundi ou vendredi
        $today = Carbon::now()->dayOfWeek;
        if (!in_array($today, [Carbon::MONDAY, Carbon::FRIDAY])) {
            return view('questionnaires.unavailable');
        }

        return view('questionnaires.index');
    }

    public function start()
    {
        // Créer une nouvelle session
        $session = QuestionnaireSession::create([
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('questionnaires.show', ['type' => 'PHQ-9', 'session' => $session->id]);
    }

    public function show($type, Request $request)
    {
        // Vérifier le type valide
        if (!in_array($type, ['PHQ-9', 'GAD-7'])) {
            abort(404);
        }

        $session = QuestionnaireSession::find($request->session);
        if (!$session) {
            abort(404);
        }

          if ($session->user_id !== Auth::id()) {
        abort(403, 'Vous n\'êtes pas autorisé à accéder à cette session.');
    }

        // Convertir le type vers le nom de fichier correct
        $fileName = strtolower(str_replace('-', '', $type)) . '_questions.json';
        $jsonPath = resource_path("json/{$fileName}");
        
        if (!File::exists($jsonPath)) {
            abort(404, "Fichier JSON non trouvé : {$fileName}");
        }
        
        $questions = json_decode(File::get($jsonPath), true);

        return view('questionnaires.show', compact('questions', 'type', 'session'));
    }

    public function store(Request $request, $type)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer|min:0|max:3',
            'session_id' => 'required|exists:questionnaire_sessions,id'
        ]);

        $score = array_sum($request->input('answers'));
        $interpretation = $this->getInterpretation($type, $score);
        
        $session = QuestionnaireSession::find($request->session_id);

        // Enregistrer le questionnaire individuel
        Questionnaire::create([
            'user_id' => Auth::id(),
            'type' => $type,
            'score' => $score,
        ]);

        // Mettre à jour la session
        if ($type === 'PHQ-9') {
            $session->update([
                'phq9_score' => $score,
                'phq9_interpretation' => $interpretation
            ]);

            // Rediriger vers GAD-7
            return redirect()->route('questionnaires.show', ['type' => 'GAD-7', 'session' => $session->id]);
        } else {
            $session->update([
                'gad7_score' => $score,
                'gad7_interpretation' => $interpretation,
                'is_completed' => true,
                'completed_at' => now()
            ]);

            // Rediriger vers les résultats
            return redirect()->route('questionnaires.result', ['session' => $session->id]);
        }
    }

    public function result(Request $request)
    {
        $session = QuestionnaireSession::find($request->session);
        if (!$session || !$session->is_completed) {
            abort(404);
        }


         if ($session->user_id !== Auth::id()) {
        abort(403, 'Vous n\'êtes pas autorisé à accéder à ces résultats.');
    }

        return view('questionnaires.result', compact('session'));
    }

    public function history()
    {
        $sessions = QuestionnaireSession::where('user_id', Auth::id())
            ->where('is_completed', true)
            ->orderBy('completed_at', 'desc')
            ->get();

        return view('questionnaires.history', compact('sessions'));
    }

    private function getInterpretation($type, $score)
    {
        if ($type === 'PHQ-9') {
            if ($score <= 4) return 'Dépression minimale';
            if ($score <= 9) return 'Dépression légère';
            if ($score <= 14) return 'Dépression modérée';
            if ($score <= 19) return 'Dépression modérément sévère';
            return 'Dépression sévère';
        } elseif ($type === 'GAD-7') {
            if ($score <= 4) return 'Anxiété minimale';
            if ($score <= 9) return 'Anxiété légère';
            if ($score <= 14) return 'Anxiété modérée';
            return 'Anxiété sévère';
        }
        return 'Inconnu';
    }
}