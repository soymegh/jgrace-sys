<?php

namespace App\Models\CajaBanco;

use DB, Illuminate\Database\Eloquent\Model;

class Afectaciones extends Model
{
    public $timestamps = false;
    protected $table = 'cjabnco.facturas_afectaciones';
    protected $primaryKey='id_afectacion';
    protected $fillable = ['descripcion','valor'];

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
     * Obtener lista de Roles
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtener($request)
    {
        $afectaciones = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $afectaciones->where($searchField, 'ilike', '%' . $searchValue . '%');
            $afectaciones->with('afectacionCuentaContable');
            $afectaciones->orderBy('cjabnco.facturas_afectaciones.id_afectacion', 'asc');
        }
        return $afectaciones->paginate($request->limit);
    }


    public function afectacionCuentaContable()
    {
        return $this->belongsTo('App\Models\Contabilidad\CuentasContablesVista','id_cuenta_contable')->select('id_cuenta_contable','cta_contable','nombre_cuenta','nombre_cuenta_completo');
    }


}
