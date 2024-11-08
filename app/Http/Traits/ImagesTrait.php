<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

trait ImagesTrait
{

    public static function bucket()
    {
        $bucket = "https://" . config("filesystems.disks.s3.bucket") . ".s3.amazonaws.com/";
        return $bucket;
    }

    /*
        funcion para guardar la imagen con el porcentaje y la ruta deseada
        $img @binario Archivo de imágen
        $dimension @int porcentaje en que desea la imagen
        $ruta @string dirección donde estará alojada
        $mini @boolean guardar una versión mini
    */
    public static function guardar_imagen_porcentaje($img, $dimension, $ruta, $mini = false, $dimension_mini = 100)
{
    try {
        // Crear la instancia de imagen usando Intervention
        $imagen_file = Image::make($img);
        $imagen_file_ancho = $imagen_file->width();
        $imagen_file_alto = $imagen_file->height();

        // Determinar el lado más largo
        $imagen_max = max($imagen_file_ancho, $imagen_file_alto);

        // Calcular el porcentaje de escalado
        $imagen_porcentaje = ($dimension / $imagen_max);

        // Solo cambiar el tamaño si la imagen es más grande que la dimensión objetivo
        if ($imagen_porcentaje < 1) {
            $imagen_ancho = $imagen_file_ancho * $imagen_porcentaje;
            $imagen_alto = $imagen_file_alto * $imagen_porcentaje;
            $imagen = $imagen_file->resize($imagen_ancho, $imagen_alto);
        } else {
            $imagen = $imagen_file;
        }

        // Obtener la extensión real de la imagen
        $ext = $img->getClientOriginalExtension();

        // Generar nombres de archivo únicos
        $nombre_aleatorio = Str::random(40) . '.' . $ext;
        // Aquí nos aseguramos de que no haya un "/" al principio
        $subruta = ($ruta != '' ? trim($ruta, '/') . '/' : '') . $nombre_aleatorio;

        // Guardar la imagen en el almacenamiento de Laravel (storage/app/public/)
        Storage::disk('public')->put($subruta, (string)$imagen->encode($ext));

        // Obtener la URL pública y eliminar el "/" inicial si es necesario
        $url_imagen = ltrim(Storage::url($subruta), '/'); // Aseguramos que no haya "/" al inicio

        // Retornar las rutas de las imágenes
        return [
            'estado' => true,
            'ruta' => $url_imagen,
            'ruta_mini' => $url_imagen // puedes ajustar para la miniatura
        ];
    } catch (\Exception $e) {
        return [
            'estado' => false,
            'ruta' => null,
            'ruta_mini' => null,
            'error' => $e->getMessage()
        ];
    }
}


    // Función para formatear imagen
    public static function formatea_imagen($imagen, $porcentaje)
    {
        $imagen_file = Image::make($imagen);
        $imagen_file_ancho = $imagen_file->width();
        $imagen_file_alto = $imagen_file->height();

        $imagen_max = max($imagen_file_ancho, $imagen_file_alto);
        $imagen_porcentaje = ($porcentaje / $imagen_max);

        if ($imagen_porcentaje < 1) {
            $imagen_ancho = $imagen_file_ancho * $imagen_porcentaje;
            $imagen_alto = $imagen_file_alto * $imagen_porcentaje;
            $imagen = $imagen_file->resize($imagen_ancho, $imagen_alto);
        }

        return $imagen;
    }

    /*
        funcion para retornar un imagen por defecto
        $tipo @Int
        tipo 1 = sin usuario, tipo 2 = sin producto, tipo 3 = sin producto v2, tipo 4 = tienda, 6= sin promocion, 7= sin cedis
    */

