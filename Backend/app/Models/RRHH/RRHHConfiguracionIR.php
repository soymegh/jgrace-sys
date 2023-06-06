<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHConfiguracionIR extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.configuraciones_ir';
    protected $primaryKey='id_configuracion_ir';
    protected $fillable = ['id_configuracion_ir','monto_inicio','monto_final','impuesto_basa','porcentaje','sobre_exceso'];
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

    public function obtenerConfiguracionIR($request)
    {
        $configuracion = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $configuracion->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $configuracion->where('activo',true);
            }*/
            $configuracion->orderBy('id_configuracion_ir', 'asc');
        }
        return $configuracion->paginate($request->limit);
    }

}
