<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'capacity',
        'equipment',
        'description',
        'is_active',
    ];

    /**
     * Relation avec les réservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
