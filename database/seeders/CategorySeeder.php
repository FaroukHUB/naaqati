<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Savons',
            'Shampoings',
            'Chantilly graisse de chameau',
            'Baumes',
            'Khôl',
            'Muscs',
            'Déodorants',
            'Talbina',
            'Sidr',
            'Henné',
        ];

        foreach ($categories as $position => $nom) {
            Category::firstOrCreate(
                ['nom' => $nom],
                ['position' => $position, 'actif' => true]
            );
        }
    }
}
