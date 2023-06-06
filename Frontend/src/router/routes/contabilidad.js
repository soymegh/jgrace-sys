export default [
    //Tipos Cuentas
    {
        path: '/contabilidad/tipos-cuenta',
        name: 'contabilidad-tipos-cuenta',
        component: () => import( /* webpackChunkName: "contabilidad-tipos-cuentas" */  '@/views/Contabilidad/TiposCuentas/Listado'),
        meta: {
            id_menu : 46,
            pageTitle: 'Tipos Cuentas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Cuentas',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/actualizar-tipos-cuenta/:id_tipo_cuenta',
        name: 'actualizar-tipo-cuenta',
        component: () => import( /* webpackChunkName: "contabilidad-tipos-cuentas" */  '@/views/Contabilidad/TiposCuentas/Actualizar'),
        meta: {
            id_menu : 47,
            pageTitle: 'Actualizar Tipo Cuenta',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipo de Cuenta',
                    active: false,
                    to: '/contabilidad/tipos-cuenta'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Niveles cuentas
    {
        path: '/contabilidad/niveles-cuentas',
        name: 'contabilidad-niveles-cuentas',
        component: () => import( /* webpackChunkName: "contabilidad-niveles-cuentas" */  '@/views/Contabilidad/NivelesCuentas/Listado'),
        meta: {
            id_menu : 48,
            pageTitle: 'Niveles Cuentas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Niveles Cuentas',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/niveles-cuentas/actualizar/:id_nivel_cuenta',
        name: 'contabilidad-niveles-cuentas-actualizar',
        component: () => import( /* webpackChunkName: "contabilidad-niveles-cuentas" */  '@/views/Contabilidad/NivelesCuentas/Actualizar'),
        meta: {
            id_menu : 49,
            pageTitle: 'Actualizar Niveles Cuentas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Niveles Cuentas',
                    active: false,
                    to:'/contabilidad/niveles-cuentas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    //Tipos documentos
    {
        path: '/contabilidad/tipos-documentos',
        name: 'contabilidad-tipos-documentos',
        component: () => import( /* webpackChunkName: "contabilidad-tipos-documentos" */  '@/views/Contabilidad/TiposDocumentos/Listado'),
        meta: {
            id_menu : 50,
            pageTitle: 'Tipos Documentos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Documentos',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/tipos-documentos/registrar',
        name: 'contabilidad-tipos-documentos-registrar',
        component: () => import( /* webpackChunkName: "contabilidad-tipos-documentos" */  '@/views/Contabilidad/TiposDocumentos/Registrar'),
        meta: {
            id_menu : 51,
            pageTitle: 'Tipos Documentos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos Documentos',
                    active: false,
                    to:'/contabilidad/tipos-documentos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/tipos-documentos/actualizar/:id_tipo_doc',
        name: 'contabilidad-tipos-documentos-actualizar',
        component: () => import( /* webpackChunkName: "contabilidad-tipos-documentos" */  '@/views/Contabilidad/TiposDocumentos/Actualizar'),
        meta: {
            id_menu : 52,
            pageTitle: 'Actualizar Tipos Documentos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipos de Documentos',
                    active: false,
                    to:'/contabilidad/tipos-documentos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/configuracion-cd',
        name: 'contabilidad-configuracion-cd',
        component: () => import( /* webpackChunkName: "contabilidad-configuracion-cd" */  '@/views/Contabilidad/ConfiguracionCD/AjustesContabilidad'),
        meta: {
            id_menu : 53,
            pageTitle: 'Contabilidad',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Configuración comprobante',
                    active: true,
                },

            ],
        },
    },
    //Centros de costo
    {
        path: '/contabilidad/centros-costos',
        name: 'contabilidad-centros-costos',
        component: () => import( /* webpackChunkName: "contabilidad-centros-costos" */  '@/views/Contabilidad/CentrosCostos/Listado'),
        meta: {
            id_menu : 54,
            pageTitle: 'Centros de Costo',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Centros de Costo',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/centros-costos/registrar',
        name: 'contabilidad-centros-costos-registrar',
        component: () => import( /* webpackChunkName: "contabilidad-centros-costos" */  '@/views/Contabilidad/CentrosCostos/Registrar'),
        meta: {
            id_menu : 55,
            pageTitle: 'Registrar Centros de Costo',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Registrar Centro de Costo',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/centros-costos/actualizar/:id_centro',
        name: 'contabilidad-centros-costos-actualizar',
        component: () => import( /* webpackChunkName: "contabilidad-centros-costos" */  '@/views/Contabilidad/CentrosCostos/Actualizar'),
        meta: {
            id_menu : 56,
            pageTitle: 'Actualizar Centro de Costo',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Actualizar Centro de Costo',
                    active: true,
                },

            ],
        },
    },
    //Catálogos Contables
    {
        path: '/contabilidad/catalogos-contables',
        name: 'contabilidad-catalogos-contables',
        component: () => import( /* webpackChunkName: "contabilidad-catalogos-contables" */  '@/views/Contabilidad/CatalogosContables/Listado'),
        meta: {
            id_menu : 57,
            pageTitle: 'Catálogos Contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Catálogos Contables',
                    active: true,
                },

            ],
        },
    },

    {
        path: '/contabilidad/catalogos-contables/registrar',
        name: 'contabilidad-catalogos-contables-registrar',
        component: () => import( /* webpackChunkName: "contabilidad-catalogos-contables" */  '@/views/Contabilidad/CatalogosContables/Registrar'),
        meta: {
            id_menu : 58,
            pageTitle: 'Catálogos Contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Catálogos Contables',
                    active: false,
                    to: '/contabilidad/catalogos-contables',
                },
                {
                    text: ' Registrar',
                    active: true,
                },

            ],
        },
    },

    {
        path: '/contabilidad/catalogos-contables/actualizar/:id_cuenta_contable',
        name: 'contabilidad-catalogos-contables-actualizar',
        component: () => import( /* webpackChunkName: "contabilidad-catalogos-contables" */  '@/views/Contabilidad/CatalogosContables/Actualizar'),
        meta: {
            id_menu : 59,
            pageTitle: 'Catálogos Contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Catálogos Contables',
                    active:false,
                    to: '/contabilidad/catalogos-contables',
                },
                {
                    text: ' Actualizar',
                    active: true,
                },

            ],
        },
    },

    {
        path: '/contabilidad/catalogos-contables/registrar/:id_cuenta_contable_padre',
        name: 'contabilidad-catalogos-contables-registrar-subcuenta-padre',
        component: () => import( /* webpackChunkName: "contabilidad-catalogos-contables" */  '@/views/Contabilidad/CatalogosContables/RegistrarSubCuenta'),
        meta: {
            id_menu : 60,
            pageTitle: 'Subcuenta',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: ' Agregar subcuenta',
                    active: false,
                    to: '/contabilidad/catalogos-contables',
                },
                {
                    text: ' Registrar',
                    active: true,
                },

            ],
        },
    },
    //Periodos contables
    {
        path: '/contabilidad/periodos-contables',
        name: 'contabilidad-periodos-contables',
        component: () => import( /* webpackChunkName: "periodos-contables" */  '@/views/Contabilidad/PeriodosContables/Listado'),
        meta: {
            id_menu : 61,
            pageTitle: 'Periodos contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Administrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/periodos-contables/registrar',
        name: 'contabilidad-periodos-contables-registrar',
        component: () => import( /* webpackChunkName: "periodos-contables" */  '@/views/Contabilidad/PeriodosContables/Registrar'),
        meta: {
            id_menu : 62,
            pageTitle: 'Periodos contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Administrar',
                    active: false,
                    to: '/contabilidad/periodos-contables',
                },
                {
                    text:'Registrar',
                    active: true,
                }

            ],
        },
    },
    {
        path: '/contabilidad/periodos-contables/actualizar/:id_periodo_fiscal',
        name: 'contabilidad-periodos-contables-actualizar',
        component: () => import( /* webpackChunkName: "periodos-contables" */  '@/views/Contabilidad/PeriodosContables/Actualizar'),
        meta: {
            id_menu : 63,
            pageTitle: 'Periodos contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Administrar',
                    active: false,
                    to: '/contabilidad/periodos-contables',
                },
                {
                    text:'Actualizar',
                    active: true,
                }

            ],
        },
    },
    //Tasas de cambio
    {
        path: '/contabilidad/tasas-cambio',
        name: 'tasas-cambio',
        component: () => import( /* webpackChunkName: "tasas-cambio" */  '@/views/Contabilidad/TasasCambios/Listado'),
        meta: {
            id_menu : 64,
            pageTitle: 'Tasas de cambio',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Administrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/tasas-cambio-paralela',
        name: 'tasas-cambio-paralela',
        component: () => import( /* webpackChunkName: "tasas-cambio" */  '@/views/Contabilidad/TasasCambios/ListadoParalelo'),
        meta: {
            id_menu :141,
            pageTitle: 'Tasas de cambio paralelas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Administrar',
                    active: true,
                },

            ],
        },
    },
    // Documentos contables
    {
        path: '/contabilidad/documentos-contables',
        name: 'documentos-contables',
        component: () => import( /* webpackChunkName: "documentos-contables" */  '@/views/Contabilidad/DocumentosContables/Listado'),
        meta: {
            id_menu : 65,
            pageTitle: 'Documentos contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Administrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/documentos-contables/registrar',
        name: 'documentos-contables-registrar',
        component: () => import( /* webpackChunkName: "documentos-contables" */  '@/views/Contabilidad/DocumentosContables/Registrar'),
        meta: {
            id_menu : 66,
            pageTitle: 'Documentos contables',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Administrar',
                    active: false,
                    to:'/contabilidad/documentos-contables'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/balanza-comprobacion',
        name: 'contabilidad-balanza-comprobacion',
        component: () => import( /* webpackChunkName: "balanza-comprobacion" */  '@/views/Contabilidad/BalanzaComprobacion/BalanzaComprobacion'),
        meta: {
            id_menu : 67,
            pageTitle: 'Balanza comprobación',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Balanza comprobación',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/balanza-comprobacion-dol',
        name: 'contabilidad-balanza-comprobacion-dol',
        component: () => import( /* webpackChunkName: "balanza-comprobacion" */  '@/views/Contabilidad/BalanzaComprobacion/BalanzaComprobacionDolares'),
        meta: {
            id_menu : 67,
            pageTitle: 'Balanza comprobación',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Balanza comprobación',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/balance-general',
        name: 'contabilidad-balance-general',
        component: () => import( /* webpackChunkName: "balance-general" */  '@/views/Contabilidad/BalanceGeneral/BalanceGeneral'),
        meta: {
            id_menu : 68,
            pageTitle: 'Balance general',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Balance general',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/balance-general-dol',
        name: 'contabilidad-balance-general-dol',
        component: () => import( /* webpackChunkName: "balance-general" */  '@/views/Contabilidad/BalanceGeneral/BalanceGeneralDolares'),
        meta: {
            id_menu : 68,
            pageTitle: 'Balance general',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Balance general',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/estado-resultado',
        name: 'contabilidad-estado-resultado',
        component: () => import( /* webpackChunkName: "estado-resultado" */  '@/views/Contabilidad/EstadoResultado/EstadoResultado'),
        meta: {
            id_menu : 69,
            pageTitle: 'Estado resultado',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Estado resultado',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/estado-resultado-dol',
        name: 'contabilidad-estado-resultado-dol',
        component: () => import( /* webpackChunkName: "estado-resultado" */  '@/views/Contabilidad/EstadoResultado/EstadoResultadoDolares'),
        meta: {
            id_menu : 69,
            pageTitle: 'Estado resultado',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Estado resultado',
                    active: true,
                },

            ],
        },
    },
    //Cuentas Bancarias
    {
        path: '/contabilidad/cuentas-bancarias',
        name: 'contabilidad-cuentas-bancarias',
        component: () => import( /* webpackChunkName: "contabilidad-cuentas-bancarias" */  '@/views/Contabilidad/CuentasBancarias/Listado'),
        meta: {
            id_menu : 70,
            pageTitle: 'Cuentas Bancarias',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Cuentas Bancarias',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/cuentas-bancarias/registrar',
        name: 'contabilidad-cuentas-bancarias-registrar',
        component: () => import( /* webpackChunkName: "contabilidad-cuentas-bancarias" */  '@/views/Contabilidad/CuentasBancarias/Registrar'),
        meta: {
            id_menu : 71,
            pageTitle: 'Cuentas Bancarias',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Cuentas Bancarias',
                    active: false,
                    to: '/contabilidad/cuentas-bancarias'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/cuentas-bancarias/actualizar/:id_cuenta_bancaria',
        name: 'contabilidad-cuentas-bancarias-actualizar',
        component: () => import( /* webpackChunkName: "contabilidad-cuentas-bancarias" */  '@/views/Contabilidad/CuentasBancarias/Actualizar'),
        meta: {
            id_menu : 72,
            pageTitle: 'Cuentas Bancarias',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Cuentas Bancarias',
                    active: false,
                    to: '/contabilidad/cuentas-bancarias'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/contabilidad/Reportes',
        name: 'contabilidad-Reportes',
        component: () => import( /* webpackChunkName: "contabilidad-Reportes" */  '@/views/Contabilidad/Reportes/Reportes'),
        meta: {
            id_menu : 148,
            pageTitle: 'Reportes módulo contabilidad',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Reportes',
                    active: true,
                },

            ],
        },
    },
]
