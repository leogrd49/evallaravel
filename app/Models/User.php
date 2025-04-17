<?php

namespace App\Models;

use App\Traits\Identity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class User extends Authenticatable /* implements MustVerifyEmail */
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasRolesAndAbilities;
    use Identity;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'administrateur';
    }

    public function isEmploye()
    {
        return $this->role === 'employe';
    }

    /**
     * Récupère les réservations de l'utilisateur.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}