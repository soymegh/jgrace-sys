import store from "@/store";
export default [
    {
        path: '/home',
        name: 'home',
        component: () => import( /* webpackChunkName: "Home" */  '@/views/Home.vue'),
        meta: {
            pageTitle: 'Inicio',
            requiresAuth: true,
            id_menu:1,
            breadcrumb: [
                {
                    text: 'Inicio',
                    active: true,
                },

            ],
        },
    },
    // Componentes de usuarios
    {
        path: '/admon/usuarios',
        name: 'admon-usuarios',
        component: () => import( /* webpackChunkName: "admon-usuarios" */  '@/views/Admon/Usuarios/Listado'),
        meta: {
            id_menu:8,
            pageTitle: 'Usuarios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado de usuarios',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/usuarios/registrar',
        name: 'admon-usuarios-registrar',
        component: () => import( /* webpackChunkName: "admon-usuarios" */  '@/views/Admon/Usuarios/Registrar'),
        meta: {
            id_menu:9,
            pageTitle: 'Registrar usuarios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Usuarios',
                    active: false,
                    to:'/admon/usuarios'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/usuarios/actualizar/:id',
        name: 'admon-usuarios-actualizar',
        component: () => import( /* webpackChunkName: "admon-usuarios" */  '@/views/Admon/Usuarios/Actualizar'),
        meta: {
            id_menu:10,
            pageTitle: 'Actualizar usuarios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Usuarios',
                    active: false,
                    to:'/admon/usuarios'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    // Componentes de paises
    {
        path: '/admon/paises',
        name: 'admon-paises',
        component: () => import( /* webpackChunkName: "admon-paises" */  '@/views/Admon/Paises/Listado'),
        meta: {
            id_menu:11,
            pageTitle: 'Listado de países',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado de países',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/paises/registrar',
        name: 'admon-paises-registrar',
        component: () => import( /* webpackChunkName: "admon-paises" */  '@/views/Admon/Paises/Registrar'),
        meta: {
            id_menu:12,
            pageTitle: 'Registrar paises',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Paises',
                    active: false,
                    to:'/admon/paises'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/paises/actualizar/:id_pais',
        name: 'admon-paises-actualizar',
        component: () => import( /* webpackChunkName: "admon-paises" */  '@/views/Admon/Paises/Actualizar'),
        meta: {
            id_menu:13,
            pageTitle: 'Actualizar paises',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Paises',
                    active: false,
                    to:'/admon/paises'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    // Componentes de departamentos
    {
        path: '/admon/departamentos',
        name: 'admon-departamentos',
        component: () => import( /* webpackChunkName: "admon-departamentos" */  '@/views/Admon/Departamentos/Listado'),
        meta: {
            id_menu:14,
            pageTitle: 'Departamentos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado de departamentos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/departamentos/registrar',
        name: 'admon-departamentos-registrar',
        component: () => import( /* webpackChunkName: "admon-departamentos" */  '@/views/Admon/Departamentos/Registrar'),
        meta: {
            id_menu:15,
            pageTitle: 'Departamentos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Departamentos',
                    active: false,
                    to:'/admon/departamentos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/departamentos/actualizar/:id_departamento',
        name: 'admon-departamentos-actualizar',
        component: () => import( /* webpackChunkName: "admon-departamentos" */  '@/views/Admon/Departamentos/Actualizar'),
        meta: {
            id_menu:16,
            pageTitle: 'Departamentos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Departamentos',
                    active: false,
                    to:'/admon/departamentos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    // Componentes de municipios
    {
        path: '/admon/municipios',
        name: 'admon-municipios',
        component: () => import( /* webpackChunkName: "admon-municipios" */  '@/views/Admon/Municipios/Listado'),
        meta: {
            id_menu:17,
            pageTitle: 'Municipios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado de municipios',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/municipios/registrar',
        name: 'admon-municipios-registrar',
        component: () => import( /* webpackChunkName: "admon-municipios" */  '@/views/Admon/Municipios/Registrar'),
        meta: {
            id_menu:18,
            pageTitle: 'Municipios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Municipios',
                    active: false,
                    to:'/admon/municipios'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/municipios/actualizar/:id_municipio',
        name: 'admon-municipios-actualizar',
        component: () => import( /* webpackChunkName: "admon-municipios" */  '@/views/Admon/Municipios/Actualizar'),
        meta: {
            id_menu:19,
            pageTitle: 'Municipios',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Actualizar municipios',
                    active: true,
                    to:'/admon/municipios'
                },
            ],
        },
    },
    // Componentes de codigos de invitacion
    {
        path: '/admon/invites',
        name: 'admon-invites',
        component: () => import( /* webpackChunkName: "admon-invites" */  '@/views/Admon/InvitesCode/Listado'),
        meta: {
            id_menu:151,
            pageTitle: 'Códigos de autorización',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado de codigos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/invites/registrar',
        name: 'admon-invites-registrar',
        component: () => import( /* webpackChunkName: "admon-invites" */  '@/views/Admon/InvitesCode/Registrar'),
        meta: {
            id_menu:18,
            pageTitle: 'Códigos de autorización',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Códigos de autorización',
                    active: false,
                    to:'/admon/invites'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/invites/actualizar/:id_municipio',
        name: 'admon-invites-actualizar',
        component: () => import( /* webpackChunkName: "admon-invites" */  '@/views/Admon/InvitesCode/Actualizar'),
        meta: {
            id_menu:19,
            pageTitle: 'Códigos de autorización',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Actualizar codigo',
                    active: true,
                    to:'/admon/invites'
                },
            ],
        },
    },
    {
        path: '/admon-ajustes',
        name: 'admon-ajustes',
        component: () => import( /* webpackChunkName: "admon-ajustes" */  '@/views/Admon/AjustesGenerales'),
        meta: {
            resources: 'ajustes',
            action: 'Leer',
            id_menu:20,
            pageTitle: 'Ajustes generales',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Ajustes generales',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/roles',
        name: 'admon-roles',
        component: () => import( /* webpackChunkName: "admon-roles" */  '@/views/Admon/Roles/Listado'),
        meta: {
            id_menu:21,
            resource: 'Roles',
            action: 'read',
            pageTitle: 'Roles',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Listado de roles',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/roles/registrar',
        name: 'admon-roles-registrar',
        component: () => import( /* webpackChunkName: "admon-roles" */  '@/views/Admon/Roles/Registrar'),
        meta: {
            id_menu:22,
            pageTitle: 'Registrar roles',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Roles',
                    active: false,
                    to:'/admon/roles'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/roles/actualizar/:id_rol',
        name: 'admon-roles-actualizar',
        component: () => import( /* webpackChunkName: "admon-roles" */  '@/views/Admon/Roles/Actualizar'),
        meta: {
            id_menu:23,
            pageTitle: 'Actualizar roles',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Roles',
                    active: false,
                    to:'/admon/roles'

                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/permisos',
        name: 'admon-permisos',
        component: () => import( /* webpackChunkName: "Permisos" */  '@/views/Admon/Permisos/Actualizar'),
        meta: {
            pageTitle: 'Permisos',
            requiresAuth: true,
            id_menu:7,
            breadcrumb: [
                {
                    text: 'Actualizar permisos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/sucursales',
        name: 'admon-sucursales',
        component: () => import( /* webpackChunkName: "admon-sucursales" */  '@/views/Admon/Sucursales/Listado'),
        meta: {
            pageTitle: 'Sucursales',
            requiresAuth: true,
            id_menu:25,
            breadcrumb: [
                {
                    text: 'sucursales',
                    active: false,
                },
            ],
        },
    },
    {
        path: '/admon/sucursales/registrar',
        name: 'admon-sucursales-registrar',
        component: () => import( /* webpackChunkName: "admon-sucursales" */  '@/views/Admon/Sucursales/Registrar'),
        meta: {
            pageTitle: 'Sucursales',
            requiresAuth: true,
            id_menu:26,
            breadcrumb: [
                {
                    text: 'Sucursales',
                    active: false,
                    to:'/admon/sucursales'
                },{
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/sucursales/actualizar/:id_sucursal',
        name: 'admon-sucursales-actualizar',
        component: () => import( /* webpackChunkName: "admon-sucursales" */  '@/views/Admon/Sucursales/Actualizar'),
        meta: {
            pageTitle: 'Sucursales',
            requiresAuth: true,
            id_menu:27,
            breadcrumb: [
                {
                    text: 'Sucursales',
                    active: false,
                    to:'/admon/sucursales'
                },{
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/login',
        name: 'login',
        component: () => import(/* webpackChunkName: "login" */'@/views/Login.vue'),
        meta: {
            pageTitle: 'Inicio de sesión',
            layout: 'full',
            requiresAuth: false
        },
    },
    {
        path: '/error-404',
        name: 'error-404',
        component: () => import(/* webpackChunkName: "error-404" */'@/views/error/Error404.vue'),
        meta: {
            layout: 'full',
            requiresAuth: false
        },
    },
    //Impuestos
    {
        path: '/admon/impuestos',
        name: 'admon-impuestos',
        component: () => import( /* webpackChunkName: "admon-impuestos" */  '@/views/Admon/Impuestos/Listado'),
        meta: {
            id_menu:28,
            pageTitle: 'Impuestos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Impuestos',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/impuestos/registrar',
        name: 'admon-impuestos-registrar',
        component: () => import( /* webpackChunkName: "admon-impuestos" */  '@/views/Admon/Impuestos/Registrar'),
        meta: {
            id_menu:29,
            pageTitle: 'Impuestos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Impuestos',
                    active: true,
                    to: '/admon/impuestos'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/impuestos/actualizar/:id_impuesto',
        name: 'admon-impuestos-actualizar',
        component: () => import( /* webpackChunkName: "admon-impuestos" */  '@/views/Admon/Impuestos/Actualizar'),
        meta: {
            id_menu:30,
            pageTitle: 'Impuestos',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Impuestos',
                    active: true,
                    to: '/admon/impuestos'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    // Zonas
    {
        path: '/admon/zonas',
        name: 'admon-zonas',
        component: () => import( /* webpackChunkName: "admon-zonas" */  '@/views/Admon/Zonas/Listado'),
        meta: {
            id_menu:31,
            pageTitle: 'Zonas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Zonas',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/zonas/registrar',
        name: 'admon-zonas-registrar',
        component: () => import( /* webpackChunkName: "admon-zonas" */  '@/views/Admon/Zonas/Registrar'),
        meta: {
            id_menu:32,
            pageTitle: 'Zonas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Zonas',
                    active: false,
                    to : '/admon/zonas'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/zonas/actualizar/:id_zona',
        name: 'admon-zonas-actualizar',
        component: () => import( /* webpackChunkName: "admon-zonas" */  '@/views/Admon/Zonas/Actualizar'),
        meta: {
            id_menu:33,
            pageTitle: 'Zonas',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Zonas',
                    active: false,
                    to : '/admon/zonas'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
    //sectores
    {
        path: '/admon/sectores',
        name: 'admon-sectores',
        component: () => import( /* webpackChunkName: "admon-sectores" */  '@/views/Admon/Sectores/Listado'),
        meta: {
            id_menu:145,
            pageTitle: 'Sectores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Sectores',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/sectores/registrar',
        name: 'admon-sectores-registrar',
        component: () => import( /* webpackChunkName: "admon-sectores" */  '@/views/Admon/Sectores/Registrar'),
        meta: {
            id_menu:146,
            pageTitle: 'Sectores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Sectores',
                    active: false,
                    to: '/admon/sectores'
                },
                {
                    text: 'Registrar',
                    active: true,
                },
            ],
        },
    },
    {
        path: '/admon/sectores/actualizar/:id_sector',
        name: 'admon-sectores-actualizar',
        component: () => import( /* webpackChunkName: "admon-sectores" */  '@/views/Admon/Sectores/Actualizar'),
        meta: {
            id_menu:147,
            pageTitle: 'Sectores',
            requiresAuth: true,
            breadcrumb: [
                {
                    text: 'Sectores',
                    active: false,
                    to: '/admon/sectores'
                },
                {
                    text: 'Actualizar',
                    active: true,
                },
            ],
        },
    },
]
