<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class TipoBodega extends Model {

    use HasFactory;

    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.tipo_bodegas';
    protected $primaryKey='id_tipo_bodega';
    protected $fillable = ['descripcion','u_grabacion','u_modificacion','estado'];
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

    public function obtenerTiposBodega($request)
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
                $users->where('estado',true);
                $users->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $users->orderBy('id_tipo_bodega', 'asc');
        }
        return $users->paginate($request->limit);
    }

}