    public function no_imagen($tipo = null): string
    {
        switch ($tipo) {
            case 1:
                return "/img/no-imagen/sin_user.png";
                break;
            case 2:
                return "/img/sin_datos/mercado.svg";
                break;
            case 3:
                return "/img/sin_datos/mercado-1.svg";
                break;
            case 4:
                return "/img/no-imagen/sin_cliente.png"; // sin foto de la tienda
                break;
            case 5:
                return "/img/no-imagen/pedidos_manuales.png"; // avatar para usuario sin foto o para pedidos manuales
                break;
            case 6:
                return "/img/no-imagen/promociones.png";
                break;
            case 7:
                return "/img/no-imagen/sin_cedis.png";
                break;
            case 8:
                return "/img/modales/Grupo 25651.svg";
                break;
            default:
                return "/img/no-imagen/default.jpg";
                break;
        }
    }
    public function convertir_gif_video($rutaArchivo)
    {
        // $this->gbp_api;
        $bucket = config("filesystems.disks.s3.bucket");
        $img = 'http://' . $bucket . '.s3.amazonaws.com/' . $rutaArchivo ?? '';


        return $img2 = $this->gbp_api . '/' . $bucket . '/s3.amazonaws.com/' . $rutaArchivo;
    }
    public static function getStoreFiles($file, $destinationPath = false, $nameFile = null, $ext = null)
    {
        try {
            if (isset($file)) {
                $response = [];
                $nombreReal = $file->getClientOriginalName();
                $response['filename'] = $file->getClientOriginalName();
                $response['peso'] = $file->getSize();
                $response['ext'] = !is_null($ext) ? $ext : $file->getClientOriginalExtension();
                $nombre_del_archivo = !is_null($nameFile) ? $nameFile : self::generateFileName($response['ext']);
                $archivo = Storage::putFileAs($destinationPath, $file, $nombre_del_archivo);
                return [
                    'ruta' => $archivo,
                    'ruta_mini' => null,
                    'nombre_real' => $nombreReal
                ];
            }
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function generateFileName($ext)
    {
        return uniqid(rand(), true) . str_replace(" ", "", microtime()) . ".$ext";
    }

    public static function toBase64($base, $ruta, $miniatura = false, $dimension_mini = 200)
    {
        try {
            $ex = explode("/", $base);
            $ext = explode(";", $ex[1]);
            $base64ImageParts = explode(',', $base);
            $base64ImageData = $base64ImageParts[1]; // Esto contiene los datos de la imagen en formato base64
            $imagenBinaria = base64_decode($base64ImageData);
            $nombre_aleatorio = uniqid(rand(), true) . str_replace(" ", "", microtime()) . ".$ext[0]";
            $subruta = ($ruta != '' ? "$ruta/" : '') . $nombre_aleatorio;
            // Guardar la imagen principal
            Storage::put($subruta, $imagenBinaria);
            // Crear y guardar la miniatura si se especifica
            if ($miniatura) {
                $imagen_mini = self::formatea_imagen($imagenBinaria, $dimension_mini);
                $nombre_aleatorio_mini = uniqid(rand(), true) . str_replace(" ", "", microtime()) . ".$ext[0]";
                $subruta_mini = ($ruta != '' ? "$ruta/" : '') . $nombre_aleatorio_mini;
                Storage::put($subruta_mini, (string)$imagen_mini->encode($ext[0]));
            }
            return [
                'estado' => true,
                'ruta' => $subruta,
                'ruta_mini' => ($miniatura && isset($subruta_mini)) ? $subruta_mini : null
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function deleteFile($fileName)
    {
        $route = 'trash/deleteFiles.json';
        try {
            $existingData = [];
            try {
                $body = Storage::get($route);
                $existingData = json_decode($body, true);
            } catch (\Exception $error) {
                if (!Str::contains($error->getMessage(), 'The specified key does not exist')) {
                    echo 'Error obteniendo el objeto existente: ' . $error->getMessage();
                }
            }
            $existingData[] = $fileName;
            Storage::put($route, json_encode($existingData, JSON_PRETTY_PRINT));
            return true;
        } catch (\Exception $error) {
            echo 'Error: ' . $error->getMessage();
        }
    }
}
