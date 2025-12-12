<?php

// app/Models/Reponse.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Reponse",
 *     type="object",
 *     title="Réponse",
 *     description="Schéma représentant une réponse d'utilisateur",
 *
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *
 *     @OA\Property(
 *         property="question_id",
 *         type="integer",
 *         example=5
 *     ),
 *
 *     @OA\Property(
 *         property="utilisateur_id",
 *         type="integer",
 *         example=8
 *     ),
 *
 *     @OA\Property(
 *         property="question_possible_answer_id",
 *         type="integer",
 *         nullable=true,
 *         example=3
 *     ),
 *
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         nullable=true,
 *         example="Ma réponse textuelle"
 *     ),
 *
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-01-10T14:03:21Z"
 *     ),
 *
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-01-10T14:03:21Z"
 *     )
 * )
 */

class Reponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'question_id',
        'utilisateur_id',
        'question_possible_answer_id',
        'description',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
