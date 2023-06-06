<?php

namespace App\Models\CajaBanco;

use DB, Illuminate\Database\Eloquent\Model;

class ProformasSeguimiento extends Model {

	public $timestamps = false;
	protected $table = 'cjabnco.proformas_seguimiento';
	protected $primaryKey='id_proforma_seguimiento';
	const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';


    public function obtenerProformas($request)
    {
        $proformas = $this->select(['*']);
        if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $proformas->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $proformas->with('proformaVendedor');

        $proformas->orderBy('id_proforma', 'desc');

        return $proformas->paginate($request->limit);
    }

    public function proformaSeguimiento()
    {
        return $this->belongsTo('App\Models\CajaBanco\Proformas','id_proforma');
    }

    public function proformaVendedor()
    {
        return $this->belongsTo('App\Models\Ventas\Vendedores','id_vendedor')->select(['*','nombre_completo as text']);
    }
}
