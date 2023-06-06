<template>
    <b-card>
        <b-row>
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="label-search">Periodo Actual:</label>
                    <v-select
                            :options="periodos_fiscales"
                            label="periodo"
                            v-model="filter.periodo"
                            v-on:input="obtenerMesesPeriodo()"
                    ></v-select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Mes Actual:</label>
                    <v-select
                            :options="periodos_meses"
                            label="mes_letras"
                            v-model="filter.mes"
                            v-on:input="obtenerDiasMes()"
                    ></v-select>
                </div>
            </div>


            <div class="col-sm-2">
                <div class="form-group">
                    <label class="label-search">Periodo Anterior:</label>
                    <v-select
                            :options="periodos_fiscales1"
                            label="periodo"
                            v-model="filter.periodo1"
                            v-on:input="obtenerMesesPeriodo1()"
                    ></v-select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Mes Anterior:</label>
                    <v-select
                            :options="periodos_meses1"
                            label="mes_letras"
                            v-model="filter.mes1"
                            v-on:input="obtenerDiasMes1()"
                    ></v-select>
                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <b-button @click="obtenerEstadoResultados" variant="primary" class="mt-2"><i class="pe-7s-search"></i> Generar</b-button>
                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label> Formato:</label>
                    <select class="form-control" v-model="formato">
                        <option value="pdf">PDF</option>
                        <option value="xls">Excel</option>
                    </select>
                </div>
            </div>


            <div class="col-sm-3 mt-2">
                <b-button
                        type="button"
                        variant="primary"
                        @click="descargarReporte"
                >Imprimir
                </b-button>
            </div>
        </b-row>
        <div class="invoice-body">
            <table class="table table-responsive-md ">
                <thead>
                <tr>
                    <th class="text-left table-number">Cuenta</th>
                    <th class="text-left table-number">Operacion</th>
                    <th class="text-center table-number">Saldo Actual</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(cuentaContable,key) in cuentasContables">
                    <tr :key="cuentaContable.id_cierre_mensual">
                        <td class="text-left" style="width: 15%">{{ cuentaContable.nombre }}</td>
                        <td class="text-center" v-if="cuentaContable.signo"></td>
                        <td class="text-center" v-if="!cuentaContable.signo">( - )</td>
                        <td class="text-center" style="width: 10%">{{ (Math.abs(Number(cuentaContable.saldox))) | formatMoney(2)}}</td>
                    </tr>

                </template>
                </tbody>
                <tfoot>
                <tr>
                    <td class="text-right" colspan="2"><strong>RESULTADO DESPUÉS DEL I.S.R.</strong></td>
                    <td class="text-center"><strong>C$ {{total_perdida_ganancia | formatMoney(2)}}</strong></td>
                </tr>
                </tfoot>
            </table>
            <!--<div class="invoice-terms">
                <div class="invoice-terms-title">
                    Terms and Conditions
                </div>
                <div class="invoice-terms-content">
                    Should be paid as soon as received, otherwise a 20% penalty fee is applied
                </div>
            </div>-->
        </div>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>
    </b-card>
