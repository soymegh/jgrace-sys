<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-3 form-group">
                    <label for="name">* Descripción:</label>
                    <input type="text" class="form-control" placeholder="Dígite el nombre de la bodega" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name"> Sucursal:</label>
                        <v-select v-model="form.sucursal_bodega"
                                  label="descripcion"
                                  :options="sucursales"

                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.sucursal_bodega">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <!--<div class="col-sm-3">
                    <div class="form-group">
                        <label for="name"> Centro de Costo:</label>
                        <v-select v-model="form.centros_costos_ingresos"
                                  label="descripcion"
                                  :options="centros_costos_ingresos"
                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.centros_costos_ingresos">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>-->
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name"> Tipo Bodega:</label>
                        <v-select v-model="form.tipo_bodega"
                                  label="descripcion"
                                  :options="tipos_bodegas"
                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.tipo_bodega">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-lg-3 form-inline">
                    <div class="form-group">
                        <label class="check-label2"><input v-model="form.permite_venta" type="checkbox" class="mx-sm-1">   Bodega Permite Ventas</label>
                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <b-row>
                <div class="col-md-6 text-left p-0">
                    <template v-if="form.estado">
                        <b-button type="submit" variant="danger" @click="desactivar(form.id_bodega)">
                            <feather-icon icon="Trash2Icon"></feather-icon> Eliminar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="success" @click="activar(form.id_bodega)">
                            <feather-icon icon="CheckIcon"></feather-icon> Habilitar
                        </b-button>
                    </template>

                </div>
                <div class="col-md-6">
                    <router-link  :to="{name: 'inventario-bodegas'}">
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
    import bodega from "../../../api/Inventario/bodegas";
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
                sucursales:[],
                tipos_bodegas:[],
                form: {
                    bodega: '',
                    sucursal_bodega:'',
                    tipo_bodega:'',
                    permite_venta: ''
                },
                btnAction: 'Actualizar',
                errorMessages: [],

            }
        },
        methods: {

            obtenerBodega(){
                var self = this;
                bodega.obtenerBodega({
                    id_bodega: this.$route.params.id_bodega
                }, data =>{
                    self.form = data.bodega;
                    self.sucursales = data.sucursales;
                    self.tipos_bodegas = data.tipos_bodega;
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
                    /*this.$router.push({
                        name : 'inventario-bodegas'
                    })*/
                })
            },

            actualizar(){
                var self = this
                self.btnAction = 'Guardando, espere....'
                self.loading = true;
                bodega.actualizar(self.form, data =>{
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
                            name : 'inventario-bodegas'
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

            activar(bodegaID){
                var self = this;
                self.$swal({
                    title: '¿Esta seguro?',
                    text: '¿ Desea activar la bodega?',
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
                            text : '¡Bodega habilitada!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        bodega.activar({
                            id_bodega: bodegaID
                        }, data =>{
                            this.$router.push({
                                name : 'inventario-bodegas'
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
                                name : 'inventario-bodegas'
                            })
                        })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'La bodega no ha sido habilitada',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }

                })
            },

            desactivar(bodegaID){
                var self = this;
                self.$swal({
                    title: 'Info!',
                    text: '¿Esta seguro de desactivar la bodega?',
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
                            text : '¡La bodega ha sido eliminada!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        bodega.desactivar({id_bodega: bodegaID
                            },
                            data =>{
                                this.$router.push({
                                    name : 'inventario-bodegas'
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
                                    name : 'inventario-bodegas'
                                })
                            })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'La bodega no ha sido eliminada',
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
            this.obtenerBodega();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>
