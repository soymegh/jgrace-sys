export default {
    obtenerTodas(cb, errorCb) {
        axios.get('nivel-academico/obtener-todas')
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
        axios.post('nivel-academico/obtener', data)
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
    obtenerNivelAcademico(data, cb, errorCb) {
        axios.post('nivel-academico/obtener-nivel', data)
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
        axios.post('nivel-academico/registrar', data)
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
        axios.put('nivel-academico/actualizar', data)
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
        axios.put('nivel-academico/desactivar', data)
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
        axios.put('nivel-academico/activar', data)
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