<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="name">* Descripcrión:</label>
                        <input type="text" class="form-control" v-model="form.descripcion" placeholder="Dígite la descripción">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.descripcion">{{ message }}</li>
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
                        <b-button type="submit" variant="danger" @click="desactivar(form.id_tipo_bodega)">
                            <feather-icon icon="Trash2Icon"></feather-icon> Eliminar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="success" @click="activar(form.id_tipo_bodega)">
                            <feather-icon icon="CheckIcon"></feather-icon> Habilitar
                        </b-button>
                    </template>

                </div>
                <div class="col-md-6">
                    <router-link  :to="{name: 'inventario-tipos-bodegas'}">
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
    import tipo_bodega from "../../../api/Inventario/tipo_bodega";
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
                form: {
                    descripcion : ''

                },
                btnAction: 'Actualizar',
                errorMessages: [],

            }
        },
        methods: {

            obtenerTipoBodega(){
                var self = this;
                tipo_bodega.obtenerTipoBodega({
                    id_tipo_bodega: this.$route.params.id_tipo_bodega
                }, data =>{
                    self.form = data;
                    self.loading = false;
                }, err =>{
                    this.$toast({
                        component : ToastificationContent,
                        props : {
                            title : 'Notificación',
                            icon : 'InfoIcon',
                            text : 'Ha ocurrido un error al cargar los datos',
                            variant : 'warning',
                            position : 'bottom-rigth'
                        }
                    });
                    this.$router.push({
                        name : 'inventario-tipos-bodegas'
                    })
                })
            },

            actualizar(){
                var self = this
                self.btnAction = 'Guardando, espere....'
                self.loading = true;
                tipo_bodega.actualizar(self.form, data =>{
                        self.loading = false;
                        this.$toast({
                            component : ToastificationContent,
                            props: {
                                title : 'Notificación',
                                icon : 'CheckIcon',
                                text : 'Datos actualizados correctamente.',
                                variant : 'success',
                                position : 'bottom-right'
                            }
                        });
                        this.$router.push({
                            name : 'inventario-tipos-bodegas'
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

            activar(tipobodegaID){
                var self = this;
                self.$swal({
                    title: '¿Esta seguro?',
                    text: '¿ Desea activar el tipo de bodega?',
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
                            title : '¡Habilitada!',
                            text : '¡Tipo bodega habilitada!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        tipo_bodega.activar({
                            id_tipo_bodega: tipobodegaID
                        }, data =>{
                            this.$router.push({
                                name : 'inventario-tipos-bodegas'
                            })
                        }, err =>{
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
                            this.$router.push({
                                name : 'inventario-tipos-bodegas'
                            })
                        })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'La unidad de medida no ha sido habilitada',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }

                })
            },

            desactivar(tipobodegaID){
                var self = this;
                self.$swal({
                    title: 'Info!',
                    text: '¿Esta seguro de desactivar el tipo de bodega?',
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
                            title : '¡Eliminada!',
                            text : '¡Tipo de bodega eliminada!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        tipo_bodega.desactivar({id_tipo_bodega: tipobodegaID
                            },
                            data =>{
                                this.$router.push({
                                    name : 'inventario-tipos-bodegas'
                                })
                            }, err =>{
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
                                this.$router.push({
                                    name : 'inventario-tipos-bodegas'
                                })
                            })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'El tipo de bodega no ha sido eliminada',
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
            this.obtenerTipoBodega();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>
