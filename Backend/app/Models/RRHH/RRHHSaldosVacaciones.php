<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHSaldosVacaciones extends Model
{
    /*const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';*/
    public $timestamps = false;
    protected $table = 'rrhh.vacaciones_saldos';
    protected $primaryKey='id_vacacion_saldo';
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

    public function obtenerSaldoVacaciones($request)
    {
        $solicitud = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $solicitud->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $solicitud->where('estado',true);
            }*/
            $solicitud->with('saldoEmpleado');
            $solicitud->with('trabajadorCargo');
            $solicitud->with('trabajadorArea');
            $solicitud->orderBy('id_vacacion_saldo', 'asc');
        }
        return $solicitud->paginate($request->limit);
    }

    public function saldoEmpleado()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadores','id_trabajador');
    }
    public function trabajadorCargo()
    {
        return $this->belongsTo('App\Models\RRHHCargos','id_cargo');
    }

    public function trabajadorArea()
    {
        return $this->belongsTo('App\Models\PublicAreas','id_area');
    }
}
