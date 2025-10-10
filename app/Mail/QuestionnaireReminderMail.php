<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class QuestionnaireReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $period,
        public string $dayName
    ) {}

    public function build()
    {
        $subject = "Rappel - Évaluation psychologique du {$this->dayName}";
        
        return $this->subject($subject)
                    ->view('emails.questionnaire_reminder')
                    ->with([
                        'user' => $this->user,
                        'period' => $this->period,
                        'dayName' => $this->dayName,
                        'url' => url('/questionnaires')
                    ]);
    }
}