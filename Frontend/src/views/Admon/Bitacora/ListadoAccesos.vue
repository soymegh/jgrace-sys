<template>
    <div>
        <b-card>
            <b-row>
                <div @keyup.enter="filter.page = 1; obtenerAccesos();" class="col-md-6 sm-text-center form-inline">
                    <select v-model="filter.search.field" class="form-control mb-2 mr-sm-2 mb-sm-0 search-field">
                        <option value="alias">Nombre usuario</option>
                        <option value="direccion_ip">Dirección IP</option>
                        <option value="dispositivo">Dispositivo</option>
                    </select>
                    <input v-model="filter.search.value" class="form-control mb-2 mr-sm-2 mb-sm-0" placeholder="Buscar"
                           type="text">
                </div>

                <div @keyup.enter="filter.page = 1; obtenerAccesos()" class="col-md-6 sm-text-center form-inline">
                    <label class="label-search">Rango de fechas (opcional):</label>
                    <!--                    <datepicker :clear-button="false" placeholder="Fecha Inicial" :language="es" v-model="fecha_inicialx" @selected="onDateSelect" :format="format" :disabledDates="disabledDates"></datepicker>-->
                    <b-form-datepicker
                            local="es"
                            placeholder="F. inicial"
                            class="mb-0"
                            selected-variant="primary"
                            v-model="filter.search.fecha_inicial"
                            @select="onDateSelect"
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                    />

                    <b-form-datepicker
                            local="es"
                            placeholder="F. final"
                            class="mb-0"
                            selected-variant="primary"
                            v-model="filter.search.fecha_final"
                            @select="onDateSelect2"
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                    />
                    <!--                    <datepicker :clear-button="false" placeholder="Fecha Final" :language="es" v-model="fecha_finalx" @selected="onDateSelect2" :format="format" :disabledDates="disabledDates"></datepicker>-->

                    <b-button class="btn btn-info mx-1" style="margin-right: .5rem!important;"
                              @click="filter.page = 1; obtenerAccesos();"> <feather-icon icon="SearchIcon"></feather-icon></b-button>
                    <!--<a :disabled="descargando" @click.prevent="downloadItem('administracion/accesos/reporte')" style="color: #ffffff;"><button :disabled="descargando" class="btn btn-success"><i aria-hidden="true" class="fa fa-file-pdf-o"></i></button></a>-->
                    <!--	 <download-excel
                            class = "btn btn-success" style = "margin-right: .5rem!important;" :fetch   = "obtenerAccesosReporte" :fields = "json_fields"
                            worksheet = "Listado Accesos" title = "Listado Accesos" name = "ReporteAccesos" :before-generate = "startDownload" :before-finish = "finishDownload"
                            type    = "xls">
                            <i class="pe-7s-download"></i>
                            Descargar Excel
                        </download-excel>-->
                    <!--<div class="form-group">
                    <typeahead style="width: 300px;" :initial="usuarioBusqueda" :trim="80" :url="usuariosBusquedaURL" @input="seleccionarUsuario"></typeahead>
                   </div>-->
                </div>
            </b-row>
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center table-number">Usuario</th>
                        <th class="text-center table-number">Dispositivo</th>
                        <th class="text-center table-number">Dirección IP</th>
                        <th class="text-center table-number">Fecha / Hora acceso</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="acceso in accesos" :key="acceso.id_acceso">
                        <td class="text-center">{{ acceso.alias }}</td>
                        <td class="text-center">{{ acceso.dispositivo }}</td>
                        <td class="text-center">{{ acceso.direccion_ip }}</td>
                        <td class="text-center">{{ acceso.f_acceso }}</td>

                    </tr>
                    <tr v-if="!accesos.length">
                        <td class="text-center" colspan="4">Sin datos</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <b-card-footer>
                <pagination class="pagination b-pagination mb-0" @changePage="changePage" @changeLimit="changeLimit" :items="accesos" :total="total"
                            :page="filter.page" :limitOptions="filter.limitOptions" :limit="filter.limit"></pagination>
            </b-card-footer>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card>
    </div>
</template>
<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton} from 'bootstrap-vue'
    import acceso from '../../../api/admon/bitacora'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";

    var fecha_actual = new Date();
    fecha_actual.setHours(23, 59, 59, 999);
    export default {
        components: {
            BFormDatepicker,
            BRow,
            BCard,
            BCardFooter,
            BPaginationNav,
            BButton
        },
        data() {
            return {
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                json_fields: {
                    'Nombre de usuario': 'alias',
                    'Fecha de acceso': 'f_acceso',
                    'Direccion IP': 'direccion_ip',
                },
                descargando: false,

                disabledDates: {
                    to: '', // Disable all dates up to specific date
                    from: fecha_actual, // Disable all dates after specific date
                },
                es: es,
                format: "d MMMM yyyy",
                fecha_inicialx: new Date(),
                fecha_finalx: new Date(),
                filter: {
                    page: 1,
                    limit: 10,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: 'alias',
                        value: '',
                        fecha_inicial: '',//moment(new Date()).format("YYYY-MM-DD"),
                        fecha_final: '',//moment(new Date()).format("YYYY-MM-DD"),
                    }
                },
                accesos: [],
                accesos_full: [],
                total_full: 0,
                total: 0
            }
        },
        methods: {
            onDateSelect(date) {
                this.filter.search.fecha_inicial = moment(date).format("YYYY-MM-DD"); //
            },
            onDateSelect2(date) {
                this.filter.search.fecha_final = moment(date).format("YYYY-MM-DD"); //
            },
            obtenerAccesos() {
                var self = this
                 self.loading = true;
                acceso.obtenerAccesos(self.filter, data => {
                    self.accesos = data.rows
                    self.total = data.total
                    self.loading = false;
                }, err => {
                    console.log(err)
                })
            },
            downloadItem(url) {
                var self = this;
                if (!this.descargando) {
                    this.descargando = true;
                    self.loading = true;
                    alertify.success("Descargando datos, espere un momento.....", 5);
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})
                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'Reporte_Accesos.pdf';
                            link.click()
                            this.descargando = false;
                            self.loading = false;
                        })
                } else {
                    alertify.warning("Espere a que se complete la descarga anterior", 5);
                }
            },
            changeLimit(limit) {
                this.loading = true;
                this.filter.page = 1
                this.filter.limit = limit
                this.obtenerAccesos()
            },
            changePage(page) {
                this.loading = true;
                this.filter.page = page
                this.obtenerAccesos()
            }
        },
        /*components: {
            //'pagination': Pagination,
            'datepicker':Datepicker,
        //	'downloadExcel': JsonExcel,
            //'typeahead':Typeahead
        },*/
        mounted() {
            this.obtenerAccesos()
        }
    }
</script>

<style lang="scss" scoped>

    @import "src/assets/scss/style.scss";

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
</style>
