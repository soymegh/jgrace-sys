<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use DB, Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TipoProveedor extends Model
{
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.tipo_proveedores';
    protected $primaryKey='id_tipo_proveedor';
    protected $fillable = ['descripcion','secuencia','clasificacion_contable','estado'];
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
     * Obtener Listado de Tipos de entrada
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerTiposProveedor($request)
    {
        $tipoProveedor = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $tipoProveedor->where($searchField, 'ilike', '%' . $searchValue . '%');
            $tipoProveedor->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $tipoProveedor->where('estado',1);
            }
            $tipoProveedor->orderBy('id_tipo_proveedor', 'asc');
        }
        return $tipoProveedor->paginate($request->limit);
    }

}
