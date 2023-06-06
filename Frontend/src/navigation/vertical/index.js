export default [
  {
    title: 'Inicio',
    route: 'home',
    icon: 'HomeIcon',
  },
  {
    title: 'Contabilidad',
    route: 'contabilidad',
    icon: 'FileMinusIcon',
    children: [
      {
        title: 'Documentos Contables',
        route: 'documentos-contables',
        icon: 'FileTextIcon',
      },
      {
        title: 'Catálogos Contables',
        route: 'contabilidad-catalogos-contables',
        icon: 'BookOpenIcon',
      },
      {
        title: 'Periodos Contables',
        route: 'contabilidad-periodos-contables',
        icon: 'CalendarIcon',
      },
      {
        title: 'Balanza Comprobación',
        route: 'contabilidad-balanza-comprobacion-dol',
        icon: 'BookOpenIcon',
      },
      // {
      //   title: 'Balanza Comprobación Nueva',
      //   route: 'contabilidad-balanza-comprobacion-nueva',
      //   icon: 'BookOpenIcon',
      // },
      {
        title: 'Balance General',
        route: 'contabilidad-balance-general-dol',
        icon: 'BookOpenIcon',
      },
      {
        title: 'Estado Resultados',
        route: 'contabilidad-estado-resultado-dol',
        icon: 'BookOpenIcon',
      },
      {
        title: 'Reportes',
        route: 'contabilidad-Reportes',
        icon: 'BookOpenIcon',
      },
      {
        title: 'Catálogos',
        route: 'contabilidad-catalogos',
        icon: 'BookOpenIcon',
        children: [
          {
            title: 'Tipos Cuentas',
            route: 'contabilidad-tipos-cuenta',
          },
          {
            title: 'Niveles Cuentas',
            route: 'contabilidad-niveles-cuentas',
          },
          {
            title: 'Tipos Documentos',
            route: 'contabilidad-tipos-documentos',
          },
          {
            title: 'Configuración de CD de contabilidad',
            route: 'contabilidad-configuracion-cd',
          },
          /*{
            title: 'Centros de costos',
            route: 'contabilidad-centros-costos',
          },*/
          {
            title: 'Tasas de cambio',
            route: 'tasas-cambio',
          },
          {
            title: 'Tasas de cambio paralelas',
            route: 'tasas-cambio-paralela',
          }
        ]
      }
    ]
  },
  {
    title: 'Inventario',
    route: 'inventario',
    icon: 'BoxIcon',
    children: [
      {
        title : 'Articulos y Servicios',
        route: 'inventario-productos',
        icon: 'BoxIcon'
      },
      {
        title : 'Entrada Inicial',
        route: 'inventario-entrada-inicial',
        icon: 'BoxIcon'
      },
      {
        title : 'Control de entradas',
        route: 'inventario-entradas',
        icon: 'BoxIcon'
      },
      {
        title : 'Control de Salidas',
        route: 'inventario-salidas',
        icon: 'BoxIcon'
      },
      {
        title : 'Conteo de Inventario Fisico',
        route: 'inventario-conteo-fisico',
        icon: 'BoxIcon'
      },
      {
        title : 'Kardex',
        route: 'inventario-kardex',
        icon: 'BoxIcon'
      },
      {
        title: 'Reportes',
        route: 'inventario-Reportes',
        icon: 'BoxIcon'
      },
      {
        title : 'Catálogos',
        route: 'inventario-catalogos',
        icon: 'BoxIcon',
        children : [
          {
            title : 'Bodegas',
            route : 'inventario-bodegas',
            icon : 'BoxIcon'
          },
          /*{
            title : 'Categorías productos',
            route : 'inventario-categorias-productos',
            icon : 'BoxIcon'
          },*/
          {
            title : 'Tipos Entradas',
            route : 'inventario-tipos-entradas',
            icon : 'BoxIcon'
          },
          {
            title : 'Unidades de medida',
            route : 'inventario-unidades-medida',
            icon : 'BoxIcon'
          },
          {
            title : 'Tipos Salidas',
            route : 'inventario-tipos-salidas',
            icon : 'BoxIcon'
          },
          {
            title : 'Marcas',
            route : 'inventario-marcas',
            icon : 'BoxIcon'
          },
          {
            title : 'Tipos Bodegas',
            route : 'inventario-tipos-bodegas',
            icon : 'BoxIcon'
          },
          {
            title : 'Configuración CD de Inventario',
            route : 'inventario-configuracion-inventario',
            icon : 'BoxIcon'
          },
          {
            title : 'Tipo Proveedores',
            route : 'inventario-tipos-proveedores',
            icon : 'BoxIcon'
          },
          {
            title : 'Proveedores',
            route : 'inventario-proveedores',
            icon : 'BoxIcon'
          },
          {
            title : 'Tipo Producto',
            route : 'inventario-tipos-productos',
            icon : 'BoxIcon'
          },
        ]
      },
    ]
  },
  {
    title: 'Facturación',
    route: 'facturacion',
    icon: 'BookOpenIcon',
    children:[
      {
        title: 'Facturas',
        route: 'cajabanco-facturas',
        icon: 'UserIcon',
      },
      {
        title: 'Proformas',
        route: 'cajabanco-proformas',
        icon: 'UserIcon',
      },
      {
        title: 'Clientes',
        route: 'ventas-clientes',
        icon: 'UserIcon',
      },
      {
        title: 'Vendedores',
        route: 'ventas-vendedores',
        icon: 'UserIcon',
      },
      {
        title: 'Cuentas por cobrar',
        route: 'cxc-cuentas-por-cobrar',
        icon: 'UserIcon',
      },
      {
        title: 'Recibos',
        route: 'recibos',
        icon: 'UserIcon',
      },
      {
        title: 'Cuentas Bancarias',
        route: 'contabilidad-cuentas-bancarias',
        icon: 'DollarSignIcon',
      },
      {
        title: 'Reportes',
        route: 'cajabanco-Reportes',
        icon: 'BookOpenIcon'
      },
      {
        title: 'Catálogos',
        icon: 'ShoppingBagIcon',
        children:[
          {
            title: 'Ajustes CD',
            route: 'cajabanco-ajustes-cd'
          },
          {
            title: 'Tipos Clientes',
            route: 'ventas-tipos-clientes',
          },
          {
            title: 'Bancos',
            route: 'cajabanco-bancos',
          },
          {
            title: 'Proyectos',
            route: 'cajabanco-proyectos',
          },
        ]
      },
    ]
  },
  {
    title: 'Administración del sistema',
    route: 'admon-sistema',
    icon: 'SettingsIcon',
    children:[
      {
        title: 'Permisos',
        route: 'admon-permisos',
        icon: 'ShieldIcon',
      },
      {
        title: 'Accesos',
        route: 'bitacora-accesos',
        icon: 'AlertTriangleIcon',
      },
      {
        title: 'Usuarios',
        route: 'admon-usuarios',
        icon: 'UserIcon',
      },
      {
        title: 'Ajustes generales',
        route: 'admon-ajustes',
        icon: 'SettingsIcon',
      },
      {
        title: 'Códigos de autorización',
        route: 'admon-invites',
        icon: 'SettingsIcon',
      },
      {
        title: 'Catalogos',
        icon: 'ShoppingBagIcon',
        children:[
          {
            title: 'Roles',
            route: 'admon-roles',
            icon: 'AwardIcon',
          },
          {
            title: 'Menus',
            route: 'admon-menus',
            icon: 'MenuIcon',
          },
          {
            title: 'Países',
            route: 'admon-paises',
            icon: 'MapPinIcon',
          },
          {
            title: 'Departamentos',
            route: 'admon-departamentos',
            icon: 'DiscIcon',
          },
          {
            title: 'Municipios',
            route: 'admon-municipios',
            icon: 'MapPinIcon',
          },
          {
            title: 'Sectores',
            route: 'admon-sectores',
            icon: 'MapPinIcon',
          },
          {
            title: 'Sucursales',
            route: 'admon-sucursales',
            icon: 'AwardIcon',
          },
          {
            title: 'Impuestos',
            route: 'admon-impuestos',
            icon: 'AwardIcon',
          },
          {
            title: 'Zonas',
            route: 'admon-zonas',
            icon: 'AwardIcon',
          },
        ]
      }
    ]
  },

]
