import axios from "axios";
export default {
    obtenerTodosNivelesCuenta(cb, errorCb) {
        axios.get('contabilidad/niveles-cuentas/obtener-niveles-cuenta-todas')
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
    obtenerNivelesCuenta(data, cb, errorCb) {
        axios.post('contabilidad/niveles-cuentas/obtener-niveles-cuenta', data)
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
    obtenerNivelCuenta(data, cb, errorCb) {
        axios.post('contabilidad/niveles-cuentas/obtener-nivel-cuenta', data)
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

    actualizar(data, cb, errorCb) {
        axios.put('contabilidad/niveles-cuentas/actualizar', data)
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
