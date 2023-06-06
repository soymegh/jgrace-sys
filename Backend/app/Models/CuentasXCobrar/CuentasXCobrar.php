<?php

namespace App\Models\CuentasXCobrar;

use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB, Illuminate\Database\Eloquent\Model;
use App\Models\CuentasXCobrar\RecibosDetalles;
use App\Models\CajaBanco\Facturas;

class CuentasXCobrar extends Model {

	const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
	protected $table = 'cuentasxcobrar.cuentasxcobrar';
	protected $primaryKey='id_cuentaxcobrar';

    public function obtener($request)
    {
        $recibos = $this->select(['*']);

        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $recibos->where($searchField, 'ilike', '%' . $searchValue . '%');
        }

        if(!empty($request->type==='trabajador')){
            $recibos->whereIn('id_tipo_documento',array(5,6,7,8));
            $recibos->with('cuentaEmpleado');
        }

        if(!empty($request->type==='cliente')){
            $recibos->whereIn('id_tipo_documento',array(1));
            /*$recibos->whereHas('cuentaCliente', function($query) {
                $query('cuentaCliente.es_deudor', false);
            });*/
            $recibos->whereHas(
                'cuentaCliente', function ($query) {
                $query->where('es_deudor', false);
            }
            )->with('cuentaCliente')->get();
        }

        if(!empty($request->type==='deudor')){
            $recibos->whereIn('id_tipo_documento',array(1,2,3,4));
            $recibos->whereHas(
                'cuentaCliente', function ($query) {
                $query->where('es_deudor', true);
            }
            )->with('cuentaCliente')->get();
        }

        $recibos->with('cuentasDetalles');
        $recibos->orderBy('id_cuentaxcobrar','desc');

        return $recibos->paginate($request->limit);
    }

    public function obtenerCuentasCliente($request)
    {
        $productos = $this->select([
            'id_cuentaxcobrar',
            'id_cliente',
            'id_tipo_documento',
            'no_documento_origen',
            'identificador_origen',
            'monto_movimiento',
            'saldo_actual',
            'saldo_actual_me',
            'fecha_movimiento',
            'fecha_vencimiento',
            'descripcion_movimiento',
            'usuario_registra',
            'es_registro_importado',
            'estado',
            DB::raw("CONCAT(no_documento_origen,' ( Saldo: C$ ',round(saldo_actual,2),'. VENC:: ', fecha_vencimiento::date , ')') AS text"),
            DB::raw("CONCAT(no_documento_origen,' ( Saldo: $ ',round(saldo_actual_me,2),'. VENC:: ', fecha_vencimiento::date , ')') AS textDol")
        ]);
        if ((!empty($request->id_cliente))) {
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
            $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first(); //
            $productos->Where('id_cliente', $request->id_cliente);
            if($currency_id === 1){
                $productos->Where('saldo_actual', '>',0);
            }else{
                $productos->Where('saldo_actual_me', '>',0);
            }

            $productos->with('cuentaFactura');
            $productos->orderBy('fecha_vencimiento', 'asc');
            return $productos->get();
        }else{
            return $productos->limit(0)->get();
        }
    }


    public function obtenerCuentasTrabajador($request)
    {
        $productos = $this->select([
            'id_cuentaxcobrar',
            'id_trabajador',
            'id_tipo_documento',
            'no_documento_origen',
            'identificador_origen',
            'monto_movimiento',
            'saldo_actual',
            'fecha_movimiento',
            'fecha_vencimiento',
            'descripcion_movimiento',
            'usuario_registra',
            'es_registro_importado',
            'estado',
            DB::raw("CONCAT(no_documento_origen,' ( Saldo: C$ ',round(saldo_actual,2),')') AS text")
        ]);
        if ((!empty($request->id_trabajador))) {
            $productos->Where('id_trabajador', $request->id_trabajador);
            $productos->Where('saldo_actual', '>',0);
            //$productos->with('cuentaFactura');
            $productos->orderBy('fecha_vencimiento', 'asc');
            return $productos->get();
        }else{
            return $productos->limit(0)->get();
        }
    }


    public function cuentaCliente()
    {
        return $this->belongsTo('App\Models\Ventas\Clientes','id_cliente')
            ->select('id_cliente','contacto','correo','direccion','nombre_comercial','numero_cedula','numero_ruc','telefono');
    }

    public function cuentaEmpleado()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadores','id_trabajador');
    }

    public function cuentaFactura()
    {
        return $this->belongsTo(Facturas::class,'identificador_origen','id_factura');
    }

    public function cuentasDetalles()
    {
        return $this->hasMany(RecibosDetalles::class,'id_cuentaxcobrar');
    }

}
