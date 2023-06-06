import axios from "axios";
export default {
    obtener(data, cb, errorCb) {
        axios.post('cuentas-cobrar/roc/obtener', data)
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
    obtenerRecibosCliente(data, cb, errorCb) {
        axios.post('cuentas-cobrar/roc/obtener-recibos-cliente', data)
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
    registrar(data, cb, errorCb) {
        axios.post('cuentas-cobrar/roc/registrar', data)
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

    registrarROCTrabajador(data, cb, errorCb) {
        axios.post('cuentas-cobrar/roc/empleado/registrar', data)
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

    nuevo(data, cb, errorCb) {
        axios.post('cuentas-cobrar/roc/nuevo', data)
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
    obtenerRecibo(data, cb, errorCb) {
        axios.post('cuentas-cobrar/roc/obtener-recibo', data)
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
    cancelar(data, cb, errorCb) {
        axios.post('cuentas-cobrar/roc/cancelar', data)
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
