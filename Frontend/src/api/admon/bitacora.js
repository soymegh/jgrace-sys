import axios from "axios";
export default {
    obtenerAccesos(data, cb, errorCb) {
        axios.post('bitacora/accesos/obtener-accesos', data)
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

    obtenerAccesosReporte(data, cb, errorCb) {
        axios.post('bitacora/accesos/obtener-accesos-reporte', data)
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
