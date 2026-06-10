<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $corps = "السلام عليكم ورحمة الله وبركاته\n\n"
            . "Votre commande {{numero}} est prête à récupérer, {{nom}}. 🌸\n\n"
            . "📍 {{relais}}\n{{adresse}}\nBâtiment : {{batiment}}\nCode portail : {{code_portail}}\nÉtage : {{etage}}\n{{instructions}}\n\n"
            . "Merci de confirmer votre heure de passage. Barak Allahou fik 🌿";

        DB::table('message_templates')->where('cle', 'commande_prete')->update(['corps' => $corps]);
    }

    public function down(): void
    {
        // Pas de retour arrière sur le contenu.
    }
};
