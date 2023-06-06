<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class InventarioFisicoProductos extends Model {

    use HasFactory;

    public $timestamps= false;
    protected $table = 'inventario.inventarios_fisicos_productos';
    protected $primaryKey='id_inventario_fisico_producto';

    public function producto()
    {
        return $this->belongsTo('App\Models\Inventario\Productos','id_producto')->select(['id_producto','codigo_barra','codigo_consecutivo','codigo_sistema','condicion','costo_estandar','descripcion',DB::raw("CONCAT(inventario.productos.nombre_comercial,' (',inventario.productos.codigo_barra,')') AS text")]);
    }
}
