<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class RequisaProductos extends Model {

    use HasFactory;

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.requisas_productos';
    protected $primaryKey='id_requisa_producto';

    public function bodegaProducto()
    {
        return $this->belongsTo('App\Models\InventarioBodegaProductos','id_bodega_producto');
    }

}
