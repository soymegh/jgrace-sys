<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use DB, Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class CentrosCostosIngresos extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.centros_costos_ingresos';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_centro';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['tipo_centro','descripcion','codigo','secuencia','u_creacion','u_modificacion','estado','ubicacion','clasificacion_contable','id_empresa'];

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
     * Obtener lista de anexos segun el centro de costo y el id empresa del usurio
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */
    public function obtenerCentroCostoIngreso($request)
    {
        $centros = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $centros->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $centros->where('estado',true);
                $centros -> where('id_empresa',$usuario_empresa->id_empresa);
            }
            $centros->orderBy('tipo_centro', 'asc');
            $centros->orderBy('codigo', 'asc');
        }
        return $centros->paginate($request->limit);
    }

    /**
     * @param $tipo_centro
     * @param $ubicacion
     * @param $clasificacion_contable
     * @return mixed
     */
    public function obtenerCodigo($tipo_centro,$ubicacion,$clasificacion_contable)
    {
        //print_r($request);
        $codigo = $this->select([DB::raw("COALESCE(max(secuencia),0)+1 as secuencia")])
            ->where('tipo_centro',$tipo_centro)
            ->where('ubicacion',$ubicacion)
            ->where('clasificacion_contable',$clasificacion_contable);
        return $codigo->first();
    }

    /**
     * Relación Centro de costo - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

}





