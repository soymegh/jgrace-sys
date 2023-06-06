<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class EntradasInicial extends Model {

    use HasFactory;

    protected $table = 'inventario.entradas_inicial';
    protected $primaryKey='id_entrada_inicial';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['fecha_entrada','id_bodega','estado'];
    public $timestamps=false;


    public function obtener($request)
    {
        // $entradas = InventarioEntradas::with('entradaProveedor');
        $entradas = $this->select(['*'])->where('estado','<>',0);
        $entradas->has('entradaBaterias')->orHas('entradaProductos');

        /*if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $entradas->where($searchField, 'ilike', '%' . $searchValue . '%');
        }*/
        //$entradas->with('entradaBaterias');
        $entradas->with('trabajadorEntradaInicial');
        $entradas->with('entradaBodega');

        $entradas->orderBy('fecha_entrada', 'desc');
        //print_r(Auth::user()->id_empleado);

        return $entradas->paginate($request->limit);
    }

    public function obtenerEntradaInvInicial($request)
    {
        $entrada = $this->select(['*']);
        $entrada->where('id_entrada_inicial', $request->id_entrada_inicial);

        $entrada->with(['entradaProductos' => function($query)
        { $query->with('entradaProducto');}]);

        $entrada->with('trabajadorEntradaInicial');
        $entrada->with('entradaBodega');


        return $entrada->first();
    }

    public function entradaBaterias()
    {
        return $this->hasMany('App\Models\Inventario\EntradaInicialProductos','id_entrada_inicial');
    }

    public function entradaProductos()
    {
        return $this->hasMany('App\Models\Inventario\EntradaProductosCons','id_entrada_inicial');
    }

    public function entradaBodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega');
    }

}
