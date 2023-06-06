<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;
use App\Models\Admon\Empresas;

class DocumentosContables extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.documentos_contables';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_documento';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['id_periodo_fiscal','id_tipo_doc','id_moneda','fecha_emision','num_documento','valor','concepto','u_creacion','u_modificacion','estado','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';




    /**
     * Obtener lista de entradas
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */
    public function obtener($request)
    {
        $documentos_contables = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $documentos_contables->where('id_empresa',$usuario_empresa->id_empresa);
            $documentos_contables->where($searchField, 'ilike', '%' . $searchValue . '%');
        }

        /*$documentos_contables->with(['documentoOrigenFondo' => function($query) {
         $query->with('cuentaContableOrigenFondo');
         $query->with('monedaOrigenFondo');}]);
         */
        $documentos_contables->with('documentoPeriodo');
        $documentos_contables->with('documentoTipo');

        /*  $documentos_contables->with(['documentoCuentaBancaria' => function($query) {
           $query->with('bancoCuentasBancarias');
           $query->with('cuentaContableCuentasBancarias');
           $query->with('monedaCuentaBancaria');}]);*/

        $documentos_contables->with(['movimientosCuentas' => function($query) {
            $query->with('cuentaContable')->with('centroCosto'); }]);
        $documentos_contables->orderBy('id_documento','desc');

        return $documentos_contables->paginate($request->limit);
    }

    /**
     * Obtener documento contable
     * @param $request
     * @return string
     */
    public function obtenerDocumento($request)
    {
        $documentos_contables = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $documentos_contables->where('contabilidad.documentos_contables.id_documento', '=', $request->id_documento);
        $documentos_contables->where('id_empresa',$usuario_empresa->id_empresa);
        $documentos_contables->with(['documentoOrigenFondo' => function($query) {
            $query->with('cuentaContableOrigenFondo');
            $query->with('monedaOrigenFondo');}]);

        $documentos_contables->with('documentoPeriodo');
        $documentos_contables->with('documentoTipo');

        $documentos_contables->with(['documentoCuentaBancaria' => function($query) {
            $query->with('bancoCuentaBancaria');
            $query->with('cuentaContableCuentaBancaria');
            $query->with('monedaCuentaBancaria');}]);

        $documentos_contables->with(['movimientosCuentas' => function($query) {
            $query->with('cuentaContable');}]);
        $documentos_contables->orderBy('id_documento');

        return $documentos_contables->get();
    }

    /**
     * Obtener número DOC
     * @param $request
     * @return mixed
     */
    public function obtenerCodigoDocumento($request)
    {
        $codigo = $this->select([DB::raw("COALESCE(max(codigo_documento),0)+1 as secuencia")]);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        //print_r($request);
        // echo $request->fecha_doc;
        //echo $request->id_tipo_doc;
//        if((!empty($request['fecha_doc']) /*&& !empty($request['id_tipo_doc'])*/)){
//            $codigo->whereRaw("(EXTRACT(Month FROM TIMESTAMP '".$request['fecha_doc']."') = EXTRACT(Month FROM fecha_emision))")
////                ->where('id_tipo_doc',$request['id_tipo_doc'])
//                ->where('id_empresa',$usuario_empresa->id_empresa);
//        }
        return $codigo->get();
    }

    /**
     * Relación Documentos contables - Empresa
     * @return BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo(Empresas::class,'id_empresa');
    }

    /**
     * Relación Documentos contables - Documento movimiento
     * @return HasMany
     */
    public function movimientosCuentas()
    {
        return $this->hasMany('App\Models\Contabilidad\DocumentosCuentas','id_documento')
            ->orderby('haber','asc')->orderby('cta_contable','asc');
    }

    /**
     * Relación Documentos contables - Periodo Fiscal
     * @return BelongsTo
     */
    public function documentoPeriodo()
    {
        return $this->belongsTo('App\Models\Contabilidad\PeriodosFiscales','id_periodo_fiscal');
    }

    /**
     * Relación Documentos contables - Tipo documento
     * @return BelongsTo
     */
    public function documentoTipo()
    {
        return $this->belongsTo('App\Models\Contabilidad\TiposDocumentos','id_tipo_doc');
    }

    /**
     * Relación Documentos contables - Cuenta bancaria
     * @return BelongsTo
     */
    public function documentoCuentaBancaria()
    {
        return $this->belongsTo('App\Models\ContabilidadCuentasBancarias','id_cuenta_bancaria');
    }

    /**
     * Relación Documentos contables - Banco Moneda
     * @return BelongsTo
     */
    public function documentoMoneda()
    {
        return $this->belongsTo('App\Models\CajaBancoMonedas','id_moneda');
    }
}





