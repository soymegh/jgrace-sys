import axios from "axios";
export default {
    obtenerTodosTiposSalida(cb, errorCb) {
        axios.get('inventario/tipos-salidas/obtener-tipos-salida-todos')
            .then(function (response) {
                if (response.data.status == 'success') {
                    cb(response.data.result)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
    obtener(data, cb, errorCb) {
        axios.post('inventario/tipos-salidas/obtener', data)
            .then(function (response) {
                if (response.data.status == 'success') {
                    cb(response.data.result)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
    obtenerTipoSalida(data, cb, errorCb) {
        axios.post('inventario/tipos-salidas/obtener-tipo-salida', data)
            .then(function (response) {
                if (response.data.status == 'success') {
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
        axios.post('inventario/tipos-salidas/registrar', data)
            .then(function (response) {
                if (response.data.status == 'success') {
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
        axios.put('inventario/tipos-salidas/actualizar', data)
            .then(function (response) {
                if (response.data.status == 'success') {
                    cb(response.data.result)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
    desactivar(data, cb, errorCb) {
        axios.put('inventario/tipos-salidas/desactivar', data)
            .then(function (response) {
                if (response.data.status == 'success') {
                    cb(response.data.result)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
    activar(data, cb, errorCb) {
        axios.put('inventario/tipos-salidas/activar', data)
            .then(function (response) {
                if (response.data.status == 'success') {
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