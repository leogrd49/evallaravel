<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'heure_debut',
        'heure_fin',
        'user_id',
        'salle_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
    ];

    /**
     * Récupère l'utilisateur associé à cette réservation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère la salle associée à cette réservation.
     */
    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }
}