<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SendQuestionnaireReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Rappels du lundi
        $schedule->command('questionnaires:send-reminders morning')
                 ->weeklyOn(1, '08:00'); // Lundi 8h00
                 
        $schedule->command('questionnaires:send-reminders evening')
                 ->weeklyOn(1, '18:00'); // Lundi 18h00

        // Rappels du vendredi
        $schedule->command('questionnaires:send-reminders morning')
                 ->weeklyOn(5, '08:00'); // Vendredi 8h00
                 
        $schedule->command('questionnaires:send-reminders evening')
                 ->weeklyOn(5, '18:00'); // Vendredi 18h00
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}