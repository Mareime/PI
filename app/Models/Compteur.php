<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compteur extends Model
{
    //
    use HasFactory;

    protected $table = 'compteur';
    protected $primaryKey = 'annee'; // Définir 'annee' comme clé primaire
    public $incrementing = false; // Désactiver l'auto-incrémentation
    protected $fillable = ['annee', 'compteur'];
}
