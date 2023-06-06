import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store/index'
import admon from "@/router/routes/admon";
import bitacora from "@/router/routes/bitacora";
import modulos from '../api/admon/modulos'
import contabilidad from "@/router/routes/contabilidad";
import inventario from "@/router/routes/inventario";
import ventas from "@/router/routes/ventas";
import cajaBanco from "@/router/routes/cajabanco";
import cuentasPorcobrar from "@/router/routes/cuentasxcobrar";


Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    // eslint-disable-next-line no-unused-vars
    scrollBehavior(to, from, savedPosition) { // save scroll position to return a page at this point
        //  console.log('to', to)
        // console.log('from', from)
        // console.log('savedPosition', savedPosition)
        // eslint-disable-next-line no-unused-vars
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve(savedPosition || {left: 0, top: 0})
            }, 1000)
        })
    },
    routes: [
        {
            path: '/',
            redirect: {name: 'home'},
            meta: {
                id_menu: 1,
            }
        },
        ...admon,
        ...contabilidad,
        ...bitacora,
        ...inventario,
        ...ventas,
        ...cajaBanco,
        ...cuentasPorcobrar,


        {
            path: '*',
            redirect: 'error-404',
        },
        {
            path: '/error-permiso',
            name: 'error-permiso',
            component: () => import( /* webpackChunkName: "admon-ajustes" */  '@/views/error/NotAuthorized'),
            meta: {
                pageTitle: 'Sin permisos',
                id_menu: 12,
                layout: 'full',
                requiresAuth: false,
            },
        },
        {
            path: '/forgot-password',
            name: 'forgot-password',
            component: () => import( /* webpackChunkName: "admon-forgot-password" */  '@/views/Admon/Misc/ForgotPassword-v1'),
            meta: {
                pageTitle: 'Olvide mi contraseña',
                id_menu: 12,
                layout: 'full',
                requiresAuth: false,
            },
        },
        {
            path: '/reset-password/',
            name: 'reset-password',
            component: () => import( /* webpackChunkName: "admon-reset-password" */  '@/views/Admon/Misc/ResetPassword-v1'),
            meta: {
                pageTitle: 'Resetear contraseña',
                id_menu: 12,
                layout: 'full',
                requiresAuth: false,
            },
        },
    ],
});

router.beforeEach((to, from, next) => {
    //const requiresAuth = to.matched.some(record => record.meta.requiresAuth) //
    //
    // async // agregar async al beforeEach en caso de problemas


    const logged = store.state.auth.auth;
    // console.log(logged);

    //bloque de codigo original para tener usuario

    /*    const logged =  await axios.get('/me').then(function (response) {
            return response.data.result
        }).catch(function (err) {
             console.log(err)
        });*/

    //console.log(logged);

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (logged && to.name !== 'login') {
            modulos.VerificarPermisos({id_menu: to.meta.id_menu},
                data => {
                    if (data.status === 'success') {
                        next();
                    } else if (data.status === 'error') {
                        next({name: 'error-permiso'});
                    } else if (data.message === "Request failed with status code 401") {
                        next({name: 'login'});
                    }else if (data.message === "Request failed with status code 419") {
                        next({name: 'login'});
                    } else {
                        next();
                    }
                }, err => {
                    // console.log(err.message);
                    if (err === 'sin permiso') {
                        next({name: 'error-permiso'});
                    } else if (err.message === 'Request failed with status code 401') {
                        next({name: 'login'});
                    } else if (err.message === "CSRF token mismatch.") {
                        next({name: 'login'});
                    } else if (err.message === "Request failed with status code 419") {
                        next({name: 'login'});
                    } else {
                        next('/');
                    }


                })
            // console.log('está autenticado >>' + store.state.auth.user + '<<');
        } else if (!logged) {
            // console.log('No está autenticado >>' + store.state.auth.user + '<<');
            sessionStorage.setItem('redirectPath', to.path);
            next('/login');
        } else {
            next();
        }
    } else {
        /*        if (logged && to.name === 'login') {
                    next({name: 'home'});
                } else {
                    // console.log('No requiere auth >>' + store.state.auth.user + '<<');
                    next();
                }*/
        next();
    }

    /*    console.log(to);
        console.log(from);
        console.log(next);*/
});

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
    // Remove initial loading
    const appLoading = document.getElementById('loading-bg');
    if (appLoading) {
        appLoading.style.display = 'none'
    }
});

router.afterEach((to, from) => {
    document.title = '' + to.meta.pageTitle;
});


export default router
