<?php

namespace App\Models\Bitacora;

use App\Models\Admon\UsuariosEmpresas;
use DB, Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Accesos extends Model {

    public $table = 'bitacora.accesos';
    protected $primaryKey='id_acceso';
    public $timestamps = false;

    /**
     * Replace Field
     *
     * @access  public
     * @param
     * @return  string
     */

    public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields)) {
            return $fields[$field];
        }

        return $field;
    }

    /**
     * Obtener Lista de Accesos
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerAccesos($request)
    {
        /*$agent = new Agent();
          $agent->setUserAgent( $request->header('User-agent',null));
          $acceso->dispositivo =$agent->platform().' '.$agent->version($agent->platform()) .' '.$agent->browser().' '. (int) $agent->version($agent->browser()).' '. $agent->device();
          */

        $registro_accesos = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $registro_accesos->where('id_empresa', '=', $usuario_empresa->id_empresa)->where($searchField, 'ilike', '%' . $searchValue . '%');
        }

        if((!empty($request->search['fecha_inicial'])) && (!empty($request->search['fecha_final'])) && $request->search['fecha_inicial'] !== 'Invalid date' && $request->search['fecha_final'] !== 'Invalid date'){

            $fechafinal = Carbon::parse($request->search['fecha_final'])->addDay();
            $registro_accesos->where('id_empresa', '=', $usuario_empresa->id_empresa)->whereBetween('f_acceso', [$request->search['fecha_inicial'], $fechafinal]);
        }

        $registro_accesos->orderBy('bitacora.accesos.f_acceso', 'desc');

        return $registro_accesos->paginate($request->limit);
    }

    /**
     * Obtener Lista de Accesos
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerAccesosReporte($request)
    {
        $registro_accesos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $conf = session()->get('id_empresa');
            $registro_accesos->where('id_empresa', '=', $conf)->where($searchField, 'ilike', '%' . $searchValue . '%');
        }

        if((!empty($request->search['fecha_inicial'])) && (!empty($request->search['fecha_final'])) && $request->search['fecha_inicial']!='Invalid date' && $request->search['fecha_final']!='Invalid date'){

            $fechafinal = Carbon::parse($request->search['fecha_final'])->addDay();
            $registro_accesos->whereBetween('f_acceso', [$request->search['fecha_inicial'], $fechafinal]);
        }

        $registro_accesos->orderBy('bitacora.accesos.f_acceso', 'desc');

        return $registro_accesos->get();
    }

}
