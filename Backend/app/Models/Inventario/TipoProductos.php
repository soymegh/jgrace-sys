<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class TipoProductos extends Model {

    use HasFactory;

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.tipo_productos';
    protected $primaryKey='id_tipo_producto';
    protected $fillable = ['descripcion','u_creacion','u_modificacion','estado'];
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

    public function obtenerTipoProducto($request)
    {
        $parentesco = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $parentesco->where($searchField, 'ilike', '%' . $searchValue . '%');
            $parentesco->where('id_empresa', $usuario_empresa->id_empresa);
            if($statusValue === 0){
                $parentesco->where('estado',1);
            }
            $parentesco->orderBy('id_tipo_producto', 'asc');
        }
        return $parentesco->paginate($request->limit);
    }


}
