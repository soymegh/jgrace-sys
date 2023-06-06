<?php

namespace App\Models\Contabilidad;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TasasCambios extends Model
{

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.tasas_cambios';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_tasa_cambio';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable = ['fecha','tasa','tasa_paralela'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

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
     * Obtener listado de tasas de cambio
     *
     * @access    public
     * @param
     * @return
     * @author octaviom
     */
    public function obtenerTasasCambio($request)
    {
        $tasas = $this->select(['*']);

        if((!empty($request->search['anio'])) && (!empty($request->search['mes'])) && $request->search['anio']!==0 && $request->search['mes']!==0){


            $tasas->whereRaw("cast(EXTRACT(month FROM fecha) as INTEGER) =".$request->search['mes']['mes']);
            $tasas->whereRaw("cast(EXTRACT( year FROM fecha) as INTEGER) = ".$request->search['anio']['periodo']);

        }

        $tasas->orderBy('contabilidad.tasas_cambios.fecha', 'desc');

        return $tasas->paginate($request->limit);
    }

    /**
     * Obtener reporte de tasas de cambio
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerTasasReporte($request)
    {
        $tasas = $this->select(['*']);

        if((!empty($request->search['fecha_inicial'])) && (!empty($request->search['fecha_final'])) && $request->search['fecha_inicial']!=='Invalid date' && $request->search['fecha_final']!=='Invalid date'){

            $fechafinal = Carbon::parse($request->search['fecha_final'])->addDay();
            $tasas->whereBetween('fecha', [$request->search['fecha_inicial'], $fechafinal]);
        }

        $tasas->orderBy('contabilidad..tasas_cambios.fecha', 'desc');

        return $tasas->get();
    }


    /**
     * Obtener mes y año pendiente
     *
     * @access  public
     * @param
     * @return
     * @author octaviom
     */

    public function obtenerMesAnioPendiente($request)
    {
        $mes_anio = $this->select(
            DB::raw("EXTRACT(MONTH FROM DATE(max(fecha) + INTERVAL '1' DAY)) as mes"),
            DB::raw("EXTRACT(YEAR FROM DATE(max(fecha) + INTERVAL '1' DAY)) as anio")
        )->where('tasa','>',0);
        return $mes_anio->get();
    }

}





