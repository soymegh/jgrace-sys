<?php

namespace App\Models\CuentasXCobrar;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB, Illuminate\Database\Eloquent\Model;

class Proyectos extends Model
{
    protected $table = 'cuentasxcobrar.proyectos';
    protected $primaryKey='id_proyecto';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = ['num_documento','beneficiario','valor','estado'];

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

    /**
     * Obtener Lista de entradas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
        $recibos = $this->select(['*']);
        if (!empty($request->search['field'])) {
           $searchField = $request->search['field'];
           $searchValue = $request->search['value'];
           $recibos->where($searchField, 'ilike', '%' . $searchValue . '%');
       }
        $recibos->where('id_empresa',$usuario_empresa->id_empresa);
        $recibos->orderBy('id_proyecto','desc');

        return $recibos->paginate($request->limit);
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
}
