<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'capacite',
        'surface',
    ];

    /**
     * Récupère les réservations associées à cette salle.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}