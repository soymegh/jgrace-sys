<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\CuentasXCobrarCatAuxiliar;
use App\Models\Admon\Departamentos;
use App\Models\Admon\Impuestos;
use App\Models\Admon\Zonas;
use App\Models\Ventas\TipoCliente;
use App\Models\Ventas\Vendedores;
use Illuminate\Http\JsonResponse;
use PHPJasper\PHPJasper;
use Illuminate\Support\Facades\Validator, Illuminate\Support\Facades\Auth;
use App\Models\Ventas\Clientes;
use Illuminate\Http\Request;

class ClientesController extends Controller
{


    /**
     * Busqueda de Clientes
     *
     * @access  public
     * @param Request $request
     * @param Clientes $clientes
     * @return JsonResponse
     * @author octaviom <omorales@capitalsoftware.com.ni>
     */

    public function buscar(Request $request, Clientes $clientes)
    {
        $clientes = $clientes->buscar($request);
        return response()->json([
            'results' => $clientes
        ]);
    }

    /**
     * Obtener una lista de Clientes
     *
     * @access  public
     * @param Request $request
     * @param Clientes $clientes
     * @return JsonResponse
     */

    public function obtener(Request $request, Clientes $clientes)
    {
        $clientes = $clientes->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $clientes->total(),
                'rows' => $clientes->items()
            ],
            'messages' => null
        ]);
    }

    public function obtenerSolicitudConsignacion(Request $request, Clientes $clientes)
    {
        $clientes = $clientes->obtenerSolicitudConsignacion($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $clientes->total(),
                'rows' => $clientes->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Clientes sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param Clientes $clientes
     * @return JsonResponse
     * @author omorales <omorales@capitalsoftware.com.ni>
     */

    public function obtenerTodos(Request $request, Clientes $clientes)
    {
        $clientes = Clientes::where('estado', 1)->get();
        return response()->json([
            'status' => 'success',
            'result' => $clientes,
            'messages' => null
        ]);
    }


    /**
     * obtener Cliente Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerCliente(Request $request)
    {
        $rules = [
            'id_cliente' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $cliente = Clientes::where('id_cliente',$request->id_cliente)->with('tipoCliente')->with('municipioCliente')->with('impuestoCliente')->With('vendedorCliente')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $departamentos = Departamentos::with('municipiosDepartamento')->get();
            $zonas = Zonas::select(['id_zona','descripcion'])->where('id_empresa',$usuario_empresa->id_empresa)->get();
            $impuestos = Impuestos::select(['id_impuesto','descripcion'])->where('id_empresa',$usuario_empresa->id_empresa)->get();
            $tipos_clientes = TipoCliente::select(['id_tipo_cliente','descripcion'])->where('id_empresa',$usuario_empresa->id_empresa)->get();
            $vendedores = Vendedores::select('id_vendedor','nombre_completo','id_zona')->where('id_empresa',$usuario_empresa->id_empresa)->get();
            //$auxiliares = CuentasXCobrarCatAuxiliar::select('*')->where('estado',1)->get();
            $currency_id = Ajustes::where('id_ajuste',1)->where('id_empresa',$usuario_empresa->id_empresa)->select('valor')->first();

            if(!empty($cliente)){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'cliente' => $cliente,
                        'departamentos' => $departamentos,
                        'impuestos' => $impuestos,
                        'tipos_clientes' => $tipos_clientes,
                        'zonas' => $zonas,
                        'vendedores' => $vendedores,
                        'currency_id' => $currency_id->valor,
                    ],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_cliente'=>["Datos no encontrados"]),
                    'messages' => null
                ]);
            }


        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Crear un nuevo proveedor
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function registrar(Request $request)
    {
        $messages = [
            'numero_cedula.required_if' => 'El campo cédula es requerido para tipo de persona Natural',
            'numero_ruc.required_if' => 'El campo RUC es requerido para tipo de persona Juridíca',
        ];

        $rules = [
            'tipo_persona' => 'required|integer|min:1|max:2',
            'nombre_comercial' => 'required|required_if:tipo_persona,1|string|max:100',
            'nombre_completo' => 'nullable|string|max:100',
            'razon_social' => 'nullable|required_if:tipo_persona,2|string|max:100',
            'contacto' => 'nullable|string|max:100',
            'numero_ruc' => 'nullable|required_if:tipo_persona,2|string|max:50',
            'numero_cedula' => 'nullable|required_if:tipo_persona,1|string|max:50',
            'direccion' => 'nullable|string|max:200',
            'tipo_cliente' => 'required|array|min:1',
            'tipo_cliente.id_tipo_cliente' => 'required|integer|min:1',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|string|max:50',
            'departamento' => 'required|array|min:1',
            'departamento.id_departamento' => 'required|integer|min:1',
            'municipio' => 'required|array|min:1',
            'municipio.id_municipio' => 'required|integer|min:1',
            'giro_negocio' => 'nullable|string|max:100',
            'zona' => 'nullable|array|min:1',
            'zona.id_zona' => 'nullable|integer|min:1',
            'impuesto' => 'required|array|min:1',
            'impuesto.id_impuesto' => 'required|integer|min:1',
            'tipo_contribuyente' => 'required|integer|min:1|max:3',
            'retencion_ir' => 'required|boolean',
            'retencion_imi' => 'required|boolean',
            'clasificacion' => 'required|integer|min:1|max:2',
            'permite_credito' => 'required|boolean',
            'plazo_credito' => 'required_if:permite_credito,true|integer|min:0',
            'limite_credito' => 'required_if:permite_credito,true|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
            'limite_credito_me' => 'required_if:permite_credito,true|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
            'porcentaje_descuento' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0|max:50',
            'observaciones' => 'nullable|string|max:100',
//            'permite_consignacion' => 'required|boolean',
//            'plazo_consignacion' => 'required_if:permite_consignacion,true|integer|min:0',
//            'numero_contrato_consignacion' => 'nullable|required_if:permite_consignacion,true|string|max:50',
            'tramite_cheque' => 'required|integer|min:0',
//            'es_deudor' => 'required|boolean',
            'vendedor' => 'required|array|min:1',
            'vendedor.id_vendedor' => 'required|integer|min:1',
//            'monto_max_consignacion'=>'required_if:permite_consignacion,true|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $cliente = new Clientes;

            $codigo_nuevo = $cliente->obtenerCodigoCliente($request->departamento['id_departamento'],$request->municipio['id_municipio']); //,$request->zona['id_zona']

            $cliente->es_deudor = $request->es_deudor;

            $str_length = 5;
            $str_length2 = 2;
            $str_length3 = 2;
            $str = substr("0000{$codigo_nuevo['secuencia']}", -$str_length);
            $cliente->secuencia_codigo = $codigo_nuevo['secuencia'];

            if(!$cliente->es_deudor){
            $cliente->codigo = 'C'.substr("0{$request->departamento['id_departamento']}", -$str_length2) .substr("00{$request->municipio['id_municipio']}", -$str_length3)  .$str; //.substr("0{$request->zona['id_zona']}", -$str_length2)
            }else{
                $cliente->codigo = 'D'.substr("0{$request->departamento['id_departamento']}", -$str_length2) .substr("00{$request->municipio['id_municipio']}", -$str_length3)  .$str; //.substr("0{$request->zona['id_zona']}", -$str_length2)
            }
            $cliente->tipo_persona = $request->tipo_persona;
            $cliente->nombre_comercial = $request->nombre_comercial;
            $cliente->nombre_completo = $request->nombre_completo;
            $cliente->razon_social = $request->razon_social;


            $cliente->contacto = $request->contacto;
            if( $request->numero_ruc == null){
                $cliente->numero_ruc = '';
            }else{
                $cliente->numero_ruc = $request->numero_ruc;
            }
            $cliente->numero_cedula = $request->numero_cedula;
            $cliente->direccion = $request->direccion;
            $cliente->id_tipo_cliente = $request->tipo_cliente['id_tipo_cliente'];
            $cliente->id_vendedor = $request->vendedor['id_vendedor'];
            $cliente->telefono = $request->telefono;
            $cliente->correo = $request->correo;
            $cliente->id_municipio = $request->municipio['id_municipio'];
//            $cliente->giro_negocio = $request->giro_negocio;
            $cliente->id_zona = 1;//$request->zona['id_zona'];
            $cliente->id_impuesto = $request->impuesto['id_impuesto'];
            $cliente->tipo_contribuyente = $request->tipo_contribuyente;
            $cliente->retencion_ir = $request->retencion_ir;
            $cliente->retencion_imi = $request->retencion_imi;
            $cliente->clasificacion = $request->clasificacion;
            $cliente->permite_credito = $request->permite_credito;
            $cliente->permite_anticipo = $request->permite_anticipo;

/*            if($request->permite_consignacion){
                $cliente->plazo_consignacion = $request->plazo_consignacion;
                $cliente->numero_contrato_consignacion = $request->numero_contrato_consignacion;
                $cliente->monto_max_consignacion  = $request->monto_max_consignacion;
            }else{
                $cliente->plazo_consignacion =0;
                $cliente->numero_contrato_consignacion ='';
                $cliente->monto_max_consignacion  = 0;
            }*/

            if($request->permite_credito){
                $cliente->plazo_credito = $request->plazo_credito;
                $cliente->limite_credito = $request->limite_credito;
                $cliente->limite_credito_me = $request->limite_credito_me;
            }else{
                $cliente->plazo_credito =0;
                $cliente->limite_credito =0;
                $cliente->limite_credito_me =0;
            }

            $cliente->porcentaje_descuento = $request->porcentaje_descuento;
            $cliente->observaciones = $request->observaciones;
//            $cliente->permite_consignacion = $request->permite_consignacion;
            $cliente->tramite_cheque = $request->tramite_cheque;

            // New column to save auxiliar id.
//            $cliente->id_cat_auxiliar_cxc = $request->auxiliar['id_cat_auxiliar_cxc'];

            $cliente->u_creacion = Auth::user()->name;
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $cliente->id_empresa = $usuario_empresa->id_empresa;
            $cliente->estado = 1;
            $cliente->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }



    public function registrarBasico(Request $request)
    {
        $messages = [
            'numero_cedula.required_if' => 'El campo cédula es requerido para tipo de persona Natural',
            'numero_ruc.required_if' => 'El campo RUC es requerido para tipo de persona Juridíca',
        ];

        $rules = [
            'tipo_persona' => 'required|integer|min:1|max:2',
            'nombre_comercial' => 'required|string|max:100',
            //'nombre_completo' => 'nullable|string|max:100',
            //'razon_social' => 'nullable|string|max:100',
            'contacto' => 'nullable|string|max:100',
//            'numero_ruc' => 'nullable|required_if:tipo_persona,2|string|max:50|min:14',
//            'numero_cedula' => 'nullable|required_if:tipo_persona,1|string|max:50|min:14',
            'numero_ruc' => 'nullable|string|max:50|min:14',
            'numero_cedula' => 'nullable|string|max:50|min:14',
            'direccion' => 'nullable|string|max:200',
            /*'tipo_cliente' => 'required|array|min:1',*/
            'id_tipo_cliente' => 'required|integer|min:1',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|string|max:50',
            'departamento' => 'required|array|min:1',
            'departamento.id_departamento' => 'required|integer|min:1',
            'municipio' => 'required|array|min:1',
            'municipio.id_municipio' => 'required|integer|min:1',
            'vendedor' => 'required|array|min:1',
            'vendedor.id_vendedor' => 'required|integer|min:1',
            'giro_negocio' => 'nullable|string|max:100',
            'zona' => 'nullable|array|min:1',
            'zona.id_zona' => 'nullable|integer|min:1',
            /*'impuesto' => 'required|array|min:1',*/
            'id_impuesto' => 'required|integer|min:1',
            'tipo_contribuyente' => 'required|integer|min:1|max:3',
            'retencion_ir' => 'required|boolean',
            'retencion_imi' => 'required|boolean',
            'clasificacion' => 'required|integer|min:1|max:2',
            'permite_credito' => 'required|boolean',
            'plazo_credito' => 'required_if:permite_credito,true|integer|min:0',
            'limite_credito' => 'required_if:permite_credito,true|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
            'porcentaje_descuento' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0|max:50',
            'observaciones' => 'nullable|string|max:100',
            'permite_consignacion' => 'required|boolean',
            'tramite_cheque' => 'required|integer|min:0',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $cliente = new Clientes;

            $codigo_nuevo = $cliente->obtenerCodigoCliente($request->departamento['id_departamento'],$request->municipio['id_municipio']);


            $str_length = 5;
            $str_length2 = 2;
            $str_length3 = 2;
            $str = substr("0000{$codigo_nuevo['secuencia']}", -$str_length);
//            print_r('Codigo nuevo para cliente' . $str);

            $cliente->secuencia_codigo = $codigo_nuevo['secuencia'];
            //$cliente->codigo = $request->departamento['id_departamento'].$request->municipio['id_municipio'].$request->zona['id_zona'] .$str;

            if(!$cliente->es_deudor){
                $cliente->codigo = 'C'.substr("0{$request->departamento['id_departamento']}", -$str_length2) .substr("00{$request->municipio['id_municipio']}", -$str_length3)  .$str;
            }else{
                $cliente->codigo = 'D'.substr("0{$request->departamento['id_departamento']}", -$str_length2) .substr("00{$request->municipio['id_municipio']}", -$str_length3)  .$str;
            }

            $cliente->tipo_persona = $request->tipo_persona;
            $cliente->nombre_comercial = $request->nombre_comercial;
            $cliente->nombre_completo = $request->nombre_comercial;
            $cliente->razon_social = $request->nombre_comercial;

            $cliente->contacto = $request->contacto;
            if( $request->numero_ruc == null){
                $cliente->numero_ruc = '';
            }else{
                $cliente->numero_ruc = $request->numero_ruc;
            }
            $cliente->numero_cedula = $request->numero_cedula;
            $cliente->direccion = $request->direccion;
            $cliente->id_tipo_cliente = 1;
            $cliente->telefono = $request->telefono;
            $cliente->correo = $request->correo;
            $cliente->id_municipio = $request->municipio['id_municipio'];
            $cliente->id_vendedor = $request->vendedor['id_vendedor'];
//            $cliente->giro_negocio = $request->giro_negocio;
            $cliente->id_zona = 1;//$request->zona['id_zona'];
            $cliente->id_impuesto = $request->id_impuesto;
            $cliente->tipo_contribuyente = $request->tipo_contribuyente;
            $cliente->retencion_ir = $request->retencion_ir;
            $cliente->retencion_imi = $request->retencion_imi;
            $cliente->clasificacion = $request->clasificacion;

            $cliente->es_deudor = false;

            $cliente->permite_credito = false;
            $cliente->permite_anticipo = false;

            /*if($request->permite_credito){
                $cliente->plazo_credito = $request->plazo_credito;
                $cliente->limite_credito = $request->limite_credito;
            }else{*/
                $cliente->plazo_credito =0;
                $cliente->limite_credito =0;
           // }

            $cliente->porcentaje_descuento = $request->porcentaje_descuento;
            $cliente->observaciones = $request->observaciones;
//            $cliente->permite_consignacion = $request->permite_consignacion;
            $cliente->tramite_cheque = $request->tramite_cheque;

            $cliente->u_creacion = Auth::user()->name;
            $cliente->estado = 1;
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $cliente->id_empresa = $usuario_empresa->id_empresa;
            $cliente->save();

            $custom_cliente = $cliente;
            $custom_cliente->text=$cliente->nombre_comercial;

            return response()->json([
                'status' => 'success',
                'result' => $custom_cliente,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    public function cambiarEstadoConsignacion(Request $request)
    {
        $rules = [
            'id_cliente' => 'required|integer|min:1',
            'estado' => 'required|integer|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cliente = Clientes::find($request->id_cliente);
            if($cliente->aprobacion_consignacion==2){
                $cliente->aprobacion_consignacion = $request->estado;
                $cliente->permite_consignacion =false;
                $cliente->plazo_consignacion =0;
                $cliente->numero_contrato_consignacion ='';
            }else{
                $cliente->aprobacion_consignacion = $request->estado;
            }
            $cliente->u_modificacion = Auth::user()->name;
            $cliente->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Actualizar cliente existente
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function actualizar(Request $request)
    {
        $messages = [
            'numero_cedula.required_if' => 'El campo cédula es requerido para tipo de persona Natural',
            'numero_ruc.required_if' => 'El campo RUC es requerido para tipo de persona Juridíca',
        ];


        $rules = [
            'id_cliente' => 'required|integer|min:1',
            'tipo_persona' => 'required|integer|min:1|max:2',
            'nombre_comercial' => 'required|string|max:100',
            'nombre_completo' => 'nullable|string|max:100',
            'razon_social' => 'nullable|string|max:100',
            'contacto' => 'nullable|string|max:100',
            'numero_ruc' => 'nullable|required_if:tipo_persona,2|string|max:50',
            'numero_cedula' => 'nullable|required_if:tipo_persona,1|string|max:50',
            'direccion' => 'nullable|string|max:200',
            'tipo_cliente' => 'required|array|min:1',
            'tipo_cliente.id_tipo_cliente' => 'required|integer|min:1',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|string|max:50',
            'departamento_cliente' => 'required|array|min:1',
            'departamento_cliente.id_departamento' => 'required|integer|min:1',
            'municipio_cliente' => 'required|array|min:1',
            'municipio_cliente.id_municipio' => 'required|integer|min:1',
//            'giro_negocio' => 'nullable|string|max:100',
//            'zona_cliente' => 'required|array|min:1',
//            'zona_cliente.id_zona' => 'required|integer|min:1',
            'impuesto_cliente' => 'required|array|min:1',
            'impuesto_cliente.id_impuesto' => 'required|integer|min:1',
            'tipo_contribuyente' => 'required|integer|min:1|max:3',
            'retencion_ir' => 'required|boolean',
            'retencion_imi' => 'required|boolean',
            'clasificacion' => 'required|integer|min:1|max:2',
            'permite_credito' => 'required|boolean',
            'plazo_credito' => 'required_if:permite_credito,true|integer|min:0',
            'limite_credito' => 'required_if:permite_credito,true|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
            'limite_credito_me' => 'required_if:permite_credito,true|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
            'porcentaje_descuento' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0|max:50',
            'observaciones' => 'nullable|string|max:100',
//            'permite_consignacion' => 'required|boolean',
            'tramite_cheque' => 'required|integer|min:0',
//            'plazo_consignacion' => 'required_if:permite_consignacion,true|integer|min:0',
//            'numero_contrato_consignacion' => 'nullable|required_if:permite_consignacion,true|string|max:50',
//            'aprobacion_consignacion' => 'required|integer|min:0',
//            'es_deudor'=> 'required|boolean',
            'vendedor_cliente' => 'required|array|min:1',
            'vendedor_cliente.id_vendedor' => 'required|integer|min:1',
//            'monto_max_consignacion'=>'required_if:permite_consignacion,true|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $cliente = Clientes::find($request->id_cliente);

            $cliente->tipo_persona = $request->tipo_persona;
            $cliente->nombre_comercial = $request->nombre_comercial;
            $cliente->nombre_completo = $request->nombre_completo;
            $cliente->razon_social = $request->razon_social;
            $cliente->contacto = $request->contacto;
            if( $request->numero_ruc == null){
                $cliente->numero_ruc = '';
            }else{
                $cliente->numero_ruc = $request->numero_ruc;
            }
            $cliente->numero_cedula = $request->numero_cedula;
            $cliente->direccion = $request->direccion;
            $cliente->id_tipo_cliente = $request->tipo_cliente['id_tipo_cliente'];
            $cliente->telefono = $request->telefono;
            $cliente->correo = $request->correo;
            $cliente->id_municipio = $request->municipio_cliente['id_municipio'];
//            $cliente->giro_negocio = $request->giro_negocio;
//            $cliente->id_zona = $request->zona_cliente['id_zona'];
            $cliente->id_impuesto = $request->impuesto_cliente['id_impuesto'];
            $cliente->tipo_contribuyente = $request->tipo_contribuyente;
            $cliente->retencion_ir = $request->retencion_ir;
            $cliente->retencion_imi = $request->retencion_imi;
            $cliente->clasificacion = $request->clasificacion;
            $cliente->u_modificacion = Auth::user()->name;
//            $cliente->aprobacion_consignacion = $request->aprobacion_consignacion;

            $cliente->id_vendedor = $request->vendedor_cliente['id_vendedor'];
            $cliente->permite_credito = $request->permite_credito;
            $cliente->permite_anticipo = $request->permite_anticipo;
            if($request->permite_credito){
                $cliente->plazo_credito = $request->plazo_credito;
                $cliente->limite_credito = $request->limite_credito;
                $cliente->limite_credito_me = $request->limite_credito_me;
            }else{
                $cliente->plazo_credito =0;
                $cliente->limite_credito =0;
                $cliente->limite_credito_me =0;
            }
            $cliente->porcentaje_descuento = $request->porcentaje_descuento;
            $cliente->observaciones = $request->observaciones;
//            $cliente->permite_consignacion = $request->permite_consignacion;
            $cliente->tramite_cheque = $request->tramite_cheque;


//            if($request->permite_consignacion){
//                $cliente->plazo_consignacion = $request->plazo_consignacion;
//                $cliente->monto_max_consignacion  = $request->monto_max_consignacion;
//                $cliente->numero_contrato_consignacion = $request->numero_contrato_consignacion;
//            }else{
//                $cliente->monto_max_consignacion =0;
//                $cliente->plazo_consignacion =0;
//                $cliente->numero_contrato_consignacion ='';
//            }

//            $cliente->es_deudor = $request->es_deudor;

            // new column to save auxiliar id
//            $cliente->id_cat_auxiliar_cxc = $request->auxiliar_cliente['id_cat_auxiliar_cxc'];

            $cliente->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Desactiva Proveedor
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_cliente' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cliente = Clientes::find($request->id_cliente);
            $cliente->estado = 0;
            $cliente->u_modificacion = Auth::user()->name;
            $cliente->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    public function nuevo(Request $request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $tipos_cliente = TipoCliente::select('id_tipo_cliente','descripcion')->orderby('id_tipo_cliente')->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $impuestos = Impuestos::select('id_impuesto','descripcion')->where('estado',1)->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $zonas = Zonas::select('id_zona','codigo','descripcion')->where('estado',1)->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $departamentos = Departamentos::select('id_departamento','descripcion')->where('id_empresa',$usuario_empresa->id_empresa)->With('municipiosDepartamento')->orderby('id_departamento')->get();
        $vendedores = Vendedores::select('id_vendedor','nombre_completo','id_zona')->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $currency_id = Ajustes::where('id_ajuste',1)->where('id_empresa',$usuario_empresa->id_empresa)->select('valor')->first(); // 1-cordobas 2-dolares
//        $auxiliares = CuentasXCobrarCatAuxiliar::select('*')->where('estado',1)->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'tipos_cliente' => $tipos_cliente,
                'impuestos'=>$impuestos,
                'zonas' => $zonas,
                'departamentos' => $departamentos,
                'vendedores' => $vendedores,
                'currency_id' => $currency_id
            ],
            'messages' => null
        ]);

    }

    public function activar(Request $request)
    {
        $rules = [
            'id_cliente' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cliente = Clientes::find($request->id_cliente);
            $cliente->estado = 1;
            $cliente->u_modificacion = Auth::user()->name;
            $cliente->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }
    public function generarReporte($ext)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteTrabajadores';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteTrabajadores';
            $input = '/var/www/html/resources/reports/ListadoClientes';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ListadoClientes';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [],
                'db_connection' => [
                    'driver' => 'postgres',
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'host' => env('DB_HOST'),
                    'database' => env('DB_DATABASE'),
                    'port' => env('DB_PORT')
                ]
            ];

            $jasper = new PHPJasper;

            $jasper->process($input, $output, $options)->execute();
            /*header('Content-Description: File Transfer');
            header('Content-Type: multipart/form-data;boundary="boundary"');
            header('Content-Disposition: form-data; filename=' . $hora_actual. 'Accesos.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);*/
            // unlink($output . '.' . $ext);

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'ListadoClientes.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}
