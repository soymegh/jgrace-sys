<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Direcciones extends Model
{
    protected $table = 'public.direcciones';
    protected $dateFormat = 'Y-m-d H:i:s.u';
    protected $primaryKey='id_direccion';
    protected $fillable = ['descripcion','u_creacion','estado','id_empresa','u_creacion','u_modificacion'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
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
     * Obtener Listado de Direcciones
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerDirecciones($request)
    {
        $bancos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $bancos->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $bancos->where('estado',1);
            }
            $bancos->with('direccionSucursal');
            $bancos->orderBy('id_direccion', 'asc');
        }
        return $bancos->paginate($request->limit);
    }

    public function direccionSucursal()
    {
        return $this->belongsTo('App\Models\Admon\Sucursales', 'id_sucursal') ->select('id_sucursal','descripcion','serie');
    }
}



