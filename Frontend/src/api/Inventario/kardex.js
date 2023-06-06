import axios from "axios";
export default {
    obtenerMovimientos(data, cb, errorCb) {
        axios.post('inventario/kardex/obtener-por-producto', data)
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
    obtenerListaCodigos(data, cb, errorCb) {
        axios.post('inventario/kardex/obtener-lista-codigos', data)
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
