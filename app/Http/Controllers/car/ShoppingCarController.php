<?php

namespace App\Http\Controllers\car;

use App\Http\Controllers\Controller;
use App\Http\Requests\shopping\ShoppingCarRequest;
use App\Models\Carrito;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingCarController extends Controller
{
    public function addToCart(ShoppingCarRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $producto = Producto::findOrFail($request->producto);

                if ($producto->stock < $request->stock) {
                    return $this->errorResponse('Stock insuficiente', 400);
                }

                $precioTotal = $producto->precio * $request->stock;

                $carrito = Carrito::create([
                    'producto' => $producto->id,
                    'usuario' => auth()->user()->id,
                    'stock' => $request->stock,
                    'precio_total' => $precioTotal,
                ]);

                $producto->decrement('stock', $request->stock);

                return $this->susccesResponse(['message' => 'Producto añadido al carrito', 'data' => $carrito]);
            }, 5);
        } catch (\Exception $error) {
            return $this->capturar($error, __("error al ingresar"));
        }
    }

    public function viewCart()
    {
        try {
            $carrito = Carrito::where('usuario', auth()->user()->id)
                ->with('product')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'producto' => $item->product->nombre,
                        'stock' => $item->stock,
                        'precio' => $item->product->precio,
                        'impuesto' => $item->impuesto,
                        'precio_total' => $item->precio_total,
                    ];
                });

            return $this->susccesResponse(['message' => 'Carrito obtenido correctamente', 'data' => $carrito]);
        } catch (\Exception $error) {
            return $this->capturar($error, __("error al ingresar"));
        }
    }
}
