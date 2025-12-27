<?php

// app/Models/Utilisateur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User;
 

class Utilisateur  extends Authenticatable implements AuthenticatableContract
{
    use HasFactory;
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'age',
        'sexe',
        'poids',
        'taille',
        'email',
        'mot_de_passe',
        'tour_de_taille',
        'tour_de_hanche',
        'tour_du_cou',
        'niveau_d_activite_physique',
        'maladie_chronique_id',
        'tdee',
        'img_notification',
        'imc_notification',
        'rth_notification'
    ];

    /**
     * Define the relationship with MaladieChronique.
     * A Utilisateur chooses one MaladieChronique.
     */
    public function maladieChronique()
    {
        return $this->belongsTo(MaladieChronique::class,  'maladie_chronique_id');
    }

    /**
     * Define the relationship with Question.
     * A Utilisateur responds to multiple Questions.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Define the relationship with PlanNutritionnel.
     * A Utilisateur receives one PlanNutritionnel.
     */
    public function planNutritionnel()
    {
        return $this->hasOne(PlanNutritionnel::class);
    }

    /**
     * Define the relationship with Reponse.
     * A Utilisateur provides one Reponse.
     */
    public function reponse()
    {
        return $this->hasMany(Reponse::class);
    }

    
     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mot_de_passe' => 'hashed',
    ];

    
}

  