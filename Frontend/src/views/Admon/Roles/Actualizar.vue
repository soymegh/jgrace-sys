<template>
    <div class="main">

        <b-card>
            <div class="form-group">
                <label for=""> * Descripción del rol:</label>
                <input class="form-control" v-model="form.descripcion" placeholder="Digita nombre del rol">
                <ul class="error-messages">
                    <li v-for="message in errorMessages.descripcion" :key="message" v-text="message"></li>
                </ul>
            </div>

            <b-card-footer>
                <div class="col-md-6">
                    <div class="content-box-footer text-left">
                        <template v-if="form.estado">
                            <!--                            <button :disabled="form.id_rol === 1" class="btn btn-danger"-->
                            <!--                                    @click="desactivarRol(form.id_rol)"><i class="fa fa-trash-o"> Inhabilitar</i></button>-->

                            <b-button :disabled="form.id_rol === 1" class="btn btn-danger"
                                      @click="desactivarRol(form.id_rol)">
                                <feather-icon icon="Trash2Icon"></feather-icon>
                                Inhabilitar
                            </b-button>
                        </template>
                        <template v-else>
                            <b-button :disabled="form.id_rol === 1" class="btn btn-success"
                                      @click="activarRol(form.id_rol)">
                                <feather-icon icon="CheckCircleIcon"></feather-icon>
                                Habilitar
                            </b-button>
                        </template>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="content-box-footer text-right">
                        <router-link :to="{name: 'admon-roles'}">
                            <b-button type="submit" variant="secondary" class="mx-1">
                                Cancelar
                            </b-button>
                        </router-link>


                        <b-button type="submit" variant="primary" @click="actualizarRol"
                                  :disabled="btnAction !== 'Guardar'">
                            {{btnAction}}
                        </b-button>
                    </div>
                </div>


                <template v-if="loading">
                    <BlockUI :url="url"></BlockUI>
                </template>
            </b-card-footer>

        </b-card>
    </div>

</template>

<script type="text/ecmascript-6">
    import rol from '../../../api/admon/roles'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BButton, BAlert, BCard, BCardFooter} from 'bootstrap-vue'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import Ripple from 'vue-ripple-directive'

    export default {
        components: {
            BButton,
            BAlert,
            BCard,
            BCardFooter
        },
        directives: {
            Ripple,
        },
        data() {
            return {
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                roles: [],
                form: {
                    descripcion: ''
                },
                btnAction: 'Guardar',
                errorMessages: []
            }
        },
        methods: {
            desactivarRol(rolId) {
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
                        var self = this
                        this.$swal({
                            icon: 'success',
                            title: 'Inhabilitado!',
                            text: 'El registro ha sido inhabilitado correctamente.',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                        self.loading = true
                        rol.desactivarRol({
                            id_rol: rolId
                        }, data => {
                            self.loading = false
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Registro desactivado correctamente',
                                    variant: 'success',
                                    position: 'top-center'
                                }
                            });
                            this.$router.push({name: 'admon-roles'})
                        }, err => {
                            self.loading = false
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'BellIcon',
                                    text: 'Ha ocurrido un error',
                                    variant: 'warning',
                                    position: 'top-center'
                                }
                            });
                            console.log(err)
                        })
                    } else {

                        this.btnAction = "Guardar";
                    }
                })
            },
            activarRol(rolId) {
                var self = this;
                self.$swal.fire({
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
                        var self = this
                        self.loading = true;
                        rol.activarRol({
                            id_rol: rolId
                        }, data => {

                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Registro activado correctamente',
                                    variant: 'success',
                                    position: 'top-center'
                                }
                            });
                            this.$router.push({name: 'admon-roles'})
                            self.loading = false
                        }, err => {
                            self.loading = false
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'BellIcon',
                                    text: 'Ha ocurrido un error',
                                    variant: 'warning',
                                    position: 'top-center'
                                }
                            });
                            console.log(err)
                        })
                    } else {

                        this.btnAction = "Guardar";
                    }
                })
            },
            obtenerRolEspecifico() {
                var self = this
                rol.obtenerRolEspecifico({
                    id_rol: this.$route.params.id_rol
                }, data => {
                    self.form = data
                    self.loading = false;
                }, err => {
                    //console.log(err)
                    alertify.error(err.id_rol[0], 5);
                    this.$router.push({
                        name: 'admon-roles'
                    });
                })
            },
            actualizarRol() {
                var self = this;
                self.loading = true;
                self.btnAction = 'Guardando, espere......'

                rol.actualizarRol(self.form, data => {
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Registro desactivado correctamente',
                            variant: 'success',
                            position: 'top-center'
                        }
                    });
                    this.$router.push({name: 'admon-roles'})
                    self.loading = false;
                }, err => {
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            }
        },
        mounted() {
            this.obtenerRolEspecifico()
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>
