<?php


namespace App\Jobs;

use App\Models\Blog;
use App\Services\RecommendationBahaService;
use App\Mail\RecommendationEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessRecommendationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $blog;
    public $tries = 3;
    public $timeout = 60;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function handle(RecommendationBahaService $recommendationService)
    {
        try {
            // Obtenir la recommandation
            $recommendationData = $recommendationService->getRecommendation($this->blog->id);

            if ($recommendationData && $recommendationData['success']) {
                $recommendation = $recommendationData['recommendation'];

                // Envoyer l'email à l'auteur du blog
                Mail::to($this->blog->user->email)->send(
                    new RecommendationEmail($this->blog, $recommendation)
                );

                Log::info("Email de recommandation envoyé", [
                    'blog_id' => $this->blog->id,
                    'user_email' => $this->blog->user->email,
                    'recommendation_title' => $recommendation['title']
                ]);

                // Sauvegarder la recommandation en base (optionnel)
                $this->saveRecommendation($recommendation);

            } else {
                Log::warning("Aucune recommandation obtenue", [
                    'blog_id' => $this->blog->id,
                    'response' => $recommendationData
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors du traitement de la recommandation", [
                'blog_id' => $this->blog->id,
                'error' => $e->getMessage()
            ]);
            throw $e; // Re-throw pour retry automatique
        }
    }

    private function saveRecommendation($recommendation)
    {
        // Optionnel: sauvegarder en base pour affichage dans l'interface
        // Vous pouvez créer un modèle Recommendation si besoin
    }
}