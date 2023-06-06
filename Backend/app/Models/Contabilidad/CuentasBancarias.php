<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class   CuentasBancarias extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.cuentas_bancarias';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_cuenta_bancaria';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['id_banco','id_cuenta_contable','id_moneda','numero_cuenta','u_creacion','u_modificacion','estado','numeracion_chequera','formato_cheque','tipo_cuenta','id_empresa'];

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
     * Obtener lista de anexos segun el centro de costo y el id empresa del usurio
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */
    public function obtenerCuentasBancarias($request)
    {
        $cuentasBancarias = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $cuentasBancarias->where($searchField, 'ilike', '%' . $searchValue . '%');
            $cuentasBancarias->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $cuentasBancarias->where('estado',true);
                $cuentasBancarias -> where('id_empresa',$usuario_empresa->id_empresa);
            }

            $cuentasBancarias->with('bancoCuentaBancaria');
            $cuentasBancarias->with('cuentaContableCuentaBancaria');
            $cuentasBancarias->with('monedaCuentaBancaria');
            $cuentasBancarias->orderby('id_cuenta_bancaria');
        }
        return $cuentasBancarias->paginate($request->limit);
    }

    /**
     * Relación Cuenta Bancaria - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

    /**
     * Relación Cuenta bancaria - Bancos
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bancoCuentaBancaria()
    {
        return $this->belongsTo('App\Models\CajaBanco\Bancos','id_banco')->select('id_banco','descripcion');
    }

    /**
     * Relación Cuenta bancaria - Cuentas contables
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cuentaContableCuentaBancaria()
    {
        return $this->belongsTo('App\Models\Contabilidad\CuentasContablesVista','id_cuenta_contable')->select('id_cuenta_contable','cta_contable','nombre_cuenta_completo');
    }

    /**
     * Relación Cuenta bancaria - Monedas
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function monedaCuentaBancaria()
    {
        return $this->belongsTo('App\Models\Contabilidad\Monedas','id_moneda')->select('id_moneda','descripcion','codigo','descripcion_singular');
    }


}





