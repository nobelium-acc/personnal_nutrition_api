<?php

/**
 * @OA\Info(
 *     title="Personnal Nutrition",
 *     version="1.0.0",
 *     description="",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 */

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

}
