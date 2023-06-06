<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHGruposFamiliares extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.grupos_familiares';
    protected $primaryKey='id_grupo_familiar';
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

    public function obtenerGrupoFamiliar($request)
    {
        $grupo = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $grupo->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $grupo->where('estado',true);
            }*/
            $grupo->with('GrupoParentesco');
            $grupo->orderBy('id_grupo_familiar', 'asc');
        }
        return $grupo->paginate($request->limit);
    }

    public function GrupoParentesco()
    {
        return $this->belongsTo('App\Models\RRHHParentesco','id_parentesco');
    }

}
