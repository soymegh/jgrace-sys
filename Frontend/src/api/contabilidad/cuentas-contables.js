import axios from "axios";
export default {
    obtenerTodasCuentasContables(cb, errorCb) {
        axios.get('contabilidad/cuentas-contables/obtener-cuentas-contables-todas')
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

    obtenerCuentasNivel(data, cb, errorCb) {
        axios.post('contabilidad/cuentas-contables/obtener-cuentas-nivel', data)
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

    obtenerCuentasContables(data, cb, errorCb) {
        axios.post('contabilidad/cuentas-contables/obtener-cuentas-contables', data)
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
    obtenerCuentaContable(data, cb, errorCb) {
        axios.post('contabilidad/cuentas-contables/obtener-cuenta-contable', data)
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
    obtenerCuentaContableV(data, cb, errorCb) {
        axios.post('contabilidad/cuentas-contables/obtener-cuenta-contable-v', data)
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
    guardarCuentaContable(data, cb, errorCb) {
        axios.post('contabilidad/cuentas-contables/registrar', data)
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
    actualizarCuentaContable(data, cb, errorCb) {
        axios.put('contabilidad/cuentas-contables/actualizar', data)
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
    desactivarCuentaContable(data, cb, errorCb) {
        axios.put('contabilidad/cuentas-contables/desactivar', data)
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
    activarCuentaContable(data, cb, errorCb) {
        axios.put('contabilidad/cuentas-contables/activar', data)
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
    }
}
