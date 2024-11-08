<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use App\Http\Traits\LoginExceptionTrait as LoginExceptions;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{

    /**
     * Utiliza el trait de excepciones (App\Traits\sTrait)
     */

    use LoginExceptions;

    /**
     * Crea una nueva instancia del controlador
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }




    public function login(LoginRequest $request): \Illuminate\Http\Response
    {
        try {
            $credentials = [
                'username' => $request->get('correo'),
                'password' => $request->get('password')
            ];


            $user = User::where('correo', $credentials['username'])->first();

            $token = JWTAuth::attempt(['correo' => $credentials['username'], 'password' => $credentials['password']]);
            if (!$token) return $this->throwLoginErrorResponse(3, 400);

            $data = (object)[
                'token' => $token,
            ];

            return response(['message' => __("Logueado Correctamente"), 'data' =>  $data], 200);
        } catch (\Exception $error) {
            return $this->capturar($error, __("error al ingresar"));
        }
    }
}
