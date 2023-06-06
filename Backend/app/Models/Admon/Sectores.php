<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Sectores extends Model
{

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'public.sectores';
    protected $primaryKey='id_sector';
    protected $fillable = ['descripcion','id_municipio','u_creacion','u_modificacion','longitud','latitud','estado'];
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
        $sectores= $this->select(['*']);
       /* $zonas = $this->select(['public.zonas.id_zona','public.zonas.descripcion as zonas','public.zonas.codigo','public.zonas.estado','public.zonas.id_empresa','public.zonas.id_departamento','public.zonas.id_municipio','public.municipios.descripcion as municipios','public.departamentos.descripcion as departamentos']);
        $zonas->join('public.municipios','public.municipios.id_municipio', '=', 'public.zonas.id_municipio')->join('public.departamentos','public.departamentos.id_departamento','=','public.zonas.id_departamento');*/
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $sectores->where($searchField, 'ilike', '%' . $searchValue . '%');
            $sectores->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $sectores->where('estado',1);
                $sectores->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $sectores->with('sectorDepartamento');
            $sectores->orderBy('id_sector', 'asc');
        }
        return $sectores->paginate($request->limit);
    }

    public function sectorDepartamento() {
        return $this->belongsTo('App\Models\Admon\Departamentos','id_departamento');
    }
}



