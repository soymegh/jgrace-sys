<?php

namespace App\Models\CajaBanco;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB, Illuminate\Database\Eloquent\Model;
use App\Models\CajaBanco\FacturaDetalle;

class Facturas extends Model
{

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'cjabnco.facturas';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    //protected $fillable = ['codigo_requisa','fecha_solicitud','estado'];    protected $table = 'cjabnco.facturas';
    protected $primaryKey='id_factura';

    /**
     * Obtener Lista de Salidas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerFacturas($request)
    {
        $facturas = $this->select(['*']);
        if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $facturas->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $facturas->id_empresa = $usuario_empresa->id_empresa;
        $facturas->with('facturaProductos');
        $facturas->with('facturaMoneda');
        $facturas->with('facturaSucursal');
        $facturas->with('facturaBodega');
        $facturas->with('facturaVendedor');
        $facturas->with('facturaCliente');
        $facturas->with('facturaTipoCliente');

      //$facturas->where('u_creacion',Auth::user()->usuario);

        if(Auth::user()->id_sucursal>0){
            $facturas->where('id_sucursal', Auth::user()->id_sucursal);
        }

        $facturas->orderBy('f_creacion', 'desc');

        return $facturas->paginate($request->limit);
    }

    public function obtenerFactura($request)
    {
        $factura = $this->select(['*']);
        $factura->where('id_factura', $request->id_factura);

        $factura->with(['facturaProductos' => function($query) {
            $query->with(['bodegaProducto' => function($query2) {
                $query2->with('productoSimple');
            }]);
        }]);

        $factura->with('facturaSucursal');
        $factura->with('facturaMoneda');

        $factura->with('facturaBodega');
        $factura->with('facturaVendedor');
        $factura->with('facturaCliente');
        $factura->with('facturaTipoCliente');
        return $factura->first();
    }


    public function obtenerFacturasCliente($request)
    {
        $productos = $this->select([
            'id_factura',
            'no_documento',
            'id_tipo',
            'f_factura',
            'id_cliente',
            'impuesto_exonerado',
            'mt_total',
            'mt_total_me',
            'saldo_factura',
            'saldo_factura_me',
            'f_vencimiento',
            'estado',
            DB::raw("CONCAT(no_documento,' ( Saldo: ',' (C$ ',round(saldo_factura,2),')') AS text"),
            DB::raw("CONCAT(no_documento,' ( Saldo: ',' ($ ',round(saldo_factura_me,2),')') AS textDolar")
        ]);
        if ((!empty($request->id_cliente))) {
            $productos->Where('id_cliente', $request->id_cliente);
            $productos->Where('id_tipo', $request->id_tipo);
            $productos->Where('saldo_factura', '>',0);
            $productos->WhereIn('estado', array(1,2));
            $productos->orderBy('f_factura', 'asc');
            return $productos->get();
        }

        return $productos->limit(0)->get();
    }


    public function facturaProductos()
    {
        return $this->hasMany(FacturaDetalle::class,'id_factura');
    }

    public function facturaMoneda()
    {
        return $this->belongsTo('App\Models\Contabilidad\Monedas','id_moneda')->select(['*','descripcion as text']);
    }

    public function facturaSucursal()
    {
        return $this->belongsTo('App\Models\Admon\Sucursales','id_sucursal')->select(['*','descripcion as text']);
    }

    public function facturaBodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega')->select(['*','descripcion as text']);
    }

    public function facturaCliente()
    {
        return $this->belongsTo('App\Models\Ventas\Clientes','id_cliente')->select(['*','nombre_comercial as text']);
    }

    public function facturaVendedor()
    {
        return $this->belongsTo('App\Models\Ventas\Vendedores','id_vendedor')->select(['*','nombre_completo as text']);
    }

    public function facturaTipoCliente()
    {
        return $this->belongsTo('App\Models\Ventas\TipoCliente','id_tipo_cliente')->select(['*','descripcion as text']);
    }


}

