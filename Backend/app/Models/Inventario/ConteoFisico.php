<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class ConteoFisico extends Model {

    use HasFactory;

    protected $table = 'inventario.conteo_fisico';
    protected $primaryKey='id_conteo_fisico';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $timestamps=false;


    public function obtener($request)
    {
        $conteos = $this->select(['*'])->where('estado','<>',0);
        $conteos->has('conteoBaterias');

        /*if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $conteos->where($searchField, 'ilike', '%' . $searchValue . '%');
        }*/
        //$conteos->with('entradaBaterias');
        $conteos->with('trabajadorConteo');
        $conteos->with('conteoBodega');

        $conteos->orderBy('fecha_conteo', 'desc');
        //print_r(Auth::user()->id_empleado);

        return $conteos->paginate($request->limit);
    }

    public function obtenerConteoFisico($request)
    {
        $entrada = $this->select(['*']);
        $entrada->where('id_conteo_fisico', $request->id_conteo_fisico);

        /*$entrada->with(['entradaProductos' => function($query)
        { $query->with('entradaProducto');}]);*/

        $entrada->with(['conteoBaterias' => function($query)
        { $query->with('productoSimple');}]);


        $entrada->with('trabajadorConteo');
        $entrada->with('conteoBodega');


        return $entrada->first();
    }

    public function conteoBaterias()
    {
        return $this->hasMany('App\Models\InventarioConteoFisicoBaterias','id_conteo_fisico');
    }

    /*public function entradaProductos()
    {
        return $this->hasMany('App\Models\InventarioEntradaProductosCons','id_entrada_inicial');
    }*/

    public function conteoBodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega');
    }
    public function trabajadorConteo()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadores','id_trabajador')->select('id_trabajador','primer_apellido','primer_nombre','segundo_apellido','segundo_nombre','codigo',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre_completo"));
    }

}
