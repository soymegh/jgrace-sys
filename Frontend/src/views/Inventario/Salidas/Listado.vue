<template>
    <b-card>
        <b-row>
            <div class="col-md-4 sm-text-center">
                <router-link v-b-tooltip.hover.top="'Registrar salida de productos'" class="btn btn-success mx-1"
                             tag="button" :to="{ name: 'inventario-salidas-registrar' }">
                    <feather-icon icon="PlusCircleIcon"></feather-icon>
                    Salida
                </router-link>
                <router-link v-b-tooltip.hover.top="'Registrar traslado de productos'" class="btn btn-success"
                             tag="button" :to="{ name: 'inventario-salidas-traslados' }">
                    <feather-icon icon="PlusCircleIcon"></feather-icon>
                    Traslado
                </router-link>
            </div>
            <div @keyup.enter="filter.page = 1;obtenerSalidas();"
                 class="col-md-8 sm-text-center form-inline justify-content-end">
                <select v-model.number="filter.search.estado" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field">
                    <option value="100">Todos</option>
                    <option value="0">Anulados</option>
                    <option value="1">Emitidos</option>
                    <option value="2">Despachados</option>
                </select>
                <select
                        v-model="filter.search.field"
                        class="form-control mb-1 mr-sm-1 mb-sm-0 search-field"
                >
                    <option value="numero_documento">No. Documento</option>
                    <option value="codigo_salida">Código</option>
                    <option value="descripcion_salida">Descripción/Cliente</option>
                </select>
                <input
                        v-model="filter.search.value"
                        class="form-control mb-1 mr-sm-1 mb-sm-0"
                        placeholder="Buscar"
                        type="text"
                >
                <b-button variant="info" @click="filter.page = 1;obtenerSalidas();" v-b-tooltip.hover.top="'Buscar'">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th class="text-center table-number">Número Documento</th>
                    <th class="text-center table-number">Código salida</th>
                    <th>Tipo salida</th>
                    <th>Cliente</th>
                    <th>Fecha Salida</th>
                    <th>Bodega</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(salida,key) in salidas">
                    <tr :key="salida.id_salida">
                        <td class="detail-link">
                            <div v-b-tooltip.hover.top="'Mostrar productos'" @click="mostrarProductos(key)"
                                 class="link"></div>
                        </td>
                        <td>{{ salida.numero_documento }}</td>
                        <td>{{ salida.codigo_salida }}</td>
                        <td>{{ salida.salida_tipo.descripcion }}</td>
                        <td v-if="salida.salida_cliente">{{
                            (salida.salida_cliente.nombre_completo?salida.salida_cliente.nombre_completo:'') /*+ ' ' + (salida.salida_cliente.razon_social?salida.salida_cliente.razon_social:'')*/}}
                        </td>
                        <td v-else>{{ 'N/D'}}</td>
                        <td>{{ formatDate(salida.fecha_salida) }}</td>
                        <!--  <td>{{ salida.salida_proveedor? salida.salida_proveedor.nombre_comercial:'N/A' }}</td>-->
                        <td>{{ salida.salida_bodega.descripcion }}</td>
                        <td class="text-center">
                            <div v-if="salida.estado===0">
                                <span class="badge badge-danger">Anulada</span>
                            </div>
                            <div v-if="salida.estado===1">
                                <span class="badge badge-primary">Emitida</span>
                            </div>
                            <div v-if="salida.estado===2">
                                <span class="badge badge-success">Despachada</span>
                            </div>
                            <div v-if="salida.estado===3">
                                <span class="badge badge-dark">Devolución</span>
                            </div>
                            <div v-if="salida.estado===99">
                                <span class="badge badge-info">Despacho en Proceso</span>
                            </div>
                        </td>
                        <td class="text-center">

                            <!-- Column: Action -->
                            <span>
                                <b-dropdown variant="link" toggle-class="text-decoration-none" right text="Right align"
                                            id="dropdown-buttons"
                                            style="position: inherit !important;" no-caret>
                                    <template v-slot:button-content>
                                        <feather-icon icon="MoreVerticalIcon" size="16"
                                                      class="text-body align-middle mr-25"/>
                                    </template>
                                    <!--Actualizar salida-->
                                    <template v-if="([1,99].indexOf(salida.estado) >= 0)">
                                      <b-dropdown-item>
                                                        <router-link
                                                                style="padding: 3px;color: unset"
                                                                :to="{ name: 'inventario-salidas-actualizar', params: { id_salida: salida.id_salida } }"
                                                                tag="a">
                                                            <feather-icon icon="EditIcon" color="#2675dc"
                                                                          class="mr-50"></feather-icon><span>Actualizar</span>
                                                        </router-link>
                                      </b-dropdown-item>
                                    </template>
                                    <!--Despachar salida-->
                                    <template v-if="([1,99].indexOf(salida.estado) >= 0)">
                                      <b-dropdown-item>
                                                        <router-link
                                                                style="padding: 3px;color: unset"
                                                                :to="{ name: 'inventario-salidas-despachar', params: { id_salida: salida.id_salida } }"
                                                                tag="a">
                                                            <feather-icon icon="CheckIcon" color="#2675dc"
                                                                          class="mr-50"></feather-icon><span>Despachar</span>
                                                        </router-link>



                                      </b-dropdown-item>
                                    </template>

                                    <!--Devolución de salida-->
                                    <template
                                            v-if="(salida.estado === 2) && ([1,7].indexOf(salida.id_tipo_salida) >= 0)">
                                        <b-dropdown-item>
                                                <router-link
                                                        style="padding: 3px;color: unset"
                                                        :to="{ name: 'inventario-salidas-devolucion', params: { id_salida: salida.id_salida } }"
                                                        tag="a">
                                                        <feather-icon icon="RotateCcwIcon" color="#2675dc"
                                                                      class="mr-50"></feather-icon><span>Devolución</span>
                                                </router-link>
                                        </b-dropdown-item>
                                    </template>

                                    <!--Mostrar detalle de salida-->
                                    <template>
                                        <b-dropdown-item>
                                        <router-link
                                                style="padding: 3px;color: unset"
                                                :to="{ name: 'inventario-salidas-mostrar', params: { id_salida: salida.id_salida } }"
                                                tag="a"
                                                >
                                                    <feather-icon icon="EyeIcon" color="#2675dc" class="mr-50"></feather-icon><span>Vista previa</span>
                                        </router-link>

                                    </b-dropdown-item>
                                    </template>


                                    <!--Anular salida-->
                                    <template
                                            v-if="(salida.estado === 1 || salida.estado === 2) && ([4,7].indexOf(salida.id_tipo_salida) >= 0)">
                                        <b-dropdown-item>
                                            <a style="padding: 3px;color: unset"
                                               @click.prevent="anular(salida.id_salida)">
                                                <feather-icon icon="DeleteIcon" color="red"
                                                              class="mr-50"></feather-icon><span>Anular</span>
                                            </a>


                                        </b-dropdown-item>
                                    </template>

                                    <template
                                            v-if="(salida.estado === 1) && ([15].indexOf(salida.id_tipo_salida) >= 0)">
                                        <b-dropdown-item>
                                                <a style="padding: 3px;color: unset"
                                                   @click.prevent="anular(salida.id_salida)">
                                                    <feather-icon icon="DeleteIcon" color="red"
                                                                  class="mr-50"></feather-icon><span>Anular</span>
                                                </a>

                                        </b-dropdown-item>

                                    </template>
                                </b-dropdown>
                            </span>
                        </td>
                    </tr>
                    <tr v-if="salida.mostrar" :key="salida.id_salida">
                        <td colspan="8">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center">Código producto</th>
                                    <th>Descripción producto</th>
                                    <th>Unidad de medida</th>
                                    <th>Cantidad Saliente</th>
                                    <th>Cantidad Despachada</th>
                                    <!--<th>Costo</th>
                                      <th>Subtotal</th>-->
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                        v-for="productosDetalle in salida.salida_productos"
                                        :key="productosDetalle.id_salida_producto">
                                    <td>{{ productosDetalle.codigo_producto }}</td>
                                    <td>{{ productosDetalle.descripcion_producto }}</td>
                                    <td>{{ productosDetalle.unidad_medida }}</td>
                                    <td>{{ productosDetalle.cantidad_saliente }}</td>
                                    <td>{{ productosDetalle.cantidad_despachada }}</td>
                                    <!--<td>{{ productosDetalle.precio_unitario }}</td>
                                    <td>{{(Number(productosDetalle.cantidad_saliente * Number(productosDetalle.precio_unitario)).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"))}}</td>-->
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="item-footer" colspan="2"></td>
                                    <td>Total Unidades</td>
                                    <td class="item-footer">
                                        <strong>{{salida.tot_unidades}}</strong>
                                    </td>
                                    <td class="item-footer">
                                        <strong>{{salida.tot_unidades_despachadas}}</strong>
                                    </td>
                                    <!--<td>Total</td>
                                    <td> <strong>C$ {{salida.subtotal | formatMoney(2)}}</strong></td>-->
                                </tr>
                                </tfoot>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                </template>
                <tr v-if="!salidas.length">
                    <td class="text-center" colspan="10">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination
                    @changePage="changePage"
                    @changeLimit="changeLimit"
                    :items="salidas"
                    :total="total"
                    :page="filter.page"
                    :limitOptions="filter.limitOptions"
                    :limit="filter.limit"
            ></pagination>

        </b-card-footer>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>

    </b-card>

</template>

<script type="text/ecmascript-6">
    import salida from "../../../api/Inventario/salidas";
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import {
        BPaginationNav,
        BFormCheckbox,
        BFormGroup,
        BCard,
        BCardFooter,
        VBTooltip,
        BRow,
        BButton,
        BFormCheckboxGroup,
        BFormDatepicker,
        BAlert,
        BDropdown, BDropdownItem,
    } from 'bootstrap-vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'

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
            BDropdown,
            BDropdownItem
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 5,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: "numero_documento",
                        value: "",
                        estado: 1
                    }
                },
                salidas: [],
                total: 0
            };
        },
        methods: {
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
            mostrarProductos(key) {
                if (this.salidas[key].mostrar) {
                    this.salidas[key].mostrar = 0;
                } else {
                    this.salidas[key].mostrar = 1;
                }
            },
            obtenerSalidas() {
                var self = this;
                self.loading = true;
                salida.obtenerSalidas(
                    self.filter,
                    data => {
                        data.rows.forEach((salidas, key) => {
                            data.rows[key].mostrar = 0;
                        });
                        self.salidas = data.rows;
                        self.total = data.total;
                        self.loading = false;
                    },
                    err => {
                        self.loading = false;
                        console.log(err);
                    }
                );
            },
            changeLimit(limit) {
                this.filter.page = 1;
                this.filter.limit = limit;
                this.obtenerSalidas();
            },
            changePage(page) {
                this.filter.page = page;
                this.obtenerSalidas();
            },

            anular(id_salida) {


                this.$swal.fire({
                    title: 'Esta seguro de anular esta salida?',
                    text: "Digite la causa de la anulación de la salida",
                    input: 'text',
                    icon: 'danger',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger ml-1',
                    },
                    confirmButtonText: 'Si, anular salida'
                }).then((result) => {
                    if (result.value) {
                        salida.anular(
                            {
                                id_salida: id_salida,
                                causa_anulacion: result.value
                            },
                            data => {
                                this.$swal.fire(
                                    'Anulado',
                                    'El registro de la salida ha sido anulado',
                                    'success'
                                )
                                this.obtenerSalidas();
                            },
                            err => {
                                this.$swal.fire(
                                    'No se puede anular salida!',
                                    err,
                                    'warning'
                                )
                            }
                        );


                    }
                })

            },
        },
        /*components: {
          pagination: Pagination
        },*/
        mounted() {
            this.obtenerSalidas();
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
