<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Toko Online API",
 *    description="API documentation for Toko Online",
 *    version="1.0.0",
 * ),
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="token",
 *     name="Authorization"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
