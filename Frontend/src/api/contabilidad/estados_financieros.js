import axios from "axios";
export default {
    obtenerTodosEstadosFinacieros(cb, errorCb) {
        axios.get('contabilidad/estados-financieros/obtener-estados-financieros-todas')
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
    obtenerEstadosFinacierosLista(cb, errorCb) {
        axios.post('contabilidad/estados-financieros/obtener-estados-financieros-lista')
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


    obtenerEstadosFinacieros(data, cb, errorCb) {
        axios.post('contabilidad/estados-financieros/obtener-estados-financieros', data)
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
    obtenerEstadoFinaciero(data, cb, errorCb) {
        axios.post('contabilidad/estados-financieros/obtener-estado-financiero-contable', data)
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

}