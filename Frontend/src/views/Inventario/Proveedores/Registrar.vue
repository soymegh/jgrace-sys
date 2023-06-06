<template>
    <b-card>
        <form>
            <div class="row">
                <!--<div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Descripción:</label>
                        <b-form-select
                                class="form-group" v-model.number="form.clasificacion_contable"
                        >
                            <option value="1">Proveedor</option>
                            <option value="2">Acreedor Diverso</option>
                        </b-form-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.clasificacion_contable">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>-->
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Tipo Persona:</label>
                        <select class="form-control"
                                 v-model.number="form.tipo_persona"
                        >
                            <option value="1">Natural</option>
                            <option value="2">Jurídico</option>
                        </select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.tipo_persona">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Número RUC:</label>
                        <input type="text"  placeholder="Número RUC" class="form-control" v-model="form.numero_ruc"> <!--v-mask="'A#############'"-->
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.numero_ruc">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Número de Cédula:</label>
                        <input type="text" v-mask="'###-######-####A'" placeholder="Número Cédula" class="form-control" v-model="form.numero_cedula">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.numero_cedula">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Nombre Comercial:</label>
                        <input type="text" placeholder="Nombre Comercial" class="form-control" v-model="form.nombre_comercial">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.nombre_comercial">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Páguese a:</label>
                        <input type="text" placeholder="Páguese a" class="form-control" v-model="form.paguese_a">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.paguese_a">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Razón Social:</label>
                        <input type="text" placeholder="Razón Social" class="form-control" v-model="form.razon_social">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.razon_social">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Teléfono:</label>
                        <input type="tel" placeholder="Teléfono" class="form-control" v-model="form.telefono">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.telefono">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Contacto:</label>
                        <input type="text" placeholder="Contacto" class="form-control" v-model="form.contacto_proveedor">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.contacto_proveedor">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Teléfono Contacto:</label>
                        <input type="tel" placeholder="Teléfono Contacto" class="form-control" v-model="form.telefono_contacto">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.telefono_contacto">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Correo Contacto:</label>
                        <input type="email" placeholder="Correo Contacto" class="form-control" v-model="form.correo_contacto">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.correo_contacto">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Dirección Entrega:</label>
                        <input type="email" placeholder="Dirección Entrega" class="form-control" v-model="form.direccion">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.direccion">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Días Credito:</label>
                        <select
                                class="form-control" v-model.number="form.plazo_credito"
                        >
                            <option value="0">Contado</option>
                            <option value="8">Trámite de (8 días)</option>
                            <option value="15">Crédito (15 días)</option>
                            <option value="30">Crédito (30 días)</option>
                            <option value="45">Crédito (45 días)</option>
                        </select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.plazo_credito">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Departamentos:</label>
                        <v-select style="width: 100%" v-model="form.departamento" @input="obtenerMunicipios" :options="departamentos" label="descripcion">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.plazo_credito">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Municipios:</label>
                        <v-select style="width: 100%" v-model="form.municipio" :options="municipios" label="descripcion">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.plazo_credito">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4 form-inline">
                    <div class="form-group">
                        <label class="check-label2"><input v-model="form.es_extranjero" type="checkbox" class="mx-sm-1">  Proveedor Extranjero</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="name">* Observaciones:</label>
                        <input type="text" placeholder="Observaciones" class="form-control" v-model="form.observaciones">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.observaciones">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'inventario-proveedores'}">
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
    import proveedor from "../../../api/Inventario/proveedores";
    import departamento from "../../../api/admon/departamentos";
    import tipo_proveedor from "../../../api/Inventario/tipos_proveedores";
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
                departamentos : [],
                municipios : [],
                tipos_proveedores : [],
                tipos_proveedores_filtrados : [],
                form: {
                    municipio:'',
                    razon_social: '',
                    direccion: '',
                    contacto_proveedor: '',
                    numero_ruc: '',
                    tipo_proveedor:'',
                    clasificacion_contable: '1',
                    observaciones:'',
                    es_extranjero:false,
                    numero_cedula:'',
                    nombre_comercial:'',
                    telefono:'',
                    telefono_contacto:'',
                    correo_contacto:'',
                    tipo_persona:2,
                    paguese_a:'',
                    plazo_credito:0,

                },
                btnAction: 'Registrar',
                errorMessages: [],

            }
        },
        methods: {

            /*buscarTipoProveedores(){
                var self = this;
                self.tipos_proveedores_filtrados=[];
                self.tipos_proveedores.forEach((tipox, indice)=>{
                    if(tipox.clasificacion_contable === self.form.clasificacion_contable){
                        self.tipos_proveedores_filtrados.push(self.tipos_proveedores[indice])
                    }
                })
            },*/

            obtenerTodosDepartamentos(){
              var self = this;
              departamento.obtenerTodos(data=>{
                  self.departamentos = data;
                  self.form.departamento = self.departamentos[11];
                  self.municipios = self.form.departamento.municipios_departamento;
                  self.form.municipio= self.municipios[2]
              },err =>{
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title : 'Notificación',
                                icon : 'InfoIcon',
                                text : 'Ha ocurrido un error al cargar los departamentos, intentelo de nuevo',
                                variant : 'warning',

                            }
                        },
                        {
                            position : 'bottom-right'
                        });
                    console.log(err);
                    /*this.$router.push({
                        name : 'inventario-proveedores'
                    })*/
                })
            },

            obtenerMunicipios(){
                var self = this;
                self.form.municipio = null;
                if(self.form.departamento.municipios_departamento){
                    self.municipios = self.form.departamento.municipios_departamento
                }
            },

            obtenerTodosTipos(){
                var self = this;
                tipo_proveedor.obtenerTodos(data=>{
                    self.tipos_proveedores = data
                },err =>{
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title : 'Notificación',
                                icon : 'InfoIcon',
                                text : 'Ha ocurrido un error al cargar los tipos de proveedores, intentelo de nuevo',
                                variant : 'warning',

                            }
                        },
                        {
                            position : 'bottom-right'
                        });
                    console.log(err);
                    /*this.$router.push({
                        name : 'inventario-proveedores'
                    })*/
                })
            },

            registrar(){
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                proveedor.registrar(self.form, data =>{
                        self.loading = false;
                        this.$toast({
                                component : ToastificationContent,
                                props: {
                                    title : 'Notificación',
                                    icon : 'CheckIcon',
                                    text : 'Datos registrados correctamente.',
                                    variant : 'success',
                                }
                            },
                            {
                                position : 'bottom-right'
                            });
                        this.$router.push({
                            name : 'inventario-proveedores'
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
                        console.log(err);
                        /*this.$router.push({
                            name : 'inventario-proveedores'
                        })*/
                    })
            },


        },
        mounted() {
            this.obtenerTodosTipos();
            this.obtenerTodosDepartamentos();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
