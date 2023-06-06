<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Invites extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'public.invites';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id';

    /**
     * Estacemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =[''];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     * @var array<int, string>
     */
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Obtener listado de codigos de invitación
     * filtro de busqueda, por empresa y  estado. Se muestra solamente los registros activos y se ocultan los inactivos
     * @param $request
     * @return mixed
     * @author octaviom
     */

    public function obtenerInvites($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $impuesto = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $impuesto->where($searchField, 'ilike', '%' . $searchValue . '%');
//            $impuesto->where('id_empresa',$usuario_empresa->id_empresa);
/*            if($statusValue === 0){
                $impuesto->where('estado',1);
                $impuesto->where('id_empresa',$usuario_empresa->id_empresa);
            }*/
            $impuesto->orderBy('id', 'asc');
        }
        return $impuesto->paginate($request->limit);
    }

}

