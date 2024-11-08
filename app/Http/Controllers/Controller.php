<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponserTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(title="Prueba tecnica", version="1.0")
 *
 * @OA\Server(url="http://127.0.0.1:8000")
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ApiResponserTrait;
}
