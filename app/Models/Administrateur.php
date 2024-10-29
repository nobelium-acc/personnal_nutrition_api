<?php

// app/Models/Administrateur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Administrateur extends Model
{
    use HasFactory;
    use HasApiTokens, Notifiable;

    protected $fillable = ['identifiant', 'mot_de_passe'];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function isAdmin()
    {
         return $this->identifiant === env('ADMIN_USERNAME');
    }

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $casts = [
        'mot_de_passe' => 'hashed',
    ];
}


  
