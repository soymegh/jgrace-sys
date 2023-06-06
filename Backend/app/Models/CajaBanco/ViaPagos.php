<?php

namespace App\Models\CajaBanco;

use DB, Illuminate\Database\Eloquent\Model;

class ViaPagos extends Model
{

    protected $table = 'public.via_pagos';
    protected $primaryKey='id_via_pago';
    protected $fillable = ['descripcion','activo'];
    public $timestamps = false;

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

    public function obtenerViaPagos($request)
    {
        $viaPago = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $viaPago->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $viaPago->where('activo',true);
            }
            $viaPago->orderBy('id_via_pago', 'asc');
        }
        return $viaPago->paginate($request->limit);
    }

}
