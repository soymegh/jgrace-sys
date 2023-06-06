import axios from "axios";
export default {
    obtenerCatalago( cb, errorCb) {
        axios.get('contabilidad/obtener-varios')
            .then(function (response) {
                if (response.data.status === 'success') {
                    cb(response.data)
                } else {
                    errorCb(response.data)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    }
}
