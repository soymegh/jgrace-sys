export default {
    obtener(data, cb, errorCb) {
        axios.post('solicitud-viatico/obtener', data)
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
    obtenerSolicitud(data, cb, errorCb) {
        axios.post('solicitud-viatico/obtener-viatico', data)
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
    nuevo(data, cb, errorCb) {
        axios.post('solicitud-viatico/nuevo', data)
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
        axios.post('solicitud-viatico/registrar', data)
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
        axios.put('solicitud-viatico/actualizar', data)
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
        axios.put('solicitud-viatico/desactivar', data)
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
        axios.put('solicitud-viatico/activar', data)
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