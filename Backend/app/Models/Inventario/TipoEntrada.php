<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use DB, Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TipoEntrada extends Model
{
    protected $table = 'inventario.tipo_entradas';
    protected $primaryKey='id_tipo_entrada';
    protected $fillable = ['tipo_entrada','u_registra','estado'];
    const CREATED_AT = 'f_grabacion';
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
     * Obtener Listado de Tipos de entrada
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerTiposEntrada($request)
    {
        $users = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $users->where($searchField, 'ilike', '%' . $searchValue . '%');
            $users->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $users->where('estado',1);
                $users->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $users->orderBy('id_tipo_entrada', 'asc');
        }
        return $users->paginate($request->limit);
    }

}
