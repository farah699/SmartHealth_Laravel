<?php

namespace App\Notifications;

use App\Models\Blog;
use App\Models\BlogRecommendation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRecommendationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $blog;
    protected $recommendation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Blog $blog, BlogRecommendation $recommendation)
    {
        $this->blog = $blog;
        $this->recommendation = $recommendation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('blogs.show', $this->blog->id);
        $recommendationUrl = route('recommendations.show', $this->recommendation->id);

        return (new MailMessage)
            ->subject('✨ Nouvelle recommandation pour votre article')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Notre IA a généré une recommandation personnalisée pour votre article récemment publié :')
            ->line('**"' . $this->blog->title . '"**')
            ->line('La ressource recommandée est : **' . $this->recommendation->title . '**')
            ->line($this->recommendation->description)
            ->action('Voir les détails', $recommendationUrl)
            ->line('Cette recommandation a été sélectionnée spécifiquement pour compléter votre article.')
            ->line('Vous pouvez également consulter votre article pour voir la recommandation :')
            ->action('Voir mon article', $url)
            ->salutation('Merci d\'utiliser ' . config('app.name') . '!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'blog_id' => $this->blog->id,
            'blog_title' => $this->blog->title,
            'recommendation_id' => $this->recommendation->id,
            'recommendation_title' => $this->recommendation->title,
            'type' => 'recommendation'
        ];
    }
}