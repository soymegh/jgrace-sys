export default [
    //Cuentas por cobrar
    {
        path: '/cuentas-por-cobrar/cxc',
        name: 'cxc-cuentas-por-cobrar',
        component: () => import( /* webpackChunkName: "cxc-cuentas-por-cobrar" */  '@/views/CuentasXCobrar/cxc/Listado'),
        meta: {
            id_menu : 73,
            pageTitle: 'Cuentas por cobrar',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Cuentas por cobrar',
                    active: true,
                },
            ],
        },
    },
    //Recibos oficiales de caja
    {
        path: '/cuentas-por-cobrar/roc',
        name: 'recibos',
        component: () => import( /* webpackChunkName: "recibos-oficiales-caja" */  '@/views/CuentasXCobrar/Recibos/Listado'),
        meta: {
            id_menu : 74,
            pageTitle: 'Control de recibos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Recibos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cuentas-por-cobrar/roc/registrar',
        name: 'recibos-registrar',
        component: () => import( /* webpackChunkName: "recibos-oficiales-caja" */  '@/views/CuentasXCobrar/Recibos/Registrar'),
        meta: {
            id_menu : 75,
            pageTitle: 'Control de recibos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cuentas-por-cobrar/roc'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cuentas-por-cobrar/roc/registrar-dol',
        name: 'recibos-registrar-dol',
        component: () => import( /* webpackChunkName: "recibos-oficiales-caja" */  '@/views/CuentasXCobrar/Recibos/RegistrarDolares'),
        meta: {
            id_menu : 75,
            pageTitle: 'Control de recibos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cuentas-por-cobrar/roc'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/cuentas-por-cobrar/roc/vista-previa/:id_recibo',
        name: 'recibos-mostrar',
        component: () => import( /* webpackChunkName: "recibos-oficiales-caja" */  '@/views/CuentasXCobrar/Recibos/Mostrar'),
        meta: {
            id_menu : 76,
            pageTitle: 'Control de recibos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado',
                    active: false,
                    to:'/cuentas-por-cobrar/roc'
                },
                {
                    text: 'Vista previa',
                    active: true,
                },
            ],
        },
    },
]
