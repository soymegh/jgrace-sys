<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class PeriodosMeses extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.periodos_meses';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_periodo_mes';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','id_periodo_fiscal','mes','u_creacion','u_modificacion','estado','id_empresa'];

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
     * Obtener Periodos Meses según el id_empresa
     * @param $request
     * @return mixed
     */
    public function obtenerMesesPeriodo($request)
    {
        $meses = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $meses -> where('id_empresa',$usuario_empresa->id_empresa);
            $meses->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $meses->with('periodoFiscal');
        return $meses->paginate($request->limit);
    }


    /**
     * Relación Periodos Fiscal - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

    /**
     * Relación Periodos meses - Periodo fiscal
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function periodoFiscal()
    {
        return $this->belongsTo('App\Models\ContabilidadPeriodoFiscal','id_periodo_fiscal');
    }

}





