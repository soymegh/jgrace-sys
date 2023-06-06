<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;

class  ZonasSectores extends Model
{

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'public.zonas_detalles';
    protected $primaryKey='id_zona_detalle';
    protected $fillable = ['id_sector','id_zona','u_modificacion','u_creacion','estado'];
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
            $zonas->with('zona')->with('sector');
            $zonas->orderBy('id_zona_detalle', 'asc');
        }
        return $zonas->paginate($request->limit);
    }

    public function zona(){
        return $this->belongsTo('App\Models\Admon\Sectores', 'id_zona');
    }

    Public function sector(){
        return $this->belongsTo('App\Models\Admon\Sectores', 'id_sector');
    }
}



