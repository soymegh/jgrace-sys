<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="name">* Descripción:</label>
                        <input type="text" class="form-control" v-model="form.descripcion" placeholder="Dígite la descripción">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label> Cliente</label>
                        <div class="form-group">
                            <v-select
                                    :filterable="false"
                                    :options="clientes"
                                    @search="onSearch"
                                    label="text"
                                    style="width: 100%;"
                                    v-model="form.cliente_proyecto"
                            >
                                <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select-->
                                <template slot="no-options">
                                    Escriba para buscar un cliente
                                </template>
                            </v-select>
                        </div>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_cliente"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="observacion">* Observaciones:</label>
                        <input type="text" class="form-control" v-model="form.observacion" placeholder="Dígite una observacion para el proyecto">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.observacion">{{ message }}</li>
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
                        <b-button type="submit" variant="danger" @click="desactivar(form.id_proyecto)">
                            <feather-icon icon="Trash2Icon"></feather-icon> Eliminar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button type="submit" variant="success" @click="activar(form.id_proyecto)">
                            <feather-icon icon="CheckIcon"></feather-icon> Habilitar
                        </b-button>
                    </template>

                </div>
                <div class="col-md-6">
                    <router-link  :to="{name: 'cajabanco-proyectos'}">
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
    import proyecto from "../../../api/CajaBanco/proyectos";
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
                    descripcion : '',
                    cliente_proyecto:[],
                    observacion:[],
                    es_deudor: false,

                },
                btnAction: 'Actualizar',
                es_deudor: false,
                errorMessages: [],
                clientes:[],

            }
        },
        methods: {

            obtenerproyecto(){
                const self = this;
                self.loading = true;
                proyecto.obtenerProyecto({
                    id_proyecto: this.$route.params.id_proyecto
                }, data =>{
                    self.form = data;
                    self.loading = false;
                }, err =>{
                    self.loading = false;
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
                        name : 'cajabanco-proyectos'
                    })
                })
            },

            onSearch(search, loading) {
                if (search.length) {
                    loading(true);
                    this.search(loading, search, this)
                }
            },
            select(e) {
                this.$emit('input', {
                    target: {
                        value: result,
                    },
                });
                this.onEsc()
            },
            search: _.debounce((loading, search, vm) => { // /ventas/clientes/buscar
                const self = this
                axios.get(`/cuentas-por-cobrar/clientes/buscar?q=${escape(search)}&es_deudor=${false}`).then(res => {
                    vm.options = res.data.results;
                    vm.clientes = res.data.results;
                    loading(false)
                })
            }, 100),

            actualizar(){
                var self = this
                self.btnAction = 'Guardando, espere....'
                self.loading = true;
                proyecto.actualizar(self.form, data =>{
                        self.loading = false;
                        this.$toast({
                                component : ToastificationContent,
                                props: {
                                    title : 'Notificación',
                                    icon : 'CheckIcon',
                                    text : 'Datos actualizados correctamente.',
                                    variant : 'success',
                                }
                            },
                            {
                                position : 'bottom-right'
                            });
                        this.$router.push({
                            name : 'cajabanco-proyectos'
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

            activar(proyectoID){
                var self = this;
                self.$swal({
                    title: '¿Esta seguro?',
                    text: '¿ Desea activar el proyecto?',
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
                            title : '¡Habilitado!',
                            text : '¡proyecto habilitado!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        proyecto.activar({
                            id_proyecto : proyectoID
                        }, data =>{
                            this.$router.push({
                                name : 'cajabanco-proyectos'
                            })
                        }, err =>{
                            console.log(err);
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
                            this.$router.push({
                                name : 'cajabanco-proyectos'
                            })
                        })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'El proyecto no ha sido habilitado',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-success',
                            },
                        })
                    }

                })
            },

            desactivar(proyectoID){
                var self = this;
                self.$swal({
                    title: 'Info!',
                    text: '¿Esta seguro de desactivar el proyecto?',
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
                            title : '¡Eliminado!',
                            text : '¡proyecto eliminado!',
                            customClass: {
                                confirmButton : 'btn btn-success',
                            }
                        });
                        proyecto.desactivar({id_proyecto: proyectoID
                            },
                            data =>{
                                this.$router.push({
                                    name : 'cajabanco-proyectos'
                                })
                            }, err =>{
                                console.log(err);
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
                                this.$router.push({
                                    name : 'cajabanco-proyectos'
                                })
                            })
                    }else if (result.dismiss === 'cancel'){
                        self.$swal({
                            title: 'Cancelado',
                            text: 'El tipo cliente no ha sido eliminado',
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
            this.obtenerproyecto();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';
</style>
