<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;

class Zonas extends Model
{

    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'public.zonas';
    protected $primaryKey='id_zona';
    protected $fillable = ['pdescripcion','estado'];
    protected $dateFormat = 'Y-m-d H:i:s.u';

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

    public function obtenerCodigoZona()
    {
        $codigo = $this->select([DB::raw("COALESCE(max(codigo::integer),0)+1 as secuencia")]);
        return $codigo->first();
    }

    /**
     * Obtener Listado de Paises
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerZonas($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $zonas= $this->select(['*']);
       /* $zonas = $this->select(['public.zonas.id_zona','public.zonas.descripcion as zonas','public.zonas.codigo','public.zonas.estado','public.zonas.id_empresa','public.zonas.id_departamento','public.zonas.id_municipio','public.municipios.descripcion as municipios','public.departamentos.descripcion as departamentos']);
        $zonas->join('public.municipios','public.municipios.id_municipio', '=', 'public.zonas.id_municipio')->join('public.departamentos','public.departamentos.id_departamento','=','public.zonas.id_departamento');*/
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $zonas->where($searchField, 'ilike', '%' . $searchValue . '%');
            $zonas->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $zonas->where('estado',1);
                $zonas->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $zonas->with(['zonaMunicipio' => function($query){
                $query->with('departamentoMunicipio');
            }]);
            $zonas->orderBy('id_zona', 'asc');
        }
        return $zonas->paginate($request->limit);
    }

    public function zonaCentroCosto()
    {
        return $this->belongsTo('App\Models\Contabilidad\CentrosCostosIngresos','id_centro_costo')->select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"));
    }


    public function zonaCentroIngreso()
    {
        return $this->belongsTo('App\Models\Contabilidad\CentrosCostosIngresos','id_centro_ingreso')->select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"));
    }

    public function zonaMunicipio() {
        return $this->belongsTo('App\Models\Admon\Municipios','id_municipio')->select('id_municipio','descripcion','id_departamento');
    }

    Public function zonaSector(){
        return $this->belongsTo('App\Models\Admon\Sectores', 'id_sector');
    }
}



