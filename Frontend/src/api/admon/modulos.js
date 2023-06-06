import axios from "axios";
export default {
    obtenerModulos(cb, errorCb) {
        axios.get('admon/modulos/obtener')
            .then(function (response) {
                if (response.data.status === 'success') {
                    cb(response.data.result)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
    VerificarPermisos(data,cb, errorCb) {
            axios.post('admon/menus/verificar', data)
                .then(function (response) {
                    if (response.data.status === 'success') {
                        cb(response.data.result)
                    } else {
                        errorCb(response.data.result)
                    }
                })
                .catch(function (error) {
                    errorCb(error)
                })
    },
     getUser(cb, errorCb) {
        axios.get('/me')
            .then(function (response) {
                if (response.data.status === 'success') {
                    cb(response.data.result)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
    obtenerMenusTodos(cb, errorCb) {
        axios.get('admon/menu/obtener-menus-todos')
            .then(function (response) {
                if (response.data.status === 'success') {
                    cb(response.data.result)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
}
