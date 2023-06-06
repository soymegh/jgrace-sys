<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Tipos Cuentas:</label>
                    <v-select
                            :disabled="true"
                            :options="niveles_cuenta"
                            label="descripcion"
                            v-model="filter.nivel_cuenta">
                    </v-select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Periodo:</label>
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
                    <label class="label-search">Mes:</label>
                    <v-select :options="periodos_meses" label="mes_letras" v-model="filter.mes" v-on:input="obtenerDiasMes()"></v-select>
                </div>
            </div>
            <div class="col-sm-3 mt-2">
                <div class="form-group">
                    <b-button @click="obtenerBalanceGeneral" variant="info"><i class="pe-7s-search"></i> Generar</b-button>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-responsive-md ">
                                <thead>
                                <tr>
                                    <th class="text-center table-number">Cuenta</th>
                                    <th class="text-left table-number">Nombre Cuenta</th>
                                    <th class="text-center table-number">Saldo</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr :key="cuentaContable.id_cierre_mensual" v-for="cuentaContable in cuentasContablesActivos">
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 2" ><strong>{{ cuentaContable.cta_contable }}</strong></td>
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 3">{{ cuentaContable.cta_contable }}</td>
                                    <td class="text-left" v-if="cuentaContable.id_nivel_cuenta === 2" ><strong>{{ cuentaContable.nombre_cuenta }}</strong></td>
                                    <td class="text-left" style="padding-left:2em" v-if="cuentaContable.id_nivel_cuenta === 3">{{ cuentaContable.nombre_cuenta }}</td>
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 2" ><strong>{{ Number(cuentaContable.saldox) | formatMoney(2)}}</strong></td>
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 3">{{ Number(cuentaContable.saldox) | formatMoney(2)}}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <!--<tr>
                                    <td>Total</td>
                                    <td class="text-right" colspan="2">$</td>
                                </tr>-->
                                </tfoot>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-responsive-md ">
                                <thead>
                                <tr>
                                    <th class="text-center table-number">Cuenta</th>
                                    <th class="text-left table-number">Nombre Cuenta</th>
                                    <th class="text-center table-number">Saldo</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr :key="cuentaContable.id_cierre_mensual" v-for="cuentaContable in cuentasContablesPasivosCapital">
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 2" ><strong>{{ cuentaContable.cta_contable }}</strong></td>
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 3">{{ cuentaContable.cta_contable }}</td>
                                    <td class="text-left" v-if="cuentaContable.id_nivel_cuenta === 2" ><strong>{{ cuentaContable.nombre_cuenta }}</strong></td>
                                    <td class="text-left" style="padding-left:2em" v-if="cuentaContable.id_nivel_cuenta === 3">{{ cuentaContable.nombre_cuenta }}</td>
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 2" ><strong>{{ Number(cuentaContable.saldox) | formatMoney(2)}}</strong></td>
                                    <td class="text-center" v-if="cuentaContable.id_nivel_cuenta === 3">{{ Number(cuentaContable.saldox) | formatMoney(2)}}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th class="text-center table-number"></th>
                                    <th class="text-left table-number"><strong>Total Activos</strong></th>
                                    <th class="text-center table-number"><strong>C$ {{total_pasivos_capital | formatMoney(2)}}</strong></th>-->
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-responsive-md ">
                                <thead>
                                <tr>
                                    <th class="text-right table-number"><strong>Total Activos</strong></th>
                                    <th class="text-right table-number"><strong>C$ {{total_activos | formatMoney(2)}}</strong></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-responsive-md ">
                                <thead>
                                <tr>
                                    <th class="text-right table-number"><strong>Total Pasivos + Capital</strong></th>
                                    <th class="text-right table-number"><strong>C$ {{total_pasivos_capital | formatMoney(2)}}</strong></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
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
    fecha_actual.setHours(23,59,59,999);

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
                loading: false,
                url: loadingImage,
                niveles_cuenta: [],
                periodos_fiscales:[],
                periodos_meses:[],
                formato:'pdf',
                disabledDates: {
                    to: '', // Disable all dates up to specific date
                    from: fecha_actual, // Disable all dates after specific date
                },
                es: es,
                format: "d MMMM yyyy",
                fecha_inicialx: '',//new Date(),
                fecha_finalx: '',//new Date(),
                filter: {
                    nivel_cuenta: "",
                    periodo:"",
                    mes:""
                },
                cuentasContablesActivos: [],
                cuentasContablesPasivosCapital: [],
                total: 0
            }

        },
        computed: {
            total_activos() {
                return this.cuentasContablesActivos.reduce((carry, item) => {
                    return (carry + (item.id_nivel_cuenta ===2 ? Number(item.saldox) : 0))

                }, 0)
            },
            total_pasivos_capital() {
                return this.cuentasContablesPasivosCapital.reduce((carry, item) => {
                    return (carry + (item.id_nivel_cuenta ===2 ? Number(item.saldox) : 0))
                }, 0)
            },
        },
        methods: {
            descargarReporte() {
                var self=this;
                self.loading=true;
                axios.post('contabilidad/estados-financieros/balance-general/reporte',
                    {
                        id_periodox: self.filter.periodo.id_periodo_fiscal,
                        mesx:self.filter.mes.mes,
                        id_mesx:self.filter.mes.id_periodo_mes,
                        id_nivel_cuenta:3,
                        extension:self.formato

                    }, { responseType: 'blob' })
                    .then(({ data }) => {
                        let blob = new Blob([data], { type: 'application/pdf' })
                        self.formato === 'xls' ? blob = new Blob([data], { type: 'application/octet-stream' }) : blob = new Blob([data], { type: 'application/pdf' });
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob)
                        link.download = 'ReporteSituacionFinanciera.'+self.formato;
                        link.click()
                        self.loading = false;
                    }).catch(function (error) {
                    console.log(error);
                    alertify.error("Error Descargando archivo.....", 5);
                    self.loading = false;
                });
            },
            obtenerMesesPeriodo() {
                var self = this;
                self.filter.mes = null;
                if(self.periodos_fiscales.meses_periodo){
                    self.periodos_meses = self.filter.periodo.meses_periodos;
                }
            },
            obtenerDiasMes() {
                var self = this;
                self.fecha_finalx =  moment(new Date(self.filter.periodo.periodo,
                    self.filter.mes.mes-1, self.daysInMonth(self.filter.mes.mes,
                        self.filter.periodo.periodo))).format("DD/MM/YYYY");
            },

            daysInMonth (month, year) {
                return new Date(year, month, 0).getDate();
            },
            /* onDateSelect(date) {
             this.filter.fecha_inicial = moment(date).format("YYYY-MM-DD"); //
             },
             onDateSelect2(date) {
             this.filter.fecha_final = moment(date).format("YYYY-MM-DD"); //
             },*/
            obtenerBalanceGeneral() {
                var self = this
                self.loading=true;
                reportes_financieros.obtenerBalanceGeneral(self.filter, data => {
                    self.cuentasContablesActivos = data.activos;
                    self.cuentasContablesPasivosCapital = data.pasivo_capital;
                    self.loading=false;
                }, err => {
                    self.loading = false;
                    console.log(err)
                })
            },
            obtenerTodosPeriodos() {
                let self = this;
                periodo.obtenerTodos(
                    data => {
                        self.periodos_fiscales = data.periodos;
                        self.filter.periodo=data.periodos[0];
                        self.periodos_meses = data.periodos[0].meses_periodo;
                        self.filter.mes=data.periodos[0].meses_periodo[0];
                        self.fecha_finalx =moment(new Date(self.filter.periodo.periodo,
                            self.filter.mes.mes-1, self.daysInMonth(self.filter.mes.mes,
                                self.filter.periodo.periodo))).format("DD/MM/YYYY");
                        this.loading=false;
                    },
                    err => {
                        console.log(err);
                    }
                );
            },
            obtenerTodosNivelesCuenta() {
                var self = this;
                nivel_cuenta.obtenerTodosNivelesCuenta(
                    data => {
                        self.niveles_cuenta = data;
                        self.filter.nivel_cuenta=self.niveles_cuenta[1];
                    },
                    err => {
                        console.log(err);
                    }
                );
            },
        },
        mounted() {
            this.obtenerTodosNivelesCuenta();
            this.obtenerTodosPeriodos();
        }
    }
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
