export default {
    obtenerTodas(cb, errorCb) {
        axios.get('control-planilla/obtener-todas')
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
        axios.post('control-planilla/obtener', data)
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
    obtenerContratoTipo(data, cb, errorCb) {
        axios.post('control-planilla/obtener-datos', data)
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
        axios.post('zonas/registrar', data)
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
        axios.put('zonas/actualizar', data)
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
        axios.put('zonas/desactivar', data)
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
        axios.put('zonas/activar', data)
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