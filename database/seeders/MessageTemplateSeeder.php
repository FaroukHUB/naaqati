<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        MessageTemplate::firstOrCreate(
            ['cle' => 'commande_prete'],
            [
                'nom' => 'Commande prête à récupérer',
                'type' => 'pret',
                'variables' => ['nom', 'numero', 'relais', 'adresse', 'batiment', 'code_portail', 'etage', 'creneau'],
                'corps' => "السلام عليكم ورحمة الله وبركاته\n\n"
                    . "Votre commande {{numero}} est prête à récupérer, {{nom}}. 🌸\n\n"
                    . "📍 {{relais}}\n{{adresse}}\nBâtiment : {{batiment}}\nCode portail : {{code_portail}}\nÉtage : {{etage}}\n{{instructions}}\n\n"
                    . "Merci de confirmer votre heure de passage. Barak Allahou fik 🌿",
                'actif' => true,
            ]
        );

        MessageTemplate::firstOrCreate(
            ['cle' => 'suivi_j15'],
            [
                'nom' => 'Suivi après achat (J+15)',
                'type' => 'suivi',
                'variables' => ['nom', 'lien_feedback'],
                'corps' => "Bonjour {{nom}}, avez-vous testé votre produit ?\n"
                    . "Votre avis nous aide à nous améliorer : {{lien_feedback}}",
                'actif' => true,
            ]
        );
    }
}
