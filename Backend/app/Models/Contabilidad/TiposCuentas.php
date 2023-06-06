<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class TiposCuentas extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.tipos_cuentas';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_tipo_cuenta';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','tipo_abreviado','naturaleza','u_creacion','u_modificacion','estado','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
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
     * Obtener Lista de cuenta según el id_empresa
     * @param $request
     * @return mixed
     */
    public function obtenerTiposCuenta($request)
    {
        $tiposCuenta = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];

            $tiposCuenta->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $tiposCuenta -> where('id_empresa',$usuario_empresa->id_empresa);
         //   ->where($searchField, 'ilike', '%' . $searchValue . '%');
        $tiposCuenta->with('tipoCuentaEstadoFinanciero');
        $tiposCuenta->orderBy('contabilidad.tipos_cuentas.id_tipo_cuenta');
        return $tiposCuenta->paginate($request->limit);
    }



    /**
     * Relación Periodos Fiscal - Empresa
     * @return BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }


    /**
     * Relación Tipos de cuentas - Estado finaciero
     * @return BelongsTo
     */
    public function tipoCuentaEstadoFinanciero()
    {
        return $this->belongsTo('App\Models\Contabilidad\EstadosFinancieros','id_estado_financiero');
    }

}





