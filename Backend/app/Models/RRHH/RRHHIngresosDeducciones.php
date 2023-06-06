<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHIngresosDeducciones extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.cat_ingresos_deducciones';
    protected $primaryKey='id_cat_ingreso_deduccion';
    protected $fillable = ['id_cat_ingreso_deduccion','cve_ingreso_deduccion','codigo','descripcion','orden','abreviacion',
        'id_cuenta_contable_venta','id_cuenta_contable_administrativa','grabable','estado'];
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


    public function obtenerIngresos($request)
    {
        $deduccion = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $deduccion->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $deduccion->where('activo',true);
            }*/
            $deduccion->where('cve_ingreso_deduccion','I');
            $deduccion->orderBy('id_cat_ingreso_deduccion', 'asc');
        }
        $deduccion->with('ingresoDeduccionCuentaContable');
        $deduccion->with('ingresoDeduccionCuentaContableAdministrativa');
        return $deduccion->paginate($request->limit);
    }

    public function obtenerDeducciones($request)
    {
        $deduccion = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $deduccion->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $deduccion->where('activo',true);
            }*/
            $deduccion->where('cve_ingreso_deduccion','D');
            $deduccion->orderBy('id_cat_ingreso_deduccion', 'asc');
        }
        $deduccion->with('ingresoDeduccionCuentaContable');
        $deduccion->with('ingresoDeduccionCuentaContableAdministrativa');
        return $deduccion->paginate($request->limit);
    }

    public function obtenerIngresosDeducciones($request)
    {
        $deduccion = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $deduccion->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $deduccion->where('activo',true);
            }*/
            $deduccion->orderBy('id_cat_ingreso_deduccion', 'asc');
        }
        $deduccion->with('ingresoDeduccionCuentaContable');
        $deduccion->with('ingresoDeduccionCuentaContableAdministrativa');
        return $deduccion->paginate($request->limit);
    }


    public function ingresoDeduccionCuentaContable()
    {
        return $this->belongsTo('App\Models\ContabilidadCuentasContablesVista','id_cuenta_contable_venta')->select('id_cuenta_contable','cta_contable','nombre_cuenta_completo');
    }

    public function ingresoDeduccionCuentaContableAdministrativa()
    {
        return $this->belongsTo('App\Models\ContabilidadCuentasContablesVista','id_cuenta_contable_administrativa')->select('id_cuenta_contable','cta_contable','nombre_cuenta_completo');
    }
}
