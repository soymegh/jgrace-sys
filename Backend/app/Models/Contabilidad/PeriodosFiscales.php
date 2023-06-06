<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class PeriodosFiscales extends Model
{

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.periodos_fiscales';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_periodo_fiscal';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','periodo','u_creacion','u_modificacion','estado','id_empresa'];

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
     * Obtener Periodos contables
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtener($request)
    {
        $periodos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $periodos->where('id_empresa', '=', $usuario_empresa->id_empresa);
            $periodos->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $periodos->with('mesesPeriodo');
        $periodos->orderby('periodo','desc');
        return $periodos->paginate($request->limit);
    }


    /**
     * Relación Periodos Fiscal - periodo meses
     * @return HasMany
     */
    public function mesesPeriodo()
    {
        return $this->hasMany('App\Models\Contabilidad\PeriodosMeses','id_periodo_fiscal')
            ->select('id_periodo_fiscal','descripcion','id_periodo_mes','mes','estado',
                DB::raw("contabilidad.ultimo_dia_mes(id_periodo_mes)"),
                DB::raw("case when mes=1 then 'Enero' when mes=2 then 'Febrero' when mes=3 then 'Marzo' when mes=4 then 'Abril'
                 when mes=5 then 'Mayo' when mes=6 then 'Junio' when mes=7 then 'Julio' when mes=8 then 'Agosto'
                  when mes=9 then 'Septiembre' when mes=10 then 'Octubre' when mes=11 then 'Noviembre' when mes=12 then 'Diciembre' end as mes_letras"))->orderby('mes');
    }

}





