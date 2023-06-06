import axios from "axios";
export default {
    obtener(data, cb, errorCb) {
        axios.post('cuentas-cobrar/obtener', data)
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
    obtenerCuentaPorCobrar(data, cb, errorCb) {
        axios.post('cuentas-cobrar/obtener-cc', data)
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
    obtenerCuentasCliente(data, cb, errorCb) {
        axios.post('cuentas-cobrar/obtener-cuentas-cliente', data)
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
    obtenerCuentasTrabajador(data, cb, errorCb) {
        axios.post('cuentas-cobrar/obtener-cuentas-trabajador', data)
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
    subirExcel(data, cb, errorCb) {
        axios.post('cuentas-cobrar/cuentasxcobrar/importar', data,{
            header:{
                'Content-Type' : 'multipart/form-data'
            }
        })
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
    registrarCuentasxCobrar(data, cb, errorCb) {
        axios.post('cuentas-cobrar/cuentasxcobrar/registrar-importacion', data)
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
