<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\QuestionnaireReminderMail;
use Carbon\Carbon;

class SendQuestionnaireReminders extends Command
{
    protected $signature = 'questionnaires:send-reminders {period}';
    protected $description = 'Send questionnaire reminders to users';

    public function handle()
    {
        $period = $this->argument('period'); // morning or evening
        $today = Carbon::now()->dayOfWeek;

        // Vérifier si c'est lundi ou vendredi
        if (!in_array($today, [Carbon::MONDAY, Carbon::FRIDAY])) {
            $this->info('Reminders are only sent on Mondays and Fridays.');
            return;
        }

        $dayName = $today === Carbon::MONDAY ? 'lundi' : 'vendredi';
        
        // Pour le moment, utiliser user_id = 1 (statique)
      $users = User::all(); // ou User::where('active', true)->get();
$sentCount = 0;
$errorCount = 0;

foreach ($users as $user) {
    try {
        Mail::to($user->email)->send(new QuestionnaireReminderMail($user, $period, $dayName));
        $this->info("Reminder email sent successfully to {$user->email} for {$period} on {$dayName}.");
        $sentCount++;
    } catch (\Exception $e) {
        $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
        $errorCount++;
    }
}

$this->info("Reminders sent: {$sentCount}, Errors: {$errorCount}");
    }
}