<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use App\Models\Contabilidad\TiposDocumentos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TiposDocumentosController extends Controller
{

    /**
     * Obtener una lista de Estados Financieros
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtener(Request $request, TiposDocumentos $tipos_documentos)
    {
        $tipos_documentos = $tipos_documentos->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $tipos_documentos->total(),
                'rows' => $tipos_documentos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Roles sin ningun filtro
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTodos(Request $request, TiposDocumentos $tipos_documentos)
    {
        $tipos_documentos = TiposDocumentos::where('permite_registro_manual',1)->where('estado',1)->orderby('id_tipo_doc')->get();
        return response()->json([
            'status' => 'success',
            'result' => $tipos_documentos,
            'messages' => null
        ]);
    }

    /**
     * obtener Estado Finaciero Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTipoDocumento(Request $request)
    {
        $rules = [
            'id_tipo_doc'  => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_documento = TiposDocumentos::find($request->id_tipo_doc);

            if(!empty($tipo_documento)){
                return response()->json([
                    'status' => 'success',
                    'result' => $tipo_documento,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_tipo_doc'=>["Datos no encontrados"]),
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
     * Crear un nuevo tipo de Salida
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'permite_registro_manual'  => 'required|boolean',
            'prefijo'  => ['required','string','max:4',Rule::unique('pgsql.contabilidad.tipos_documentos')],
            'descripcion' => [
                'required',
                'string',
                'max:90',
                Rule::unique('pgsql.contabilidad.tipos_documentos')
            ]
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_doc = new TiposDocumentos;
            $tipo_doc->descripcion = $request->descripcion;
            $tipo_doc->permite_registro_manual = $request->permite_registro_manual;
            $tipo_doc->prefijo = $request->prefijo;
            $tipo_doc->secuencia = 1;
            $tipo_doc->id_empresa = $usuario_empresa->id_empresa;
            $tipo_doc->estado = 1;
            $tipo_doc->u_creacion = Auth::user()->name;
            $tipo_doc->save();

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
     * Actualizar Tipo de documento existente
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_tipo_doc'  => 'required|integer|min:1',
            'prefijo'  => ['required','string','max:4',Rule::unique('pgsql.contabilidad.tipos_documentos')->ignore($request->id_tipo_doc,'id_tipo_doc')],
            'permite_registro_manual'  => 'required|boolean',
            'descripcion' => [
                'required',
                'string',
                'max:90',
                Rule::unique('pgsql.contabilidad.tipos_documentos')->ignore($request->id_tipo_doc,'id_tipo_doc')
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_doc = TiposDocumentos::find($request->id_tipo_doc);
            $tipo_doc->descripcion = $request->descripcion;
            $tipo_doc->permite_registro_manual = $request->permite_registro_manual;
            $tipo_doc->prefijo = $request->prefijo;
            $tipo_doc->u_modificacion = Auth::user()->name;
            $tipo_doc->save();

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
     * Desactiva rol
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_tipo_doc'  => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo = TiposDocumentos::find($request->id_tipo_doc);
            $tipo->estado = 0;
            $tipo->save();

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


    public function activar(Request $request)
    {
        $rules = [
            'id_tipo_doc'  => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo = TiposDocumentos::find($request->id_tipo_doc);
            $tipo->estado = 1;
            $tipo->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteTiposDocumentos';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteTiposDocumentos';
            $input = '/var/www/html/resources/reports/ReporteTiposDocumentos';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteTiposDocumentos';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteTiposDocumentos.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}
