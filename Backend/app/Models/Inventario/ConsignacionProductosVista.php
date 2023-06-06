<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class ConsignacionProductosVista extends Model {

    use HasFactory;

    public $timestamps = false;
    protected $table = 'inventario.vista_consignacion_cliente';
    protected $primaryKey='id_cliente';

    public function obtenerPorCliente($request)
    {


        $consignacion = $this->select(['*']);
        $consignacion->where('id_cliente',$request->cliente['id_cliente'])
            //->where('cantidad','>',0)
        ;

        return $consignacion->get();
    }

    public function obtenerTodos($request)
    {

        $consignacion = $this->select(['*']);
        //$consignacion->where('id_cliente',$request->cliente['id_cliente']);
        return $consignacion->get();
    }

}
