<?php

namespace App\Models\Inventario;

use DB, Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'consulta_kardex_detallado';
    protected $primaryKey='id_bodega_producto';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /**
     * Obtener Lista de Salidas
     *
     * @access  public
     * @param
     * @return  json(array)
     */


    public function reporteKardex($request)
    {
        $kardex = $this->select(['*']);
        $kardex->where('consulta_kardex_detallado.id_bodega_producto', '=', $request->id_bodega_producto);
        //$kardex->with('bodega');
        //$kardex->with('producto');
        return $kardex->get();
    }

    public function bodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega');
    }

    public function producto()
    {
        return $this->hasMany('App\Models\Inventario\Productos','id_producto');
    }
}
