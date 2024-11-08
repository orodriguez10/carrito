<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;
    protected $table = 'carritos';
    public $timestamps = false;

    protected $fillable = [
        'producto',
        'usuario',
        'stock',
        'precio_total',
    ];

    public function product()
    {
        return $this->belongsTo(Producto::class, 'producto');
    }
}
