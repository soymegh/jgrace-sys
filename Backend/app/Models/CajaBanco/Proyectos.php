<?php

namespace App\Models\CajaBanco;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Ventas\Clientes;
use Illuminate\Support\Facades\DB;

class Proyectos extends Model
{

    protected $table = 'cuentasxcobrar.proyectos';
    protected $primaryKey='id_proyecto';
    protected $fillable = ['descripcion','u_creacion','u_modificacion','estado','id_empresa'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

    /**
     * Replace Field
     *
     * @access    public
     * @param
     * @param array $fields
     * @return    string
     */

    public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields)) {
            return $fields[$field];
        }

        return $field;
    }

    /**
     * Obtener Listado de Bancos
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerProyectos($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $bancos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $bancos->where($searchField, 'ilike', '%' . $searchValue . '%');
            $bancos->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue === 0){
                $bancos->where('estado',1);
                $bancos->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $bancos->with('clienteProyecto');
            $bancos->orderBy('cuentasxcobrar.proyectos.id_proyecto', 'asc');
        }
        return $bancos->paginate($request->limit);
    }

    public function obtenerProyectosCliente($request)
    {
        $proyectos = $this->select([
            '*',
        ]);
        if ( (!empty($request->id_cliente)) ) {
            $proyectos->Where('id_cliente', $request->id_cliente);
            $proyectos->Where('estado', '=',1);
            return $proyectos->get();
        }

        return $proyectos->limit(0)->get();
    }

    public function clienteProyecto()
    {
        return $this->belongsTo(Clientes::class,'id_cliente')->select(["*", DB::raw("CONCAT((case venta.clientes.tipo_persona when 1 then venta.clientes.nombre_completo else venta.clientes.razon_social end)) AS text")]);
    }

}

