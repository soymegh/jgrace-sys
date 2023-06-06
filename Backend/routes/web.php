<?php

use App\Http\Controllers\Admon\AjustesController;
use App\Http\Controllers\Admon\InviteController;
use App\Http\Controllers\Admon\UsuariosController;
use App\Http\Controllers\Admon\RolesController;
use App\Http\Controllers\Admon\ModulosController;
use App\Http\Controllers\Admon\PaisesController;
use App\Http\Controllers\Admon\DepartamentosController;
use App\Http\Controllers\Admon\MunicipiosController;
use App\Http\Controllers\Admon\PermisosController;
use App\Http\Controllers\Admon\SucursalesController;
use App\Http\Controllers\Admon\ImpuestosController;
use App\Http\Controllers\Admon\ZonasController;
use App\Http\Controllers\Admon\SectoresController;
use App\Http\Controllers\Bitacora\AccesosController;
use App\Http\Controllers\CajaBanco\FacturasConfiguracionController;
use App\Http\Controllers\CajaBanco\ProformasController;
use App\Http\Controllers\CajaBanco\ProyectosController;
use App\Http\Controllers\Contabilidad\ReportesContabilidadController;
use App\Http\Controllers\CuentasXCobrar\RecibosController;
use App\Http\Controllers\Inventario\CategoriasController;
use App\Http\Controllers\Inventario\InventarioFisicoController;
use App\Http\Controllers\Inventario\MovimientosProductosController;
use App\Http\Controllers\Inventario\ReportesController;
use App\Http\Controllers\Inventario\SalidasController;
use App\Http\Controllers\Inventario\TipoEntradaController;
use App\Http\Controllers\CajaBanco\ProformaSeguimientoController;
use App\Http\Controllers\Ventas\ClientesController;
use App\Http\Controllers\CajaBanco\ReportesCjaBncoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Contabilidad\AnexosController;
use App\Http\Controllers\Contabilidad\CentrosCostosIngresosController;
use App\Http\Controllers\Contabilidad\CierresMensualesController;
use App\Http\Controllers\Contabilidad\CuentasBancariasController;
use App\Http\Controllers\Contabilidad\DocumentosContablesController;
use App\Http\Controllers\Contabilidad\EstadosFinancierosController;
use App\Http\Controllers\Contabilidad\NivelesCuentasController;
use App\Http\Controllers\Contabilidad\PeriodosFiscalesController;
use App\Http\Controllers\Contabilidad\ReportesFinancierosController;
use App\Http\Controllers\Contabilidad\TiposCuentasController;
use App\Http\Controllers\Contabilidad\TiposDocumentosController;
use App\Http\Controllers\Contabilidad\ConfiguracionComprobantesController;
use App\Http\Controllers\Contabilidad\TasasCambioController;
use App\Http\Controllers\Inventario\UnidadMedidaController;
use App\Http\Controllers\Inventario\ProductosController;
use App\Http\Controllers\Inventario\TiposProductosController;
use App\Http\Controllers\Inventario\BodegasController;
use App\Http\Controllers\Inventario\InventarioInicialController;
use App\Http\Controllers\Inventario\TipoBodegaController;
use App\Http\Controllers\Inventario\EntradasController;
use App\Http\Controllers\Inventario\TipoSalidaController;
use App\Http\Controllers\Inventario\TipoProveedorController;
use App\Http\Controllers\Inventario\ProveedoresControllers;
use App\Http\Controllers\Inventario\MarcasController;
use App\Http\Controllers\Inventario\ConfiguracionInventarioController;
use App\Http\Controllers\Ventas\VendedoresController;
use App\Http\Controllers\Ventas\TipoClienteController;
use App\Http\Controllers\CajaBanco\BancosController;
use App\Http\Controllers\CajaBanco\FacturasController;
use App\Http\Controllers\CuentasXCobrar\CuentasXCobrarController;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which


