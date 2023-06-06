export default [
    //Bancos
    {
        path: '/cajabanco/bancos',
        name: 'cajabanco-bancos',
        component: () => import( /* webpackChunkName: "cajabanco-bancos" */  '@/views/CajaBanco/Bancos/Listado'),
        meta: {
            id_menu : 35,
            pageTitle: 'Bancos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Bancos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/bancos/registar',
        name: 'cajabanco-bancos-registrar',
        component: () => import( /* webpackChunkName: "cajabanco-bancos" */  '@/views/CajaBanco/Bancos/Registrar'),
        meta: {
            id_menu : 36,
            pageTitle: 'Bancos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Bancos',
                    active: false,
                    to: '/cajabanco/bancos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/bancos/actualizar/:id_banco',
        name: 'cajabanco-bancos-actualizar',
        component: () => import( /* webpackChunkName: "cajabanco-bancos" */  '@/views/CajaBanco/Bancos/Actualizar'),
        meta: {
            id_menu : 37,
            pageTitle: 'Bancos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Bancos',
                    active: false,
                    to: '/cajabanco/bancos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    //Proyectos
    {
        path: '/cajabanco/proyectos',
        name: 'cajabanco-proyectos',
        component: () => import( /* webpackChunkName: "cajabanco-proyectos" */  '@/views/CuentasXCobrar/Proyectos/Listado'),
        meta: {
            id_menu : 142,
            pageTitle: 'Proyectos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Proyectos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proyectos/registar',
        name: 'cajabanco-proyectos-registrar',
        component: () => import( /* webpackChunkName: "cajabanco-proyectos" */  '@/views/CuentasXCobrar/Proyectos/Registrar'),
        meta: {
            id_menu : 143,
            pageTitle: 'Proyectos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Proyectos',
                    active: false,
                    to: '/cajabanco/proyectos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proyectos/actualizar/:id_proyecto',
        name: 'cajabanco-proyectos-actualizar',
        component: () => import( /* webpackChunkName: "cajabanco-proyectos" */  '@/views/CuentasXCobrar/Proyectos/Actualizar'),
        meta: {
            id_menu : 144,
            pageTitle: 'Proyectos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Proyectos',
                    active: false,
                    to: '/cajabanco/proyectos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    //Facturacion
    {
        path: '/cajabanco/facturas',
        name: 'cajabanco-facturas',
        component: () => import( /* webpackChunkName: "cajabanco-facturas" */  '@/views/CajaBanco/Facturacion/Listado'),
        meta: {
            id_menu : 38,
            pageTitle: 'Facturación',
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
        path: '/cajabanco/facturas/registrar',
        name: 'cajabanco-facturas-registrar',
        component: () => import( /* webpackChunkName: "cajabanco-facturas" */  '@/views/CajaBanco/Facturacion/Registrar'),
        meta: {
            id_menu : 39,
            pageTitle: 'Facturación',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/facturas'
                },                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/facturas/registrar-dol',
        name: 'cajabanco-facturas-registrar-dol',
        component: () => import( /* webpackChunkName: "cajabanco-facturas" */  '@/views/CajaBanco/Facturacion/RegistrarDolares'),
        meta: {
            id_menu : 39,
            pageTitle: 'Facturación',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/facturas'
                },                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/ajustes-cd',
        name: 'cajabanco-ajustes-cd',
        component: () => import( /* webpackChunkName: "cajabanco-ajustes-cd" */  '@/views/CajaBanco/ConfiguracionCD/AjustesFactura.vue'),
        meta: {
            id_menu : 40,
            pageTitle: 'Ajustes CD',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proformas',
        name: 'cajabanco-proformas',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Proformas/Listado.vue'),
        meta: {
            id_menu : 41,
            pageTitle: 'Proformas',
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
        path: '/cajabanco/proformas/registrar',
        name: 'cajabanco-proformas-registrar',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Proformas/Registrar.vue'),
        meta: {
            id_menu : 42,
            pageTitle: 'Proformas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/proformas'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proformas/registrar-dol',
        name: 'cajabanco-proformas-registrar-dol',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Proformas/RegistrarDolares.vue'),
        meta: {
            id_menu : 42,
            pageTitle: 'Proformas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/proformas'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proformas/actualizar/:id_proforma',
        name: 'cajabanco-proformas-actualizar',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Proformas/Actualizar.vue'),
        meta: {
            id_menu : 43,
            pageTitle: 'Proformas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/proformas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proformas/actualizar-dol/:id_proforma',
        name: 'cajabanco-proformas-actualizar-dol',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Proformas/ActualizarDolares.vue'),
        meta: {
            id_menu : 43,
            pageTitle: 'Proformas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/proformas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proformas/seguimiento/:id_proforma',
        name: 'cajabanco-proformas-seguimiento',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Proformas/RegistrarSeguimiento.vue'),
        meta: {
            id_menu : 44,
            pageTitle: 'Proformas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/proformas'
                },
                {
                    text: 'Seguimiento',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/proformas/vista-previa/:id_proforma',
        name: 'cajabanco-proformas-mostrar',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Proformas/Mostrar.vue'),
        meta: {
            id_menu : 45,
            pageTitle: 'Proformas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cajabanco/proformas'
                },
                {
                    text: 'Vista previa',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cajabanco/Reportes',
        name: 'cajabanco-Reportes',
        component: () => import( /* webpackChunkName: "cajabanco-proformas" */  '@/views/CajaBanco/Reportes/Reportes'),
        meta: {
            id_menu : 150,
            pageTitle: 'Reportes',
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
