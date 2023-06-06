<?php

namespace App\Models\CuentasXCobrar;

use Illuminate\Support\Facades\DB, Illuminate\Database\Eloquent\Model;

class ReciboViaPagos extends Model {

	public $timestamps = false;
	protected $table = 'cuentasxcobrar.recibos_via_pagos';
	protected $primaryKey='id_recibo_via';

}
