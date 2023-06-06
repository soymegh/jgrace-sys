<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Usuario</label>
                    <input type="text" class="form-control" placeholder="Nombre de usuario" v-model="form.name">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.name">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Email</label>
                    <input type="email" class="form-control" placeholder="Correo electronico" v-model="form.email">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.email">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Rol</label>
                    <v-select
                            v-model="form.rol"
                            :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                            label="descripcion"
                            :options="roles"
                    />
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.id_rol">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Contraseña</label>
                    <input type="password" class="form-control" placeholder="Contraseña del usuario" v-model="form.password">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.password">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Confirmar contraseña</label>
                    <input type="password" class="form-control" placeholder="Confirmar contraseña" v-model="form.password_confirmation">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.password_confirmation">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
        </b-row>
        <b-card-footer class="text-right">
            <router-link :to="{name: 'admon-usuarios'}">
                <b-button class="btn btn-default mx-1" variant="secondary">Cancelar</b-button>
            </router-link>

            <b-button :disabled="btnAction !== 'Guardar' ? true : false" @click="registrar" variant="primary">{{btnAction}}</b-button>
        </b-card-footer>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, VBTooltip,BAlert} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import usuarios from '../../../api/admon/usuarios'
    import rol from '../../../api/admon/roles'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    export default {
        components: {
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, BAlert,
            vSelect
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data(){
            return{
                loading:false,
                url: loadingImage,
                roles:[],
                form:{
                    name:'',
                    email:'',
                    password:'',
                    password_confirmation:'',
                    rol:[],
                },
                btnAction: 'Guardar',
                errorMessages:[]
            }
        },
        methods:{
            obtenerTodosRoles() {
                var self = this;
                rol.obtenerTodosRoles(data => {
                    self.roles = data
                }, err => {
                    console.log(err)
                })
            },
            registrar() {
                var self = this
                self.btnAction = 'Guardando, espere...'
                self.loading = true;
                usuarios.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Usuario registrado correctamente',
                            variant: 'success',
                            position: 'bottom-right'
                        }
                    });
                    this.$router.push({
                        name: 'admon-usuarios'
                    })
                }, err => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'BellIcon',
                            text: 'Ha ocurrido un error',
                            variant: 'warning',
                            position: 'bottom-right'
                        }
                    });
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            }
        },
        mounted() {
            this.obtenerTodosRoles();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>

