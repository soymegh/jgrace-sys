<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHContratoTipos extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'rrhh.contratos_tipos';
    protected $primaryKey='id_contrato_tipo';
    protected $fillable = [];
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

    public function obtenerContratoTipo($request)
    {
        $contrato = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $contrato->where($searchField, 'ilike', '%' . $searchValue . '%');
            /*if($statusValue == 0){
                $contrato->where('activo',true);
            }*/
            $contrato->orderBy('id_contrato_tipo', 'asc');
        }

        return $contrato->paginate($request->limit);
    }

}
