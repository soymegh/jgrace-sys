<?php

namespace App\Http\Controllers\CajaBanco;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\CajaBanco\Proformas;
use App\Models\CajaBanco\ProformasSeguimiento;
use App\Models\CajaChicaCaja;
use App\Models\CajaChicaComprobante;
use App\Models\CajaChicaComprobanteDetalle;
use App\Models\CajaChicaSolicitudViatico;
use App\Models\RRHHTrabajadores;
use App\Models\Ventas\Vendedores;
use Hash, Illuminate\Support\Facades\Validator;
//use App\Models\CajaChicaSolicitudViaticoDetalle;
//use App\Models\AdmonUsuarios;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PHPJasper\PHPJasper;

class ProformaSeguimientoController extends Controller
{

    /**
     * Busqueda de solicitud
     *
     * @access  public
     * @param Request $request
     * @param ProformasSeguimiento $seguimiento
     * @return JsonResponse
     */

    public function buscar(Request $request, ProformasSeguimiento $seguimiento)
    {
        $seguimiento = $seguimiento->buscar($request);
        return response()->json([
            'results' => $seguimiento
        ]);
    }

    /**
     * Obtener Lista de comprobante con paginación
     *
     * @access  public
     * @param Request $request
     * @param ProformasSeguimiento $seguimiento
     * @return JsonResponse
     */

    public function obtener(Request $request, ProformasSeguimiento $seguimiento)
    {
        $seguimiento = $seguimiento->obtenerProformas($request);

        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $seguimiento->total(),
                'rows' => $seguimiento->items()
            ],
            'messages' => null
        ]);
    }


    /**
     * Obtener comprobante especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerSeguimiento(Request $request)
    {
        $rules = [
            'id_proforma' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $seguimiento = ProformasSeguimiento::where('id_proforma', $request->id_proforma)->with('proformaSeguimiento', 'proformaVendedor')->get();
            $vendedores = Vendedores::select('*', 'nombre_completo as text')->get();
            if (!empty($seguimiento)) {
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'seguimiento' => $seguimiento,
                        'vendedores' => $vendedores
                    ],
                    'messages' => null
                ]);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_proforma' => ["Datos no encontrados"]),
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
     * Registrar nuevo comprobante
     *
     * @access    public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            //'id_proforma' => 'integer|integer',
            'proforma_seguimiento.*.nombre_contacto' => 'required|string',
            'proforma_seguimiento.*.cargo_contacto' => 'required|string',
            'proforma_seguimiento.*.medio_contacto' => 'required|integer',
            'proforma_seguimiento.*.correo' => 'required|string',
            'proforma_seguimiento.*.telefono' => 'required|string',
            'proforma_seguimiento.*.nota_seguimiento' => 'required|string',
            'proforma_seguimiento.*.proximo_contacto' => 'required|date',
            'proforma_seguimiento.*.proforma_vendedor.id_vendedor' => 'required|integer',

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try {
                DB::beginTransaction();

                ProformasSeguimiento::where('id_proforma', $request->id_proforma)->delete();

                $i = 1;
                foreach ($request->proforma as $seguimiento_detalle) {
                    $seguimiento = new ProformasSeguimiento();
                    $seguimiento->id_proforma = $request->id_proforma;
                    $seguimiento->id_vendedor = $seguimiento_detalle['proforma_vendedor']['id_vendedor'];
                    $seguimiento->nombre_contacto = $seguimiento_detalle['nombre_contacto'];
                    $seguimiento->cargo_contacto = $seguimiento_detalle['cargo_contacto'];
                    $seguimiento->medio_contacto = $seguimiento_detalle['medio_contacto'];
                    $seguimiento->correo = $seguimiento_detalle['correo'];
                    $seguimiento->telefono = $seguimiento_detalle['telefono'];
                    $seguimiento->nota_seguimiento = $seguimiento_detalle['nota_seguimiento'];
                    $seguimiento->proximo_contacto = $seguimiento_detalle['proximo_contacto'];
                    $seguimiento->u_grabacion = Auth::user()->name;
                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                    $seguimiento->id_empresa = $usuario_empresa->id_empresa;
                    $seguimiento->estado = 1;
                    $seguimiento->activo = 1;
                    $seguimiento->save();
                    $i++;
                }

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
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
     * cargar datos para seguimiento
     *
     * @access    public
     * @param Request $request
     * @return JsonResponse
     */

    public function nueva(Request $request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        //$proforma = CajaBancoProformas::find($request->id_proforma);
        $vendedores = Vendedores::select('*', 'nombre_completo as text')->where('id_empresa',$usuario_empresa->id_empresa)->get();

        if (!empty($request->id_proforma)) {

            $seguimientos = ProformasSeguimiento::where('id_proforma', $request->id_proforma)
                ->with('proformaVendedor')->get();

            $proforma = Proformas::find($request->id_proforma);

            return response()->json([
                'status' => 'success',
                'result' => [
                    'no_documento' => $proforma->no_documento,
                    'id_proforma' => $proforma->id_proforma,
                    'seguimientos' => $seguimientos,
                    'vendedores' => $vendedores
                ],
                'messages' => null
            ]);
        }

    }
}
