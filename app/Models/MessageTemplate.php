<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $fillable = ['cle', 'nom', 'corps', 'variables', 'type', 'actif'];

    protected $casts = [
        'variables' => 'array',
        'actif' => 'boolean',
    ];

    /** Remplace les variables {{cle}} par leurs valeurs. */
    public function render(array $data): string
    {
        $corps = $this->corps;

        foreach ($data as $cle => $valeur) {
            $corps = str_replace('{{' . $cle . '}}', (string) $valeur, $corps);
        }

        return $corps;
    }
}
