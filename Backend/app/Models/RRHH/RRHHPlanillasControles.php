<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHPlanillasControles extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.planillas_controles';
    protected $primaryKey='id_planilla_control';
    protected $fillable = ['id_planilla_control','id_sucursal','codigo_planilla','tipo_planilla',
        'descripcion','f_desde','f_hasta','anio_proceso','mes_proceso','f_calculo','estado'];
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * Replace Field
     *
     * @access 	public
     * @param 	
     * @return 	string
     */


	public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields)) {
            return $fields[$field];
        }

        return $field;
    }

    /**
     * Obtener Listado de contrato
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

    public function obtenerPlanillasControles($request)
    {
        $planilla = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $planilla->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $planilla->where('activo',true);
            }*/
            $planilla->orderBy('id_planilla_control', 'asc');
        }
        $planilla->with('planillaControlSucursal');
        //$planilla->with('ingresoDeduccionCuentaContableAdministrativa');
        return $planilla->paginate($request->limit);
    }


    public function planillaControlSucursal()
    {
        return $this->belongsTo('App\Models\PublicSucursales','id_sucursal');
    }

    public function planillaPeriodoProceso()
    {
        return $this->belongsTo('App\Models\ContabilidadPeriodoFiscal','anio_proceso','periodo')->with('mesesPeriodo');
    }
    public function planillaMesProceso()
    {
        return $this->belongsTo('App\Models\ContabilidadPeriodoMeses','mes_proceso','mes')
            ->select('id_periodo_fiscal','descripcion','id_periodo_mes','mes','estado',
                DB::raw("contabilidad.ultimo_dia_mes(id_periodo_mes)"),
                DB::raw("case when mes=1 then 'Enero' when mes=2 then 'Febrero' when mes=3 then 'Marzo' when mes=4 then 'Abril'
                 when mes=5 then 'Mayo' when mes=6 then 'Junio' when mes=7 then 'Julio' when mes=8 then 'Agosto' 
                  when mes=9 then 'Septiembre' when mes=10 then 'Octubre' when mes=11 then 'Noviembre' when mes=12 then 'Diciembre' end as mes_letras"))->orderby('mes');;
    }

    public function ingresoDeduccionCuentaContableAdministrativa()
    {
        return $this->belongsTo('App\Models\ContabilidadCuentasContablesVista','id_cuenta_contable_administrativa')->select('id_cuenta_contable','cta_contable','nombre_cuenta_completo');
    }
}
