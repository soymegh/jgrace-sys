<template>
    <b-card>
        <form>
            <div class="row">

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name"> Departamento:</label>
                        <v-select v-model="form.departamento"
                                  @input="obtenerMunicipios"
                                  label="descripcion"
                                  :options="departamentos"

                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.departamento">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name"> Municipio:</label>
                        <v-select v-model="form.zona_municipio"
                                  label="descripcion"
                                  :options="municipios"

                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.zona_municipio">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-3 form-group">
                    <label for="name">* Barrio:</label>
                    <input type="text" class="form-control" placeholder="Dígite el nombre de la bodega" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>

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
                        <label for="name"> Código Postal:</label>
                        <input type="text" class="form-control" placeholder="Dígite el nombre de la bodega" v-model="form.codigo">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.codigo">{{ message }}</li>
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
                        <b-button type="submit" variant="danger" @click="desactivar(form.id_zona)">
                            <feather-icon icon="Trash2Icon"></feather-icon> Eliminar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="success" @click="activar(form.id_zona)">
                            <feather-icon icon="CheckIcon"></feather-icon> Habilitar
                        </b-button>
                    </template>

                </div>
                <div class="col-md-6">
                    <router-link  :to="{name: 'admon-zonas'}">
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
    import zona from "../../../api/admon/zonas";
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
                municipios:[],
                departamentos:[],
                form: {
                    descripcion: '',
                    departamento: '',
                    codigo: ''
                },
                btnAction: 'Actualizar',
                errorMessages: [],

            }
        },
        methods: {

            obtenerMunicipios() {
                var self = this
                self.form.zona_municipio = null;
                if (self.form.departamento.municipios_departamento)
                    self.municipios = self.form.departamento.municipios_departamento
            },

            obtenerZona(){
                var self = this;
                zona.obtenerZona({
                    id_zona: this.$route.params.id_zona
                }, data =>{
                    self.form = data.zona;
                    self.departamentos = data.departamentos;
                    self.loading = false;
                    if(self.form.zona_municipio){
                        self.departamentos.forEach((departamentox, indice) => {
                            if(departamentox.id_departamento === self.form.zona_municipio.id_departamento){
                                self.form.departamento = self.departamentos[indice];
                                self.municipios = self.form.departamento.municipios_departamento
                            }
                        })  ;
                    }else{
                        this.$toast({
                                component : ToastificationContent,
                                props : {
                                    title : 'Notificación',
                                    icon : 'InfoIcon',
                                    text : 'Ha ocurrido un error al cargar los datos del departamento',
                                    variant : 'warning',
                                }
                            },
                            {
                                position : 'bottom-rigth'
                            });
                        this.$router.push({
                            name : 'admon-zonas'
                        })
                    }
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
                zona.actualizar(self.form, data =>{
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
                            name : 'admon-zonas'
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

            activar(zonaID){
                var self = this;
                self.$swal({
                    title: '¿Esta seguro?',
                    text: '¿ Desea activar la zona?',
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
                            text : '¡Zona habilitada!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        zona.activar({
                            id_zona : zonaID
                        }, data =>{
                            this.$router.push({
                                name : 'admon-zonas'
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
                                name : 'admon-zonas'
                            })
                        })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'La zona no ha sido habilitada',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }

                })
            },

            desactivar(zonaID){
                var self = this;
                self.$swal({
                    title: 'Info!',
                    text: '¿Esta seguro de desactivar la zona?',
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
                            text : '¡La zona ha sido eliminada!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        zona.desactivar({id_zona : zonaID
                            },
                            data =>{
                                this.$router.push({
                                    name : 'admon-zonas'
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
                                    name : 'admon-zonas'
                                })
                            })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'La zona no ha sido eliminada',
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
            this.obtenerZona();
            this.obtenerMunicipios();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>
