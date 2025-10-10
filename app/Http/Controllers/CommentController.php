<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
     protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Ajouter un commentaire
     */
    public function store(Request $request, $blogId)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);

        $blog = Blog::findOrFail($blogId);

         $comment = Comment::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'blog_id' => $blog->id,
        ]);

         // Créer une notification pour le propriétaire du blog
        $this->notificationService->createCommentNotification($comment, $blog);

        return redirect()->route('blogs.show', $blog->id)->with('success', 'Commentaire ajouté avec succès.');
    }

    /**
     * Supprimer un commentaire
     */
    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        
        // Vérifier que l'utilisateur est le propriétaire du commentaire
        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez supprimer que vos propres commentaires.');
        }

        $blogId = $comment->blog_id;
        $comment->delete();

        return redirect()->route('blogs.show', $blogId)->with('success', 'Commentaire supprimé avec succès.');
    }

    /**
     * Liker/Disliker un commentaire
     */
    public function toggleLike(Request $request, $commentId)
    {
        $request->validate([
            'is_like' => 'required|boolean',
        ]);

        $comment = Comment::findOrFail($commentId);
        $userId = Auth::id();
        $user = Auth::user();
        $isLike = $request->is_like;

        // Vérifier si l'utilisateur a déjà voté sur ce commentaire
        $existingVote = CommentLike::where('user_id', $userId)
                                  ->where('comment_id', $commentId)
                                  ->first();

        $shouldCreateNotification = false;


        if ($existingVote) {
            if ($existingVote->is_like == $isLike) {
                // Si c'est le même vote, on le supprime (annuler le vote)
                $existingVote->delete();
                $action = 'removed';
            } else {
                // Si c'est un vote différent, on le met à jour
                $existingVote->update(['is_like' => $isLike]);
                $action = 'updated';
                $shouldCreateNotification = true;
            }
        } else {
            // Nouveau vote
            CommentLike::create([
                'user_id' => $userId,
                'comment_id' => $commentId,
                'is_like' => $isLike,
            ]);
            $action = 'created';
            $shouldCreateNotification = true;

        }

         // Créer une notification si nécessaire
        if ($shouldCreateNotification) {
            $this->notificationService->createCommentLikeNotification($comment, $user, $isLike);
        }

        // Retourner les nouveaux counts pour AJAX
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'action' => $action,
                'likes_count' => $comment->fresh()->likes_count,
                'dislikes_count' => $comment->fresh()->dislikes_count,
                'user_liked' => $comment->fresh()->isLikedBy($userId),
                'user_disliked' => $comment->fresh()->isDislikedBy($userId),
            ]);
        }

        return redirect()->back()->with('success', 'Vote enregistré avec succès.');
    }
}