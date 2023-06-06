<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-4">
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
                <div class="col-sm-4">
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
                <div class="col-sm-4 form-inline mt-2">
                    <div class="form-group ">
                        <b-form-checkbox v-model="form.permite_registro_manual" class="custom-control-primary">
                            Tipo documento permite registros manuales
                        </b-form-checkbox>
                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right">

            <router-link  :to="{name: 'contabilidad-tipos-documentos'}">
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
                    prefijo: '',
                    permite_registro_manual: false
                },
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods: {
            registrar() {
                var self = this;
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                tipos_documentos.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Documento registrado correctamente',
                            variant: 'success',
                            position: 'top-center'
                        }
                    });
                    this.$router.push({name: 'contabilidad-tipos-documentos'})
                }, err => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'BellIcon',
                            text: 'Ha ocurrido un error',
                            variant: 'warning',
                            position: 'top-center'
                        }
                    });
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                })
            }
        }
    }
</script>

