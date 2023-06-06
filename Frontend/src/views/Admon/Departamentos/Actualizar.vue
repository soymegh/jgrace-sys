<template>
    <b-card>
        <b-row>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="">* Descripción</label>
                    <input type="text" class="form-control" placeholder="Nombre del departamento" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>
                </div>
            </div>
        </b-row>
            <b-card-footer>
                <b-row>
                    <div class="col-md-12 col-12 text-lg-right text-center mt-1">
                        <router-link :to="{name: 'admon-departamentos'}">
                            <b-button class="btn btn-default mx-1" variant="secondary">Cancelar</b-button>
                        </router-link>

                        <b-button :disabled="btnAction !== 'Guardar' ? true : false" @click="actualizar"
                                  variant="primary">
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
    import {
        BFormDatepicker,
        BRow,
        BCol,
        BCard,
        BCardFooter,
        BPaginationNav,
        BButton,
        VBTooltip,
        BAlert
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import departamento from '../../../api/admon/departamentos'
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
        data() {
            return {
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                form: {
                    descripcion: '',
                    codigo: ''
                },
                btnAction: 'Guardar',
                errorMessages: []
            }
        },
        methods: {
            obtenerDepartamento() {
                var self = this;
                self.loading = true;
                departamento.obtenerDepartamento({
                    id_departamento: self.$route.params.id_departamento
                }, data => {
                    self.form = data
                    self.loading = false;
                }, err => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'BellIcon',
                                text: 'Ha ocurrido un error',
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    this.$router.push({
                        name: 'admon-departamentos'
                    });
                })


            },
            actualizar() {
                var self = this;
                self.loading = true;
                self.btnAction = 'Guardando, espere......';
                departamento.actualizar(self.form, data => {
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Departamento actualizado correctamente',
                            variant: 'success',
                            position: 'bottom-right'
                        }
                    },
                        {
                            position: 'bottom-right'
                        }
                    );
                    this.$router.push({
                        name: 'admon-departamentos'
                    })
                }, err => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'BellIcon',
                            text: 'Ha ocurrido un error',
                            variant: 'warning',

                        }
                    },
                        {
                            position: 'bottom-right'
                        });
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            },
        },
        mounted() {
            this.obtenerDepartamento();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '~sweetalert2/dist/sweetalert2.css';
</style>

