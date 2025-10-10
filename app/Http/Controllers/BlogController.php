<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use App\Services\TextToSpeechService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\BlogCreated;

class BlogController extends Controller
{
    private $ttsService;
    private $categories = [
        'Alimentation saine',
        'Activité physique',
        'Sommeil & récupération',
        'Gestion du stress',
        'Bien-être mental',
        'Vie étudiante',
        'Prévention santé',
        'Développement personnel'
    ];

    public function __construct(TextToSpeechService $ttsService)
    {
        $this->ttsService = $ttsService;
    }

    public function index()
    {
        $blogs = Blog::with('user')->latest()->get();
        $categories = $this->categories;
        return view('pages-blog-list', compact('blogs', 'categories'));  
    }

    public function create()
    {
        $categories = $this->categories;
        return view('pages-blog-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', $this->categories),
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $filename = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('images', $filename, 'public');
            $data['image_url'] = $path;
        } else {
            $defaultImages = ['blog-health-1.png', 'blog-health-2.png'];
            $randomImage = $defaultImages[array_rand($defaultImages)];
            $data['image_url'] = 'defaults/' . $randomImage;
        }

        $blog = Blog::create([
            'title' => $data['title'],
            'category' => $data['category'],
            'content' => $data['content'],
            'user_id' => Auth::id(),
            'image_url' => $data['image_url'],
            'audio_generated' => false,
        ]);

        // Déclencher l'event pour la recommandation
        event(new BlogCreated($blog));

        // 🎵 Générer l'audio DIRECTEMENT (synchrone)
        $this->generateAudioSync($blog);

