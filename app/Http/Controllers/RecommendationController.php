<?php

namespace App\Http\Controllers;

use App\Models\BlogRecommendation;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    /**
     * Display a listing of recommendations
     */
    public function index()
    {
        // If not admin, redirect to personal recommendations
        if (Auth::user()->role !== 'Admin') {
            return redirect()->route('recommendations.my');
        }
        
        // Only admin can see all recommendations
        $blogsWithRecommendations = Blog::has('recommendation')
            ->with(['recommendation', 'user'])
            ->latest()
            ->paginate(10);
        
        return view('recommendations.index', compact('blogsWithRecommendations'));
    }
    
    /**
     * Display recommendations for the current user's blogs
     */
    public function myRecommendations()
    {
        // Get current user's blogs with recommendations
        $blogsWithRecommendations = Blog::where('user_id', Auth::id())
            ->has('recommendation')
            ->with('recommendation')
            ->latest()
            ->paginate(10);
        
        return view('recommendations.my-recommendations', compact('blogsWithRecommendations'));
    }
    
    /**
     * Display a specific recommendation
     */
    public function show($id)
    {
        $recommendation = BlogRecommendation::with('blog.user')->findOrFail($id);
        
        // Only the blog owner can see recommendation details
        if (Auth::id() !== $recommendation->blog->user_id) {
            abort(403, 'Unauthorized action. You can only view details for recommendations on your own blogs.');
        }
        
        return view('recommendations.show', compact('recommendation'));
    }

    /**
     * Delete a recommendation
     */
    public function destroy($id)
    {
        $recommendation = BlogRecommendation::findOrFail($id);
        
        // Vérification que l'utilisateur est autorisé à supprimer
        if (Auth::id() !== $recommendation->blog->user_id && Auth::user()->role !== 'Admin') {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à supprimer cette recommandation.');
        }
        
        // Suppression de la recommandation
        $recommendation->delete();
        
        return redirect()->route('recommendations.index')
            ->with('success', 'Recommandation supprimée avec succès.');
    }

    /**
     * Mark a recommendation as read
     */
    public function markAsRead($id)
    {
        $recommendation = BlogRecommendation::findOrFail($id);
        
        // Vérification que l'utilisateur est bien le propriétaire du blog
        if (Auth::id() === $recommendation->blog->user_id) {
            $recommendation->update(['is_new' => false]);
        }
        
        return redirect()->back();
    }

    /**
     * 🆕 Regenerate recommendation for a specific blog
     */
    public function regenerate($blogId)
    {
        $blog = Blog::findOrFail($blogId);
        
        // Vérifier que l'utilisateur est le propriétaire
        if (Auth::id() !== $blog->user_id && Auth::user()->role !== 'Admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        try {
            Log::info('🔄 Regenerating recommendation', ['blog_id' => $blogId]);

            // Appeler l'API Docker
            $response = Http::timeout(30)
                ->get(env('RECOMMENDATION_API_URL', 'http://blog-ai:5002') . '/api/recommend/' . $blogId);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    // Mettre à jour la recommandation
                    BlogRecommendation::updateOrCreate(
                        ['blog_id' => $blogId],
                        [
                            'recommendations' => json_encode($data['recommendations'] ?? []),
                            'similarity_scores' => json_encode($data['similarity_scores'] ?? []),
                            'ai_analysis' => json_encode($data['ai_analysis'] ?? []),
                            'generated_at' => now(),
                            'is_new' => true
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Recommandations régénérées avec succès',
                        'recommendations_count' => count($data['recommendations'] ?? [])
                    ]);
                }
            }

            throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('❌ Error regenerating recommendation', [
                'blog_id' => $blogId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Erreur lors de la régénération',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🆕 Test Blog AI connection
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(10)
                ->get(env('RECOMMENDATION_API_URL', 'http://blog-ai:5002') . '/health');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blog AI service is online',
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Blog AI service returned error',
                'status' => $response->status()
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot connect to Blog AI service',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * 🆕 Get API statistics
     */
    public function stats()
    {
        try {
            $response = Http::timeout(10)
                ->get(env('RECOMMENDATION_API_URL', 'http://blog-ai:5002') . '/api/stats');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to get stats'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 503);
        }
    }
}