<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @OA\Info(
     *     title="Your API Title",
     *     version="1.0.0",
     *     description="A description of your API",
     *     @OA\Contact(
     *         email="support@example.com"
     *     )
     * )
     */

}
