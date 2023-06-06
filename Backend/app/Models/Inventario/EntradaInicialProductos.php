<?php

namespace App\Models\Inventario;

use DB, Illuminate\Database\Eloquent\Model;

class EntradaInicialProductos extends Model {

	protected $table = 'inventario.entrada_inicial_productos';
	protected $primaryKey='id_entrada_inicial_productos';
	public $timestamps=false;


    public function entradaInicial()
    {
        return $this->belongsTo('App\Models\Inventario\EntradaInicial','id_entrada_inicial');
    }

    public function entradaProducto()
    {
        return $this->belongsTo('App\Models\Inventario\Productos','id_producto');
    }
}
