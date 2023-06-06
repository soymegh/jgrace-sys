<?php

namespace App\Models\CajaBanco;

use App\Models\Admon\UsuariosEmpresas;
use  Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Proformas extends Model
{

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'cjabnco.proformas';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //protected $fillable = ['codigo_requisa','fecha_solicitud','estado'];    protected $table = 'cjabnco.proformas';
    protected $primaryKey='id_proforma';

    /**
     * Obtener Lista de Salidas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerProformas($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $proformas = $this->select(['*']);
        if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $proformas->where($searchField, 'ilike', '%' . $searchValue . '%');
            $proformas->where('id_empresa',$usuario_empresa->id_empresa);
        }
        if($request->search['estado']<>100){
            if($request->search['estado']==1){
                $proformas->whereIn('estado',array(1));
            }else{
                $proformas->where('estado',$request->search['estado']);
            }
        }
        $proformas->with('proformaProductos');
        $proformas->with('proformaMoneda');
        $proformas->with('proformaSucursal');
        $proformas->with('proformaBodega');
        $proformas->with('proformaVendedor');
        $proformas->with('proformaCliente');
        $proformas->with('proformaTipoCliente');
        $proformas->with('facturaProforma');
        $proformas->orderBy('f_creacion', 'desc');

        return $proformas->paginate($request->limit);
    }

    public function obtenerProforma($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $proforma = $this->select(['*']);
        $proforma->where('id_proforma', $request->id_proforma);
        $proforma->where('id_empresa', $usuario_empresa->id_empresa);

        $proforma->with(['proformaProductos' => function($query) {
            $query->with('afectacionProducto');
            $query->with(['bodegaProducto' => function($query2) {
                $query2->with('productoSimple');
            }]);
        }]);

        $proforma->with('proformaSucursal');
        $proforma->with('proformaMoneda');

        $proforma->with('proformaBodega');
        $proforma->with('proformaVendedor');
        $proforma->with('proformaCliente');
        $proforma->with('proformaTipoCliente');
        return $proforma->first();
    }


    public function obtenerProformasCliente($request)
    {
        $productos = $this->select([
            'id_proforma',
            'no_documento',
            'f_proforma',
            'id_cliente',
            'impuesto_exonerado',
            'mt_total',
            'saldo_proforma',
            'f_vencimiento',
            'estado',
        ]);
        if ((!empty($request->id_cliente))) {
            $productos->Where('id_cliente', $request->id_cliente);
           // $productos->Where('saldo_actual', '>',0);
            $productos->orderBy('f_proforma', 'asc');
            return $productos->get();
        }else{
            return $productos->limit(0)->get();
        }
    }

    public function buscar($request)
    {

        $proformas = $this->select(['*',DB::raw("no_documento AS text")])
            ->whereIn('estado',array(1,3));

        if ((!empty($request->q))) {
            $searchValue = $request->q;
            $proformas->where('no_documento', 'ILIKE', '%' . $searchValue . '%');
            $proformas->orderBy('no_documento', 'asc');
            return $proformas->limit(6)->get();
        }else{
            $proformas->orderBy('no_documento', 'asc');
            return $proformas->limit(6)->get();

        }
    }


    public function proformaProductos()
    {
        return $this->hasMany('App\Models\CajaBanco\ProformasDetalles','id_proforma');
    }



    public function proformaSeguimiento()
    {
        return $this->hasMany('App\Models\CajaBanco\ProformasSeguimiento','id_proforma');
    }
    public function facturaProforma()
    {
        return $this->belongsTo('App\Models\CajaBanco\Facturas','id_factura');
    }

    public function proformaMoneda()
    {
        return $this->belongsTo('App\Models\Contabilidad\Monedas','id_moneda')->select(['*','descripcion as text']);
    }

    public function proformaSucursal()
    {
        return $this->belongsTo('App\Models\Admon\Sucursales','id_sucursal')->select(['*','descripcion as text']);
    }

    public function proformaBodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega')->select(['*','descripcion as text']);
    }

    public function proformaCliente()
    {
        return $this->belongsTo('App\Models\Ventas\Clientes','id_cliente')->select(['*','nombre_comercial as text', DB::raw("coalesce(cuentasxcobrar.obtener_monto_credito_dol(id_cliente),0) AS monto_credito_dol_disponible")]);
    }

    public function proformaVendedor()
    {
        return $this->belongsTo('App\Models\Ventas\Vendedores','id_vendedor')->select(['*','nombre_completo as text']);
    }

    public function proformaTipoCliente()
    {
        return $this->belongsTo('App\Models\Ventas\TipoCliente','id_tipo_cliente')->select(['*','descripcion as text']);
    }


}

