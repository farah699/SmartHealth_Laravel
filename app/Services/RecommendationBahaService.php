<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationBahaService
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('app.recommendation_api_url', 'http://localhost:5002');
    }

    /**
     * Obtenir une recommandation pour un blog
     */
    public function getRecommendation($blogId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->apiUrl}/api/recommend/{$blogId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("Recommendation API failed", [
                'blog_id' => $blogId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("Recommendation API error", [
                'blog_id' => $blogId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Vérifier si l'API est disponible
     */
    public function isApiAvailable()
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}