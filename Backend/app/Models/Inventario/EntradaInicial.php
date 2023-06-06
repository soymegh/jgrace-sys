<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use DB,Auth, Illuminate\Database\Eloquent\Model;

class EntradaInicial extends Model
{
    protected $table = 'inventario.entradas_inicial';
    protected $primaryKey='id_entrada_inicial';
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $dateFormat = 'Y-m-d H:i:s.u';
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
        $entradas->has('entradaProductos');

        /*if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $entradas->where($searchField, 'ilike', '%' . $searchValue . '%');
        }*/
        //$entradas->with('entradaBaterias');
//        $entradas->with('trabajadorEntradaInicial');
        $entradas->with('entradaBodega');

        $entradas->orderBy('fecha_entrada', 'desc');
        //print_r(Auth::user()->id_empleado);

        return $entradas->paginate($request->limit);
    }

    public function obtenerEntradaInvInicial($request)
    {
        $entrada = $this->select(['*']);
        $entrada->where('id_entrada_inicial', $request->id_entrada_inicial);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
        $entrada->where('id_empresa',$usuario_empresa->id_empresa);

        $entrada->with(['entradaProductos' => function($query)
        { $query->with('entradaProducto');}]);

//        $entrada->with(['entradaBaterias' => function($query)
//        { $query->with('productoSimple');}]);
//        $entrada->with('trabajadorEntradaInicial');
        $entrada->with('entradaBodega');


        return $entrada->first();
    }


    public function entradaProductos()
    {
        return $this->hasMany('App\Models\Inventario\EntradaInicialProductos','id_entrada_inicial');
    }

    public function entradaBodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega');
    }

}
