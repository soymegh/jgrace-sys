<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHPlanillaControl extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.planillas_controles';
    protected $primaryKey='id_planilla_control';
    protected $fillable = [];
    //protected $dateFormat = 'Y-m-d H:i:s.u';

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

    public function obtenerPlanillaControl($request)
    {
        $contrato = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $contrato->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $contrato->where('estado',true);
            }
            $contrato->with('PlanillaSucursal');
            $contrato->orderBy('id_planilla_control', 'desc');
        }

        return $contrato->paginate($request->limit);
    }

    public function PlanillaSucursal()
    {
        return $this->BelongsTo('App\Models\PublicSucursales','id_sucursal');
    }
}
