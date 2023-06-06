<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class EntradaProductosCons extends Model {

    use HasFactory;

    protected $table = 'inventario.entradas_inicial_productos';
    protected $primaryKey='id_entrada_inicial_producto';
    public $timestamps=false;


    public function entradaInicial()
    {
        return $this->belongsTo('App\Models\InventarioEntradasInicial','id_entrada_inicial');
    }

    public function entradaProducto()
    {
        return $this->belongsTo('App\Models\InventarioProductos','id_producto');
    }
}
