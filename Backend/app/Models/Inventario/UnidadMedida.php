<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class UnidadMedida extends Model {

    use HasFactory;

    protected $table = 'inventario.unidades_medidas';
    protected $primaryKey='id_unidad_medida';
    protected $fillable = ['siglas','u_grabacion','estado'];
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
     * Obtener Listado de unidad de medida
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtener($request)
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
            }
            $users->orderBy('id_unidad_medida', 'asc');
        }
        return $users->paginate($request->limit);
    }

}