/*
|--------------------------------------------------------------------------
| Web contabilidad
|--------------------------------------------------------------------------
|
| Here is where you can register web contabilidad for your application. These
| contabilidad are loaded by the contabilidaderviceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Funciones de autenticación
Route::get('/obtener-recursos', [AjustesController::class, 'obtenerRecursos']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// Rutas sanctum

Route::group(['middleware' => ["auth:sanctum"]], function () {

    // Obtener datos del usuario en sesion
    Route::get('me', [AuthController::class, 'me']);
    Route::get('user-activity', [AuthController::class, 'userActivity']);

    // Funciones ajustes generales

    Route::get('ajustes/cargar-basico', [AjustesController::class, 'obtenerAjustesBasicos']);
    Route::get('ajustes/cargar-imagenes', [AjustesController::class, 'obtenerImagenes']);
    Route::get('ajustes/cargar', [AjustesController::class, 'obtenerAjustes']);
    Route::get('ajustes/cargar-contabilidad', [AjustesController::class, 'obtenerAjustesContabilidad']);
    Route::post('ajustes/guardar', [AjustesController::class, 'registrar']);

    // Funciones catalogo de roles

    Route::post('admon/roles/obtener-roles', [RolesController::class, 'obtenerRoles']);
    Route::get('admon/roles/obtener-roles-todos', [RolesController::class, 'obtenerTodosRoles']);
    Route::post('admon/roles/obtener-rol', [RolesController::class, 'obtenerRolEspecifico']);
    Route::post('admon/roles/registrar', [RolesController::class, 'crearRol']);
    Route::put('admon/roles/actualizar', [RolesController::class, 'actualizarRol']);
    Route::put('admon/roles/desactivar', [RolesController::class, 'desactivaRol']);
    Route::put('admon/roles/activar', [RolesController::class, 'activaRol']);

    // funciones catalogo usuarios

    Route::post('admon/usuarios/obtener', [UsuariosController::class, 'obtener']);
    Route::post('admon/usuarios/obtener-usuario', [UsuariosController::class, 'obtenerUsuario']);
    Route::post('admon/usuarios/obtener-user-login', [UsuariosController::class, 'obtenerUserLogin']);
    Route::get('admon/usuarios/obtener-activos', [UsuariosController::class, 'obtenerUsuario']);
    Route::post('admon/usuarios/registrar', [UsuariosController::class, 'registrar']);
    Route::put('admon/usuarios/cambiar-contrasena', [UsuariosController::class, 'cambiarContrasena']);

    // funciones modulos

    Route::get('admon/modulos/obtener', [ModulosController::class, 'obtenerModulos']);
    Route::post('admon/menus/verificar', [ModulosController::class, 'verificarPermisoVista']);
    Route::get('admon/menu/obtener-menus-todos', [ModulosController::class, 'obtenerMenusTodos']);

    // funciones Roles
    Route::post('bitacora/accesos/obtener-accesos', [AccesosController::class, 'obtenerAccesos']);
    Route::post('bitacora/accesos/obtener-accesos-reporte', [AccesosController::class, 'obtenerAccesosReporte']);

    // Rutas Permisos
    Route::post('admon/permisos/obtener-permisos', [PermisosController::class, 'obtenerPermisos']);
    Route::post('admon/permisos/guardar', [PermisosController::class, 'guardarPermiso']);

    // Rutas Paises
    Route::post('admon/paises/obtener', [PaisesController::class, 'obtener']);
    Route::get('admon/paises/obtener-todos', [PaisesController::class, 'obtenerTodosPaises']);
    Route::post('admon/paises/obtener-pais', [PaisesController::class, 'obtenerPais']);
    Route::post('admon/paises/registrar', [PaisesController::class, 'registrar']);
    Route::put('admon/paises/actualizar', [PaisesController::class, 'actualizar']);
    Route::put('admon/paises/desactivar', [PaisesController::class, 'desactivar']);
    Route::put('admon/paises/activar', [PaisesController::class, 'activar']);

    // Rutas Departamentos
    Route::post('admon/departamentos/obtener', [DepartamentosController::class, 'obtener']);
    Route::get('admon/departamentos/obtener-todos', [DepartamentosController::class, 'obtenerTodosDepartamentos']);
    Route::post('admon/departamentos/obtener-departamento', [DepartamentosController::class, 'obtenerDepartamento']);
    Route::post('admon/departamentos/registrar', [DepartamentosController::class, 'registrar']);
    Route::put('admon/departamentos/actualizar', [DepartamentosController::class, 'actualizar']);

    // Rutas Municipios
    Route::post('admon/municipios/obtener', [MunicipiosController::class, 'obtener']);
    Route::get('admon/municipios/obtener-todos', [MunicipiosController::class, 'obtenerTodosMunicipios']);
    Route::post('admon/municipios/obtener-municipio', [MunicipiosController::class, 'obtenerMunicipio']);
    Route::post('admon/municipios/registrar', [MunicipiosController::class, 'registrar']);
    Route::put('admon/municipios/actualizar', [MunicipiosController::class, 'actualizar']);

    // Rutas Sucursales
    Route::post('admon/sucursales/obtener', [SucursalesController::class, 'obtener']);
    Route::get('admon/sucursales/obtener-todas', [SucursalesController::class, 'obtenerTodas']);
    Route::post('admon/sucursales/obtener-sucursal', [SucursalesController::class, 'obtenerSucursal']);
    Route::post('admon/sucursales/registrar', [SucursalesController::class, 'registrar']);
    Route::put('admon/sucursales/actualizar', [SucursalesController::class, 'actualizar']);
    Route::put('admon/sucursales/activar', [SucursalesController::class, 'activar']);
    Route::put('admon/sucursales/desactivar', [SucursalesController::class, 'desactivar']);
    Route::get('admon/sucursales/buscar', [SucursalesController::class, 'buscar']);
    Route::get('admon/sucursales/reporte/{ext}', [SucursalesController::class, 'generarReporte']);

    //Rutas Impuestos
    Route::post('admon/impuestos/obtener', [ImpuestosController::class, 'obtener']);
    Route::get('admon/impuestos/obtener-impuestos-todos', [ImpuestosController::class, 'obtenerTodos']);
    Route::post('admon/impuestos/obtener-impuesto', [ImpuestosController::class, 'obtenerImpuesto']);
    Route::post('admon/impuestos/registrar', [ImpuestosController::class, 'registrar']);
    Route::put('admon/impuestos/actualizar', [ImpuestosController::class, 'actualizar']);
    Route::put('admon/impuestos/activar', [ImpuestosController::class, 'activar']);
    Route::put('admon/impuestos/desactivar', [ImpuestosController::class, 'desactivar']);
    Route::put('admon/impuestos/reporte/{ext}', [ImpuestosController::class, 'generarReporte']);
    //Rutas Impuestos Fin

    //Rutas Zonas
    Route::post('admon/zonas/obtener', [ZonasController::class, 'obtener']);
    Route::get('admon/zonas/nuevo', [ZonasController::class, 'nuevo']);
    Route::post('admon/zonas/obtener-zona', [ZonasController::class, 'obtenerZona']);
    Route::post('admon/zonas/registrar', [ZonasController::class, 'registrar']);
    Route::put('admon/zonas/actualizar', [ZonasController::class, 'actualizar']);
    Route::put('admon/zonas/activar', [ZonasController::class, 'activar']);
    Route::put('admon/zonas/desactivar', [ZonasController::class, 'desactivar']);
    Route::put('admon/zonas/reporte/{ext}', [ZonasController::class, 'generarReporte']);
    //Rutas Zonas Fin

    //Rutas Sectores
    Route::post('admon/sectores/obtener', [SectoresController::class, 'obtener']);
    Route::get('admon/sectores/nuevo', [SectoresController::class, 'nuevo']);
    Route::post('admon/sectores/obtener-sector', [SectoresController::class, 'obtenerSector']);
    Route::post('admon/sectores/regitrar', [ZonasController::class, 'registrar']);
    Route::put('admon/sectores/actualizar', [ZonasController::class, 'actualizar']);
    Route::put('admon/sectores/activar', [ZonasController::class, 'activar']);
    Route::put('admon/sectores/desactivar', [ZonasController::class, 'desactivar']);
    Route::put('admon/sectores/reporte/{ext}', [ZonasController::class, 'generarReporte']);
    //Rutas Sectores Fin

//contabilidad contabilidad -> Anexos
    Route::post('contabilidad/anexos/obtener-anexos', [AnexosController::class, 'obtenerAnexos']);
    Route::get('contabilidad/anexos/obtener-anexos-todos', [AnexosController::class, 'obtenerTodosAnexos']);
    Route::post('contabilidad/anexos/obtener-anexos-estado-financiero', [AnexosController::class, 'obtenerAnexosPorEstadoFinanciero']);
    Route::post('contabilidad/anexos/obtener-anexo', [AnexosController::class, 'obtenerAnexo']);
    Route::post('contabilidad/anexos/registrar', [AnexosController::class, 'registrar']);
    Route::put('contabilidad/anexos/actualizar', [AnexosController::class, 'actualizar']);
    Route::put('contabilidad/anexos/desactivar', [AnexosController::class, 'desactivar']);
    Route::put('contabilidad/anexos/activar', [AnexosController::class, 'activar']);
    Route::put('contabilidad/anexos/actualizar-orden', [AnexosController::class, 'actualizarOrdenAnexos']);
//Fin contabilidad contabilidad -> Anexos

//contabilidad contabilidad -> Centros Costos Ingresos
    Route::post('contabilidad/centro-costo/obtener', [CentrosCostosIngresosController::class, 'obtener']);
    Route::get('contabilidad/centro-costo/obtener-todos', [CentrosCostosIngresosController::class, 'obtenerTodos']);
    Route::post('contabilidad/centro-costo/obtener-centro', [CentrosCostosIngresosController::class, 'obtenerCentro']);
    Route::post('contabilidad/centro-costo/registrar', [CentrosCostosIngresosController::class, 'registrar']);
    Route::put('contabilidad/centro-costo/actualizar', [CentrosCostosIngresosController::class, 'actualizar']);
    Route::put('contabilidad/centro-costo/desactivar', [CentrosCostosIngresosController::class, 'desactivar']);
    Route::put('contabilidad/centro-costo/activar', [CentrosCostosIngresosController::class, 'activar']);
    Route::get('contabilidad/centro-costo/reporte/{ext}', [CentrosCostosIngresosController::class, 'generarReporte']);
//Fin contabilidad contabilidad -> Centros Costos Ingresos

//Route contabilidad -> Cierres Mensuales
    Route::post('contabilidad/cuentas-contables/obtener-cuentas-contables', [CierresMensualesController::class, 'obtenerCuentasContables']);
    Route::get('contabilidad/cuentas-contables/obtener-cuentas-contables-todas', [CierresMensualesController::class, 'obtenerTodasCuentasContables']);
    Route::post('contabilidad/cuentas-contables/obtener-cuenta-contable', [CierresMensualesController::class, 'obtenerCuentaContable']);
    Route::post('contabilidad/cuentas-contables/obtener-cuenta-contable-v', [CierresMensualesController::class, 'obtenerCuentaContableV']);
    Route::post('contabilidad/cuentas-contables/registrar', [CierresMensualesController::class, 'guardarCuentaContable']);
    Route::put('contabilidad/cuentas-contables/actualizar', [CierresMensualesController::class, 'actualizarCuentaContable']);
    Route::put('contabilidad/cuentas-contables/desactivar', [CierresMensualesController::class, 'desactivarCuentaContable']);
    Route::put('contabilidad/cuentas-contables/activar', [CierresMensualesController::class, 'activarCuentaContable']);
    Route::post('contabilidad/cuentas-contables/obtener-cuentas-nivel', [CierresMensualesController::class, 'obtenerCuentasContablesNivel']);
    Route::post('contabilidad/cuentas-contables/buscar', [CierresMensualesController::class, 'buscarCuentasContables']);
    Route::post('contabilidad/cuentas-contables/buscarf', [CierresMensualesController::class, 'buscarCuentasContablesF']);
    Route::get('contabilidad/cuentas-contables/reporte/{ext}', [ReportesContabilidadController::class, 'generarReporteCatalogoCuentasContables']);
    Route::get('contabilidad/documentos-contables-detallados/reporte/{ext}/{f_inicial}/{f_final}', [ReportesContabilidadController::class, 'generarReporteDocumentosContablesDetallado']);
    Route::get('contabilidad/documentos-contables-detallados/reporte/{ext}/{f_inicial}/{f_final}', [ReportesContabilidadController::class, 'generarReporteDocumentosContablesDetallado']);
    //Reporte de Listado de Facturas

    Route::get('contabilidad/obtener-varios', [ReportesContabilidadController::class, 'obtenerCatalago']);
    Route::get('contabilidad/listado-de-facturas/{extension}/{id_cliente}/{id_vendedor}/{fecha_inicial}/{fecha_final}', [ReportesContabilidadController::class, 'generarReporteListadoFactura']);


//Fin contabilidad contabilidad -> Cierres Mensuales

//Route contabilidad -> Cuentas Bancarias
    Route::post('contabilidad/cuentas-bancarias/obtener', [CuentasBancariasController::class, 'obtenerCuentasBancarias']);
    Route::get('contabilidad/cuentas-bancarias/obtener-cuentas-bancarias-todas', [CuentasBancariasController::class, 'obtenerTodasCuentasBancarias']);
    Route::post('contabilidad/cuentas-bancarias/obtener-cuentas-bancarias', [CuentasBancariasController::class, 'obtenerCuentasBancarias']);
    Route::post('contabilidad/cuentas-bancarias/obtener-cuenta-bancaria', [CuentasBancariasController::class, 'obtenerCuentaBancaria']);
    Route::post('contabilidad/cuentas-bancarias/nueva', [CuentasBancariasController::class, 'nueva']);
    Route::post('contabilidad/cuentas-bancarias/registrar', [CuentasBancariasController::class, 'registrar']);
    Route::put('contabilidad/cuentas-bancarias/actualizar', [CuentasBancariasController::class, 'actualizar']);
    Route::put('contabilidad/cuentas-bancarias/desactivar', [CuentasBancariasController::class, 'desactivar']);
    Route::put('contabilidad/cuentas-bancarias/activar', [CuentasBancariasController::class, 'activar']);
    Route::get('contabilidad/cuentas-bancarias/reporte/{ext}', [ReportesCjaBncoController::class, 'generarReporteCuentasBancarias']);
//Fin Route contabilidad -> Cuentas Bancarias

//Route contabilidad -> Documentos Contables
    Route::post('contabilidad/documentos-contables/obtener', [DocumentosContablesController::class, 'obtener']);
    Route::get('contabilidad/documentos-contables/obtener-todos', [DocumentosContablesController::class, 'obtenerTodos']);
    Route::post('contabilidad/documentos-contables/obtener-documento', [DocumentosContablesController::class, 'obtenerDocumentoContable']);
    Route::post('contabilidad/documentos-contables/registrar', [DocumentosContablesController::class, 'registrar']);
    Route::put('contabilidad/documentos-contables/actualizar', [DocumentosContablesController::class, 'actualizar']);
    Route::post('contabilidad/documentos-contables/obtener-codigo-documento', [DocumentosContablesController::class, 'obtenerCodigoDocumento']);
    Route::post('contabilidad/documentos-contables/nuevo', [DocumentosContablesController::class, 'nuevo']);
    Route::post('contabilidad/documentos-contables/anular', [DocumentosContablesController::class, 'anular']);
    Route::get('contabilidad/documentos-contables/reporte/{ext}/{type}/{fecha_inicial}/{fecha_final}', [ReportesContabilidadController::class, 'generarReporteDocumentosContables']);
    Route::get('contabilidad/documentos-contables/reporte-especifico/{ext}/{id_documento}', [ReportesContabilidadController::class, 'generarReporteDocumentoContableEspecifico']);
//Fin Route contabilidad -> Documentos Contables

//Route contabilidad -> Estados Financieros
    Route::post('contabilidad/Estados-financieros/obtener-estados-financieros', [EstadosFinancierosController::class, 'obtenerEstadosFinacieros']);
    Route::get('contabilidad/Estados-financieros/obtener-estados-financieros-todas', [EstadosFinancierosController::class, 'obtenerTodosEstadosFinacieros']);
    Route::get('contabilidad/Estados-financieros/obtener-estado-financiero-contable', [EstadosFinancierosController::class, 'obtenerEstadoFinaciero']);
    Route::get('contabilidad/Estados-financieros/obtener-estados-financieros-lista', [EstadosFinancierosController::class, 'obtenerListaEstadosFinacieros']);
//Fin Route contabilidad -> Estados Financieros

//Route contabilidad -> Niveles Cuentas
    Route::post('contabilidad/niveles-cuentas/obtener-niveles-cuenta', [NivelesCuentasController::class, 'obtenerNivelesCuenta']);
    Route::get('contabilidad/niveles-cuentas/obtener-niveles-cuenta-todas', [NivelesCuentasController::class, 'obtenerTodosNivelesCuenta']);
    Route::post('contabilidad/niveles-cuentas/obtener-nivel-cuenta', [NivelesCuentasController::class, 'obtenerNivelCuenta']);
    Route::put('contabilidad/niveles-cuentas/actualizar', [NivelesCuentasController::class, 'actualizar']);
    Route::get('contabilidad/niveles-cuentas/reporte/{ext}', [ReportesContabilidadController::class, 'generarReporteNivelesCuentas']);
//Fin Route contabilidad -> Niveles Cuentas

//Route contabilidad -> Periodos Fiscales
    Route::post('contabilidad/periodos/obtener', [PeriodosFiscalesController::class, 'obtener']);
    Route::get('contabilidad/periodos/obtener-todos', [PeriodosFiscalesController::class, 'obtenerTodos']);
    Route::post('contabilidad/periodos/obtener-periodo', [PeriodosFiscalesController::class, 'obtenerPeriodo']);
    Route::post('contabilidad/periodos/registrar', [PeriodosFiscalesController::class, 'registrar']);
    Route::put('contabilidad/periodos/actualizar', [PeriodosFiscalesController::class, 'actualizar']);
    Route::put('contabilidad/periodos/cerrar-mes', [PeriodosFiscalesController::class, 'cerrarMes']);
//Fin Route contabilidad -> Periodos Fiscales

//Route contabilidad -> Reportes Financieros
    Route::post('contabilidad/estados-financieros/balanza', [ReportesFinancierosController::class, 'obtenerBalanzaComprobacion']);
    Route::post('contabilidad/estados-financieros/balanza/dependicias', [ReportesFinancierosController::class, 'obtenerDependenciasBalanzaComprobacion']);
    Route::post('contabilidad/estados-financieros/balanza/dependicias', [ReportesFinancierosController::class, 'obtenerDependenciasBalanzaComprobacion']);
    Route::post('contabilidad/estados-financieros/balanza-nueva', [ReportesFinancierosController::class, 'obtenerBalanzaComprobacionRta91']);
    Route::get('contabilidad/estados-financieros/balanza-comprobacion/reporte/{id_nivel_cuenta}/{fecha_inicial}/{fecha_final}/{currency}/{extension}', [ReportesFinancierosController::class, 'reporteBalanzaComprobacion']);
    Route::get('contabilidad/reporte/movimiento-por-cuenta/{extension}/{id_cuenta_contable}/{fecha_inicial}/{fecha_final}', [ReportesContabilidadController::class, 'generarReporteMovimientosPorCuenta']);
    Route::get('contabilidad/reporte/ingresocostorubro/{extension}/{fecha_inicial}/{fecha_final}', [ReportesContabilidadController::class, 'generarReporteIngresoCostoRubro']);
    Route::post('contabilidad/estados-financieros/balanza-comprobacion/reporte-anual', [ReportesFinancierosController::class, 'reporteBalanzaComprobacionAnual']);
    Route::post('contabilidad/estados-financieros/balance-general', [ReportesFinancierosController::class, 'obtenerBalanceGeneral']);
    Route::post('contabilidad/estados-financieros/estado-resultado', [ReportesFinancierosController::class, 'obtenerEstadoResultados']);
    Route::post('contabilidad/estados-financieros/balance-general/reporte', [ReportesFinancierosController::class, 'obtenerBalanceGeneralReporte']);

    Route::get('contabilidad/estados-financieros/estado-resultado/reporte/{id_periodo_fiscal}/{mes}/{currency}/{extension}/{periodo}', [ReportesContabilidadController::class, 'generarReporteEstadoResultado']);

    Route::get('contabilidad/estados-financieros/libro-diario/reporte/{ext}/{periodo}/{id_periodo}/{mes}', [ReportesContabilidadController::class, 'generarReporteLibroDiario']);
    Route::get('contabilidad/estados-financieros/libro-mayor/reporte/{ext}/{periodo}/{id_periodo}/{mes}', [ReportesContabilidadController::class, 'generarReporteLibroMayor']);
    Route::get('contabilidad/estados-financieros/cambio-patrimonio/reporte/{ext}/{id_period}/{mes}', [ReportesContabilidadController::class, 'generarReporteCambioPatrimonio']);
    Route::get('contabilidad/estados-financieros/balanza-anual/reporte/{ext}/{id_nivel_cuenta}/{id_periodo}/{mes}', [ReportesContabilidadController::class, 'generarReporteBalanzaComprobacion']);
    Route::post('contabilidad/estados-financieros/notas/reporte', [ReportesFinancierosController::class, 'reporteNotasBGER']);
    Route::post('contabilidad/estados-financieros/anexo-flujo/reporte', [ReportesFinancierosController::class, 'reporteAnexoFlujo']);
    Route::post('contabilidad/estados-financieros/flujo-efectivo/reporte', [ReportesFinancierosController::class, 'reporteFlujoEfectivo']);
    Route::post('contabilidad/estados-financieros/centro-costos/reporte', [ReportesFinancierosController::class, 'reporteMovimientosCentroCosto']);
    Route::get('contabilidad/estados-financieros/razones-financieras-comparativo/reporte/{ext}/{id_periodo_act}/{mes_act}/{id_periodo_ant}/{mes_ant}', [ReportesContabilidadController::class, 'generarReporteRazonesFinancieras']);

    Route::get('contabilidad/movimiento-con-saldos/{ext}/{id_bodega}/{f_inicial}/{f_final}', [ReportesContabilidadController::class,'generarReporteMovimientosConSaldos']);
//Fin Route contabilidad -> Reportes Financieros

// contabilidad contabilidad -> Tipos Cuentas
    Route::post('contabilidad/tipos-cuenta/obtener-tipos-cuenta', [TiposCuentasController::class, 'obtenerTiposCuenta']);
    Route::get('contabilidad/tipos-cuenta/obtener-tipos-cuenta-todas', [TiposCuentasController::class, 'obtenerTodosTiposCuenta']);
    Route::post('contabilidad/tipos-cuenta/obtener-tipo-cuenta-contable', [TiposCuentasController::class, 'obtenerTipoCuenta']);
    Route::put('contabilidad/tipos-cuenta/actualizar', [TiposCuentasController::class, 'actualizar']);
    Route::get('contabilidad/tipos-cuenta/reporte/{ext}', [ReportesContabilidadController::class, 'generarReporteTipoCuentas']);
//Fin contabilidad contabilidad -> Tipos Cuentas

// contabilidad contabilidad -> Tipos Documentos
    Route::post('contabilidad/tipos-documentos/obtener', [TiposDocumentosController::class, 'obtener']);
    Route::get('contabilidad/tipos-documentos/obtener-todos', [TiposDocumentosController::class, 'obtenerTodos']);
    Route::post('contabilidad/tipos-documentos/obtener-tipo-documento', [TiposDocumentosController::class, 'obtenerTipoDocumento']);
    Route::post('contabilidad/tipos-documentos/registrar', [TiposDocumentosController::class, 'registrar']);
    Route::put('contabilidad/tipos-documentos/actualizar', [TiposDocumentosController::class, 'actualizar']);
    Route::put('contabilidad/tipos-documentos/desactivar', [TiposDocumentosController::class, 'desactivar']);
    Route::put('contabilidad/tipos-documentos/activar', [TiposDocumentosController::class, 'activar']);
    Route::get('contabilidad/tipos-documentos/reporte/{ext}', [ReportesContabilidadController::class, 'generarReporteTipoDocumentos']);
// Fin contabilidad contabilidad -> Tipos Documentos

    //Rutas pantalla configuracion de CD contabilidad
    Route::post('contabilidad/obtener-configuracion', [ConfiguracionComprobantesController::class, 'obtener']);
    Route::put('contabilidad/actualizar-configuracion', [ConfiguracionComprobantesController::class, 'actualizar']);

    //Rutas Tasas de cambio
    Route::post('contabilidad/tasas-cambio/descargar', [TasasCambioController::class, 'descargarTasasCambio']);
    Route::post('contabilidad/tasas-cambio/dia', [TasasCambioController::class, 'obtenerTC']);
    Route::post('contabilidad/tasas-cambio/dia/paralela', [TasasCambioController::class,'obtenerTCParalela']);
    Route::post('contabilidad/tasas-cambio/obtener-tasas', [TasasCambioController::class, 'obtenerTasasCambio']);
    Route::post('contabilidad/tasas-cambio/obtener-tasas-reporte', [TasasCambioController::class, 'obtenerTasasReporte']);
    Route::get('contabilidad/tasas-cambio/reporte/{ext}/{periodo}/{mes}', [ReportesContabilidadController::class, 'generarReporteTasaCambio']);
    Route::get('contabilidad/comprobantes-descuadrados/reporte/{ext}/{periodo}/{mes}', [ReportesContabilidadController::class, 'generarReporteComprobantesDescuadrados']);
    Route::put('contabilidad/tasas-cambio/paralelas/actualizar', [TasasCambioController::class,'actualizarTCParalelas']);

    //Rutas Inventaio

    //Rutas bodegas
    Route::post('bodegas/obtener',[BodegasController::class,'obtener']);
    Route::get('bodegas/obtener-bodegas-todas',[BodegasController::class,'obtenerTodas']);
    Route::get('bodegas/obtener-bodegas-productos',[BodegasController::class,'obtenerBodegaProductos']);
    Route::get('bodegas/obtener-productos_venta',[BodegasController::class,'obtenerBodegaProductos']);
    Route::post('bodegas/obtener-bodegas-productos-garantia',[BodegasController::class,'obtenerBodegaProductosGarantia']);
    Route::post('bodegas/obtener-bodegas-productos-venta',[BodegasController::class,'obtenerBodegaProductosVenta']);
    Route::post('bodegas/obtener-bodegas-productos-recuperados',[BodegasController::class,'obtenerBodegaProductosRecuperados']);
    Route::post('bodegas/obtener-bodegas-productos-obsoleto',[BodegasController::class,'obtenerBodegaProductosObsoletos']);
    Route::post('bodegas/obtener-bodega',[BodegasController::class,'obtenerBodega']);
    Route::post('bodegas/registrar',[BodegasController::class,'registrar']);
    Route::put('bodegas/actualizar',[BodegasController::class,'actualizar']);
    Route::put('bodegas/activar',[BodegasController::class,'activar']);
    Route::put('bodegas/desactivar',[BodegasController::class,'desactivar']);
    Route::get('bodegas/buscar',[BodegasController::class,'buscar']);
    Route::get('bodegas/reporte/{ext}',[ReportesController::class,'generarReporteBodegas']);
    Route::get('bodegas/reporte-articulos-bodega/{ext}/{id_bodega}',[ReportesController::class,'generarReporteArticulosxBodega']);
    Route::post('bodegas/nuevo',[BodegasController::class,'nuevo']);
    //Rutas bodegas fin

    // Rutas proyectos
    Route::post('proyectos/obtener',[ProyectosController::class,'obtenerProyectos']);
    Route::post('proyectos/obtener-proyecto',[ProyectosController::class,'obtenerProyecto']);
    Route::get('proyectos/obtener-proyectos-todas',[ProyectosController::class,'obtenerTodosProyectos']);
    Route::post('proyectos/registrar',[ProyectosController::class,'registrar']);
    Route::put('proyectos/actualizar',[ProyectosController::class,'actualizar']);
    Route::put('proyectos/activar',[ProyectosController::class,'activar']);
    Route::put('proyectos/desactivar',[ProyectosController::class,'desactivar']);
    Route::post('proyectos/obtener-proyectos-cliente', [ProyectosController::class,'obtenerProyectosCliente']);
    // Fin rutas proyeectos

    // Rutas codigos de invitacion
    Route::post('invites/obtener',[InviteController::class,'obtener']);
    Route::post('invites/obtener-invites',[InviteController::class,'obtenerProyecto']);
    Route::get('invites/obtener-invites-todas',[InviteController::class,'obtenerTodosProyectos']);
    Route::post('invites/registrar',[InviteController::class,'store']);
    Route::put('invites/actualizar',[InviteController::class,'actualizar']);
    Route::put('invites/activar',[InviteController::class,'activar']);
    Route::put('invites/desactivar',[InviteController::class,'desactivar']);
    // Fin rutas codigos de invitación
    //Rutas categorias productos
    Route::post('categorias/obtener',[CategoriasController::class,'obtener']);
    Route::get('categorias/obtener-categorias-todas',[CategoriasController::class,'obtenerTodas']);
    Route::post('categorias/obtener-categoria',[CategoriasController::class,'obtenerCategoria']);
    Route::post('categorias/registrar',[CategoriasController::class,'registrar']);
    Route::put('categorias/actualizar',[CategoriasController::class,'actualizar']);
    Route::put('categorias/activar',[CategoriasController::class,'activar']);
    Route::put('categorias/desactivar',[CategoriasController::class,'desactivar']);
    Route::get('categorias/reporte/{ext}',[CategoriasController::class,'generarReporte']);
    //Rutas categorias fin

    //Rutas compras
    Route::post('compras/conteo-fisico',[CategoriasController::class,'obtner']);
    //Rutas Conteo Fisico FIn

    //Rutas Productos
    Route::post('productos/obtener-productos',[ProductosController::class,'obtenerProductos']);
    Route::post('productos/obtener-producto',[ProductosController::class,'obtenerProducto']);
    Route::post('productos/registrarPS',[ProductosController::class,'registrarPS']);
    Route::put('productos/actualizarPS',[ProductosController::class,'actualizarPS']);
    Route::post('productos/nuevo-producto-servicio',[ProductosController::class,'nuevoPS']);
    Route::get('productos/ps/reporte/{ext}',[ReportesController::class,'generarReporteProductos']);
    Route::get('productos/ps/reporte-articulos/{ext}',[ReportesController::class,'generarReporteGeneralArticulos']);
    Route::get('productos/ps/reporte-servicios/{ext}',[ReportesController::class,'generarReporteGeneralServicios']);
    Route::post('inventario/productos/buscar',[ProductosController::class,'buscarProductos']);
    Route::post('inventario/productos/buscarPS',[ProductosController::class,'buscarPS']);
    Route::put('producto/desactivar',[ProductosController::class,'desactivaProducto']);
    Route::put('producto/activar',[ProductosController::class,'activaProducto']);
    Route::put('productos/obtener-productos-bodega',[ProductosController::class,'obtenerProductosBodega']);
    Route::get('productos/obtener-productos-validos',[ProductosController::class,'obtenerProductosValidos']);
    Route::put('productos/obtener-codigo-producto',[ProductosController::class,'obtenerCodigoProducto']);
    Route::get('productos/buscar-bodega',[ProductosController::class,'buscarProductosBodega']);
    Route::get('productos/buscar-bodega-venta',[ProductosController::class,'buscarProductosBodegaVenta']);
    //Rutas Productos Fin

    //Rutas tipo productos
    Route::post('tipo-producto/obtener',[TiposProductosController::class,'obtener']);
    Route::get('tipo-producto/obtener-todas',[TiposProductosController::class,'obtenerTodas']);
    Route::post('tipo-producto/obtener-tipo',[TiposProductosController::class,'obtenerTipoProducto']);
    Route::post('tipo-producto/registrar', [TiposProductosController::class,'registrar']);
    Route::put('tipo-producto/actualizar', [TiposProductosController::class,'actualizar']);
    Route::put('tipo-producto/activar', [TiposProductosController::class,'activar']);
    Route::put('tipo-producto/desactivar', [TiposProductosController::class,'desactivar']);
    Route::get('tipo-producto/reporte/{ext}', [ReportesController::class,'generarReporteTipoProductos']);
    //Rutas tipo productos fin

    //Rutas Unidad de Medida
    Route::post('unidad-medida/obtener',[UnidadMedidaController::class,'obtener']);
    Route::get('unidad-medida/obtener-unidad-medidas-todas',[UnidadMedidaController::class,'obtenerTodos']);
    Route::post('unidad-medida/obtener-unidad-medida',[UnidadMedidaController::class,'obtenerUnidadMedida']);
    Route::post('unidad-medida/registrar',[UnidadMedidaController::class,'registrar']);
    Route::put('unidad-medida/actualizar',[UnidadMedidaController::class,'actualizar']);
    Route::put('unidad-medida/activar',[UnidadMedidaController::class,'activar']);
    Route::put('unidad-medida/desactivar',[UnidadMedidaController::class,'desactivar']);
    Route::get('unidad-medida/reporte/{ext}',[ReportesController::class,'generarReporteUnidadesMedida']);
    //Rutas Unidad de Medida fin

    //Rutas inventario inical
    Route::post('entradas/inventario-inicial/obtener-entrada', [InventarioInicialController::class,'obtenerEntradaInvInicial']);
    Route::post('entradas/inventario-inicial/obtener', [InventarioInicialController::class,'obtener']);
    Route::post('entradas/nuevo-inventario-inicial', [InventarioInicialController::class,'nuevo']);
    Route::put('entradas/inventario-inicial/recibir', [InventarioInicialController::class,'recibir']);
    Route::put('entradas/inventario-inicial/actualizar', [InventarioInicialController::class,'actualizar']);
    Route::put('entradas/inventario-inicial/registrar', [InventarioInicialController::class,'registrar']);
    Route::get('entradas/inventario-inicial/reporte/{ext}/{id_entrada_inicial}', [InventarioInicialController::class,'generarReporteInventarioInicial']);
    Route::post('entradas/nuevo-inventario-inicial-varios', [InventarioInicialController::class,'nuevoManual']);
    Route::post('entradas/inventario-inicial-manual/registrar', [InventarioInicialController::class,'registrarInvManual']);
    Route::put('entradas/inventario-inicial/actualizar-manual', [InventarioInicialController::class,'actualizarManual']);

    //Rutas control de entradas
    // Rutas Entradas
    Route::post('entradas/obtener', [EntradasController::class,'obtener']);
    Route::post('entradas/obtener-entrada', [EntradasController::class,'obtenerEntrada']);
    Route::post('entradas/obtener-proveedores', [EntradasController::class,'obtenerProveedores']);
    //Route::get('entradas/obtener-entrada-por-codigo', 'InventarioEntradasController@obtenerEntradaPorCodigo');
    Route::post('entradas/registrar', [EntradasController::class,'registrar']);
    Route::post('entradas/nuevo', [EntradasController::class,'nuevo']);
    Route::post('entradas/autosave-entrada', [EntradasController::class,'autosaveEntradaProducto']);
    Route::put('entradas/actualizar', [EntradasController::class,'actualizar']);
    Route::put('entradas/recibir', [EntradasController::class,'recibir']);
    Route::put('entradas/recibir-compra', [EntradasController::class,'recibirCompra']);
    Route::get('entradas/reporte/{ext}/{id_entrada}', [EntradasController::class,'reporteEntrada']);
    Route::get('entradas/reporte-entrada-bodega/{ext}/{id_bodega}/{f_inicial}/{f_final}', [ReportesController::class,'generarReporteEntradaxBodega']);

    //Rutas Inventaio Fin

    //Rutas Tipos de bodega
    Route::post('inventario/tipos-bodegas/obtener', [TipoBodegaController::class,'obtener']);
    Route::get('inventario/tipos-bodegas/obtener-todos', [TipoBodegaController::class,'obtenerTodos']);
    Route::post('inventario/tipos-bodegas/obtener-tipo-bodega', [TipoBodegaController::class,'obtenerTipoBodega']);
    Route::post('inventario/tipos-bodegas/registrar', [TipoBodegaController::class,'registrar']);
    Route::put('inventario/tipos-bodegas/actualizar', [TipoBodegaController::class,'actualizar']);
    Route::put('inventario/tipos-bodegas/activar', [TipoBodegaController::class,'activar']);
    Route::put('inventario/tipos-bodegas/desactivar', [TipoBodegaController::class,'desactivar']);
    Route::get('inventario/tipos-bodegas/reporte/{ext}', [ReportesController::class,'generarReporteTipoBodegas']);
    //Rutas Tipos de bodega Fin

    //Rutas Tipos de Entradas
    Route::post('inventario/tipos-entradas/obtener', [TipoEntradaController::class,'obtener']);
    Route::get('inventario/tipos-entradas/obtener-tipos-entradas-todos', [TipoEntradaController::class,'obtenerTodosTiposEntrada']);
    Route::post('inventario/tipos-entradas/obtener-tipo-entrada', [TipoEntradaController::class,'obtenerTipoEntrada']);
    Route::post('inventario/tipos-entradas/registrar', [TipoEntradaController::class,'registrar']);
    Route::put('inventario/tipos-entradas/actualizar', [TipoEntradaController::class,'actualizar']);
    Route::put('inventario/tipos-entradas/activar', [TipoEntradaController::class,'activar']);
    Route::put('inventario/tipos-entradas/desactivar', [TipoEntradaController::class,'desactivar']);
    Route::get('inventario/tipos-entradas/reporte/{ext}', [ReportesController::class,'generarReporteTipoEntrada']);
    //Rutas Tipos de Entradas Fin

    //Rutas Tipos de Salidas
    Route::post('inventario/tipos-salidas/obtener', [TipoSalidaController::class,'obtener']);
    Route::get('inventario/tipos-salidas/obtener-todos-tipos-salidas', [TipoSalidaController::class,'obtenerTodosTiposSalida']);
    Route::post('inventario/tipos-salidas/obtener-tipo-salida', [TipoSalidaController::class,'obtenerTipoSalida']);
    Route::post('inventario/tipos-salidas/registrar', [TipoSalidaController::class,'registrar']);
    Route::put('inventario/tipos-salidas/actualizar', [TipoSalidaController::class,'actualizar']);
    Route::put('inventario/tipos-salidas/activar', [TipoSalidaController::class,'activar']);
    Route::put('inventario/tipos-salidas/desactivar', [TipoSalidaController::class,'desactivar']);
    Route::get('inventario/tipos-salidas/reporte/{ext}', [ReportesController::class,'generarReporteTipoSalida']);
    Route::get('inventario/tipos-salidas-bodegas/reporte/{ext}/{id_bodega}/{f_inicial}/{f_final}', [ReportesController::class,'generarReporteSalidaxBodega']);
    //Rutas Tipos de Salidas Fin

    //Rutas Tipos de proveedores
    Route::post('inventario/tipo-proveedor/obtener', [TipoProveedorController::class,'obtener']);
    Route::get('inventario/tipo-proveedor/obtener-todos', [TipoProveedorController::class,'obtenerTodos']);
    Route::post('inventario/tipo-proveedor/obtener-tipo-proveedor', [TipoProveedorController::class,'obtenerTipoProveedor']);
    Route::post('inventario/tipo-proveedor/registrar', [TipoProveedorController::class,'registrar']);
    Route::put('inventario/tipo-proveedor/actualizar', [TipoProveedorController::class,'actualizar']);
    Route::put('inventario/tipo-proveedor/activar', [TipoProveedorController::class,'activar']);
    Route::put('inventario/tipo-proveedor/desactivar', [TipoProveedorController::class,'desactivar']);
    Route::get('inventario/tipo-proveedor/reporte/{ext}', [ReportesController::class,'generarReporteTipoProveedores']);
    //Rutas Tipos de proveedores Fin

    //Rutas Proveedores
    Route::post('inventario/proveedores/obtener', [ProveedoresControllers::class,'obtener']);
    Route::get('inventario/proveedores/obtener-proveedores-todos', [ProveedoresControllers::class,'obtenerTodos']);
    Route::get('inventario/proveedores/obtener-proveedores-producto', [ProveedoresControllers::class,'obtenerProveedoresProducto']);
    Route::post('inventario/proveedores/obtener-proveedor', [ProveedoresControllers::class,'obtenerProveedor']);
    Route::post('inventario/proveedores/registrar', [ProveedoresControllers::class,'registrar']);
    Route::put('inventario/proveedores/actualizar', [ProveedoresControllers::class,'actualizar']);
    Route::put('inventario/proveedores/activar', [ProveedoresControllers::class,'activar']);
    Route::put('inventario/proveedores/desactivar', [ProveedoresControllers::class,'desactivar']);
    Route::get('inventario/proveedores/buscar', [ProveedoresControllers::class,'buscar']);
    Route::get('inventario/proveedores/reporte/{ext}', [ReportesController::class,'generarReporteProveedores']);
    //Rutas Proveedores Fin

    //Rutas Marcas
    Route::post('inventario/marcas/obtener', [MarcasController::class,'obtener']);
    Route::get('inventario/marcas/obtener-todos', [MarcasController::class,'obtenerTodasMarcas']);
    Route::post('inventario/marcas/obtener-marca', [MarcasController::class,'obtenerMarca']);
    Route::post('inventario/marcas/registrar', [MarcasController::class,'registrar']);
    Route::put('inventario/marcas/actualizar', [MarcasController::class,'actualizar']);
    Route::put('inventario/marcas/activar', [MarcasController::class,'activar']);
    Route::put('inventario/marcas/desactivar', [MarcasController::class,'desactivar']);
    Route::get('inventario/marcas/reporte/{ext}', [ReportesController::class,'generarReporteMarcas']);
    Route::get('inventario/marcas/reporte-exitencia/{ext}/{id_bodega}/{id_marca}', [ReportesController::class,'generarReporteExistenciaxMarca']);
    //Rutas Marcas Fin

    //Rutas Configuracion comprabantes inventario
    Route::post('inventario/obtener-configuracion', [ConfiguracionInventarioController::class,'obtener']);
    Route::put('inventario/actualizar-configuracion', [ConfiguracionInventarioController::class,'actualizar']);
    //Rutas Configuracion comprabantes inventario fin

    // Rutas Salidas
    Route::post('salidas/obtener', [SalidasController::class,'obtener']);
    Route::post('salidas/obtener-salida', [SalidasController::class,'obtenerSalida']);
    Route::post('salidas/nueva', [SalidasController::class,'nueva']);
    Route::post('salidas/registrar', [SalidasController::class,'registrar']);
    Route::post('salidas/guardar', [SalidasController::class,'guardarSalida']);
    Route::post('salidas/registrar-manual', [SalidasController::class,'registrarSalidaManual']);
    Route::put('salidas/anular', [SalidasController::class,'anular']);
    Route::post('salidas/registrar-traslado', [SalidasController::class,'registrarTraslado']);
    Route::post('salidas/registrar-devolucion', [SalidasController::class,'registrarDevolucion']);
    Route::get('salidas/reporte/{ext}/{id_salida}', [SalidasController::class,'reporteSalida']);

    Route::put('salidas/despachar', [SalidasController::class,'despachar']);

    Route::post('salidas/crear-salida-devolucion', [SalidasController::class,'crearSalidaPorDevolucion']);
    Route::get('salidas/obtener-numero-salida', [SalidasController::class,'obtenerNumeroSalida']);
    Route::post('salidas/obtener-salida-por-codigo', [SalidasController::class,'obtenerSalidaPorCodigo']);

    Route::post('salidas/reporte', 'InventarioSalidasController@reporte');
    Route::post('entradas/reporte', 'InventarioEntradasController@reporte');

            //Rutas Facturación
    //Rutas Vendedores
    Route::post('ventas/vendedores/obtener', [VendedoresController::class,'obtener']);
    Route::post('ventas/vendedores/obtener-vendedor', [VendedoresController::class,'obtenerVendedor']);
    Route::get('ventas/vendedores/buscar', [VendedoresController::class,'buscar']);
    Route::post('ventas/vendedores/registrar', [VendedoresController::class,'registrar']);
    Route::put('ventas/vendedores/actualizar', [VendedoresController::class,'actualizar']);
    Route::put('ventas/vendedores/activar', [VendedoresController::class,'activar']);
    Route::put('ventas/vendedores/desactivar', [VendedoresController::class,'desactivar']);
    Route::get('ventas/vendedores/reporte/{ext}', [ReportesCjaBncoController::class,'generarReporteVendedores']);
    //Rutas Vendedores Fin

    //Rutas tipos clientes
    Route::post('ventas/tipo-cliente/obtener', [TipoClienteController::class,'obtener']);
    Route::get('ventas/tipo-cliente/obtener-todos', [TipoClienteController::class,'obtenerTodos']);
    Route::post('ventas/tipo-cliente/obtener-tipo-cliente', [TipoClienteController::class,'obtenerTipoCliente']);
    Route::post('ventas/tipo-cliente/registrar', [TipoClienteController::class,'registrar']);
    Route::put('ventas/tipo-cliente/actualizar', [TipoClienteController::class,'actualizar']);
    Route::put('ventas/tipo-cliente/activar', [TipoClienteController::class,'activar']);
    Route::put('ventas/tipo-cliente/desactivar', [TipoClienteController::class,'desactivar']);
    Route::get('ventas/tipo-cliente/reporte/{ext}', [ReportesCjaBncoController::class,'generarReporteTipoClientes']);
    //Rutas tipos clientes FIn

    //Rutas Bancos
    Route::post('cajabanco/bancos/obtener-bancos', [BancosController::class,'obtenerBancos']);
    Route::get('cajabanco/bancos/obtener-bancos-todos', [BancosController::class,'obtenerTodosBancos']);
    Route::post('cajabanco/bancos/obtener-banco', [BancosController::class,'obtenerBanco']);
    Route::post('cajabanco/bancos/registrar', [BancosController::class,'registrar']);
    Route::put('cajabanco/bancos/actualizar', [BancosController::class,'actualizar']);
    Route::put('cajabanco/bancos/activar', [BancosController::class,'activar']);
    Route::put('cajabanco/bancos/desactivar', [BancosController::class,'desactivar']);
    Route::get('cajabanco/bancos/reporte/{ext}', [ReportesCjaBncoController::class,'generarReporteBancos']);
    //Rutas Bancos Fin


    //Rutas kardex

    Route::post('inventario/kardex/obtener-por-producto', [MovimientosProductosController::class,'obtenerMovimientosPorProducto']);
    Route::get('inventario/kardex-consolidado/{ext}/{id_bodega}/{f_inicial}/{f_final}', [ReportesController::class,'generarReporteKardexConsolidado']);
    Route::get('inventario/kardex-consolidado-fecha/{ext}/{id_bodega}/{id_producto}/{f_inicial}/{f_final}', [ReportesController::class,'generarReporteKardexConsolidadoFecha']);
    Route::get('inventario/kardex-movimiento-productos/{ext}/{id_bodega}/{id_producto}', [ReportesController::class,'generarReporteKardexMovimientoProductos']);

    // Rutas Inventario Fisico
    Route::post('inventario/conteo-fisico/obtener', [InventarioFisicoController::class,'obtener']);
    Route::get('inventario/conteo-fisico/nuevo', [InventarioFisicoController::class,'nuevo']);
    Route::post('inventario/conteo-fisico/obtener-conteo', [InventarioFisicoController::class,'obtenerConteo']);
    Route::post('inventario/conteo-fisico/registrar', [InventarioFisicoController::class,'registrar']);
    Route::put('inventario/conteo-fisico/actualizar', [InventarioFisicoController::class,'actualizar']);
    Route::get('inventario/conteo-fisico/reporte/{ext}/{id_inventario_fisico}', [InventarioFisicoController::class,'reporte']);
    Route::get('inventario/conteo-fisico/reporte-comparativo/{ext}/{id_inventario_fisico}', [InventarioFisicoController::class,'reporteComparativo']);

    //Rutas clientes
    Route::post('cuentas-por-cobrar/clientes/obtener', [ClientesController::class,'obtener']);
    Route::get('cuentas-por-cobrar/clientes/obtener-todos', [ClientesController::class,'obtenerTodos']);
    Route::post('cuentas-por-cobrar/clientes/obtener-cliente', [ClientesController::class,'obtenerCliente']);
    Route::post('cuentas-por-cobrar/clientes/registrar', [ClientesController::class,'registrar']);
    Route::post('cuentas-por-cobrar/clientes/registrar-basico', [ClientesController::class,'registrarBasico']);
    Route::post('cuentas-por-cobrar/clientes/nuevo', [ClientesController::class,'nuevo']);
    Route::put('cuentas-por-cobrar/clientes/actualizar', [ClientesController::class,'actualizar']);
    Route::put('cuentas-por-cobrar/clientes/desactivar', [ClientesController::class,'desactivar']);
    Route::put('cuentas-por-cobrar/clientes/activar', [ClientesController::class,'activar']);
    Route::get('cuentas-por-cobrar/clientes/buscar', [ClientesController::class,'buscar']);
    Route::get('cuentas-por-cobrar/clientes/{ext}', [ReportesCjaBncoController::class,'generarReporteclientes']);

    // Rutas Facturas
    Route::post('inventario/facturas/obtener', [FacturasController::class,'obtener']);
    Route::post('inventario/facturas/obtener-factura', [FacturasController::class,'obtenerFactura']);
    Route::post('inventario/facturas/obtener-consecutivo', [FacturasController::class,'obtenerConsecutivo']);
    Route::post('inventario/facturas/registrar', [FacturasController::class,'registrar']);


    Route::post('inventario/facturas/reporte/clientes', 'CajaBancoReportesController@generarReporteVentasClienteDetallado');
    Route::post('inventario/facturas/reporte/sucursales', 'CajaBancoReportesController@generarReporteVentasSucursalDetallado');

    Route::post('inventario/facturas/reporte/comisiones', 'CajaBancoReportesController@generarReporteComisiones');

    Route::post('inventario/facturas/cancelar', [FacturasController::class,'anular']);
    Route::post('inventario/facturas/nueva', [FacturasController::class,'nueva']);
    Route::post('inventario/facturas/obtener-facturas-cliente', [FacturasController::class,'obtenerFacturasCliente']);
    Route::get('inventario/facturas/reporte/{ext}/{id_factura}', [FacturasController::class,'reporte']);

    Route::post('inventario/facturas/obtener-configuracion', [FacturasConfiguracionController::class,'obtener']);
    Route::put('inventario/facturas/actualizar-configuracion', [FacturasConfiguracionController::class,'actualizar']);

    // Rutas Cuentas Cobrar
    Route::post('cuentas-cobrar/obtener', [CuentasXCobrarController::class,'obtener']);
    Route::post('cuentas-cobrar/obtener-cc', [CuentasXCobrarController::class,'obtenerCuentasXCobrar']);
    Route::post('cuentas-cobrar/obtener-cuentas-cliente', [CuentasXCobrarController::class,'obtenerCuentasCliente']);
    Route::post('cuentas-cobrar/obtener-cuentas-trabajador', [CuentasXCobrarController::class,'obtenerCuentasTrabajador']);

    Route::post('cuentas-cobrar/Reportes/antiguedad', 'CuentasXCobrarCuentasXCobrarController@generarReporteAntiguedad');
    Route::post('cuentas-cobrar/Reportes/estado-cuenta-detallado', 'CuentasXCobrarCuentasXCobrarController@generarReporteEstadoCuentadetallado');
    Route::post('cuentas-cobrar/Reportes/estado-cuenta-consolidado', 'CuentasXCobrarCuentasXCobrarController@generarReporteEstadoCuentaConsolidado');

    Route::post('cuentas-cobrar/Reportes/estado-cuenta-detallado-trabajador', 'CuentasXCobrarCuentasXCobrarController@generarReporteEstadoCuentaDetalladoEmpleado');
    Route::post('cuentas-cobrar/Reportes/estado-cuenta-consolidado-trabajador', 'CuentasXCobrarCuentasXCobrarController@generarReporteEstadoCuentaConsolidadoEmpleado');

    Route::post('cuentas-cobrar/Reportes/estado-cuenta-detallado-occ', 'CuentasXCobrarCuentasXCobrarController@generarReporteEstadoCuentadetalladoOCC');
    Route::post('cuentas-cobrar/Reportes/estado-cuenta-consolidado-occ', 'CuentasXCobrarCuentasXCobrarController@generarReporteEstadoCuentaConsolidadoOCC');

    Route::post('cuentas-pagar/Reportes/antiguedad', 'CuentasXPagarCuentasXPagarController@generarReporteAntiguedad');
    Route::post('cuentas-pagar/Reportes/estado-cuenta-detallado', 'CuentasXPagarCuentasXPagarController@generarReporteEstadoCuentadetallado');
    Route::post('cuentas-pagar/Reportes/estado-cuenta-consolidado', 'CuentasXPagarCuentasXPagarController@generarReporteEstadoCuentaConsolidado');

    Route::get('cuentas-cobrar/Reportes/recibos/{ext}/{id_recibo}', [RecibosController::class,'reporteRecibos']);

    Route::post('cuentas-cobrar/cuentasxcobrar/importar', [CuentasXCobrarController::class,'importar_datos']);
    Route::post('cuentas-cobrar/cuentasxcobrar/registrar-importacion', 'CuentasXCobrarCuentasXCobrarController@registrarImportacioncuentasPorCobrar');

    // Rutas Proformas
    Route::post('cajabanco/proformas/obtener', [ProformasController::class,'obtener']);
    Route::post('cajabanco/proformas/obtener-factura', [ProformasController::class,'obtenerProforma']);
    Route::post('cajabanco/proformas/obtener-detalle-proforma', [ProformasController::class,'obtenerDetalleProforma']);
    Route::post('cajabanco/proformas/registrar', [ProformasController::class,'registrar']);
    Route::put('cajabanco/proformas/actualizar', [ProformasController::class,'actualizar']);
    Route::put('cajabanco/proformas/archivar', [ProformasController::class,'archivar']);
    Route::put('cajabanco/proformas/anular', [ProformasController::class,'anular']);
    Route::get('cajabanco/proformas/reporte/{ext}/{id_proforma}', [ProformasController::class,'reporte']);
    Route::post('cajabanco/proformas/nueva', [ProformasController::class,'nueva']);
    Route::get('cajabanco/proformas/buscar', [ProformasController::class,'buscar']);

    //Rutas proformmas seguimiento
    Route::post('cajabanco/proformas-seguimiento/obtener', [ProformaSeguimientoController::class,'obtener']);
    Route::post('cajabanco/proformas-seguimiento/obtener-seguimiento', [ProformaSeguimientoController::class,'obtenerSeguimiento']);
    Route::post('cajabanco/proformas-seguimiento/registrar', [ProformaSeguimientoController::class,'registrar']);
    Route::get('cajabanco/proformas-seguimiento/reporte/{ext}/{id_factura}', 'CajaBancoProformaSeguimientoController@reporte');
    Route::post('cajabanco/proformas-seguimiento/nueva', [ProformaSeguimientoController::class,'nueva']);

    // Rutas Recibos oficiales caja
    Route::post('cuentas-cobrar/roc/obtener', [RecibosController::class,'obtener']);
    Route::post('cuentas-cobrar/roc/obtener-recibo', [RecibosController::class,'obtenerRecibo']);
    Route::post('cuentas-cobrar/roc/registrar', [RecibosController::class,'registrar']);
    Route::post('cuentas-cobrar/roc/empleado/registrar', [RecibosController::class,'registrarROCTrabajador']);
    Route::post('cuentas-cobrar/roc/nuevo', [RecibosController::class,'nuevo']);
    Route::post('cuentas-cobrar/roc/obtener-recibos-cliente', [RecibosController::class,'obtenerRecibosCliente']);
    Route::post('cuentas-cobrar/roc/cancelar', [RecibosController::class,'anular']);

});


// Rutas fortify

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
    $enableViews = config('fortify.views', true);

    // Authentication...
/*    if ($enableViews) {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('login');
    }*/

    $limiter = config('fortify.limiters.login');
    $twoFactorLimiter = config('fortify.limiters.two-factor');
    $verificationLimiter = config('fortify.limiters.verification', '6,1');

