<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['nom', 'texte', 'note', 'position', 'actif'];

    protected $casts = ['actif' => 'boolean'];
}