        return redirect()->route('blogs.show', $blog->id)->with('success', 'Article créé avec succès.');  
    }

    public function show(string $id)
    {
        $blog = Blog::with(['user', 'comments.user', 'recommendation'])->findOrFail($id);
        $recentBlogs = Blog::with('user')->where('id', '!=', $id)->latest()->take(5)->get();
        
        $showRecommendationAlert = false;
        $recommendationData = null;
        if (session()->has('new_recommendation')) {
            $newRecommendation = session('new_recommendation');
            if ($newRecommendation['blog_id'] == $blog->id) {
                $showRecommendationAlert = true;
                $recommendationData = $newRecommendation;
                session()->forget('new_recommendation');
            }
        }
        
        return view('pages-blog-details', compact('blog', 'recentBlogs', 'showRecommendationAlert','recommendationData'));
    }

    public function edit(string $id)
    {
        $blog = Blog::findOrFail($id);
        
        if ($blog->user_id !== Auth::id()) {
            return redirect()->route('blogs.index')->with('error', 'Vous ne pouvez éditer que vos propres articles.');
        }
        
        $categories = $this->categories;
        return view('pages-blog-edit', compact('blog', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $blog = Blog::findOrFail($id);
        
        if ($blog->user_id !== Auth::id()) {
            return redirect()->route('blogs.index')->with('error', 'Vous ne pouvez modifier que vos propres articles.');
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', $this->categories),
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $oldImageUrl = $blog->image_url;
            
            $filename = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('images', $filename, 'public');
            $data['image_url'] = $path;
            
            if ($oldImageUrl && !$this->isDefaultImage($oldImageUrl)) {
                $this->deleteImageIfNotUsed($oldImageUrl, $blog->id);
            }
        }

        $contentChanged = $blog->content !== $data['content'] || $blog->title !== $data['title'];

        $blog->update([
            'title' => $data['title'],
            'category' => $data['category'],
            'content' => $data['content'],
            'image_url' => $data['image_url'] ?? $blog->image_url,
        ]);

        if ($contentChanged) {
            $this->generateAudioSync($blog);
        }

        return redirect()->route('blogs.show', $blog->id)->with('success', 'Article modifié avec succès.');
    }

    public function destroy(string $id)
    {
        $blog = Blog::findOrFail($id);
        
        if ($blog->user_id !== Auth::id()) {
            return redirect()->route('blogs.index')->with('error', 'Vous ne pouvez supprimer que vos propres articles.');
        }
        
        $imageUrl = $blog->image_url;
        $audioUrl = $blog->audio_url;
        
        $blog->delete();
        
        if ($imageUrl && !$this->isDefaultImage($imageUrl)) {
            $this->deleteImageIfNotUsed($imageUrl);
        }

        if ($audioUrl) {
            $this->ttsService->deleteAudio($audioUrl);
        }

        return redirect()->route('blogs.index')->with('success', 'Article supprimé avec succès.');
    }

    private function isDefaultImage($imageUrl)
    {
        return $imageUrl && (
            $imageUrl === 'defaults/blog-health-1.png' || 
            $imageUrl === 'defaults/blog-health-2.png'
        );
    }

    /**
     * ✅ CORRECTION : Génération synchrone de l'audio
     */
    private function generateAudioSync($blog)
    {
        try {
            Log::info('🎵 Starting synchronous audio generation', ['blog_id' => $blog->id]);
            
            // Générer directement sans queue
            $audioPath = $this->ttsService->generateAudioForBlog($blog);
            
            if ($audioPath) {
                Log::info('✅ Audio generation completed', [
                    'blog_id' => $blog->id,
                    'audio_path' => $audioPath,
                    'file_exists' => Storage::disk('public')->exists($audioPath)
                ]);
            } else {
                Log::error('❌ Audio generation failed', ['blog_id' => $blog->id]);
                $blog->update(['audio_generated' => false]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Audio generation exception', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $blog->update(['audio_generated' => false]);
        }
    }

    /**
     * Régénérer l'audio manuellement (route AJAX)
     */
    public function regenerateAudio($id)
    {
        $blog = Blog::findOrFail($id);
        
        if ($blog->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        try {
            Log::info('🔄 Regenerating audio', ['blog_id' => $blog->id]);

            // Supprimer l'ancien audio
            if ($blog->audio_url) {
                $this->ttsService->deleteAudio($blog->audio_url);
            }

            // Marquer comme en cours
            $blog->update(['audio_generated' => false, 'audio_url' => null]);

            // Générer l'audio synchrone
            $audioPath = $this->ttsService->generateAudioForBlog($blog);
            
            if ($audioPath) {
                return response()->json([
                    'success' => true,
                    'audio_url' => asset('storage/' . $audioPath),
                    'message' => 'Audio régénéré avec succès',
                    'audio_exists' => Storage::disk('public')->exists($audioPath),
                    'file_size' => Storage::disk('public')->size($audioPath),
                    'full_path' => Storage::disk('public')->path($audioPath)
                ]);
            }

            $blog->update(['audio_generated' => false]);
            return response()->json(['error' => 'Erreur lors de la génération audio'], 500);

        } catch (\Exception $e) {
            Log::error('❌ Regenerate audio error', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $blog->update(['audio_generated' => false]);
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    private function detectLanguage($text)
    {
        $frenchWords = ['le', 'la', 'les', 'de', 'du', 'des', 'et', 'est', 'dans', 'pour', 'avec', 'sur', 'par', 'un', 'une'];
        
        $frenchCount = 0;
        foreach ($frenchWords as $frenchWord) {
            $frenchCount += substr_count(strtolower($text), $frenchWord);
        }
        
        return $frenchCount > 5 ? 'fr' : 'en';
    }

    private function deleteImageIfNotUsed($imageUrl, $excludeBlogId = null)
    {
        if ($this->isDefaultImage($imageUrl)) {
            return;
        }

        $query = Blog::where('image_url', $imageUrl);
        
        if ($excludeBlogId) {
            $query->where('id', '!=', $excludeBlogId);
        }
        
        $otherBlogsUsingImage = $query->exists();

        if (!$otherBlogsUsingImage && Storage::disk('public')->exists($imageUrl)) {
            Storage::disk('public')->delete($imageUrl);
        }
    }
}