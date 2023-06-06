<template>
    <b-card>
        <b-row>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">* Nombre del país</label>
                    <input type="text" class="form-control" placeholder="Nombre del país" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">* Codigo del país</label>
                    <input type="text" class="form-control" placeholder="Codigo del país" v-model="form.codigo">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.codigo">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
        </b-row>
            <b-card-footer>
                <b-row>
                    <div class="col-md-6 col-12 text-lg-left text-center mt-1">
                        <template v-if="form.estado === 1">
                            <b-button class="btn btn-default mx-1" variant="danger" @click="desactivar(form.id_pais)">
                                Inhabilitar
                            </b-button>
                        </template>
                        <template v-else>
                            <b-button class="btn btn-default mx-1" variant="info" @click="activar(form.id_pais)">
                                Habilitar
                            </b-button>
                        </template>

                    </div>
                    <div class="col-md-6 col-12 text-lg-right text-center mt-1">
                        <router-link :to="{name: 'admon-paises'}">
                            <b-button class="btn btn-default mx-1" variant="secondary">Cancelar</b-button>
                        </router-link>

                        <b-button :disabled="btnAction !== 'Guardar' ? true : false" @click="actualizar"
                                  variant="primary">
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
    import {
        BFormDatepicker,
        BRow,
        BCol,
        BCard,
        BCardFooter,
        BPaginationNav,
        BButton,
        VBTooltip,
        BAlert
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import pais from '../../../api/admon/paises'
    import rol from '../../../api/admon/roles'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    export default {
        components: {
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, BAlert,
            vSelect
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                form: {
                    descripcion: '',
                    codigo: ''
                },
                btnAction: 'Guardar',
                errorMessages: []
            }
        },
        methods: {
            obtenerPais() {
                var self = this
                pais.obtenerPais({
                    id_pais: self.$route.params.id_pais
                }, data => {
                    self.form = data
                    self.loading = false;
                }, err => {
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'BellIcon',
                            text: 'Ha ocurrido un error',
                            variant: 'warning',
                            position: 'bottom-right'
                        }
                    });
                    this.$router.push({
                        name: 'admon-paises'
                    });
                })


            },
            actualizar() {
                var self = this;
                self.loading = true;
                self.btnAction = 'Guardando, espere......';
                pais.actualizar(self.form, data => {
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'País actualizado correctamente',
                            variant: 'success',
                            position: 'bottom-right'
                        }
                    },
                        {
                            position: 'bottom-right'
                        }
                    );
                    this.$router.push({
                        name: 'admon-paises'
                    })
                }, err => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'BellIcon',
                            text: 'Ha ocurrido un error',
                            variant: 'warning',

                        }
                    },
                        {
                            position: 'bottom-right'
                        });
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            },
            desactivar(paisId) {

                var self = this;
                this.$swal({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    showCloseButton: true,
                    focusConfirm: false,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ml-1',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        this.loading = true;
                        pais.desactivar({
                            id_pais: paisId
                        }, data => {
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'País desactivado correctamente',
                                    variant: 'success',

                                }
                            },
                                {
                                    position: 'bottom-right'
                                });
                            this.loading = false;
                            this.$router.push({
                                name: 'admon-paises'
                            })
                        }, err => {
                            this.loading = false;
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'BellIcon',
                                    text: 'Ha ocurrido un error',
                                    variant: 'warning',
                                }
                            },
                                {
                                    position: 'bottom-right'
                                });
                            console.log(err)
                        });
                    } else {
                        self.btnAction = "Guardar";
                    }
                })
            },
            activar(paisId) {

                var self = this;
                self.$swal({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    showCloseButton: true,
                    focusConfirm: false,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ml-1',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        this.loading = true;
                        pais.activar({
                            id_pais: paisId
                        }, data => {
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'País activado correctamente',
                                    variant: 'success',

                                }
                            },
                                {
                                    position: 'bottom-right'
                                });
                            this.loading = false
                            this.$router.push({
                                name: 'admon-paises'
                            })
                        }, err => {
                            this.loading = false;
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'BellIcon',
                                    text: 'Ha ocurrido un error',
                                    variant: 'warning',
                                }
                            },
                                {
                                    position: 'bottom-right'
                                });
                            console.log(err)
                        });
                    } else {
                        self.btnAction = "Guardar";
                    }
                })
            }
        },
        mounted() {
            this.obtenerPais();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>

