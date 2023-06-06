import axios from "axios";
export default {
    obtenerPermisos(data, cb, errorCb) {
        axios.post('admon/permisos/obtener-permisos', data)
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
    guardarPermisos(data, cb, errorCb) {
        axios.post('admon/permisos/guardar', data)
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
