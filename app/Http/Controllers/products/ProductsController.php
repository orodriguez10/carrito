<?php

namespace App\Http\Controllers\products;

use App\Http\Controllers\Controller;
use App\Http\Requests\products\ProdcutRequest;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function createOrUpdateProduct(ProdcutRequest $request)
    {
        try {
            return DB::transaction(function () use($request){
                $producto = Producto::updateOrCreate(
                    ['ean_13' => $request->ean_13],
                    [
                        'nombre' => $request->nombre,
                        'activo' => $request->activo,
                        'precio' => $request->precio,
                        'stock' => $request->stock,
                    ]
                );
                return $this->susccesResponse(['message' => ('Guardado Correctamente'), 'data' => true]);
            },5);
        } catch (\Exception $error) {
            return $this->capturar($error, __("error al ingresar"));
        }
    }

    public function listProducts(Request $request)
    {
        try {
            $query = Producto::query();

            $query->when($request->has('precio_min'), function ($query) use ($request) {
                $query->where('precio', '>=', $request->precio_min);
            });

            $query->when($request->has('precio_max'), function ($query) use ($request) {
                $query->where('precio', '<=', $request->precio_max);
            });

            $query->when($request->has('disponibilidad'), function ($query) use ($request) {
                $query->where('activo', $request->disponibilidad);
            });

            $query->when($request->has('ean_13'), function ($query) use ($request) {
                $query->where('ean_13', 'like', '%' . $request->ean_13 . '%');
            });

            $listProducts = $query->get();
            return $this->susccesResponse(['message' => ('Listado Correctamente'), 'data' => $listProducts]);
        } catch (\Exception $error) {
            return $this->capturar($error, __("error al ingresar"));
        }
    }
}
