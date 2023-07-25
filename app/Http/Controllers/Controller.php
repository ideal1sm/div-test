<?php

namespace App\Http\Controllers;

use App\Http\Traits\RespondsWithHttpStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Div test exercise API",
 *    version="1.0.0",
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, RespondsWithHttpStatus;
}
