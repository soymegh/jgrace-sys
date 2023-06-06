<template>
    <b-card>
        <b-row>
            <template v-if="this.currency_id === 1">
                <div class="col-sm-2 sm-text-center form-inline justify-content-start">
                    <router-link :to="{ name: 'recibos-registrar' }" class="btn btn-success" tag="button">
                        <feather-icon icon="PlusIcon"></feather-icon>
                        Registrar
                    </router-link>
                </div>
            </template>
            <template v-else>
                <div class="col-sm-2 sm-text-center form-inline justify-content-start">
                    <router-link :to="{ name: 'recibos-registrar-dol' }" class="btn btn-success" tag="button">
                        <feather-icon icon="PlusIcon"></feather-icon> Registrar
                    </router-link>
                </div>
            </template>

            <div
                    @keyup.enter="filter.page = 1;obtenerRecibos();"
                    class="col-sm-10 sm-text-center form-inline justify-content-end"
            >
                <b-form-select
                        class="mb-1 mr-sm-1 mb-sm-0 search-field"
                        v-model="filter.search.field"
                >
                    <option value="nombre_persona">Nombre Cliente</option>
                </b-form-select>
                <input
                        class="form-control mb-1 mr-sm-1 mb-sm-0"
                        placeholder="Buscar"
                        type="text"
                        v-model="filter.search.value"
                >
                <b-button @click="filter.page = 1;obtenerRecibos();" variant="info">
                    <feather-icon icon="SearchIcon"></feather-icon>
                    Buscar
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th>Concepto</th>
                    <th>No. Recibo</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <template v-if="currency_id === 1">
                        <th>Monto C$</th>
                    </template>
                    <template v-else>
                        <th>Monto $</th>
                    </template>
                    <th>Estado</th>

                    <!-- <th>Concepto</th>-->
                   <!-- <th class="text-center action" >Estado</th>-->
                    <th class="text-center action" colspan="2">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(recibo,key) in recibos">
                    <tr>
                        <td :key="recibo.id_recibo" @click="mostrarPagos(key)" class="detail-link">
                            <div class="link" v-b-tooltip.hover.top="'Mostrar detalles'"></div>
                        </td>
                        <td>{{ recibo.concepto}}</td>
                        <td>{{ recibo.no_documento}}</td>
                        <td>{{ recibo.tipo_recibo === 1 ? 'Cancelación factura crédito' : recibo.tipo_recibo === 3 ? 'Cancelación factura contado' : 'Anticipo de cliente'}}</td>
                        <td>{{ formatDate(recibo.fecha_emision)}}</td>
                        <td>{{ recibo.recibo_cliente.nombre_comercial }}</td>
                        <template v-if="currency_id === 1">
                            <td>{{ Number(recibo.monto_total) | formatMoney(2) }}</td>
                        </template>
                        <template v-else>
                            <td>{{ Number(recibo.monto_total_me) | formatMoney(2) }}</td>
                        </template>
                        <td class="text-center">
                            <div v-if="recibo.estado===0">
                                <b-badge variant="danger"> Cancelado</b-badge>
                            </div>
                            <div v-if="recibo.estado===1">
                                <b-badge variant="info"> Registrado</b-badge>
                            </div>
                            <div v-if="recibo.estado===3">
                                <b-badge variant="secondary"> Facturado</b-badge>
                            </div>
                        </td>
                        <!--<td>{{ recibo.concepto }}</td>-->

                        <td class="text-center">
                            <router-link
                                    :to="{ name: 'recibos-mostrar', params: { id_recibo: recibo.id_recibo } }"
                                    tag="a"
                                    v-b-tooltip.hover.top="'Ver vista previa'"
                            >
                                <feather-icon icon="EyeIcon"></feather-icon>
                            </router-link>
                        </td>
                        <!--<td class="text-center">
                            <div v-if="recibo.estado===0">
                                <span class="badge badge-danger">Anulado</span>
                            </div>
                            <div v-if="recibo.estado===1">
                                <span class="badge badge-info">Registrado</span>
                            </div>
                            <div v-if="recibo.estado===3">
                                <span class="badge badge-success">Cancelado</span>
                            </div>
                        </td>-->
                        <td class="text-center">
                            <a v-if="recibo.estado === 1" v-b-tooltip.hover.top="'Anular recibo'"
                               @click.prevent="anular(recibo.id_recibo)"> <feather-icon icon="TrashIcon" style="color: red"></feather-icon></a>
                        </td>
                    </tr>
                    <tr v-if="recibo.mostrar">
                        <td></td>
                        <td colspan="6">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Descripción Pago</th>
                                        <template v-if="currency_id === 1">
                                            <th>Monto C$</th>
                                        </template>
                                        <template v-else>
                                            <th>Monto $</th>
                                        </template>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr :key="recibosDetalle.id_recibo_producto"
                                        v-for="recibosDetalle in recibo.recibo_detalles">
                                        <td>{{ recibosDetalle.descripcion_pago }}</td>
                                        <template v-if="currency_id === 1">
                                            <td>{{ recibosDetalle.monto | formatMoney(2)}}</td>
                                        </template>
                                        <template v-else>
                                            <td>{{ recibosDetalle.monto_me | formatMoney(2)}}</td>
                                        </template>

                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>

                        </td>
                        <td></td>
                    </tr>
                </template>
                <tr v-if="!recibos.length">
                    <td class="text-center" colspan="6">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>

        <b-card-footer>
            <pagination
                    :items="recibos"
                    :limit="filter.limit"
                    :limitOptions="filter.limitOptions"
                    :page="filter.page"
                    :total="total"
                    @changeLimit="changeLimit"
                    @changePage="changePage"
            ></pagination>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import recibo from "../../../api/CuentasXCobrar/recibos_oficiales";
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import {
        BAlert,
        BButton,
        BCard,
        BCardFooter,
        BFormCheckbox,
        BFormCheckboxGroup,
        BFormDatepicker,
        BFormGroup,
        BFormSelect,
        BPaginationNav,
        BRow,
        VBTooltip,
        BBadge
    } from 'bootstrap-vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'
    import factura from "../../../api/CajaBanco/facturas";

    export default {
        components: {
            BCard,
            BCardFooter,
            BPaginationNav,
            BFormCheckbox,
            BFormGroup,
            vSelect,
            BRow,
            BButton,
            BFormCheckboxGroup,
            BFormDatepicker,
            BAlert,
            BFormSelect,
            BBadge
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: true,
                msg: 'Cargando el contenido espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 5,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: "nombre_persona",
                        value: "",
                        type: 1
                    }
                },
                recibos: [],
                total: 0,
                currency_id: 0,
            };
        },
        methods: {
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
            mostrarPagos(key) {
                if (this.recibos[key].mostrar) {
                    this.recibos[key].mostrar = 0;
                } else {
                    this.recibos[key].mostrar = 1;
                }
            },
            obtenerRecibos() {
                const self = this;
                recibo.obtener(
                    self.filter,
                    data => {
                        data.rows.forEach((recibos, key) => {
                            data.rows[key].mostrar = 0;
                        });
                        self.recibos = data.rows;
                        self.total = data.total;
                        self.currency_id = Number(data.currency_id);
                        self.loading = false;
                    },
                    err => {
                        console.log(err);
                        self.loading = false;
                    }
                );
            },
            anular(id_recibo) {

                this.$swal.fire({
                    title: 'Esta seguro de anular esta recibo?',
                    text: "Digite la causa de la anulación de la recibo",
                    input: 'text',
                    icon:'warning',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, anular recibo'
                }).then((result) => {
                    if (result.value) {
                        recibo.cancelar(
                            {
                                id_recibo: id_recibo,
                                causa_anulacion: result.value
                            },
                            data => {
                                this.$swal.fire(
                                    'Anulada',
                                    'Los documentos vinculados con esta recibo han sido anulados',
                                    'success'
                                )
                                this.obtenerRecibos();
                            },
                            err => {
                                this.$swal.fire(
                                    'No se puede anular el recibo!',
                                    err,
                                    'warning'
                                )
                            }
                        );


                    }
                })

            },
            changeLimit(limit) {
                this.filter.page = 1;
                this.filter.limit = limit;
                this.obtenerRecibos();
            },
            changePage(page) {
                this.filter.page = page;
                this.obtenerRecibos();
            },

        },
        /*components: {
          pagination: Pagination
        },*/
        mounted() {
            this.obtenerRecibos();
        }
    };
</script>
<style lang="scss" scoped>
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
            width: 180px;
        }

        .detail-link {
            width: 60px;
            position: relative;

            .link {
                width: 10px;
                height: 4px;
                margin-left: auto;
                margin-right: auto;
                cursor: pointer;
                margin-top: 8px;
                background-color: #595959;
                border: 1px solid #595959;

                &:before {
                    content: "";
                    width: 4px;
                    height: 10px;
                    left: 0px;
                    right: 0px;
                    cursor: pointer;
                    margin: 0px auto;
                    margin-top: -4px;
                    position: absolute;
                    background-color: #595959;
                    border: 1px solid #595959;
                }
            }
        }
    }
</style>
