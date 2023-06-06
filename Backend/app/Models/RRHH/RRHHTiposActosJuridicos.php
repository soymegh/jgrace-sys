<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHTiposActosJuridicos extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.tipos_actos_juridicos';
    protected $primaryKey='id_tipo_acto_juridico';
    protected $fillable = ['id_tipo_acto_juridico','descripcion'];
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

    public function obtenerTipoActoJuridico($request)
    {
        $tipo = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $tipo->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $tipo->where('estado',true);
            }
            $tipo->orderBy('id_tipo_acto_juridico', 'asc');
        }
        return $tipo->paginate($request->limit);
    }

}
