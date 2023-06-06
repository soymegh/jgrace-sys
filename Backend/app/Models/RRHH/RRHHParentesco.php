<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHParentesco extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.parentescos';
    protected $primaryKey='id_parentesco';
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

    public function obtenerParentesco($request)
    {
        $parentesco = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $parentesco->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $parentesco->where('estado',true);
            }
            $parentesco->orderBy('id_parentesco', 'asc');
        }
        return $parentesco->paginate($request->limit);
    }

}
