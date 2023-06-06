import axios from "axios";

export default {
    obtenerSalidas(data, cb, errorCb) {
        axios.post('salidas/obtener', data)
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
    registrarBateria(data, cb, errorCb) {
        axios.post('salidas/registrar-bateria', data)
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
    anular(data, cb, errorCb) {
        axios.put('salidas/anular', data)
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
    registrar(data, cb, errorCb) {
        axios.post('salidas/registrar', data)
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
    guardar(data, cb, errorCb) {
        axios.post('salidas/guardar', data)
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
    registrarSalidaManual(data, cb, errorCb) {
        axios.post('salidas/registrar-manual', data)
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
    nueva(data, cb, errorCb) {
        axios.post('salidas/nueva', data)
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
    registrarTraslado(data, cb, errorCb) {
        axios.post('salidas/registrar-traslado', data)
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

    registrarTrasladoConsignacion(data, cb, errorCb) {
        axios.post('salidas/registrar-traslado-consignacion', data)
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

    registrarDevolucionConsignacion(data, cb, errorCb) {
        axios.post('salidas/registrar-devolucion-consignacion', data)
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

    registrarDevolucion(data, cb, errorCb) {
        axios.post('salidas/registrar-devolucion', data)
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


    despachar(data, cb, errorCb) {
        axios.put('salidas/despachar', data)
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
    crearSalidaPorDevolucion(data, cb, errorCb) {
        axios.post('salidas/crear-salida-devolucion', data)
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
    obtenerSalida(data, cb, errorCb) {
        axios.post('salidas/obtener-salida', data)
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
    obtenerSalidaPorCodigo(data, cb, errorCb) {
        axios.post('salidas/obtener-salida-por-codigo', data)
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

    nuevaSalidaRecuperados(data, cb, errorCb) {
        axios.post('salidas/nuevasalida-recuperado', data)
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

    registrarTrasladoRecuperado(data, cb, errorCb) {
        axios.post('salidas/registrar-traslado-recuperado', data)
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


    obtenerNumeroSalida(cb, errorCb) {
        axios.get('salidas/obtener-numero-salida')
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
    cambiarEstado(data, cb, errorCb) {
        axios.put('salidas/cambiar-estado', data)
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
