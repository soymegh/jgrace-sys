<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Descripción</label>
                    <input type="text" class="form-control" placeholder="Descripción del municipio" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="">* Departamento</label>
                    <v-select
                            v-model="form.departamento"
                            :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                            label="descripcion"
                            :options="departamentos"
                    />
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.departamento">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
        </b-row>
        <b-card-footer class="text-right">
            <router-link :to="{name: 'admon-municipios'}">
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
    import municipio from '../../../api/admon/municipios'
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
                    departamento:'',
                },
                departamentos:[],
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods:{
            obtenerTodosDepartamentos() {
                var self = this;
                self.loading = true;
                departamentos.obtenerTodos(
                    data => {
                        self.departamentos = data;
                        self.loading = false;
                    },
                    err => {
                        console.log(err);
                        self.loading = false;
                    }
                );

            },

            registrar() {
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                municipio.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                            component: ToastificationContent,
                            props:{
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Municipio registrado correctamente',
                                variant: 'success',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    this.$router.push({
                        name: 'admon-municipios'
                    })
                }, err => {
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
                    self.loading = false;
                    self.errorMessages = err;
                    self.btnAction = 'Registrar'
                })
            }
        },
        mounted() {
            this.obtenerTodosDepartamentos();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>

