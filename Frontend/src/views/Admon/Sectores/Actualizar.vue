<template>
    <b-card>
        <form>
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name"> Departamento:</label>
                        <input disabled="true" class="form-control" type="text" v-model="form.departamento.descripcion">
                        <!--<v-select disabled="true" v-model="form.departamento"
                                  @input="obtenerSectores"
                                  label="descripcion"
                                  :options="departamentos"

                        ></v-select>-->
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.departamento">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label  for="name"> Sector:</label>
                        <input disabled="true" class="form-control" type="text" v-model="form.sector.descripcion">
                        <!--<v-select disabled="true"  v-model="form.sector"
                                  label="descripcion"
                                  :options="sectores"

                        ></v-select>-->
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.zona_municipio">{{ message }}</li>
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
            </div>

            <b-row>
                <template>
                        <l-map
                                :zoom="zoom"
                                :center="center"
                                :options = "mapOptions"
                        >
                            <l-tile-layer :url="url2"/>
                            <l-marker :lat-lng="markerLatLng">
                                <l-popup>Ubicicación del sector!</l-popup>
                            </l-marker>
                        </l-map>
                </template>
            </b-row>

        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <b-row>
                <div class="col-md-6 text-left p-0">
                    <!--<template v-if="form.estado">
                        <b-button type="submit" variant="danger" @click="desactivar(form.id_sector)">
                            <feather-icon icon="Trash2Icon"></feather-icon> Eliminar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="success" @click="activar(form.id_sector)">
                            <feather-icon icon="CheckIcon"></feather-icon> Habilitar
                        </b-button>
                    </template>-->

                </div>
                <div class="col-md-6">
                    <router-link  :to="{name: 'admon-sectores'}">
                        <b-button type="submit" variant="secondary" class="mx-1">
                            Salir
                        </b-button>
                    </router-link>


                    <!--<b-button type="submit" variant="primary" @click="actualizar" :disabled="btnAction !== 'Actualizar'">
                        {{btnAction}}
                    </b-button>-->
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
    import {LMap, LTileLayer, LMarker, LPopup} from 'vue2-leaflet'
    import Ripple from 'vue-ripple-directive'
    import vSelect from 'vue-select'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import sector from "../../../api/admon/sectores";
    import 'leaflet/dist/leaflet.css';
    import 'leaflet-defaulticon-compatibility/dist/leaflet-defaulticon-compatibility.webpack.css'
    import * as L from 'leaflet';
    import 'leaflet-defaulticon-compatibility';
    import axios from 'axios'
    import {latLng} from "leaflet";

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
            LMap,
            LTileLayer,
            LMarker,
            LPopup
        },
        directives : {
            Ripple,
        },
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                sectores:[],
                departamentos:[],
                form: {
                    descripcion: '',
                    departamento: '',
                    latitud: '',
                    longitud: '',
                    coordenadas: [],
                    sector: '',
                    sector_departamento: ''
                },
                btnAction: 'Actualizar',
                errorMessages: [],
                url2: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                mapOptions: {
                    zoomControl: true,
                    attributionControl: false,
                    zoomSnap: true,
                },
                zoom:8,
                center: ['',''],
                markerLatLng:['','', { draggable: 'true' }],
            }
        },
        methods: {

            obtenerSectores() {
                var self = this;
                self.form.sector = null;
                //self.center = [self.form.latitud, self.form.longitud + ',' + { draggable: 'true' }];
                if (self.form.departamento.sectores_departamento)
                    self.sectores = self.form.departamento.sectores_departamento

            },

            obtenerSector(){
                var self = this;
                sector.obtenerSector({
                    id_sector: this.$route.params.id_sector
                }, data =>{
                    self.form = data.sectores;
                    self.departamentos = data.departamentos;
                    self.form.sector = data.center;
                    self.center =[self.form.sector.latitud, self.form.sector.longitud];
                    self.markerLatLng =[self.form.sector.latitud, self.form.sector.longitud];
                    //self.sectores = self.form.departamento.sector_departamento;
                    self.form.coordenadas = [self.form.sector.latitud, self.form.sector.longitud];
                    self.loading = false;
                    if(self.form.id_departamento){
                        self.departamentos.forEach((departamentox, indice) => {
                            if(departamentox.id_departamento === self.form.id_departamento){
                                self.form.departamento = self.departamentos[indice];
                                self.sectores = self.form.departamento.sectores_departamento;
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
                        /*this.$router.push({
                            name : 'admon-zonas'
                        })*/
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
                sector.actualizar(self.form, data =>{
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
                            name : 'admon-sectores'
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
            this.obtenerSector();
            this.obtenerSectores();
        }

    }
</script>
<style lang="scss">
    @import 'src/@core/scss/vue/libs/vue-select';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
    @import "~leaflet/dist/leaflet.css";

    .vue2leaflet-map{
        &.leaflet-container{
            height: 350px;
        }
    }
</style>
