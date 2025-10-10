<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Blog;
use App\Models\Comment;

class NotificationService
{
    /**
     * Créer une notification pour un nouveau commentaire
     */
    public function createCommentNotification(Comment $comment, Blog $blog)
    {
        // Ne pas créer de notification si l'utilisateur commente son propre blog
        if ($comment->user_id === $blog->user_id) {
            return;
        }

        Notification::create([
            'user_id' => $blog->user_id, // Le propriétaire du blog
            'sender_id' => $comment->user_id, // L'utilisateur qui a commenté
            'type' => 'comment',
            'title' => 'Nouveau commentaire',
            'message' => $comment->user->name . ' a commenté votre article "' . \Illuminate\Support\Str::limit($blog->title, 50) . '"',
            'data' => [
                'blog_id' => $blog->id,
                'comment_id' => $comment->id,
                'blog_title' => $blog->title,
                'comment_content' => \Illuminate\Support\Str::limit($comment->content, 100)
            ]
        ]);
    }

    /**
     * Créer une notification pour un like/dislike de commentaire
     */
    public function createCommentLikeNotification(Comment $comment, User $liker, $isLike)
    {
        // Ne pas créer de notification si l'utilisateur like son propre commentaire
        if ($comment->user_id === $liker->id) {
            return;
        }

        // Vérifier s'il y a déjà une notification similaire récente (éviter le spam)
        $existingNotification = Notification::where('user_id', $comment->user_id)
            ->where('sender_id', $liker->id)
            ->where('type', 'comment_like')
            ->where('data->comment_id', $comment->id)
            ->where('created_at', '>=', now()->subMinutes(5)) // Dans les 5 dernières minutes
            ->first();

        if ($existingNotification) {
            // Mettre à jour la notification existante
            $existingNotification->update([
                'message' => $liker->name . ' a ' . ($isLike ? 'aimé' : 'n\'a pas aimé') . ' votre commentaire',
                'data' => array_merge($existingNotification->data, [
                    'is_like' => $isLike,
                    'updated_at' => now()
                ]),
                'is_read' => false,
                'created_at' => now()
            ]);
        } else {
            // Créer une nouvelle notification
            Notification::create([
                'user_id' => $comment->user_id, // Le propriétaire du commentaire
                'sender_id' => $liker->id, // L'utilisateur qui a liké
                'type' => 'comment_like',
                'title' => $isLike ? 'Nouveau like' : 'Nouveau dislike',
                'message' => $liker->name . ' a ' . ($isLike ? 'aimé' : 'n\'a pas aimé') . ' votre commentaire',
                'data' => [
                    'comment_id' => $comment->id,
                    'blog_id' => $comment->blog_id,
                    'is_like' => $isLike,
                    'comment_content' => \Illuminate\Support\Str::limit($comment->content, 100)
                ]
            ]);
        }
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues
     */
    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)->update(['is_read' => true]);
    }

    /**
     * Supprimer les anciennes notifications (plus de 30 jours)
     */
    public function cleanOldNotifications()
    {
        Notification::where('created_at', '<', now()->subDays(30))->delete();
    }
}