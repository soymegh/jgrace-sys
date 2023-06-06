<template>
    <b-card>
        <form>
            <b-row>
                <div class="col-sm-3 form-group">
                    <label for="name">* Departamento:</label>
                    <v-select v-model="form.departamento"
                              @input="obtenerMunicipio"
                              label="descripcion"
                              :options="departamentos"

                    ></v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.departamento">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Municipio:</label>
                        <v-select v-model="form.municipio"
                                  label="descripcion"
                                  :options="municipios"

                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.municipio">{{ message }}</li>
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
                        <label for="name">* Sectores:</label>
                        <v-select v-model="form.sector"
                                  label="descripcion"
                                  :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"

                                  :options="sectores"
                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.sector">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-3 form-inline form-group align-content-center justify-content-center">
                    <div class="  p-1">
                        <div class="form-group">
                            <label for>&nbsp;</label>
                            <b-button
                                    ref="agregar"
                                    variant="info"
                                    @click="cargarCoordenadas"
                                    class="btn-agregar"
                                    v-b-tooltip.hover.top="'Agregar Sector'"
                            >
                                <feather-icon icon="PlusCircleIcon"></feather-icon>

                            </b-button>
                        </div>
                    </div>
                    <div class=" p-1">
                        <div class="form-group">
                            <label for>&nbsp;</label>
                            <b-button
                                    ref="borrar"
                                    variant="danger"
                                    @click="borrarCoordenada"
                                    class="btn-agregar"
                                    v-b-tooltip.hover.top="'Borrar ultimo sector'"
                            >
                                <feather-icon icon="TrashIcon"></feather-icon>

                            </b-button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="name">*Descripción: </label>
                        <input class="form-control" type="text" v-model="form.descripcion">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                            </ul>
                        </b-alert>
                    </div>
                </div>

