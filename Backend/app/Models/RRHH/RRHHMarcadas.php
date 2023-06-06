<?php

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHMarcadas extends Model
{
    /*const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';*/
    public $timestamps=false;
    protected $table = 'rrhh.marcadas';
    protected $primaryKey = 'id_marcada';
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

    public function obtenerMarcadas($request)
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
            $lista->orderBy('id_marcada','asc');
        }
        return $lista->paginate($request->limit);
    }

    public function asignacionIngreso()
    {
        return $this->belongsTo('App\Models\RRHHIngresosDeducciones','id_cat_ingreso_deduccion')->select('id_cat_ingreso_deduccion','cve_ingreso_deduccion','descripcion','codigo',
            DB::raw("CONCAT(descripcion,'(',cve_ingreso_deduccion,')' ) AS Ingreso"));
    }
    public function asignacionTrabajador()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadores','id_trabajador');
    }



}
