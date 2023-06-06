<?php

namespace App\Models\Admon;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BitacoraAccesos extends Model
{

    /**
     * indicate the table on database
    */
    protected $table = "bitacora.accesos";

    /**
     * indicate the primary key of table on database
     */
    protected $primaryKey = "id_acceso";

    /**
     * indicate if table save register and modify dates
    */

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alias',
        'id_empleado',
        'direccion_ip',
        'f_acceso',
        'dispositivo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'direccion_ip',
        'id_empleado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'f_acceso' => 'datetime',
    ];

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
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $conf = session()->get('id_empresa');
            $registro_accesos->where('id_empresa', '=', $conf)->where($searchField, 'ilike', '%' . $searchValue . '%');
        }

        if((!empty($request->search['fecha_inicial'])) && (!empty($request->search['fecha_final'])) && $request->search['fecha_inicial']!='Invalid date' && $request->search['fecha_final']!='Invalid date'){

            $fechafinal = Carbon::parse($request->search['fecha_final'])->addDay();
            $registro_accesos->where('id_empresa', '=', $conf)->whereBetween('f_acceso', [$request->search['fecha_inicial'], $fechafinal]);
        }

        $registro_accesos->orderBy('bitacora.accesos.f_acceso', 'desc');

        return $registro_accesos->paginate($request->limit);
    }

    /**
     * Obtener Lista de Accesos para reporte
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
