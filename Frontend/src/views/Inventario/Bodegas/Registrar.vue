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
                        <v-select v-model="form.sucursal"
                                  label="descripcion"
                                  :options="sucursales"

                        ></v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.sucursal">{{ message }}</li>
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
                        <label class="check-label2"><input v-model="form.permite_venta" type="checkbox" class="mx-sm-1">  Bodega Permite Ventas</label>
                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'inventario-bodegas'}">
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
    import {BButton,BAlert,BFormCheckbox,BFormSelect,BCard,BCardFooter, BRow} from 'bootstrap-vue'
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
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                /*centros_costos_ingresos : [],*/
                sucursales : [],
                tipos_bodegas : [],
                form: {
                    descripcion : '',
                    permite_venta : 0,

                },
                btnAction: 'Registrar',
                errorMessages: [],

            }
        },
        methods: {

            nuevo(){
                var self = this;
                bodega.nuevo({},data =>{
                    self.sucursales = data.sucursales;
                    /*self.centros_costos_ingresos = data.centros_costos_ingresos;*/
                    self.tipos_bodegas = data.tipos_bodega;
                }, err =>{
                    console.log(err);
                })
            },

            registrar(){
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                bodega.registrar(self.form, data =>{
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
                            name : 'inventario-bodegas'
                        })
                    },
                    err =>{
                        self.loading = false;
                        self.errorMessages = err
                        self.btnAction = 'Registrar'
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
            this.nuevo();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
