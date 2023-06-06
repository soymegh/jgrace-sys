<template>
    <b-card>
        <b-row>
            <template v-if="form.currency_id === 1">
                <div class="col-md-4 sm-text-center">
                    <router-link class="btn btn-success" tag="button" :to="{ name: 'cajabanco-proformas-registrar' }">
                        <feather-icon icon="PlusIcon"></feather-icon>
                        Registrar
                    </router-link>
                </div>
            </template>
            <template v-else>
                <div class="col-md-4 sm-text-center">
                    <router-link class="btn btn-success" tag="button"
                                 :to="{ name: 'cajabanco-proformas-registrar-dol' }">
                        <feather-icon icon="PlusIcon"></feather-icon>
                        Registrar
                    </router-link>
                </div>
            </template>

            <div @keyup.enter="filter.page = 1;obtenerFacturas();"
                 class="col-md-8 sm-text-center form-inline justify-content-end">
                <b-form-select v-model.number="filter.search.estado" class=" mb-1 mr-sm-1 mb-sm-0 search-field">
                    <option value="100">Todos</option>
                    <option value="1">Vigente</option>
                    <option value="2">Facturado</option>
                    <option value="3">Modificado</option>
                    <option value="4">Vencido</option>
                    <option value="5">Archivado</option>
                    <option value="6">Anulado</option>
                </b-form-select>
                <b-form-select v-model="filter.search.field" class=" mb-1 mr-sm-1 mb-sm-0 search-field">
                    <option value="no_documento">No. Documento</option>
                </b-form-select>
                <input
                        v-model="filter.search.value"
                        class="form-control mb-1 mr-sm-1 mb-sm-0"
                        placeholder="Buscar"
                        type="text"
                >
                <b-button variant="info" v-b-tooltip.hover.top="'Buscar proforma'"
                          @click="filter.page = 1;obtenerFacturas();">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>Fecha</th>
                    <th>No. Cotización</th>
                    <th>Cliente</th>
                    <th>No. Factura</th>
                    <th>Observaciones</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(factura,key) in facturas">
                    <tr :key="factura.id_factura">
                        <td class="detail-link">
                            <div v-b-tooltip.hover.top="'Mostrar Detalle'" @click="mostrarProductos(key)"
                                 class="link"></div>
                        </td>
                        <td>{{ factura.f_proforma }}</td>
                        <td>{{ factura.no_documento }}</td>
                        <td>{{factura.proforma_cliente?factura.proforma_cliente.nombre_comercial:factura.nombre_razon }}
                        </td>
                        <td class="text-center">{{factura.factura_proforma?factura.factura_proforma.no_documento:'N/A' }}
                        </td>
                        <td class="">{{factura?factura.observacion:'-' }}</td>
                        <td class="text-center">
                            <div v-if="factura.estado===6">
                                <span class="badge badge-danger">Anulado</span>
                            </div>
                            <div v-if="factura.estado===1">
                                <span class="badge badge-primary">Vigente</span>
                            </div>
                            <div v-if="factura.estado===2">
                                <span class="badge badge-success">Facturado</span>
                            </div>
                            <div v-if="factura.estado===3">
                                <span class="badge badge-info">Modificado</span>
                            </div>
                            <div v-if="factura.estado===4">
                                <span class="badge badge-warning">Vencido</span>
                            </div>
                            <div v-if="factura.estado===5">
                                <span class="badge badge-dark">Archivada</span>
                            </div>
                        </td>
                        <td class="text-center">
                                <span>
                                <b-dropdown variant="link" toggle-class="text-decoration-none" right text="Right align"
                                            id="dropdown-buttons"
                                            style="position: inherit !important;" no-caret>
                                    <template v-slot:button-content>
                                        <feather-icon icon="MoreVerticalIcon" size="16"
                                                      class="text-body align-middle mr-25"/>
                                    </template>
                                    <!--Registrar seguimiento-->
                                    <template v-if="[1,3].indexOf(Number(factura.estado)) >= 0 ">
                                      <b-dropdown-item>
                                                        <router-link
                                                                style="padding: 3px;color: unset"
                                                                :to="{ name: 'cajabanco-proformas-seguimiento', params: { id_proforma: factura.id_proforma } }"
                                                                tag="a">
                                                            <feather-icon icon="MailIcon" color="#2675dc"
                                                                          class="mr-50"></feather-icon><span>Seguimiento</span>
                                                        </router-link>
                                      </b-dropdown-item>
                                    </template>
                                    <!--Actualizar cotizacion-->
                                    <template v-if="form.currency_id === 1">
                                        <template v-if="[1,3].indexOf(Number(factura.estado)) >= 0 ">
                                      <b-dropdown-item>
                                              <router-link
                                                      style="padding: 3px;color: unset"
                                                      :to="{ name: 'cajabanco-proformas-actualizar', params: { id_proforma: factura.id_proforma } }"
                                                      tag="a">
                                                            <feather-icon icon="EditIcon" color="#2675dc"
                                                                          class="mr-50"></feather-icon><span>Actualizar</span>
                                                        </router-link>
                                      </b-dropdown-item>
                                    </template>
                                    </template>
                                    <template v-else>
                                        <template v-if="[1,3].indexOf(Number(factura.estado)) >= 0 ">
                                      <b-dropdown-item>
                                              <router-link
                                                      style="padding: 3px;color: unset"
                                                      :to="{ name: 'cajabanco-proformas-actualizar-dol', params: { id_proforma: factura.id_proforma } }"
                                                      tag="a">
                                                            <feather-icon icon="EditIcon" color="#2675dc"
                                                                          class="mr-50"></feather-icon><span>Actualizar</span>
                                                        </router-link>
                                      </b-dropdown-item>
                                    </template>
                                    </template>

                                    <!--Mostrar detalle de salida-->
                                    <template>
                                        <b-dropdown-item>
                                        <router-link
                                                style="padding: 3px;color: unset"
                                                :to="{ name: 'cajabanco-proformas-mostrar', params: { id_proforma: factura.id_proforma } }"
                                                tag="a"
                                        >
                                                    <feather-icon icon="EyeIcon" color="#2675dc"
                                                                  class="mr-50"></feather-icon><span>Vista previa</span>
                                        </router-link>

                                    </b-dropdown-item>
                                    </template>
                                </b-dropdown>
                            </span>

                        </td>
                    </tr>
                    <tr v-if="factura.mostrar" :key="factura.codigo_factura">
                        <td></td>
                        <td colspan="7">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center">Código producto</th>
                                    <th>Descripción producto</th>
                                    <th>Unidad de medida</th>
                                    <th>Precio</th>
                                    <th>Cantidad Solicitada</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="productosDetalle in factura.proforma_productos"
                                    :key="productosDetalle.id_proforma_producto">
                                    <td>{{ productosDetalle.codigo_producto }}</td>
                                    <td>{{ productosDetalle.descripcion_producto }}</td>
                                    <td>{{ productosDetalle.unidad_medida }}</td>
                                    <td>{{ productosDetalle.precio}}</td>
                                    <td>{{ productosDetalle.cantidad }}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="item-footer" colspan="3"></td>
                                    <td>Total Unidades</td>
                                    <td class="item-footer">
                                        <strong>{{factura.tot_unidades}}</strong>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                </template>
                <tr v-if="!facturas.length">
                    <td class="text-center" colspan="10">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination
                    @changePage="changePage"
                    @changeLimit="changeLimit"
                    :items="facturas"
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
    import factura from "../../../api/CajaBanco/proformas";
    import es from 'vuejs-datepicker/dist/locale/translations/es'
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
        BFormSelect, BModal, VBModal, BListGroup, BListGroupItem, BFormInput, BForm,
        BDropdown, BDropdownItem,
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import Ripple from 'vue-ripple-directive'
    import roundNumber from '../../../assets/custom-scripts/Round'
    import moment from '../../../../../Backend/resources/app/assets/plugins/moment/moment'
    import ToastificationContent from '../../../@core/components/toastification/ToastificationContent'
    import loadingImage from '../../../assets/images/loader/block50.gif'


    export default {
        components: {
            BCard,
            BCardFooter,
            BPaginationNav,
            BFormCheckbox,
            BFormGroup,
            BFormInput,
            vSelect,
            BRow,
            BButton,
            BFormCheckboxGroup,
            BFormDatepicker,
            BAlert,
            BFormSelect,
            BModal,
            BListGroup,
            BListGroupItem,
            BForm,
            BDropdown,
            BDropdownItem,
        },
        directives: {
            'b-tooltip': VBTooltip,
            'b-modal': VBModal,
            Ripple,
        },
        data() {
            return {
                descargando: false,
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 5,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: "no_documento",
                        value: "",
                        estado: 1
                    }
                },
                facturas: [],
                total: 0,
                form: {
                    currency_id: 2,
                },

            };
        },
        methods: {
            mostrarProductos(key) {
                if (this.facturas[key].mostrar) {
                    this.facturas[key].mostrar = 0;
                } else {
                    this.facturas[key].mostrar = 1;
                }
            },
            obtenerFacturas() {
                var self = this;
                self.loading = true;
                factura.obtenerProformas(
                    self.filter,
                    data => {
                        data.rows.forEach((facturas, key) => {
                            data.rows[key].mostrar = 0;
                        });
                        self.facturas = data.rows;
                        self.total = data.total;
                        self.currency_id = Number(data.currency_id);
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
                this.obtenerFacturas();
            },
            changePage(page) {
                this.filter.page = page;
                this.obtenerFacturas();
            },

            downloadItem(extension, id_importacionx) {
                var self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/facturas/reporte/' + extension + '/' + id_importacionx;
                    this.descargando = true;
                    alertify.success("Descargando datos, espere un momento.....", 5);
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'text/plain'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoFactura.' + extension;
                            link.click()
                            //this.descargando = false;
                            self.loading = false;
                            self.descargando = false;
                        }).catch(function (error) {
                        alertify.error("Error Descargando archivo.....", 5);
                        self.descargando = false;
                        self.loading = false;
                    })
                } else {
                    alertify.warning("Espere a que se complete la descarga anterior", 5);
                }
            },


        },
        /*components: {
          pagination: Pagination
        },*/
        mounted() {
            this.obtenerFacturas();
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
