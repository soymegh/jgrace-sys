<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class EstadosFinancieros extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.estados_financieros';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_estado_financiero';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','codigo_maximo','u_creacion','u_modificacion','activo','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';




    /**
     * Obtener Lista de Niveles cuenta segun el id_empresa
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */
    public function obtenerNivelesCuenta($request)
    {
        $nivelesCuenta = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $nivelesCuenta -> where('id_empresa',$usuario_empresa->id_empresa);
            $nivelesCuenta->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $nivelesCuenta->orderBy('id_nivel_cuenta');
        return $nivelesCuenta->paginate($request->limit);
    }

    /**
     * Relación Estados Financieros - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

}





