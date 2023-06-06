<?php

namespace App\Models\CajaBanco;

use DB, Illuminate\Database\Eloquent\Model;

class FacturaViaPagos extends Model {

	public $timestamps = false;
	protected $table = 'cjabnco.factura_via_pagos';
	protected $primaryKey='id_factura_via';

}
