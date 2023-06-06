<?php
namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Contabilidad\Monedas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Admon\Ajustes;
use Illuminate\Http\Request;

class AjustesController extends Controller
{
    /**
     * Obtener Ajustes Generales
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerAjustes(Request $request)
    {




        //   $settings = AdmonAjustes::/*whereIn('id_ajuste',array(1, 2, 3, 4, 5, 6, 7, 8, 9, 21, 22, 26))->*/orderBy('id_ajuste')->get();
        $host=request()->getSchemeAndHttpHost();
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
        $conf = $usuario_empresa->id_empresa;
        $settings = Ajustes::where('id_empresa',$conf)->orderBy('id_ajuste')->get();
        $monedas = Monedas::select('id_moneda', 'descripcion', 'codigo')->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'ajustes' => $settings,
                'host' => $host,
                'monedas' => $monedas
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener Ajustes Basicos (no requiere usuario autenticado)
     *
     * @access  public
     * @param
     * @return JsonResponse
     */
    public function obtenerAjustesBasicos(Request $request)
    {
        $host=request()->getSchemeAndHttpHost();
        // $settings = AdmonAjustes::whereIn('id_ajuste',array(2,3,21,22))->orderBy('id_ajuste')->select('id_ajuste','valor')->get();
        $conf = session()->get('id_empresa');
        $company_name = Ajustes::where('id_empresa',$conf)->where('id_ajuste',2)->select('id_ajuste','valor')->first();
        $logo_left = Ajustes::where('id_empresa',$conf)->where('id_ajuste',3)->select('id_ajuste','valor')->first();
        $logo_icon = Ajustes::where('id_empresa',$conf)->where('id_ajuste',9)->select('id_ajuste','valor')->first();
        $logo_login = Ajustes::where('id_empresa',$conf)->where('id_ajuste',10)->select('id_ajuste','valor')->first();
        //$settings = AdmonAjustes::where('id_empresa',$conf)->whereIn('id_ajuste',array(2,3,21,22))->orderBy('id_ajuste')->select('id_ajuste','valor','id_empresa')->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'host' => $host,
                'company_name' => $company_name,
                'logo_left' => $logo_left,
                'logo_icon' => $logo_icon,
                'logo_login' => $logo_login,
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener Ajustes Contabilidad
     *
     * @access  public
     * @param
     * @return JsonResponse
     */
    public function obtenerAjustesContabilidad(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $conf = session()->get('id_empresa');
        $settings = Ajustes::where('id_empresa',$conf)->whereIn('identificador',array('cuenta_perdida_ganancia'))->orderBy('id_ajuste')->get();
        return response()->json([
            'status' => 'success',
            'result' => $settings,
            'messages' => null
        ]);
    }


    /**
     * Obtener Imagenes (logo)
     *
     * @access  public
     * @param
     * @return JsonResponse
     */
    public function obtenerImagenes(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $conf = session()->get('id_empresa');
        $settings = Ajustes::where('id_empresa',$conf)->whereIn('id_ajuste',array(21,3))->orderBy('id_ajuste')->select('id_ajuste','valor','id_empresa')->get();
        return response()->json([
            'status' => 'success',
            'result' => $settings,
            'messages' => null
        ]);
    }


    /**
     * Guardar Ajustes del sistema
     *
     * @access 	public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {



        $rules = [
            'app_title' => 'required|string|max:191',
            'company_address' => 'required|string|max:191',
            'company_email' => 'required|email|string|max:191',
            'company_name' => 'required|string|max:50',
            'company_telephone' => 'required|string|max:15',
            'company_website' => 'required|string|max:191',
//            'uploaded_logo' => 'required|string',
//            'uploaded_icon' => 'required|string',
//            'login_img' => 'required|string',
            'ruc_company' => 'required|string|max:16',

            //'footer' => 'required',
            //'footer_line_1' => 'required',
            //'footer_line_2' => 'required',
            //'footer_line_3' => 'required',
            //'footer-html' => 'required',
            //'global_bcc_email' => 'required',
            //'header' => 'required',
            //'header-html' => 'required',
            //'sent_from_email' => 'required',
            //'sent_from_name' => 'required',
            //'uploaded_logo' => 'required',

        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $conf = session()->get('id_empresa');
            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'app_title')->first();
            $setting->valor = $request->app_title;
            $setting->save();

            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'company_address')->first();
            $setting->valor = $request->company_address;
            $setting->save();

            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'company_email')->first();
            $setting->valor = $request->company_email;
            $setting->save();

            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'company_name')->first();
            $setting->valor = $request->company_name;
            $setting->save();

            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'company_telephone')->first();
            $setting->valor = $request->company_telephone;
            $setting->save();

            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'company_website')->first();
            $setting->valor = $request->company_website;
            $setting->save();

            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'currency_id')->first();
            $setting->valor = $request->moneda;
            $setting->save();


            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'ruc_company')->first();
            $setting->valor = $request->ruc_company;
            $setting->save();

            $setting = Ajustes::where('id_empresa',$conf)->where('identificador', 'discount_max')->first();
            $setting->valor = $request->discount_max;
            $setting->save();

            // upload images



            if ($request->avatarlogin && $request->imagen_nueva === true){
                $setting = Ajustes::where('identificador', 'login_img')->first();
                $name = time().'.' . explode('/', explode(':', substr($request->avatarlogin, 0, strpos($request->avatarlogin, ';')))[1])[1];
                \Image::make($request->avatarlogin)->save(public_path('img/').$name);
                $request->merge(['avatarlogin' => $name]);
                $setting->valor = '/img/'.$name;

                $setting->save();


            }
            if ($request->avataricon && $request->icono_nuevo === true){
                $setting = Ajustes::where('identificador', 'uploaded_icon')->first();
                $name = time().'.' . explode('/', explode(':', substr($request->avataricon, 0, strpos($request->avataricon, ';')))[1])[1];
                \Image::make($request->avataricon)->save(public_path('img/').$name);
                $request->merge(['avataricon' => $name]);
                $setting->valor = '/img/'.$name;

                $setting->save();


            }

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
     * Metodo para obtener imagenes desde base de dato
    */
    public function obtenerRecursos(Request $request)
    {

        // $settings = AdmonAjustes::whereIn('id_ajuste',array(2,3,21,22))->orderBy('id_ajuste')->select('id_ajuste','valor')->get();
        $host= request()->getSchemeAndHttpHost();
        $company_name = Ajustes::where('id_ajuste',2)->select('id_ajuste','valor')->first();
        $logo_left = Ajustes::where('id_ajuste',3)->select('id_ajuste','valor')->first();
        $logo_right = Ajustes::where('id_ajuste',21)->select('id_ajuste','valor')->first();
        $logo_login = Ajustes::where('id_ajuste',10)->select('id_ajuste','valor')->first();
        //$settings = AdmonAjustes::where('id_empresa',$conf)->whereIn('id_ajuste',array(2,3,21,22))->orderBy('id_ajuste')->select('id_ajuste','valor','id_empresa')->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'host'=> $host,
                'company_name' => $company_name,
                'logo_left' => $logo_left,
                'logo_right' => $logo_right,
                'logo_login' => $logo_login,
            ],
            'messages' => null
        ]);
    }
}
