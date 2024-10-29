<?php

// app/Models/MaladieChronique.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaladieChronique extends Model
{
    use HasFactory;

    protected $fillable = ['type'];

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class);
    }
}
