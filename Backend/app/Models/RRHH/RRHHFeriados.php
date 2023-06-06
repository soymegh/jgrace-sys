<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHFeriados extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.calendario_laboral';
    protected $primaryKey='id_feriado';
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

    public function obtenerDiasFeriados($request)
    {
        $nivel = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $nivel->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $nivel->where('estado',true);
            }
            $nivel->orderBy('id_feriado', 'asc');
        }
        return $nivel->paginate($request->limit);
    }

}
