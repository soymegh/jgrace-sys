export default [
//Articulos y servicios
    {
        path: '/inventario/productos',
        name: 'inventario-productos',
        component: () => import( /* webpackChunkName: "inventario-productos" */  '@/views/Inventario/Productos/Listado'),
        meta: {
            id_menu : 77,
            pageTitle: 'Artículos y servicios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/productos/registrar',
        name: 'inventario-productos-registrar',
        component: () => import( /* webpackChunkName: "inventario-productos" */  '@/views/Inventario/Productos/Registrar'),
        meta: {
            id_menu : 78,
            pageTitle: 'Artículos y servicios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/productos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/productos/actualizar/:id_producto',
        name: 'inventario-productos-actualizar',
        component: () => import( /* webpackChunkName: "inventario-productos" */  '@/views/Inventario/Productos/Actualizar'),
        meta: {
            id_menu : 79,
            pageTitle: 'Artículos y servicios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/productos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Entradas iniciales
    {
        path: '/inventario/entrada-inicial',
        name: 'inventario-entrada-inicial',
        component: () => import( /* webpackChunkName: "inventario-entrada-inicial" */  '@/views/Inventario/EntradaInicial/Listado'),
        meta: {
            id_menu : 80,
            pageTitle: 'Entrada inicial',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/entrada-inicial/registrar',
        name: 'inventario-entrada-inicial-registrar',
        component: () => import( /* webpackChunkName: "inventario-entrada-inicial" */  '@/views/Inventario/EntradaInicial/Registrar'),
        meta: {
            id_menu : 81,
            pageTitle: 'Entrada inicial',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to: '/inventario/entrada-inicial',
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/entrada-inicial/actualizar/:id_entrada_inicial',
        name: 'inventario-entrada-inicial-actualizar',
        component: () => import( /* webpackChunkName: "inventario-entrada-inicial" */  '@/views/Inventario/EntradaInicial/Actualizar'),
        meta: {
            id_menu : 82,
            pageTitle: 'Entrada inicial',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    to: '/inventario/entrada-inicial',
                    active: false,
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/entrada-inicial/mostrar/:id_entrada_inicial',
        name: 'inventario-entrada-inicial-mostrar',
        component: () => import( /* webpackChunkName: "inventario-entrada-inicial" */  '@/views/Inventario/EntradaInicial/Mostrar'),
        meta: {
            id_menu : 83,
            pageTitle: 'Entrada inicial',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    to: '/inventario/entrada-inicial',
                    active: false,
                },
                {
                    text: 'Vista previa',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/bodegas',
        name: 'inventario-bodegas',
        component: () => import( /* webpackChunkName: "inventario-bodegas" */  '@/views/Inventario/Bodegas/Listado'),
        meta: {
            id_menu : 84,
            pageTitle: 'Listado Bodegas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado  Bodegas',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/bodegas/registrar',
        name: 'inventario-bodegas-registrar',
        component: () => import( /* webpackChunkName: "inventario-bodegas" */  '@/views/Inventario/Bodegas/Registrar'),
        meta: {
            id_menu : 85,
            pageTitle: 'Registrar Bodega',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado Bodegas',
                    active: false,
                    to:'/inventario/bodegas'
                },
{
                    text: 'Registrar Bodega',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/bodegas/actualizar/:id_bodega',
        name: 'inventario-bodegas-actualizar',
        component: () => import( /* webpackChunkName: "inventario-bodegas" */  '@/views/Inventario/Bodegas/Actualizar'),
        meta: {
            id_menu : 86,
            pageTitle: 'Actualizar Bodega',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado Bodegas',
                    active: false,
                    to:'/inventario/bodegas'
                },
                {
                    text: 'Actualizar Bodega',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-bodegas',
        name: 'inventario-tipos-bodegas',
        component: () => import( /* webpackChunkName: "inventario-tipos-bodegas" */  '@/views/Inventario/TipoBodega/Listado'),
        meta: {
            id_menu : 87,
            pageTitle: 'Tipos Bodega',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Bodegas',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/inventario/tipos-bodegas/registrar',
        name: 'inventario-tipos-bodegas-registrar',
        component: () => import( /* webpackChunkName: "inventario-tipos-bodegas" */  '@/views/Inventario/TipoBodega/Registrar'),
        meta: {
            id_menu : 88,
            pageTitle: 'Registrar',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Bodegas',
                    active: false,
                    to:'/inventario/tipos-bodegas'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-bodegas/actualizar/:id_tipo_bodega',
        name: 'inventario-tipos-bodegas-actualizar',
        component: () => import( /* webpackChunkName: "inventario-tipos-bodegas" */  '@/views/Inventario/TipoBodega/Actualizar'),
        meta: {
            id_menu : 89,
            pageTitle: 'Actuaizar',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Bodegas',
                    active: false,
                    to:'/inventario/tipos-bodegas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Tipos productos
    {
        path: '/inventario/tipos-productos',
        name: 'inventario-tipos-productos',
        component: () => import( /* webpackChunkName: "inventario-tipos-productos" */  '@/views/Inventario/TiposProductos/Listado'),
        meta: {
            id_menu : 90,
            pageTitle: 'Tipos Productos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Productos',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-productos/registrar',
        name: 'inventario-tipos-productos-registrar',
        component: () => import( /* webpackChunkName: "inventario-tipos-productos" */  '@/views/Inventario/TiposProductos/Registrar'),
        meta: {
            id_menu : 91,
            pageTitle: 'Tipos Productos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Productos',
                    active: false,
                    to:'/inventario/tipos-productos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-productos/actualizar/:id_tipo_producto',
        name: 'inventario-tipos-productos-actualizar',
        component: () => import( /* webpackChunkName: "inventario-tipos-productos" */  '@/views/Inventario/TiposProductos/Actualizar'),
        meta: {
            id_menu : 92,
            pageTitle: 'Tipos Productos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Productos',
                    active: false,
                    to:'/inventario/tipos-productos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Unidades de medida
    {
        path: '/inventario/unidades-medida',
        name: 'inventario-unidades-medida',
        component: () => import( /* webpackChunkName: "inventario-unidad-medida" */  '@/views/Inventario/UnidadMedida/Listado'),
        meta: {
            id_menu : 93,
            pageTitle: 'Unidades de Medida',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Unidades de Medida',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/unidades-medida/registrar',
        name: 'inventario-unidades-medida-registrar',
        component: () => import( /* webpackChunkName: "inventario-unidad-medida" */  '@/views/Inventario/UnidadMedida/Registrar'),
        meta: {
            id_menu : 94,
            pageTitle: 'Unidades de Medida',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado Unidades de Medida',
                    active: false,
                    to:'/inventario/unidades-medida'
                },{
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/unidades-medida/actualizar/:id_unidad_medida',
        name: 'inventario-unidades-medida-actualizar',
        component: () => import( /* webpackChunkName: "inventario-unidad-medida" */  '@/views/Inventario/UnidadMedida/Actualizar'),
        meta: {
            id_menu : 95,
            pageTitle: 'Unidades de Medida',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado Unidades de Medida',
                    active: false,
                    to:'/inventario/unidades-medida'

                },{
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-entradas',
        name: 'inventario-tipos-entradas',
        component: () => import( /* webpackChunkName: "inventario-tipos-entradas" */  '@/views/Inventario/TiposEntradas/Listado'),
        meta: {
            id_menu : 96,
            pageTitle: 'Tipos de Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Entradas',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-entradas/registar',
        name: 'inventario-tipos-entradas-registrar',
        component: () => import( /* webpackChunkName: "inventario-tipos-entradas" */  '@/views/Inventario/TiposEntradas/Registrar'),
        meta: {
            id_menu : 97,
            pageTitle: 'Tipos de Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Entradas',
                    active: false,
                    to: '/inventario/tipos-entradas'
                },{
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-entradas/actualizar/:id_tipo_entrada',
        name: 'inventario-tipos-entradas-actualizar',
        component: () => import( /* webpackChunkName: "inventario-tipos-entradas" */  '@/views/Inventario/TiposEntradas/Actualizar'),
        meta: {
            id_menu : 98,
            pageTitle: 'Tipos de Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Entradas',
                    active: false,
                    to: '/inventario/tipos-entradas'
                },{
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Tipos Salidas
    {
        path: '/inventario/tipos-salidas',
        name: 'inventario-tipos-salidas',
        component: () => import( /* webpackChunkName: "inventario-tipos-salidas" */  '@/views/Inventario/TiposSalidas/Listado'),
        meta: {
            id_menu : 99,
            pageTitle: 'Tipos de Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Salidas',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-salidas/registrar',
        name: 'inventario-tipos-salidas-registrar',
        component: () => import( /* webpackChunkName: "inventario-tipos-salidas" */  '@/views/Inventario/TiposSalidas/Registrar'),
        meta: {
            id_menu : 100,
            pageTitle: 'Tipos de Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Salidas',
                    active: false,
                    to: '/inventario/tipos-salidas'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-salidas/actualizar/:id_tipo_salida',
        name: 'inventario-tipos-salidas-actualizar',
        component: () => import( /* webpackChunkName: "inventario-tipos-salidas" */  '@/views/Inventario/TiposSalidas/Actualizar'),
        meta: {
            id_menu : 101,
            pageTitle: 'Tipos de Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Salidas',
                    active: false,
                    to: '/inventario/tipos-salidas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Tipo proveedor
    {
        path: '/inventario/tipos-proveedores',
        name: 'inventario-tipos-proveedores',
        component: () => import( /* webpackChunkName: "inventario-tipos-proveedores" */  '@/views/Inventario/TipoProveedor/Listado'),
        meta: {
            id_menu : 102,
            pageTitle: 'Tipos de Proveedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Proveedores',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-proveedores/registrar',
        name: 'inventario-tipos-proveedores-registrar',
        component: () => import( /* webpackChunkName: "inventario-tipos-proveedores" */  '@/views/Inventario/TipoProveedor/Registrar'),
        meta: {
            id_menu : 103,
            pageTitle: 'Tipos de Proveedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Proveedores',
                    active: false,
                    to: '/inventario/tipos-proveedores'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/tipos-proveedores/actualizar/:id_tipo_proveedor',
        name: 'inventario-tipos-proveedores-actualizar',
        component: () => import( /* webpackChunkName: "inventario-tipos-proveedores" */  '@/views/Inventario/TipoProveedor/Actualizar'),
        meta: {
            id_menu : 104,
            pageTitle: 'Tipos de Proveedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Proveedores',
                    active: true,
                    to: '/inventario/tipos-proveedores'
                },
                {
                    text: 'Actualizar',
                    active: false,
                },

            ],
        },
    },
    //Marcas
    {
        path: '/inventario/marcas',
        name: 'inventario-marcas',
        component: () => import( /* webpackChunkName: "inventario-marcas" */  '@/views/Inventario/Marcas/Listado'),
        meta: {
            id_menu : 105,
            pageTitle: 'Marcas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Marcas',
                    active: false,
                },

            ],
        },
    },
    {
        path: '/inventario/marcas/registrar',
        name: 'inventario-marcas-registrar',
        component: () => import( /* webpackChunkName: "inventario-marcas" */  '@/views/Inventario/Marcas/Registrar'),
        meta: {
            id_menu : 106,
            pageTitle: 'Marcas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Marcas',
                    active: false,
                    to: '/inventario/marcas'

                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/marcas/actualizar/:id_marca',
        name: 'inventario-marcas-actualizar',
        component: () => import( /* webpackChunkName: "inventario-marcas" */  '@/views/Inventario/Marcas/Actualizar'),
        meta: {
            id_menu : 107,
            pageTitle: 'Marcas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Marcas',
                    active: false,
                    to: '/inventario/marcas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Proveedores
    {
        path: '/inventario/proveedores',
        name: 'inventario-proveedores',
        component: () => import( /* webpackChunkName: "inventario-proveedores" */  '@/views/Inventario/Proveedores/Listado'),
        meta: {
            id_menu : 108,
            pageTitle: 'Proveedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Proveedores',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/proveedores/registar',
        name: 'inventario-proveedores-registrar',
        component: () => import( /* webpackChunkName: "inventario-proveedores" */  '@/views/Inventario/Proveedores/Registrar'),
        meta: {
            id_menu : 109,
            pageTitle: 'Proveedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Proveedores',
                    active: false,
                    to: 'inventario-proveedores'
                },{
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/proveedores/actualizar/:id_proveedor',
        name: 'inventario-proveedores-actualizar',
        component: () => import( /* webpackChunkName: "inventario-proveedores" */  '@/views/Inventario/Proveedores/Actualizar'),
        meta: {
            id_menu : 110,
            pageTitle: 'Proveedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Proveedores',
                    active: false,
                    to: 'inventario-proveedores'
                },{
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Control de entradas
    {
        path: '/inventario/entradas',
        name: 'inventario-entradas',
        component: () => import( /* webpackChunkName: "inventario-entradas" */  '@/views/Inventario/Entradas/Listado'),
        meta: {
            id_menu : 111,
            pageTitle: 'Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/entradas/registrar',
        name: 'inventario-entradas-registrar',
        component: () => import( /* webpackChunkName: "inventario-entradas" */  '@/views/Inventario/Entradas/Registrar'),
        meta: {
            id_menu : 112,
            pageTitle: 'Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/entradas'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/entradas/actualizar/:id_entrada',
        name: 'inventario-entradas-actualizar',
        component: () => import( /* webpackChunkName: "inventario-entradas" */  '@/views/Inventario/Entradas/Actualizar'),
        meta: {
            id_menu : 113,
            pageTitle: 'Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/entradas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/entradas/recibir/:id_entrada',
        name: 'inventario-entradas-recibir',
        component: () => import( /* webpackChunkName: "inventario-entradas" */  '@/views/Inventario/Entradas/Recibir'),
        meta: {
            id_menu : 114,
            pageTitle: 'Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/entradas'
                },
                {
                    text: 'Recibir',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/entradas/mostrar/:id_entrada',
        name: 'inventario-entradas-mostrar',
        component: () => import( /* webpackChunkName: "inventario-entradas" */  '@/views/Inventario/Entradas/Mostrar'),
        meta: {
            id_menu : 115,
            pageTitle: 'Entradas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/entradas'
                },
                {
                    text: 'vista previa',
                    active: true,
                },

            ],
        },
    },
    //Categorías producto
    {
        path: '/inventario/categorias-productos',
        name: 'inventario-categorias-productos',
        component: () => import( /* webpackChunkName: "inventario-categorias-productos" */  '@/views/Inventario/CategoriasProductos/Listado'),
        meta: {
            id_menu : 116,
            pageTitle: 'Categorías Productos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Categorías Productos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/inventario/categorias-productos/registrar',
        name: 'inventario-categorias-productos-registrar',
        component: () => import( /* webpackChunkName: "inventario-categorias-productos" */  '@/views/Inventario/CategoriasProductos/Registrar'),
        meta: {
            id_menu : 117,
            pageTitle: 'Categorías Productos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Categorías Productos',
                    active: false,
                    to: '/inventario/categorias-productos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/inventario/categorias-productos/actualizar/:id_categoria',
        name: 'inventario-categorias-productos-actualizar',
        component: () => import( /* webpackChunkName: "inventario-categorias-productos" */  '@/views/Inventario/CategoriasProductos/Actualizar'),
        meta: {
            id_menu : 118,
            pageTitle: 'Categorías Productos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Categorías Productos',
                    active: false,
                    to: '/inventario/categorias-productos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    //Configuración CD Inventario
    {
        path: '/inventario/configuracion-inventario',
        name: 'inventario-configuracion-inventario',
        component: () => import( /* webpackChunkName: "inventario-configuracion-invetario" */  '@/views/Inventario/ConfiguracionCDInventario/AjustesInvetario'),
        meta: {
            id_menu : 119,
            pageTitle: 'Inventario',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Configuración comprobante',
                    active: true,
                },
            ],
        },
    },
    //Control de salidas
    {
        path: '/inventario/salidas',
        name: 'inventario-salidas',
        component: () => import( /* webpackChunkName: "inventario-salidas" */  '@/views/Inventario/Salidas/Listado'),
        meta: {
            id_menu : 120,
            pageTitle: 'Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/salidas-registrar',
        name: 'inventario-salidas-registrar',
        component: () => import( /* webpackChunkName: "inventario-salidas" */  '@/views/Inventario/Salidas/Registrar'),
        meta: {
            id_menu : 121,
            pageTitle: 'Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/salidas'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/salidas-actualizar/:id_salida',
        name: 'inventario-salidas-actualizar',
        component: () => import( /* webpackChunkName: "inventario-salidas" */  '@/views/Inventario/Salidas/Actualizar'),
        meta: {
            id_menu : 122,
            pageTitle: 'Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/salidas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/salidas-despachar/:id_salida',
        name: 'inventario-salidas-despachar',
        component: () => import( /* webpackChunkName: "inventario-salidas" */  '@/views/Inventario/Salidas/Despachar'),
        meta: {
            id_menu : 123,
            pageTitle: 'Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/salidas'
                },
                {
                    text: 'Despachar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/salidas-mostrar/:id_salida',
        name: 'inventario-salidas-mostrar',
        component: () => import( /* webpackChunkName: "inventario-salidas" */  '@/views/Inventario/Salidas/Mostrar'),
        meta: {
            id_menu : 124,
            pageTitle: 'Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/salidas'
                },
                {
                    text: 'Vista previa',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/salidas-devolucion/:id_salida',
        name: 'inventario-salidas-devolucion',
        component: () => import( /* webpackChunkName: "inventario-salidas" */  '@/views/Inventario/Salidas/RegistrarDevolucion'),
        meta: {
            id_menu : 125,
            pageTitle: 'Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/salidas'
                },
                {
                    text: 'Devolución',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/inventario/salidas-traslados',
        name: 'inventario-salidas-traslados',
        component: () => import( /* webpackChunkName: "inventario-salidas" */  '@/views/Inventario/Salidas/RegistrarTraslado'),
        meta: {
            id_menu : 126,
            pageTitle: 'Salidas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/salidas'
                },
                {
                    text: 'Traslados',
                    active: true,
                },

            ],
        },
    },
    // Kardex inventario
    {
        path: '/inventario/kardex',
        name: 'inventario-kardex',
        component: () => import( /* webpackChunkName: "inventario-kardex" */  '@/views/Inventario/Kardex/Consulta'),
        meta: {
            id_menu : 127,
            pageTitle: 'Kardex',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: true,
                },
            ],
        },
    },
    // Conteo fisico
    {
        path: '/inventario/conteo-fisico',
        name: 'inventario-conteo-fisico',
        component: () => import( /* webpackChunkName: "inventario-conteo-fisico" */  '@/views/Inventario/ConteoFisico/Listado'),
        meta: {
            id_menu : 128,
            pageTitle: 'Conteo fisico',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/inventario/conteo-fisico/registrar',
        name: 'inventario-conteo-fisico-registrar',
        component: () => import( /* webpackChunkName: "inventario-conteo-fisico" */  '@/views/Inventario/ConteoFisico/Registrar'),
        meta: {
            id_menu : 129,
            pageTitle: 'Conteo fisico',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/conteo-fisico'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/inventario/conteo-fisico/actualizar/:id_inventario_fisico',
        name: 'inventario-conteo-fisico-actualizar',
        component: () => import( /* webpackChunkName: "inventario-conteo-fisico" */  '@/views/Inventario/ConteoFisico/Actualizar'),
        meta: {
            id_menu : 130,
            pageTitle: 'Conteo fisico',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/conteo-fisico'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/inventario/conteo-fisico/vista-previa/:id_inventario_fisico',
        name: 'inventario-conteo-fisico-mostrar',
        component: () => import( /* webpackChunkName: "inventario-conteo-fisico" */  '@/views/Inventario/ConteoFisico/Mostrar'),
        meta: {
            id_menu : 131,
            pageTitle: 'Conteo fisico',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/inventario/conteo-fisico'
                },
                {
                    text: 'Vista previa',
                    active: true,
                },
            ],
        },
    },
    //inventario
    {
        path: '/inventario/Reportes',
        name:'inventario-Reportes',
        component: () => import( /* webpackChunkName: "inventario-conteo-fisico" */  '@/views/Inventario/Reportes/Reportes'),
        meta: {
            id_menu : 149,
            pageTitle: 'Reportes',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Reportes',
                    active: true,
                },

            ],
        }
    }
]
