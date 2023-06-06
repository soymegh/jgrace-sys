<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class ImportacionOrdenesCompra extends Model {

    use HasFactory;

    public $timestamps = false;
    protected $table = 'inventario.importaciones_ordenes_compras';
    protected $primaryKey='id_importacion_orden_compra';
}