</template>
<script>
    import {
        BCard,
        BCardText,
        BLink,
        BFormGroup,
        BFormFile,
        BButton,
        BAlert,
        BCardFooter,
        BRow,
        BFormDatepicker
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import nivel_cuenta from '../../../api/contabilidad/niveles_cuentas'
    import reportes_financieros from '../../../api/contabilidad/reportes_financieros'
    import periodo from '../../../api/contabilidad/periodos'
    import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'

    var fecha_actual = new Date();
    fecha_actual.setHours(23, 59, 59, 999);

    export default {
        components: {
            BCard,
            BCardText,
            BLink,
            BFormGroup,
            vSelect,
            BFormFile,
            BButton,
            BAlert,
            BCardFooter,
            BRow,
            BFormDatepicker
        },
        data() {
            return {
                loading:true,
                url : loadingImage,
                formato:'pdf',
                periodos_fiscales:[],
                periodos_fiscales1:[],
                periodos_meses:[],
                periodos_meses1:[],
                fecha_finalx:'',
                fecha_finalx1:'',
                filter: {
                    periodo:"",
                    mes:"",
                    periodo1:"",
                    mes1:""
                },
                cuentasContables: [],
                errorMessages:[],
                total: 0
            }

        },
        computed: {
            total_perdida_ganancia() {
                return this.cuentasContables.reduce((carry, item) => {
                    return (carry + Number(item.saldo_signo))
                }, 0)
            },
        },
        methods: {
            descargarReporte() {
                var self=this;
                self.loading=true;
                axios.post('contabilidad/estados-financieros/estado-resultado/reporte',
                    {
                        id_periodox: self.filter.periodo.id_periodo_fiscal,
                        mesx:self.filter.mes.mes,
                        id_mesx:self.filter.mes.id_periodo_mes,
                        id_periodox1: self.filter.periodo1.id_periodo_fiscal,
                        mesx1:self.filter.mes1.mes,
                        id_mesx1:self.filter.mes1.id_periodo_mes,
                        id_nivel_cuenta:3,
                        extension:self.formato

                    }, { responseType: 'blob' })
                    .then(({ data }) => {
                        let blob = new Blob([data], { type: 'application/pdf' })
                        self.formato === 'xls' ? blob = new Blob([data], { type: 'application/octet-stream' }) : blob = new Blob([data], { type: 'application/pdf' });
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob)
                        link.download = 'EstadoResultado.'+self.formato;
                        link.click()
                        self.loading = false;
                    }).catch(function (error) {
                    console.log(error);
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: '',
                            icon: 'CoffeeIcon',
                            variant: 'danger',
                            text: 'Error descargando archivo'
                        },
                    })
                    self.loading = false;
                });
            },
            obtenerEstadoResultados() {
                var self = this;
                self.loading = true;
                reportes_financieros.obtenerEstadoResultados(self.filter, data => {
                    self.loading = false;
                    self.cuentasContables = data;
                }, err => {
                    //console.log(err)
                    self.loading=false;
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: '',
                            icon: 'CoffeeIcon',
                            variant: 'danger',
                            text: 'Rango de fechas no válido'
                        },
                    })
                })
            },
            obtenerMesesPeriodo() {
                var self = this;
                self.filter.mes = null;
                if(self.periodos_fiscales.meses_periodo) {
                    self.loading = false;
                    self.periodos_meses = self.periodos_fiscales.meses_periodo
                }
            },
            obtenerMesesPeriodo1() {
                var self = this;
                self.filter.mes1 = null;
                if(self.periodos_fiscales1.meses_periodo){
                    self.loading = false;
                    self.periodos_meses1 = self.periodos_fiscales1.meses_periodo
                }
            },
            obtenerDiasMes() {
                var self = this;
                self.fecha_finalx =  moment(new Date(self.filter.periodo.periodo,
                    self.filter.mes.mes-1, self.daysInMonth(self.filter.mes.mes,
                        self.filter.periodo.periodo))).format("DD/MM/YYYY");
            },
            obtenerDiasMes1() {
                var self = this;
                self.fecha_finalx1 =  moment(new Date(self.filter.periodo1.periodo,
                    self.filter.mes1.mes-1, self.daysInMonth(self.filter.mes1.mes,
                        self.filter.periodo1.periodo))).format("DD/MM/YYYY");
            },

            daysInMonth (month, year) {
                return new Date(year, month, 0).getDate();
            },
            obtenerTodosPeriodos() {
                let self = this;
                self.loading = true;
                periodo.obtenerTodos(
                    data => {
                        self.periodos_fiscales = data.periodos;
                        self.filter.periodo=data.periodos[0];
                        self.periodos_meses = data.periodos[0].meses_periodo;
                        self.filter.mes=data.periodos[0].meses_periodo[0];

                        self.periodos_fiscales1 = data.periodos;
                        self.filter.periodo1=data.periodos[0];
                        self.periodos_meses1 = data.periodos[0].meses_periodo;
                        self.filter.mes1=data.periodos[0].meses_periodo[0];

                        self.fecha_finalx =moment(new Date(self.filter.periodo.periodo,
                            self.filter.mes.mes-1, self.daysInMonth(self.filter.mes.mes,
                                self.filter.periodo.periodo))).format("DD/MM/YYYY");

                        self.fecha_finalx1 =moment(new Date(self.filter.periodo1.periodo,
                            self.filter.mes1.mes-1, self.daysInMonth(self.filter.mes1.mes,
                                self.filter.periodo1.periodo))).format("DD/MM/YYYY");
                        self.loading=false;
                    },
                    err => {
                        self.loading=false;
                        console.log(err);
                    }
                );
            },
        },
        mounted() {
            this.obtenerTodosPeriodos();
        }
    }
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
