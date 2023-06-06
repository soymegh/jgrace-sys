<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class CategoriasProductos extends Model
{
    use HasFactory;
    protected $table = 'inventario.categorias_productos';
    protected $primaryKey='id_categoria';
    protected $fillable = ['nombre_categoria', 'descripcion', 'estado','u_creacion','u_modificacion'];
    protected $dateFormat = 'Y-m-d H:i:s.u';

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
     * Obtener Lista de Proveedores
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtener($request)
    {
        $categorias = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $categorias->where('id_empresa',$usuario_empresa->id_empresa);
            $categorias->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $categorias->where('estado',1);
            }
            $categorias->orderBy('id_categoria', 'asc');
        }
        return $categorias->paginate($request->limit);
    }

}
