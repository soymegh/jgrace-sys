<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Tipo Cuenta:</label>
                        <select
                                class="form-control" v-model.number="form.tipo_cuenta"
                        >
                            <option value="1">Cuenta Corriente</option>
                            <option value="2">Déposito a plazos</option>
                            <option value="3">Cuenta de Ahorro</option>
                        </select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.tipo_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Banco:</label>
                        <v-select style="width: 100%" v-model="form.banco_cuenta_bancaria" :options="bancos" label="descripcion">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.banco">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Moneda:</label>
                        <v-select style="width: 100%" v-model="form.moneda_cuenta_bancaria" :options="monedas" label="descripcion">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.moneda">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Número Cuenta:</label>
                        <input type="text" class="form-control" v-model="form.numero_cuenta" placeholder="Dígite el número de cuenta">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.numero_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Número Cuenta:</label>
                        <input type="number" min="0" class="form-control" v-model="form.numeracion_chequera" placeholder="Dígite la numeracion actual de la chequera">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.numeracion_chequera">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Cuenta Contable:</label>
                        <v-select style="width: 100%" v-model="form.cuenta_contable_cuenta_bancaria" :options="cuentas_contables" label="nombre_cuenta_completo">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.cuenta_contable">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <b-row>
                <div class="col-md-6 text-left p-0">
                    <template v-if="form.estado">
                        <b-button type="submit" variant="danger" @click="desactivar(form.id_cuenta_bancaria)">
                            <feather-icon icon="Trash2Icon"></feather-icon> Eliminar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="success" @click="activar(form.id_cuenta_bancaria)">
                            <feather-icon icon="CheckIcon"></feather-icon> Habilitar
                        </b-button>
                    </template>

                </div>
                <div class="col-md-6">
                    <router-link  :to="{name: 'contabilidad-cuentas-bancarias'}">
                        <b-button type="submit" variant="secondary" class="mx-1">
                            Cancelar
                        </b-button>
                    </router-link>


                    <b-button type="submit" variant="primary" @click="actualizar" :disabled="btnAction !== 'Actualizar'">
                        {{btnAction}}
                    </b-button>
                </div>
            </b-row>

        </b-card-footer>
        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BButton,BAlert,BFormCheckbox,BFormSelect,BCard,BCardFooter, BRow} from 'bootstrap-vue'
    import Ripple from 'vue-ripple-directive'
    import vSelect from 'vue-select'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import cuenta_bancaria from "../../../api/contabilidad/cuentas-bancarias";
    import axios from 'axios'

    export default {
        components:{
            BButton,
            BAlert,
            BFormCheckbox,
            vSelect,
            BFormSelect,
            BCard,
            BCardFooter,
            BRow,
        },
        directives : {
            Ripple,
        },
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                cuentas_contables:[],
                bancos:[],
                monedas:[],
                form: {
                    cuenta_bancaria: '',
                    banco_cuenta_bancaria: '',
                    moneda_cuenta_bancaria: '',
                    cuenta_contable_cuenta_bancaria: '',
                },
                btnAction: 'Actualizar',
                errorMessages: [],

            }
        },
        methods: {

            obtenerCuentaBancaria(){
                var self = this;
                cuenta_bancaria.obtenerCuentaBancaria({
                    id_cuenta_bancaria: this.$route.params.id_cuenta_bancaria
                }, data =>{
                    self.form = data.cuenta_bancaria;
                    self.bancos = data.bancos;
                    self.cuentas_contables = data.cuentas_contables;
                    self.monedas = data.monedas;
                    self.loading = false;
                }, err =>{
                    this.$toast({
                            component : ToastificationContent,
                            props : {
                                title : 'Notificación',
                                icon : 'InfoIcon',
                                text : 'Ha ocurrido un error al cargar los datos',
                                variant : 'warning',
                            }
                        },
                        {
                            position : 'bottom-rigth'
                        });
                    this.$router.push({
                        name : 'contabilidad-cuentas-bancarias'
                    })
                })
            },

            actualizar(){
                var self = this
                self.btnAction = 'Guardando, espere....'
                self.loading = true;
                cuenta_bancaria.actualizar(self.form, data =>{
                        self.loading = false;
                        this.$toast({
                                component : ToastificationContent,
                                props: {
                                    title : 'Notificación',
                                    icon : 'CheckIcon',
                                    text : 'Datos actualizados correctamente.',
                                    variant : 'success',
                                }
                            },
                            {
                                position : 'bottom-right'
                            });
                        this.$router.push({
                            name : 'contabilidad-cuentas-bancarias'
                        })
                    },
                    err =>{
                        self.loading = false;
                        self.errorMessages = err
                        self.btnAction = 'Guardar'
                        this.$toast({
                            component: ToastificationContent,
                            props: {
                                title : 'Notificación',
                                icon : 'InfoIcon',
                                text : 'Ha ocurrido un error, intentelo de nuevo',
                                variant : 'warning',
                                position : 'bottom-right'
                            }
                        });
                        /*this.$router.push({
                            name : 'inventario-unidades-medida'
                        })*/
                    })
            },

            activar(cuentaBancariaID){
                var self = this;
                self.$swal({
                    title: '¿Esta seguro?',
                    text: '¿ Desea activar la cuenta bancaria?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText : 'Si, confirmar',
                    cancelButtonText : 'Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton : 'btn btn-danger ml-1',
                    },
                    buttonsStyling : false,
                }).then((result) => {
                    if (result.value){
                        self.$swal({
                            icon :'success',
                            title : '¡Habilitado!',
                            text : '¡Cuenta bancaria habilitad!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        cuenta_bancaria.activar({
                            id_cuenta_bancaria : cuentaBancariaID
                        }, data =>{
                            this.$router.push({
                                name : 'contabilidad-cuentas-bancarias'
                            })
                        }, err =>{
                            console.log(err);
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title : 'Notificación',
                                        icon : 'InfoIcon',
                                        text : 'Ha ocurrido un error, intentelo de nuevo',
                                        variant : 'warning',
                                    }
                                },
                                {
                                    position : 'bottom-right'
                                });
                            this.$router.push({
                                name : 'contabilidad-cuentas-bancarias'
                            })
                        })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'La cuenta bancaria no ha sido habilitada',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }

                })
            },

            desactivar(cuentaBancariaID){
                var self = this;
                self.$swal({
                    title: 'Info!',
                    text: '¿Esta seguro de desactivar la cuenta bancaria?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText : 'Si, confirmar',
                    cancelButtonText : 'Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton : 'btn btn-danger ml-1',
                    },
                    buttonsStyling : false,
                }).then((result) => {
                    if (result.value){
                        self.$swal({
                            icon :'success',
                            title : '¡Eliminado!',
                            text : '¡Cuenta bancaria eliminada!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        cuenta_bancaria.desactivar({id_cuenta_bancaria: cuentaBancariaID
                            },
                            data =>{
                                this.$router.push({
                                    name : 'contabilidad-cuentas-bancarias'
                                })
                            }, err =>{
                                console.log(err);
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title : 'Notificación',
                                            icon : 'InfoIcon',
                                            text : 'Ha ocurrido un error, intentelo de nuevo',
                                            variant : 'warning',
                                        }
                                    },
                                    {
                                        position : 'bottom-right'
                                    });
                                this.$router.push({
                                    name : 'contabilidad-cuentas-bancarias'
                                })
                            })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'La cuenta bancaria no ha sido eliminada',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }
                })
            },

        },
        mounted() {
            this.obtenerCuentaBancaria();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>
