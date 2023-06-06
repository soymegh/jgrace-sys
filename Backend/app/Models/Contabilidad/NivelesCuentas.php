<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class NivelesCuentas extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.niveles_cuentas';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_nivel_cuenta';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','id_empresa','estado','u_creacion','u_modificacion'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';




    /**
     * Obtener Lista de Estados Financieros segun el id_empresa
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
            $statusValue = $request->search['status'];
            $nivelesCuenta->where($searchField, 'ilike', '%' . $searchValue . '%');
            $nivelesCuenta -> where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $nivelesCuenta->where('activo',true);
            }
        }
        $nivelesCuenta->orderBy('id_nivel_cuenta');
        return $nivelesCuenta->paginate($request->limit);
    }

    /**
     * Relación Cuenta Bancaria - Empresa
     * @return BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }


}





