<?php


namespace App\Mail;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecommendationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $blog;
    public $recommendation;

    public function __construct(Blog $blog, $recommendation)
    {
        $this->blog = $blog;
        $this->recommendation = $recommendation;
    }

    public function build()
    {
        return $this->subject('📚 Recommandation personnalisée pour votre article')
                    ->view('emails.recommendation')
                    ->with([
                        'blogTitle' => $this->blog->title,
                        'authorName' => $this->blog->user->name,
                        'recommendation' => $this->recommendation
                    ]);
    }
}