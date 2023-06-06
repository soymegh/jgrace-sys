import axios from "axios";
export default {
    obtener(data, cb, errorCb) {
        axios.post('inventario/obtener-configuracion', data)
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
    actualizar(data, cb,resp, errorCb) {
        axios.put('inventario/actualizar-configuracion', data)
            .then(function (response) {
                if (response.data.status === 'success') {
                    cb(response.data.result)
                } else if(response.data.status === 'array_empty') {
                    resp(response.data)
                } else {
                    errorCb(response.data.result)
                }
            })
            .catch(function (error) {
                errorCb(error)
            })
    },
}