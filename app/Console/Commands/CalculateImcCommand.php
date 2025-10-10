<?php
// filepath: app/Console/Commands/CalculateImcCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImcService;
use App\Models\User;

class CalculateImcCommand extends Command
{
    protected $signature = 'imc:calculate {user?} {--all}';
    protected $description = 'Calculer l\'IMC des utilisateurs';

    protected $imcService;

    public function __construct(ImcService $imcService)
    {
        parent::__construct();
        $this->imcService = $imcService;
    }

    public function handle()
    {
        if ($this->option('all')) {
            $this->calculateAllUsers();
        } elseif ($this->argument('user')) {
            $this->calculateSingleUser($this->argument('user'));
        } else {
            $this->calculateAllUsers();
        }
    }

    private function calculateAllUsers()
    {
        $this->info('Calcul de l\'IMC pour tous les utilisateurs...');
        
        $results = $this->imcService->calculateImcForAllUsers();
        
        $this->table(
            ['ID', 'Nom', 'Email', 'IMC', 'Catégorie', 'Poids', 'Taille'],
            collect($results)->map(function ($result) {
                return [
                    $result['user_id'],
                    $result['user_name'],
                    $result['user_email'],
                    $result['imc_data']['imc'],
                    $result['imc_data']['category'],
                    $result['imc_data']['weight'] . ' kg',
                    $result['imc_data']['height'] . ' cm'
                ];
            })
        );

        $this->info('Total: ' . count($results) . ' utilisateurs avec IMC calculé');
    }

    private function calculateSingleUser($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Utilisateur avec l'ID {$userId} non trouvé");
            return;
        }

        $imcData = $this->imcService->calculateImc($user);
        
        if (!$imcData) {
            $this->error("Impossible de calculer l'IMC pour {$user->name} - données manquantes");
            return;
        }

        $this->info("IMC pour {$user->name}:");
        $this->line("- IMC: {$imcData['imc']}");
        $this->line("- Catégorie: {$imcData['category']}");
        $this->line("- Poids: {$imcData['weight']} kg");
        $this->line("- Taille: {$imcData['height']} cm");
    }
}