<?php

// app/Models/PlanNutritionnel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanNutritionnel extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'pdf_path', 'utilisateur_id'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}