<!--                <div class="col-lg-3 form-inline">-->
<!--                    <div class="form-group">-->
<!--                        <label class="check-label2"><input v-model="form.permite_venta" type="checkbox" class="mx-sm-1">  Bodega Permite Ventas</label>-->
<!--                    </div>-->
<!--                </div>-->
            </b-row>
            <b-row>
                <template>
                    <l-map
                            :zoom.sync="zoom"
                            :options="mapOptions"
                            :center="center"
                            :min-zoom="minZoom"
                            :max-zoom="maxZoom"
                            style="height: 400px; width: 100%"
                    >
                        <l-control-layers
                                :position="layersPosition"
                                :collapsed="false"
                                :sort-layers="true"
                        />
                        <l-tile-layer
                                v-for="tileProvider in tileProviders"
                                :key="tileProvider.name"
                                :name="tileProvider.name"
                                :visible="tileProvider.visible"
                                :url="tileProvider.url"
                                :attribution="tileProvider.attribution"
                                :token="token"
                                layer-type="base"
                        />
                        <l-control-zoom :position="zoomPosition" />
                        <l-control-attribution
                                :position="attributionPosition"
                                :prefix="attributionPrefix"
                        />
                        <l-control-scale :imperial="imperial" />
                        <l-marker
                                v-for="marker in form.markers"
                                :key="marker.id_sector"
                                :visible="marker.visible"
                                :draggable="marker.draggable"
                                :lat-lng.sync="marker.position"
                                :icon="marker.icon"
                                @click="alert(marker)"
                        >
                            <l-popup :content="marker.tooltip" />
                            <l-tooltip :content="marker.tooltip" />
                        </l-marker>
                        <l-layer-group layer-type="overlay" name="Marcar Zona">
                            <l-polyline
                                    v-for="item in polylines"
                                    :key="item.id"
                                    :lat-lngs="item.points"
                                    :visible="item.visible"
                                    @click="alert(item)"
                            />
                        </l-layer-group>

                    </l-map>

                </template>
            </b-row>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'admon-zonas'}">
                <b-button type="submit" variant="secondary" class="mx-1">
                    Cancelar
                </b-button>
            </router-link>


            <b-button type="submit" variant="primary" @click="registrar" :disabled="btnAction !== 'Registrar'">
                {{btnAction}}
            </b-button>


        </b-card-footer>
        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BAlert, BButton, BCard, BCardFooter, BFormCheckbox, BFormSelect, BRow, VBTooltip} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import {
        LControlAttribution,
        LControlLayers,
        LControlScale,
        LControlZoom,
        LLayerGroup,
        LMap,
        LMarker,
        LPolyline,
        LPopup,
        LTileLayer,
        LTooltip
    } from 'vue2-leaflet'
    import {Icon, latLngBounds} from 'leaflet'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import zona from "../../../api/admon/zonas";
    import departamento from "../../../api/admon/departamentos"
    import 'leaflet/dist/leaflet.css';
    import 'leaflet-defaulticon-compatibility/dist/leaflet-defaulticon-compatibility.webpack.css'
    import 'leaflet-defaulticon-compatibility';

    const tileProviders = [
        {
            name: 'OpenStreetMap',
            visible: true,
            url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        },
        {
            name: 'OpenTopoMap',
            visible: false,
            url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
        },
    ];

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
            LPolyline,
            LLayerGroup,
            LTooltip,
            LPopup,
            LControlZoom,
            LControlAttribution,
            LControlScale,
            LControlLayers,
            latLngBounds,
            Icon
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                dir : 'ltr',
                departamentos : [],
                municipios : [],
                sectores: [],
                form: {
                    descripcion : '',
                    permite_venta : 0,
                    departamento: '',
                    municipio: '',
                    sector: [],
                    markers: [],
                },

                btnAction: 'Registrar',
                errorMessages: [],

                center: [12.865416, -85.207229],
                opacity: 0.6,
                token: 'your token if using mapbox',
                mapOptions: {
                    zoomControl: false,
                    attributionControl: false,
                    zoomSnap: true,
                },
                tileProviders: tileProviders,
                zoom: 7,
                minZoom: 1,
                maxZoom: 18,
                zoomPosition: 'topleft',
                attributionPosition: 'bottomright',
                layersPosition: 'topright',
                attributionPrefix: 'CapSoft',
                imperial: false,
                position: [],
                Positions: ['topleft', 'topright', 'bottomleft', 'bottomright'],

                polylines: [{
                    id: "p1",
                    points:[],
                    visible: true
                }],
            }
        },
        methods: {

            obtenerTodosDepartamentos(){
                var self = this;
                departamento.obtenerTodos(data=>{
                    self.departamentos = data;
                    self.form.departamento = self.departamentos[13];
                    self.municipios = self.form.departamento.municipios_departamento;
                    self.form.municipio = self.municipios[5];
                    self.sectores = self.form.departamento.sectores_departamento;

                }, err=>{
                    self.loading = false;
                    self.errorMessages = err;
                    self.btnAction = Registrar;
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title : 'Notificación',
                            icon : 'InfoIcon',
                            text : 'Ha ocurrido un error al cargar los departamentos, intentelo de nuevo',
                            variant : 'warning'
                        }
                    },
                        {
                            position : 'botton-right'
                        });
                    console.log(err);
                })
            },

            obtenerMunicipio(){
                var self = this;
                self.form.municipio = null;
                if(self.form.departamento.municipios_departamento && self.form.departamento.sectores_departamento){
                    self.municipios = self.form.departamento.municipios_departamento;
                    self.sectores = self.form.departamento.sectores_departamento;
                }
            },

            cargarCoordenadas(){
                var self = this;
                if(self.form.sector){
                    let newMarker = {
                        position: {lat: self.form.sector.latitud, lng: self.form.sector.longitud},
                        id_sector: self.form.sector.id_sector,
                        title: self.form.sector.descripcion,
                        draggable: true,
                        visible: true
                    };
                    self.form.markers.push(newMarker);
                    self.polylines[0].points.push(newMarker.position);
                    self.form.sector = null;
                }else{
                    this.$toast({
                        component : ToastificationContent,
                        props: {
                            title : 'Notificación',
                            icon : 'InfoIcon',
                            text : 'Seleccione un sector.',
                            variant : 'warning',
                        }
                    },{
                        position : 'bottom-right'
                    });
                }

            },
            borrarCoordenada () {
                var self = this;
                if(self.form.markers && self.polylines[0].points){
                    self.form.markers.pop();
                    self.polylines[0].points.pop();
                }else{
                    this.$toast({
                        component : ToastificationContent,
                        props: {
                            title : 'Notificación',
                            icon : 'InfoIcon',
                            text : 'No hay sectores en el mapa',
                            variant : 'warning',
                        }
                    },{
                        position : 'bottom-right'
                    });
                }

            },

            registrar(){
                var self = this;
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                zona.registrar(self.form, data =>{
                        self.loading = false;
                        this.$toast({
                            component : ToastificationContent,
                            props: {
                                title : 'Notificación',
                                icon : 'CheckIcon',
                                text : 'Datos registrados correctamente.',
                                variant : 'success',
                            }
                        },{
                            position : 'bottom-right'
                        });
                        this.$router.push({
                            name : 'admon-zonas'
                        })
                    },
                    err =>{
                        self.loading = false;
                        self.errorMessages = err;
                        self.btnAction = 'Registrar';
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
                        /*this.$router.push({
                            name : 'inventario-unidades-medida'
                        })*/
                    })
            },

        },
        mounted() {
            this.obtenerTodosDepartamentos();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import "~leaflet/dist/leaflet.css";
</style>
