<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Afficher toutes les notifications
     */
    public function index()
    {
        // Alternative : utiliser directement le modèle Notification
        $notifications = Notification::where('user_id', Auth::id())
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Récupérer les notifications via AJAX pour le header
     */
    public function getNotifications()
    {
        // Alternative : utiliser directement le modèle Notification
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'sender' => $notification->sender->name,
                    'sender_avatar' => $notification->sender->avatar_url,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $notification->data
                ];
            }),
            'count' => $count
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->markAsRead();

        // Rediriger vers la page appropriée selon le type
        return $this->redirectToNotificationTarget($notification);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Rediriger vers la cible de la notification
     */
    private function redirectToNotificationTarget($notification)
    {
        $data = $notification->data;

        switch ($notification->type) {
            case 'comment':
                return redirect()->route('blogs.show', $data['blog_id']);
            
            case 'comment_like':
                return redirect()->route('blogs.show', $data['blog_id']);
            
            default:
                return redirect()->route('notifications.index');
        }
    }
}