<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class Requisas extends Model {

    use HasFactory;

    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.requisas';
    protected $primaryKey='id_requisa';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['codigo_requisa','fecha_solicitud','estado'];

    /**
     * Obtener Lista de Salidas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerRequisas($request)
    {
        $requisas = $this->select(['*']);
        if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $requisas->where($searchField, 'ilike', '%' . $searchValue . '%');
        }
        $requisas->with('requisaProductos');
        $requisas->with('requisaProveedor');
        $requisas->with('requisaTrabajador');
        $requisas->with('requisaArea');
        $requisas->with('requisaBodega');
        $requisas->with('requisaSucursal');
        $requisas->orderBy('fecha_solicitud', 'desc');

        return $requisas->paginate($request->limit);
    }

    public function obtenerRequisa($request)
    {
        $requisa = $this->select(['*']);
        $requisa->where('id_requisa', $request->id_requisa);

        $requisa->with(['requisaProductos' => function($query) {
            $query->with(['bodegaProducto' => function($query2) {
                $query2->with('productoSimple');
            }]);
        }]);

        $requisa->with('requisaProveedor');
        $requisa->with('requisaTrabajador');
        $requisa->with('requisaArea');
        $requisa->with('requisaSucursal');

        $requisa->with(['requisaBodega' => function($query) {
            $query->with('productosBodega');
        }]);
        return $requisa->get();
    }


    public function requisaProductos()
    {
        return $this->hasMany('App\Models\InventarioRequisaProductos','id_requisa');
    }

    public function requisaProveedor()
    {
        return $this->belongsTo('App\Models\InventarioProveedores','id_proveedor')->select(['*','nombre_comercial as text']);
    }

    public function requisaBodega()
    {
        return $this->belongsTo('App\Models\InventarioBodegas','id_bodega')->select(['*','descripcion as text']);
    }

    public function requisaSucursal()
    {
        return $this->belongsTo('App\Models\PublicSucursales','id_sucursal')->select(['*','descripcion as text']);
    }

    public function requisaTrabajador()
    {
        return $this->belongsTo('App\Models\RRHHTrabajadores','id_trabajador')->select(['*',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS text")]);
    }

    public function requisaArea()
    {
        return $this->belongsTo('App\Models\PublicAreas','id_area')->select(['*','descripcion as text']);
    }

}
