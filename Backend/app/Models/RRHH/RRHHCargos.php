<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHCargos extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = 'f_creacion';
    //public $timestamps = false;
    protected $table = 'rrhh.cargos';
    protected $primaryKey='id_cargo';
    protected $dateFormat = 'Y-m-d H:i:s.u';
    protected $fillable = ['descripcion','u_creacion','activo'];
    /* const UPDATED_AT = 'f_modificacion';*/

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
     * Obtener Listado de Bancos
     *
     * @access 	public
     * @param 	
     * @return 	json(array)
     */

    public function obtenerCargos($request)
    {
        $bancos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $bancos->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $bancos->where('activo',true);
            }
            $bancos->orderBy('id_cargo', 'asc');
        }
        return $bancos->paginate($request->limit);
    }

}
