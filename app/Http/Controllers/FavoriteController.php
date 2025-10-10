<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use App\Models\UserBlogFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Ajouter/Retirer un favori
     */
    public function toggle(Request $request, Blog $blog)
    {
        $request->validate([
            'type' => 'required|in:favorite,read_later'
        ]);

        $type = $request->input('type', UserBlogFavorite::TYPE_FAVORITE);
        $user = Auth::user();

        // Vérifier si déjà en favori
        $existing = UserBlogFavorite::where([
            'user_id' => $user->id,
            'blog_id' => $blog->id,
            'type' => $type
        ])->first();

        if ($existing) {
            // Supprimer le favori
            $existing->delete();
            $action = 'removed';
        } else {
            // Ajouter le favori
            UserBlogFavorite::create([
                'user_id' => $user->id,
                'blog_id' => $blog->id,
                'type' => $type
            ]);
            $action = 'added';
        }

        // Recalculer les compteurs
        $favoritesCount = $blog->favorites()->where('type', UserBlogFavorite::TYPE_FAVORITE)->count();
        $readLaterCount = $blog->favorites()->where('type', UserBlogFavorite::TYPE_READ_LATER)->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'type' => $type,
            'is_favorited' => $action === 'added',
            'favorites_count' => $favoritesCount,
            'read_later_count' => $readLaterCount,
            'message' => $this->getActionMessage($action, $type)
        ]);
    }

    /**
     * Page des favoris de l'utilisateur
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'favorites'); // favorites ou read_later
        $user = Auth::user();

        if ($tab === 'read_later') {
            $blogs = $user->readLaterBlogs()
                ->with(['user', 'favorites'])
                ->latest('user_blog_favorites.created_at')
                ->paginate(12);
        } else {
            $blogs = $user->favoriteBlogs()
                ->with(['user', 'favorites'])
                ->latest('user_blog_favorites.created_at')
                ->paginate(12);
        }

        // Statistiques
        $stats = [
            'favorites_count' => $user->favoriteBlogs()->count(),
            'read_later_count' => $user->readLaterBlogs()->count(),
            'unread_count' => $user->blogFavorites()->readLater()->unread()->count()
        ];

        return view('favorites.index', compact('blogs', 'tab', 'stats'));
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead(Blog $blog)
    {
        $favorite = UserBlogFavorite::where([
            'user_id' => Auth::id(),
            'blog_id' => $blog->id,
            'type' => UserBlogFavorite::TYPE_READ_LATER
        ])->first();

        if ($favorite) {
            $favorite->update(['read_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Marqué comme lu'
            ]);
        }

        return response()->json(['error' => 'Favori non trouvé'], 404);
    }

    /**
     * Messages d'action
     */
    private function getActionMessage($action, $type)
    {
        $messages = [
            'added' => [
                'favorite' => 'Ajouté aux favoris',
                'read_later' => 'Ajouté à "Lire plus tard"'
            ],
            'removed' => [
                'favorite' => 'Retiré des favoris',
                'read_later' => 'Retiré de "Lire plus tard"'
            ]
        ];

        return $messages[$action][$type] ?? 'Action effectuée';
    }
}