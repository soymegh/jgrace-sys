<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHTiposJustificacionesMarcadas extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.marcadas_tipos_justificaciones';
    protected $primaryKey = 'id_marcada_tipo_justificacion';
    protected $fillable = ['descripcion', 'activo'];
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * Replace Field
     *
     * @access    public
     * @param
     * @return    string
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
     * @access    public
     * @param
     * @return    json(array)
     */

    public function obtenerJustificacion($request)
    {
        $lista = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $lista->where($searchField, 'ilike', '%' . $searchValue . '%');
          /*  if($statusValue == 0){
                $lista->where('estado',true);
            }*/
            $lista->orderBy('id_marcada_tipo_justificacion','asc');
        }
        return $lista->paginate($request->limit);
    }

}
