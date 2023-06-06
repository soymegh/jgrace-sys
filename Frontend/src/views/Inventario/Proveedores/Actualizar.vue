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
                        <select
                                class="form-control" v-model.number="form.tipo_persona"
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
                        <input type="text" v-mask="'A#############'" placeholder="Número RUC" class="form-control" v-model="form.numero_ruc">
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
                        <v-select style="width: 100%" v-model="form.municipio_proveedor" :options="municipios" label="descripcion">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.municipio_proveedor">{{ message }}</li>
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

            <b-row>
                <div class="col-md-6 text-left p-0">
                    <template v-if="form.estado">
                        <b-button type="submit" variant="danger" @click="desactivar(form.id_proveedor)">
                            <feather-icon icon="Trash2Icon"></feather-icon> Eliminar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="success" @click="activar(form.id_proveedor)">
                            <feather-icon icon="CheckIcon"></feather-icon> Habilitar
                        </b-button>
                    </template>

                </div>
                <div class="col-md-6">
                    <router-link  :to="{name: 'inventario-proveedores'}">
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
    import proveedor from "../../../api/Inventario/proveedores";
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
                departamentos: [],
                municipios: [],
                form: {
                    municipio : '',
                    departamento: '',
                    direccion: '',
                    contacto_proveedor: '',
                    razon_social: '',
                    numero_ruc: '',
                    proveedor_tipo: '',
                    clasificacion_contable: '',
                    es_extranjero:false,
                },
                btnAction: 'Actualizar',
                errorMessages: [],

            }
        },
        methods: {

            obtenerMunicipios() {
                var self = this
                self.form.municipio_proveedor = null;
                if (self.form.departamento.municipios_departamento)
                    self.municipios = self.form.departamento.municipios_departamento
            },

            obtenerProveedor(){
                var self = this;
                proveedor.obtenerProveedor({
                    id_proveedor: this.$route.params.id_proveedor
                }, data =>{
                    self.form = data.proveedor;
                    self.departamentos = data.departamentos;
                    self.loading = false;
                    if(self.form.municipio_proveedor){
                        self.departamentos.forEach((departamentox, indice) => {
                            if(departamentox.id_departamento === self.form.municipio_proveedor.id_departamento){
                                self.form.departamento = self.departamentos[indice];
                                self.municipios = self.form.departamento.municipios_departamento
                            }
                        });
                    }else{
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
                            name : 'inventario-proveedores'
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
                    console.log(err);
                    /*this.$router.push({
                        name : 'inventario-bodegas'
                    })*/
                })
            },

            actualizar(){
                var self = this
                self.btnAction = 'Guardando, espere....'
                self.loading = true;
                proveedor.actualizar(self.form, data =>{
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
                            name : 'inventario-proveedores'
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

            activar(proveedorID){
                var self = this;
                self.$swal({
                    title: '¿Esta seguro?',
                    text: '¿ Desea activar proveedor?',
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
                            text : '¡Proveedor habilitado!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        proveedor.activar({
                            id_proveedor: proveedorID
                        }, data =>{
                            this.$router.push({
                                name : 'inventario-proveedores'
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
                                name : 'inventario-proveedores'
                            })
                        })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'El proveedor no ha sido habilitado',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }

                })
            },

            desactivar(proveedorID){
                var self = this;
                self.$swal({
                    title: 'Info!',
                    text: '¿Esta seguro de desactivar el proveedor?',
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
                            text : '¡El proveedor ha sido eliminado!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        proveedor.desactivar({id_proveedor: proveedorID
                            },
                            data =>{
                                this.$router.push({
                                    name : 'inventario-proveedores'
                                })
                            }, err =>{
                                this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title : 'Notificación',
                                        icon : 'InfoIcon',
                                        text : 'Ha ocurrido un error, intentelo de nuevo',
                                        variant : 'warning',
                                    }
                                },
                                    {position : 'bottom-right'}
                                    );
                                this.$router.push({
                                    name : 'inventario-proveedores'
                                })
                            })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'El proveedor no ha sido eliminado',
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
            this.obtenerProveedor();
            this.obtenerMunicipios();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>
