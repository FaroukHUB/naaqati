<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\PickupSchedule;
use App\Models\Relais;
use Illuminate\Database\Seeder;

class RelaisSeeder extends Seeder
{
    public function run(): void
    {
        $algerie = Country::firstOrCreate(
            ['iso2' => 'DZ'],
            ['nom' => 'Algérie', 'devise_code' => 'DZD', 'actif' => true]
        );

        $ville = City::firstOrCreate(
            ['country_id' => $algerie->id, 'nom' => 'Alger'],
        );

        $relais = Relais::firstOrCreate(
            ['slug' => 'riadi-city'],
            [
                'city_id' => $ville->id,
                'nom' => 'Riadi City',
                'adresse' => 'Riadi City',
                'batiment' => 'Bâtiment X',
                'code_portail' => 'XXXX',
                'etage' => 'Étage X',
                'devise_code' => 'DZD',
                'actif' => true,
            ]
        );

        // Horaires de retrait par défaut : tous les jours, créneaux simples.
        // jour_semaine : 0=dimanche … 6=samedi (Carbon::dayOfWeek)
        foreach (range(0, 6) as $jour) {
            PickupSchedule::withoutGlobalScopes()->firstOrCreate(
                ['relais_id' => $relais->id, 'jour_semaine' => $jour],
                [
                    'creneaux' => ['matin', 'apres_midi', 'soir'],
                    'capacite_max' => null,
                    'actif' => true,
                ]
            );
        }
    }
}
