export default [
    {
        path: '/bitacora/accesos',
        name: 'bitacora-accesos',
        component: () => import( /* webpackChunkName: "Home" */  '@/views/Admon/Bitacora/ListadoAccesos.vue'),
        meta: {
            pageTitle: 'Listado de accesos',
            requiresAuth: true,
            id_menu:34,
            breadcrumb: [
                {
                    text: 'Listado de accesos',
                    active: true,
                },

            ],
        },
    },
]
