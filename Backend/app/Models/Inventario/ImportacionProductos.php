<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class ImportacionProductos extends Model {

    use HasFactory;

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.importaciones_producto';
    protected $primaryKey='id_importacion_producto';

    public function producto()
    {
        return $this->belongsTo('App\Models\InventarioProductos','id_producto');
    }
}
