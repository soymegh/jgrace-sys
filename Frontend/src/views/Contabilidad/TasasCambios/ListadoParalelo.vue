<template>
    <b-card>
        <b-row>
            <div class="col-md-6 sm-text-center form-inline">
                <!--<input type="file" accept="image/*" capture="camera" />-->
                <label class="label-search">Periodo:</label>
                <v-select
                        :options="periodos"
                        label="periodo"
                        v-model="filter.search.anio"
                        v-on:input="obtenerMeses"
                >
                    <template slot="no-options">No se encontraron registros</template>
                </v-select>

                <v-select :options="meses"
                          :style="'margin-left: .5rem!important;'"
                          label="descripcion"
                          v-model="filter.search.mes"

                ></v-select>

                <b-button @click="filter.page = 1; obtenerTasas();" style="margin-left: .5rem!important;"
                          v-b-tooltip.hover.top="'Buscar'"
                          variant="info">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
            </div>


            <div class="col-md-6 sm-text-center form-inline justify-content-end">
                <b-button
                        :disabled="btnAction !== 'Actualizar TC Paralelas'"
                        @click="actualizarTCParalelas"
                        type="button"
                        variant="primary"
                >{{ btnAction }}
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="text-center table-number">Fecha</th>
                    <th class="text-center table-number">Tasa de cambio paralela</th>
                    <th class="text-center table-number">Modificada</th>
                    <th class="text-center table-number">Deshacer</th>
                </tr>
                </thead>
                <tbody>
                <tr :key="tasa.id_tasa_cambio" v-for="(tasa, index) in tasas">
                    <td class="text-center">{{ tasa.fecha }}</td>
                    <td class="text-center">
                        <input @change="tasa.tasa_paralela = Math.max(Math.min(Number(tasa.tasa_paralela), 99), 1)"
                               class="form-control" min="1" step="0.0001" type="number"
                               v-model.number="tasa.tasa_paralela">
                        <b-alert variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages[`tasas_nuevas.${index}.tasa_paralela`]"
                                        v-text="message">
                                </li>
                            </ul>
                        </b-alert>

                    </td>
                    <td class="text-center">
                        <div v-if="Number(tasa.tasa_paralela)===Number(tasa.tasa_org)">
                            <span class="badge badge-dark">Sin modificar</span>
                        </div>
                        <div v-else>
                            <span class="badge badge-success">Modificada</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <div v-if="Number(tasa.tasa_paralela)===Number(tasa.tasa_org)">
<!--                            <i aria-hidden="true" class="fa fa-undo"
                               v-b-tooltip.hover.top="'Restaurar a tasa de cambio inicial'"></i>-->
                            <feather-icon aria-hidden="true" v-b-tooltip.hover.top="'Restaurar a tasa de cambio inicial'" icon="RotateCcwIcon"></feather-icon>
                        </div>
                        <div v-else>
                            <!--<i :style="'color:blue;'"
                               @click="tasa.tasa_paralela=tasa.tasa_org" aria-hidden="true" class="fa fa-undo"
                               v-b-tooltip.hover.top="'Restaurar a tasa de cambio inicial'"></i>-->
                            <feather-icon @click="tasa.tasa_paralela=tasa.tasa_org" style="color:blue" aria-hidden="true" v-b-tooltip.hover.top="'Restaurar a tasa de cambio inicial'" icon="RotateCcwIcon"></feather-icon>
                        </div>
                    </td>
                </tr>
                <tr v-if="!tasas.length">
                    <td class="text-center" colspan="4">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination :items="tasas" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page"
                        :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>
        </b-card-footer>


        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>

    </b-card>
</template>
<script type="text/ecmascript-6">
    import vSelect from 'vue-select';
    import {
        BButton,
        BCard,
        BCardFooter,
        BCol,
        BFormCheckbox,
        BFormDatepicker,
        BFormGroup,
        BPaginationNav,
        BRow,
        VBTooltip,BAlert
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
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, BFormCheckbox, BFormGroup,BAlert,
            vSelect
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: true,
                msg: 'Cargando el contenido espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                json_fields: {
                    'Fecha': 'fecha',
                    'Tasa de cambio oficial': 'tasa',
                },
                errorMessages: [],
                btnAction: "Actualizar TC Paralelas",
                format: "d MMMM yyyy",
                filter: {
                    page: 1,
                    limit: 31,
                    limitOptions: [31],
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
            actualizarTCParalelas(estadox) {
                var self = this;
                self.loading = true;
                self.btnAction = "Actualizando, espere.....";
                tasa.actualizarTCParalelas({
                        tasas_nuevas: self.tasas,
                    },
                    data => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'Tasas actualizadas correctamente',
                                    variant: 'success',
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.obtenerTasas();
                        self.btnAction = "Actualizar TC Paralelas";
                        self.loading = false;
                    },
                    err => {
                        self.errorMessages = err;
                        self.btnAction = "Actualizar TC Paralelas";
                        self.loading = false;
                    }
                );
            },

            obtenerTasas() {
                var self = this;
                self.loading = true;
                tasa.obtenerTasas(self.filter, data => {

                    data.rows.forEach((tasas, key) => {
                        data.rows[key].tasa_org = data.rows[key].tasa_paralela;
                    });

                    self.tasas = data.rows;

                    self.total = data.total;
                    self.periodos = data.periodos;
                    //self.filter.search.anio = self.periodos[0]
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
                self.meses = self.filter.search.anio.meses_periodo;
                self.filter.search.mes = self.meses[0]
            },

            changeLimit(limit) {
                this.filter.page = 1;
                this.filter.limit = limit;
                this.obtenerTasas()
            },
            changePage(page) {
                this.filter.page = page;
                this.obtenerTasas()
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
