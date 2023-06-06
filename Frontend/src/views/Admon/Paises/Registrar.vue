<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Nombre país</label>
                    <input type="text" class="form-control" placeholder="Nombre del país" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Codigo país</label>
                    <input type="email" class="form-control" placeholder="Codigo del país" v-model="form.codigo">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.codigo">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
        </b-row>
        <b-card-footer class="text-right">
            <router-link :to="{name: 'admon-paises'}">
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
    import pais from '../../../api/admon/paises'
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
                pais.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'País registrado correctamente',
                            variant: 'success',
                            position: 'bottom-right'
                        }
                    });
                    this.$router.push({
                        name: 'admon-paises'
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

