<template>
    <b-card>
        <b-row>
            <div class="col-md-6 sm-text-center form-inline">

                <label class="label-search">Periodo:</label>
                <v-select
                        label="periodo"
                        v-model="filter.search.anio"
                        :options="periodos"
                        v-on:input="obtenerMeses"
                ></v-select>

                <v-select :style="'margin-left: .5rem!important;'" label="descripcion" v-model="filter.search.mes" :options="meses" >
                </v-select>

                <button class="btn btn-info" style="margin-left: .5rem!important;"
                        @click="filter.page = 1; obtenerTasas();"><i class="pe-7s-search"></i> Buscar
                </button>
            </div>
            <div @keyup.enter="filter.page = 1; obtenerTasas();"
                 class="col-md-6 sm-text-center form-inline justify-content-end">


                <!--<label class="label-search">Rango de fechas:</label>-->
                <!--<datepicker :clear-button="true" placeholder="Fecha Inicial" :language="es" v-model="fecha_inicialx" @selected="onDateSelect" :format="format" :disabledDates="disabledDates"></datepicker>
                <datepicker :clear-button="true" placeholder="Fecha Final" :language="es" v-model="fecha_finalx" @selected="onDateSelect2" :format="format" :disabledDates="disabledDates"></datepicker> -->

                <a :disabled="descargando" @click.prevent="downloadItem('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" variant="danger" v-b-tooltip.hover.top="'Descargar PDF!'">
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItem('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" variant="success" v-b-tooltip.hover.top="'Descargar Excel!'">
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>

                <!--<download-excel
                   class = "btn btn-success" style = "margin-right: .5rem!important;" :fetch   = "obtenerTasasReporte" :fields = "json_fields"
                   worksheet = "Listado Tasas de cambio" title = "Listado Tasas de cambio" name = "ReporteTC.xls" :before-generate = "startDownload" :before-finish = "finishDownload"
                   type    = "xls">
                   <i class="pe-7s-download"></i>
                   Descargar Excel
               </download-excel>-->

                <b-button variant="success" style="margin-left: .5rem!important;" @click="descargarTasas();">
                    <feather-icon icon="DownloadIcon"></feather-icon>
                    Descargar TC Banco Central
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="text-center table-number">Fecha</th>
                    <th class="text-center table-number">Tasa de cambio Oficial Banco Central</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="tasa in tasas" :key="tasa.id_tasa_cambio">
                    <td class="text-center">{{ tasa.fecha }}</td>
                    <td class="text-center">{{ tasa.tasa > 0?tasa.tasa:'N/D' }}</td>
                </tr>
                <tr v-if="!tasas.length">
                    <td class="text-center" colspan="2">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination @changePage="changePage" @changeLimit="changeLimit" :items="tasas" :total="total"
                        :page="filter.page" :limitOptions="filter.limitOptions" :limit="filter.limit"></pagination>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import vSelect from 'vue-select';
    import {
        BFormDatepicker,
        BRow,
        BCol,
        BCard,
        BCardFooter,
        BPaginationNav,
        BButton,
        VBTooltip,
        BFormCheckbox,
        BFormGroup
    } from 'bootstrap-vue'
    import tasa from '../../../api/contabilidad/tasas-cambio'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    var fecha_actual = new Date();
    fecha_actual.setHours(23, 59, 59, 999);
    export default {
        components: {
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, BFormCheckbox, BFormGroup,
            vSelect
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                json_fields: {
                    'Fecha': 'fecha',
                    'Tasa de cambio oficial': 'tasa',
                },
                filter: {
                    page: 1,
                    limit: 20,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        mes: 0,//moment(new Date()).format("YYYY-MM-DD"),
                        anio: 0,//moment(new Date()).format("YYYY-MM-DD"),
                    }
                },
                periodos: [],
                meses: [],
                tasas: [],
                tasas_full: [],
                total_full: 0,
                total: 0,
                descargando: false,
            }
        },
        methods: {
            onDateSelect(date) {
                this.filter.search.fecha_inicial = moment(date).format("YYYY-MM-DD"); //
            },
            onDateSelect2(date) {
                this.filter.search.fecha_final = moment(date).format("YYYY-MM-DD"); //
            },
            obtenerTasas() {
                var self = this;
                self.loading = true;
                tasa.obtenerTasas(self.filter, data => {
                    self.periodos = data.periodos
                    self.tasas = data.rows
                    self.total = data.total
                    self.filter.search.anio = self.periodos[0]
                    self.meses = self.filter.search.anio.meses_periodo
                    //self.filter.search.mes = self.meses[0]
                    self.loading = false;
                }, err => {
                    self.loading = false;
                    console.log(err)
                })
            },
            obtenerMeses() {
                let self = this;
                self.filter.search.mes = [];
                self.meses = self.filter.search.anio.meses_periodo
                self.filter.search.mes = self.meses[0]
            },

            descargarTasas() {
                var self = this;
                self.msg = 'Descargando datos del Banco Central, espere un momento.....'
                self.loading = true;
                tasa.descargarTasas(self.filter, data => {
                    //console.log(data);
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'BellIcon',
                                text: 'Tasas de cambio actualizadas',
                                variant: 'success',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    this.obtenerTasas()

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
                    self.loading = false;
                })
            },


            /*async obtenerTasasReporte() {
            var self = this
              const response =  await axios.post('contabilidad/tasas-cambio/obtener-tasas-reporte', self.filter);
            return response.data.result;
            },*/
            startDownload() {
                alertify.warning("Ha empezado la descarga del documento...", 2);
            },
            finishDownload() {
                alertify.success("Documento descargado correctamente", 3);
            },
            changeLimit(limit) {
                this.filter.page = 1
                this.filter.limit = limit
                this.obtenerTasas()
            },
            changePage(page) {
                this.filter.page = page
                this.obtenerTasas()
            },
            downloadItem(ext) {
                var self = this;
                self.msg = 'Descargando datos, espere un momento.....'
                self.loading = true;
                //if(!this.descargando){
                //	this.descargando=true;
                //alertify.success("Descargando datos, espere un momento.....",5);

                axios.get('contabilidad/tasas-cambio/reporte/' + ext + '/' + self.filter.search.anio.periodo + '/' + self.filter.search.mes.mes, {responseType: 'blob'})
                    .then(({data}) => {
                        let blob = new Blob([data], {type: 'application/pdf'})
                        ext === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob)
                        link.download = 'Reporte_tasas_cambio.' + ext;
                        link.click()
                        //this.descargando=false;
                        self.loading = false;
                        self.msg = 'Cargando el contenido espere un momento'
                    }).catch(function (error) {
                    alertify.error("Error Descargando archivo.....", 5);
                    //self.descargando = false;
                    self.loading = false;
                    self.msg = 'Cargando el contenido espere un momento'
                })
                /*}else{
                    alertify.warning("Espere a que se complete la descarga anterior",5);
                }*/
            },
        },
        /*components: {
            //'pagination': Pagination,
            //'datepicker':Datepicker,
            //'downloadExcel': JsonExcel,
            //'typeahead':Typeahead
        },*/
        mounted() {
            this.obtenerTasas()
        }
    }
</script>
<style lang="scss">
    @import '../../../@core/scss/vue/libs/vue-select';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';

    .search-field {
        min-width: 120px;
    }

    .table {
        a {
            color: #2675dc;
        }

        .table-number {
            width: 60px;
        }

        .action {
            width: 100px;
        }
    }

    .table-groups {
        tr {
            &.active {
                color: #fff;
                background: rgba(29, 39, 94, 0.72);
            }

            .group-list {
                display: -webkit-box;
                display: -moz-box;
                display: -ms-flexbox;
                display: -webkit-flex;
                display: flex;

                i {
                    margin-top: auto;
                    margin-bottom: auto;
                    margin-left: auto;
                }
            }
        }
    }
</style>
