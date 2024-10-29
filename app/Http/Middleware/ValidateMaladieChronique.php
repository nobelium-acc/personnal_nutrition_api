<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValidateMaladieChronique
{
    public function handle(Request $request, Closure $next)
    {
        $typeMaladieChronique = $request->input('type_maladie_chronique');
        $validTypes = ['Obésité modérée','Obésité sévère',' Obésité morbide'];

        if (!in_array($typeMaladieChronique, $validTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Type de maladie chronique invalide',
            ], 400);
        }

        return $next($request); // Continue le traitement normal si tout est valide
    }
}
