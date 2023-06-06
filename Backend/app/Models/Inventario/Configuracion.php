<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class Configuracion extends Model {

    use HasFactory;
    public $timestamps = false;
    protected $table = 'inventario.configuracion_comprobante';

    protected $primaryKey='id_configuracion';

}
