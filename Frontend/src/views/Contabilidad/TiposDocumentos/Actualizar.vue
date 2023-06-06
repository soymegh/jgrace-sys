<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Descripción:</label>
                        <input type="text" class="form-control" v-model="form.descripcion"
                               placeholder="Digite la descripción del rol">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Prefijo:</label>
                        <input type="text" class="form-control" v-model="form.prefijo"
                               placeholder="Digite la descripción del rol">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.prefijo">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="name"> secuencia:</label>
                        <input type="text" disabled class="form-control" v-model="form.secuencia"
                               placeholder="Digite la descripción del rol">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.prefijo">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4 form-inline ">
                    <div class="form-group ">
                        <b-form-checkbox v-model="form.permite_registro_manual" class="custom-control-primary">
                            Tipo documento permite registros manuales
                        </b-form-checkbox>
                    </div>
                </div>
            </div>
        </form>

        <b-card-footer class="row mt-2">

            <div class="col-md-6">
                <div class="content-box-footer text-left">
                    <template v-if="form.estado">
                        <b-button @click="desactivar(form.id_tipo_doc)" class="btn btn-danger">
                            <feather-icon icon="Trash2Icon"></feather-icon>
                            Desactivar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button @click="activar(form.id_tipo_doc)" class="btn btn-success" >
                            <feather-icon icon="CheckCircleIcon"></feather-icon>
                            Activar
                        </b-button>
                    </template>
                </div>
            </div>
            <div class="col-md-6">
                <div class="content-box-footer text-right">

                    <router-link  :to="{name: 'contabilidad-tipos-documentos'}">
                        <b-button type="submit" variant="secondary" class="mx-1">
                            Cancelar
                        </b-button>
                    </router-link>


                    <b-button type="submit" variant="primary" @click="actualizarTipoDocumento" :disabled="btnAction !== 'Registrar'">
                        {{btnAction}}
                    </b-button>


                </div>
            </div>

        </b-card-footer>


        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BButton,BAlert,BFormCheckbox,BCard,BCardFooter} from 'bootstrap-vue'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import tipos_documentos from "../../../api/contabilidad/tipos_documentos";

    export default {
        components:{
            BButton,
            BAlert,
            BFormCheckbox,
            BCard,
            BCardFooter
        },
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                form: {
                    descripcion: '',
                },
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods: {
            obtenerTipoDocumento() {
                var self = this;
                tipos_documentos.obtenerTipoDocumento({
                    id_tipo_doc: this.$route.params.id_tipo_doc
                },data=>{
                    self.form = data
                    self.loading = false;
                },data => {
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title : 'Notificación',
                            icon : 'InfoIcon',
                            text : 'Ha ocurrido un error al cargar la información',
                            variant : 'warning',
                            position : 'top-center'
                        }
                    })
                    this.$router.push({
                        name:'contabilidad-tipos-documentos'
                    })
                })
            },
            actualizarTipoDocumento(){
                var self = this;
                self.loading = true;
                self.btnAction = 'Guardando, espere.......'
                tipos_documentos.actualizar(self.form, data=>{
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon : 'InfoIcon',
                            text : 'Tipo de documento actualizado correctamente',
                            variant: 'success',
                            position: 'top-center'
                        }
                    })
                    this.$router.push({
                        name : 'contabilidad-tipos-documentos'
                    })
                }, err=>{
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon : 'InfoIcon',
                            text : 'Ha ocurrido un error al actualizar el tipo de documento',
                            variant: 'warnig',
                            position: 'top-center'
                        }
                    })
                    this.$router.push({
                        name : 'contabilidad-tipos-documentos'
                    })
                    }
                )
            },

            desactivar(tipoId) {

                var self = this;
                self.$swal.fire({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText:'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        tipos_documentos.desactivar({
                            id_tipo_doc: tipoId
                        }, data => {
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon : 'InfoIcon',
                                    text : '¡Ha desactivado el tipo de docuemento con exito!',
                                    variant: 'warnig',
                                    position: 'top-center'
                                }
                            })
                            this.$router.push({
                                name: 'contabilidad-tipos-documentos'
                            })
                        }, err => {
                            console.log(err)
                        })
                    }else{
                        self.btnAction = "Guardar";
                    }
                })
            },
            activar(tipoId) {

                var self = this;
                self.$swal.fire({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText:'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        tipos_documentos.activar({
                            id_tipo_doc: tipoId
                        }, data => {
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon : 'InfoIcon',
                                    text : '¡El tipo de documento ha sido activado con exito!',
                                    variant: 'warnig',
                                    position: 'top-center'
                                }
                            })
                            this.$router.push({
                                name: 'contabilidad-tipos-documentos'
                            })
                        }, err => {
                            console.log(err)
                        })
                    }else{
                        self.btnAction = "Guardar";
                    }
                })
            }
        },
        mounted() {
            this.obtenerTipoDocumento();
        }
    }
</script>

