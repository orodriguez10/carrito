<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Message;
use Illuminate\Auth\Events\Validated;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

trait ApiResponserTrait
{
    /**
     * susccesResponse
     *
     * @param  string $data
     * @param  int  $code
     * @return Illuminate\Http\JsonResponse
     */
    public function susccesResponse($data, $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $code);
    }
    /**
     * errorResponse
     *
     * @param  string $errorMessage
     * @param  int $code
     * @param string $title
     * @return Illuminate\Http\JsonResponse
     */
    public function errorResponse(string $errorMessage, int $code, string $title = null): JsonResponse
    {
        if($title) return response()->json(['error' => $errorMessage, 'title' => $title], $code);
        return response()->json(['error' => $errorMessage], $code);
    }

    /**
     * errorResponse
     *
     * @param  MessageBag $errorsMessage
     * @param  int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function errorsResponse(MessageBag $errorsMessage, int $code): JsonResponse
    {
        return response()->json(['errors' => $errorsMessage], $code);
    }

    /**
     * Captura una excepción y genera un response a partir de ella
     *
     * @param  Exception $excepcion = Excepción capturada
     * @param  String $titulo (opcional) = Título del error desplegado para el cliente
     * @param  String $mensaje (opcional) = Mensaje del error desplegado para el cliente
     * @param  Int $http_status_code (opcional) = código de error HTTP, con el que responderá la petición (4xx)
     * @return Response
     */
    public function capturar($excepcion, String $titulo = null, String $mensaje = null, Int $http_status_code = 400)
    {
        $validationException = is_a($excepcion, 'Illuminate\Validation\ValidationException');
        $queryException = is_a($excepcion, 'Illuminate\Database\QueryException');

        if ($validationException) {
            $titulo = $titulo ?? 'Ha ocurrido un error al validar los datos';
            $mensaje = collect($excepcion->errors())->flatten()->join(' ');
        }
        if ($queryException) {
            $titulo = $titulo ?? 'Ha ocurrido un error al validar los datos';
            if ($excepcion->errorInfo && is_array($excepcion->errorInfo)) {
                switch ($excepcion->errorInfo[1]) {
                    case 1451:
                        $mensaje = 'El dato que deseas eliminar tiene registros asociados';
                        break;
                    case 1048:
                        $mensaje = 'Datos incompletos';
                        break;
                    case 1062:
                        $mensaje = 'Registro ya existe';
                        break;
                    default:
                        $mensaje = 'Hay un problema con los datos, inténtalo nuevamente';
                        break;
                }
            }
        }

        $http_status_code = $validationException || $http_status_code === 0 ? 422 : $http_status_code;
        $http_status_code = $queryException || $http_status_code === 0 ? 422 : $http_status_code;

        $tmp = [
            'url_origen' => url()->previous(),
            'url_destino' => url()->current(),
            'mensaje' => $excepcion->getMessage(),
            'error' => substr((string)$excepcion, 0, 1999),
            'archivo' => $excepcion->getFile(),
            'linea' => $excepcion->getLine(),
            'parametros' => json_encode(request()->all()),
        ];
        if (auth()->check()) {
            $tmp['created_by'] = auth()->user()->id;
        }
        //$errores = Errores::create($tmp);
        $response = [
            'timestamp'   => Carbon::now()->format('d/m/y h:i A'),
            'status'      => $http_status_code,
            'titulo'      => $titulo ?? 'Ha ocurrido un error al ejecutar la consulta',
            'error'     => $mensaje ?? $excepcion->getMessage(),
            'error_message'       => "{$excepcion->getMessage()} - Archivo: {$excepcion->getFile()} - Línea: {$excepcion->getLine()}",
            'validaciones' => $validationException ? $excepcion->errors() : null,
            //'reportado' => "Reportado en {$errores->id} tabla z_errores",
        ];

        return response($response, $http_status_code);
    }

    public function guardarError($dataError)
    {
        $ruta = env('RUTA_CDE');
        $response = Http::post($ruta, $dataError);
        $response= $response->getBody();
        $rta= json_decode($response);
        return $rta;
    }
}
