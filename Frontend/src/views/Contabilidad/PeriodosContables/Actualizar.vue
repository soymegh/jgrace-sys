<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Descripción Periodo</label>
                    <input class="form-control" placeholder="Dígite la descripcion del periodo"
                           v-model="form.descripcion">
                    <ul class="error-messages">
                        <li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Periodo</label>
                    <input disabled class="form-control" type="number" v-model="form.periodo"
                           placeholder="Dígite la descripcion del periodo">
                    <ul class="error-messages">
                        <li v-for="message in errorMessages.periodo" :key="message" v-text="message"></li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Estado</label>
                    <select class="form-control" v-model="form.estado">
                        <option value="0">En progreso</option>
                        <option value="1">Completado</option>
                    </select>
                    <ul class="error-messages">
                        <li v-for="message in errorMessages.estado" :key="message" v-text="message"></li>
                    </ul>
                </div>
            </div>
        </b-row>
        <b-card-footer>
            <div class="text-right">
                <router-link class="mx-1" :to="{ name: 'contabilidad-periodos-contables' }">
                    <b-button variant="secondary">Cancelar</b-button>
                </router-link>
                <b-button variant="primary" @click="actualizar"
                        :disabled="btnAction != 'Guardar' ? true : false">{{ btnAction }}
                </b-button>
            </div>
            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,VBTooltip,BFormCheckbox,BFormGroup} from 'bootstrap-vue'
    import periodo from '../../../api/contabilidad/periodos'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    export default {
        components:{
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,BFormCheckbox,BFormGroup
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading:true,
                url : loadingImage,   //It is important to import the loading image then use here
                form:{
                    descripcion: '',
                    periodo: 2019,
                    salario_mensual_techo: 0,
                    porcentaje_inss_base: 0,
                    porcentaje_ir_base: 0,
                },
                btnAction: 'Guardar',
                errorMessages: []
            }
        },
        methods: {
            obtenerPeriodo() {
                var self = this
                periodo.obtenerPeriodo({
                    id_periodo_fiscal: self.$route.params.id_periodo_fiscal
                }, data => {
                    self.form = data;
                    self.loading = false;
                }, err => {
                    alertify.error(err.id_periodo_fiscal[0], 5);
                    this.$router.push({
                        name: 'contabilidad-periodos-contables'
                    });
                })


            },
            actualizar() {
                var self = this
                self.loading = true;
                self.btnAction = 'Guardando, espere......'
                periodo.actualizar(self.form, data => {
                    this.$toast({
                            component: ToastificationContent,
                            props:{
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Datos Registrados correctamente',
                                variant: 'success',
                                position: 'top-center'
                            }
                        },
                        {
                            position : 'bottom-right'
                        });
                    this.$router.push({
                        name: 'contabilidad-periodos-contables'
                    })
                }, err => {
                    self.loading = false;
                    this.$toast({
                            component: ToastificationContent,
                            props:{
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Ha ocurrido un error: '+ err,
                                variant: 'warning',
                            }
                        },
                        {
                            position : 'bottom-right'
                        });
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            }
        },
        mounted() {
            this.obtenerPeriodo()
        }
    }
</script>