/*    Route::post('/login', [AuthController::class, 'login'])
        ->middleware(array_filter([
            'guest:' . config('fortify.guard'),
            $limiter ? 'throttle:' . $limiter : null,
        ]));*/

/*    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');*/

    // Password Reset...
    if (Features::enabled(Features::resetPasswords())) {
        if ($enableViews) {
            Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('password.request');

            Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('password.reset');
        }

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.email');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware(['guest:' . config('fortify.guard')])
            ->name('password.update');
    }

    // Email Verification...
    if (Features::enabled(Features::emailVerification())) {
        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'signed', 'throttle:' . $verificationLimiter])
            ->name('verification.verify');

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'throttle:' . $verificationLimiter])
            ->name('verification.send');
    }

    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
            ->name('user-profile-information.update');
    }

    // Passwords...
    if (Features::enabled(Features::updatePasswords())) {
        Route::put('/user/password', [PasswordController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
            ->name('user-password.update');
    }

    // Password Confirmation...
    /*if ($enableViews) {
        Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
            ->name('password.confirm');
    }

    Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])
        ->name('password.confirmation');

    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')]);*/

    // Two Factor Authentication...
    if (Features::enabled(Features::twoFactorAuthentication())) {
        if ($enableViews) {
            Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
                ->middleware(['guest:' . config('fortify.guard')])
                ->name('two-factor.login');
        }

        Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
            ->middleware(array_filter([
                'guest:' . config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
            ]));

        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'password.confirm']
            : [config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')];

        Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.enable');

        Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.disable');

        Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.qr-code');

        Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.recovery-codes');

        Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
            ->middleware($twoFactorMiddleware);
    }
});





