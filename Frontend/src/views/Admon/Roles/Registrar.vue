<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-12">
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
            </div>
        </form>
        <b-card-footer class="text-right">

            <router-link  :to="{name: 'admon-roles'}">
                <b-button type="submit" variant="secondary" class="mx-1">
                    Cancelar
                </b-button>
            </router-link>


            <b-button type="submit" variant="primary" @click="crearRol" :disabled="btnAction !== 'Registrar'">
                {{btnAction}}
            </b-button>


        </b-card-footer>
        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import rol from '../../../api/admon/roles'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BButton,BAlert, BCard, BCardFooter} from 'bootstrap-vue'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    export default {
        components:{
            BButton,
            BAlert,
            BCard,
            BCardFooter
        },
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                roles: [],
                form: {
                    descripcion: '',
                },
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods: {
            crearRol() {
                var self = this;
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                rol.crearRol(self.form, data => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Registros actualizado correctamente',
                            variant: 'success',
                            position: 'top-center'
                        }
                    });
                    this.$router.push({name: 'admon-roles'})
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

