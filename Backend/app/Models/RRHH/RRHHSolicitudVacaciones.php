<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHSolicitudVacaciones extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.vacaciones_solicitudes';
    protected $primaryKey='id_vacacion_solicitud';
    protected $fillable = ['descripcion','activo'];
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
     * Obtener Listado de Paises
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

    public function obtenerSolicitudVacaciones($request)
    {
        $solicitud = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $solicitud->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $solicitud->whereIn('estado', array(1,2));
            }
            $solicitud->with('solicitudDetalle');
            $solicitud->with('solicitudTrabajador');
            $solicitud->with('trabajadorDetalles');
            $solicitud->with('trabajadorSaldoVacacion');
            $solicitud->orderBy('id_vacacion_solicitud', 'asc');
        }
        return $solicitud->paginate($request->limit);
    }

    public function solicitudDetalle()
    {
        return $this->hasMany('App\Models\RRHHSolicitudVacacionesDetalle','id_vacacion_solicitud');
    }

    public function solicitudTrabajador()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadores','id_trabajador')->select('id_trabajador','id_area','id_cargo','primer_apellido','primer_nombre','segundo_apellido','segundo_nombre','codigo',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre_completo"),
            DB::raw("coalesce((select sv.saldo_actual from rrhh.vacaciones_saldos sv where sv.id_trabajador = rrhh.trabajadores.id_trabajador),0) AS saldo_actual"));
    }
    public function trabajadorDetalles()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadoresDetalles','id_trabajador');
    }

    public function trabajadorCargo()
    {
        return $this->belongsTo('App\Models\RRHHCargos','id_cargo');
    }
    public function trabajadorSaldoVacacion()
    {
        return $this->belongsTo('App\Models\RRHHSaldosVacaciones','id_trabajador');
    }

    public function trabajadorArea()
    {
        return $this->belongsTo('App\Models\PublicAreas','id_area');
    }




}
