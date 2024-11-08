<?php

namespace App\Http\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait LoginExceptionTrait
{
    /**
     * Arroja una respuesta con excepción, en caso de ocurrir un error al momento de ingresar
     *
     * @param  int $errorType
     * @param  int $statusCodeError
     * @return \Illuminate\Http\Response
     */

    public function throwLoginErrorResponse(int $errorType = 0, int $statusCodeError = 400): \Illuminate\Http\Response
    {
        $errors = [
            0 => '¡Lo sentimos! Ha ocurrido un error inesperado al ingresar',
            1 => 'Usuario eliminado o bloqueado',
            2 => 'Usuario incorrecto',
            3 => 'El usuario y contraseña no coinciden. Por favor, inténtelo nuevamente',
            4 => 'El usuario se encuentra inactivo',
        ];

        return response([
            "errors" => ['username' => [$errors[$errorType]]]
        ], $statusCodeError);
    }

    public static function contrasenaErronea(User $usuario, String $contrasena): bool
    {
        return !(Hash::check($contrasena, $usuario->password));
    }
}
