<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class CuentasContablesVista extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.v_cuentas_contables';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_cuenta_contable';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['nombre_cuenta','codigo_cuenta','id_cuenta_padre','cta_contable','id_tipo_cuenta','id_nivel_cuenta','permite_movimiento','u_creacion','u_modificacion','estado','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';




    /**
     * Obtener lista de cuentas contables el id empresa del usurio
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */
    public function obtenerCuentasContables($request)
    {
        $cuentas_contables = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $cuentas_contables->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $cuentas_contables->where('estado',true);
            }
            $cuentas_contables -> where('id_empresa',$usuario_empresa->id_empresa);
            $cuentas_contables->with('cuentaPadre');
            $cuentas_contables->with('cuentaNivel');
            $cuentas_contables->with(['cuentaTipo' => function($query) {
                $query->with('tipoCuentaEstadoFinanciero');
            }]);
            $cuentas_contables->orderBy('cta_contable');
        }
        return $cuentas_contables->paginate($request->limit);
    }

    /**
     * Buscar las cuentas contables según el id_empresa
     * Busquedade cuentas contables
     * @param $request
     * @return string
     */
    public function buscarCuentasContables($request)
    {
        $cuentas_contables = $this->select(['*','contabilidad.v_cuentas_contables.nombre_cuenta_completo AS text']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

        if ((!empty($request->q)) && (!empty($request->id_nivel_cuenta)) && (is_numeric($request->id_nivel_cuenta))) {
            $searchValue = $request->q;
            $cuentas_contables->where('contabilidad.v_cuentas_contables.estado', '=', 1);
            $cuentas_contables->where('id_empresa',$usuario_empresa->id_empresa);
//            $cuentas_contables->where('contabilidad.v_cuentas_contables.id_nivel_cuenta', '=', $request->id_nivel_cuenta);
            $cuentas_contables->with(['cuentaTipo' => function($query) {
                $query->with('tipoCuentaEstadoFinanciero');
            }]);
            $cuentas_contables->where('contabilidad.v_cuentas_contables.nombre_cuenta_completo', 'ILIKE', '%' . $searchValue . '%');
            $cuentas_contables->orderBy('contabilidad.v_cuentas_contables.nombre_cuenta_completo', 'asc');
            return $cuentas_contables->limit(6)->get();
        }else{
            return '';
        }

    }

    /**
     * @param $request
     * @return mixed
     */
    public function obtenerCuentasContablesNivel($request)
    {
        $cuentas_contables = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $cuentas_contables->where('contabilidad.v_cuentas_contables.estado', '=', 1);
        $cuentas_contables->where('contabilidad.v_cuentas_contables.id_nivel_cuenta', '=', $request->id_nivel_cuenta);
        $cuentas_contables->where('id_empresa',$usuario_empresa->id_empresa);
        $cuentas_contables->with(['cuentaTipo' => function($query) {
            $query->with('tipoCuentaEstadoFinanciero');
        }]);
        return $cuentas_contables->get();
    }

    /**
 * @param $request
 * @return mixed
 */
    public function obtenerCuentaContable($request)
    {
        $cuenta_contable = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $cuenta_contable->where('id_cuenta_contable', '=', $request->id_cuenta_contable);
        $cuenta_contable->where('id_empresa',$usuario_empresa->id_empresa);
        $cuenta_contable->with('cuentaNivel');
        $cuenta_contable->with('cuentaTipo');
        return $cuenta_contable->get();
    }



    /**
     * @param $request
     * @return mixed
     */
    public function buscarCuentasContablesF($request)
    {
        $cuentas_contables = $this->select(['*','contabilidad.v_cuentas_contables.nombre_cuenta_completo AS text']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $searchValue = $request->q;
        $cuentas_contables->where('contabilidad.v_cuentas_contables.estado', '=', 1);
        $cuentas_contables->where('contabilidad.v_cuentas_contables.permite_movimiento', '=', 1);
        $cuentas_contables->where('id_empresa',$usuario_empresa->id_usuario);
        $cuentas_contables->with('cuentaTipo');
        $cuentas_contables->where('contabilidad.v_cuentas_contables.nombre_cuenta_completo', 'ILIKE', '%' . $searchValue . '%');
        $cuentas_contables->orderBy('contabilidad.v_cuentas_contables.nombre_cuenta_completo', 'asc');
        return $cuentas_contables->limit(10)->get();
    }



    /**
     * Relación Cuenta Bancaria - Empresa
     * @return BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

    /**
     * Relación Cuenta contable - cuentas contables vista
     * @return BelongsTo
     */
    public function cuentaPadre()
    {
        return $this->belongsTo('App\Models\Contabilidad\CuentasContablesVista','id_cuenta_padre');
    }

    /**
     * Relación Cuenta contable - Nivel cuenta
     * @return BelongsTo
     */
    public function cuentaNivel()
    {
        return $this->belongsTo('App\Models\Contabilidad\NivelesCuentas','id_nivel_cuenta');
    }

    /**
     * Relación Cuenta contable - Tipo Cuenta
     * @return BelongsTo
     */
    public function cuentaTipo()
    {
        return $this->belongsTo('App\Models\Contabilidad\TiposCuentas','id_tipo_cuenta');
    }


}





