<?php

namespace App\Models\CuentasXCobrar;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB, Illuminate\Database\Eloquent\Model;
use App\Models\CuentasXCobrar\RecibosDetalles;
use App\Models\Ventas\Clientes;

class Recibos extends Model
{
    protected $table = 'cuentasxcobrar.recibos_ingresos';
    protected $primaryKey='id_recibo';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = ['num_documento','beneficiario','valor','estado'];

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

    /**
     * Obtener Lista de entradas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
        $recibos = $this->select(['*']);
        if (!empty($request->search['field'])) {
           $searchField = $request->search['field'];
           $searchValue = $request->search['value'];
           $recibos->where($searchField, 'ilike', '%' . $searchValue . '%');
       }
        $recibos->where('id_empresa',$usuario_empresa->id_empresa);
        $recibos->whereNotIn('estado',array(0));
        $recibos->whereIn('tipo_recibo',array(1,2,3));
        $recibos->with('reciboDetalles','reciboCliente');
//        $recibos->where('tipo_recibo',$request->search['type']);
/*        if($request->search['type']===1){
            $recibos->with('reciboDetalles','reciboCliente');
        }

        if($request->search['type']===2){
            $recibos->with('reciboDetalles','reciboTrabajador');
        }*/

        $recibos->orderBy('id_recibo','desc');

        return $recibos->paginate($request->limit);
    }

    /**
     * Buscar recibos por cliente y proyecto
     * @param $request
     * @return
     */
    public function obtenerRecibosCliente($request)
    {
        $productos = $this->select([
            'id_recibo',
            'tipo_recibo',
            'no_documento',
            'fecha_emision',
            'id_cliente',
            'nombre_persona',
            'concepto',
            't_cambio',
            'monto_total',
            'monto_total_me',
            'id_proyecto',
            'u_creacion',
            'estado'
        ]);
        if ((!empty($request->id_cliente)) && (!empty($request->id_proyecto))) {
            $productos->Where('id_cliente', $request->id_cliente);
            $productos->Where('id_proyecto', $request->id_proyecto);
            $productos->Where('estado', '=',1);
            return $productos->get();
        }else{
            return $productos->limit(0)->get();
        }
    }


    public function reciboDetalles()
    {
        return $this->hasMany(RecibosDetalles::class,'id_recibo');
    }

    public function reciboCliente()
    {
        return $this->belongsTo(Clientes::class,'id_cliente')->select('id_cliente','tipo_persona','nombre_comercial','numero_ruc','numero_cedula');
    }

    public function reciboTrabajador()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadores','id_trabajador')->select('id_trabajador','primer_nombre','primer_apellido','segundo_nombre','segundo_apellido');
    }

    /*public function reciboMoneda()
    {
        return $this->belongsTo('App\Models\CajaBancoMonedas','id_moneda');
    }*/

}
