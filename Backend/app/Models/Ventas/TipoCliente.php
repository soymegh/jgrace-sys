<?php

namespace App\Models\Ventas;

use App\Models\Admon\UsuariosEmpresas;
use DB, Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TipoCliente extends Model
{

    protected $table = 'venta.tipo_clientes';
    protected $primaryKey='id_tipo_cliente';
    protected $fillable = ['descripcion','u_creacion','u_modificacion','id_empresa'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
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
     * Obtener Listado de Paises
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerTiposClientes($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $tipo_cliente = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $tipo_cliente->where($searchField, 'ilike', '%' . $searchValue . '%');
            $tipo_cliente->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue===0){
                $tipo_cliente->where('estado',1);
                $tipo_cliente->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $tipo_cliente->orderBy('id_tipo_cliente', 'asc');
        }
        return $tipo_cliente->paginate($request->limit);
    }

}
