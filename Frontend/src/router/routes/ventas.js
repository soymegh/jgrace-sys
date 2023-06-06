export default [
    //Vendedor
    {
        path: '/ventas/vendedores',
        name: 'ventas-vendedores',
        component: () => import( /* webpackChunkName: "ventas-vendedores" */  '@/views/Ventas/Vendedores/Listado'),
        meta: {
            id_menu : 132,
            pageTitle: 'Vendedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Vendedores',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/ventas/vendedores/registrar',
        name: 'ventas-vendedores-registrar',
        component: () => import( /* webpackChunkName: "ventas-vendedores" */  '@/views/Ventas/Vendedores/Registrar'),
        meta: {
            id_menu : 133,
            pageTitle: 'Vendedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Vendedores',
                    active: false,
                    to: '/ventas/vendedores'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/ventas/vendedores/actualizar/:id_vendedor',
        name: 'ventas-vendedores-actualizar',
        component: () => import( /* webpackChunkName: "ventas-vendedores" */  '@/views/Ventas/Vendedores/Actualizar'),
        meta: {
            id_menu : 134,
            pageTitle: 'Vendedores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Vendedores',
                    active: false,
                    to: '/ventas/vendedores'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    //Tipo Clientes
    {
        path: '/ventas/tipos-clientes',
        name: 'ventas-tipos-clientes',
        component: () => import( /* webpackChunkName: "ventas-tipo-clientes" */  '@/views/Ventas/TipoClientes/Listado'),
        meta: {
            id_menu : 135,
            pageTitle: 'Tipo Clientes',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipo Clientes',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/ventas/tipos-clientes/registrar',
        name: 'ventas-tipos-clientes-registrar',
        component: () => import( /* webpackChunkName: "ventas-tipo-clientes" */  '@/views/Ventas/TipoClientes/Registrar'),
        meta: {
            id_menu : 136,
            pageTitle: 'Tipo Clientes',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipo Clientes',
                    active: false,
                    to: '/ventas/tipos-clientes'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/ventas/tipos-clientes/actualizar/:id_tipo_cliente',
        name: 'ventas-tipos-clientes-actualizar',
        component: () => import( /* webpackChunkName: "ventas-tipo-clientes" */  '@/views/Ventas/TipoClientes/Actualizar'),
        meta: {
            id_menu : 137,
            pageTitle: 'Tipo Clientes',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Tipo Clientes',
                    active: false,
                    to: '/ventas/tipos-clientes'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    //Clientes
    {
        path: '/ventas/clientes',
        name: 'ventas-clientes',
        component: () => import( /* webpackChunkName: "ventas-clientes" */  '@/views/Ventas/Clientes/Listado'),
        meta: {
            id_menu : 138,
            pageTitle: 'Clientes',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Clientes',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/ventas/clientes/registrar',
        name: 'ventas-clientes-registrar',
        component: () => import( /* webpackChunkName: "ventas-clientes" */  '@/views/Ventas/Clientes/Registrar'),
        meta: {
            id_menu : 139,
            pageTitle: 'Registrar clientes',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Clientes',
                    active: false,
                    to:'/ventas/clientes'
                },
                {
                    text: 'Registrar',
                    active: true,
                },

            ],
        },
    },
    {
        path: '/ventas/clientes/actualizar/:id_cliente',
        name: 'ventas-clientes-actualizar',
        component: () => import( /* webpackChunkName: "ventas-clientes" */  '@/views/Ventas/Clientes/Actualizar'),
        meta: {
            id_menu : 140,
            pageTitle: 'Actualizar clientes',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Clientes',
                    active: false,
                    to:'/ventas/clientes'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },

            ],
        },
    },
]
