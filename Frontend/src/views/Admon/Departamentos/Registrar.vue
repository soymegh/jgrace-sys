<template>
    <b-card>
        <b-row>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">* Nombre departamento</label>
                    <input type="text" class="form-control" placeholder="Nombre del departamento" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
        </b-row>
        <b-card-footer class="text-right">
            <router-link :to="{name: 'admon-departamentos'}">
                <b-button class="btn btn-default mx-1" variant="secondary">Cancelar</b-button>
            </router-link>

            <b-button :disabled="btnAction !== 'Registrar' ? true : false" @click="registrar" variant="primary">{{btnAction}}</b-button>
        </b-card-footer>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, VBTooltip,BAlert} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import departamentos from '../../../api/admon/departamentos'
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
                url : loadingImage,   //It is important to import the loading image then use here
                form: {
                    descripcion: '',
                    codigo: '',
                },
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods:{
            registrar() {
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                departamentos.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Departamento registrado correctamente',
                            variant: 'success',
                            position: 'bottom-right'
                        }
                    },
                        {
                            position: 'bottom-right'
                        });
                    this.$router.push({
                        name: 'admon-departamentos'
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
                    },
                        {
                            position: 'bottom-right'
                        });
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                })
            }
        },
        mounted() {
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>

