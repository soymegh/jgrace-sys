<template>
    <b-card>
        <b-row>
            <div class="col-md-6 sm-text-center">
                <router-link class="btn btn-success" tag="button"
                             :to="{ name: 'contabilidad-catalogos-contables-registrar' }">
                    <feather-icon icon="PlusCircleIcon"></feather-icon>
                    Registrar
                </router-link>
            </div>
            <div @keyup.enter="filter.page = 1;obtenerCuentasContables();"
                 class="col-md-6 sm-text-center form-inline justify-content-end">
                <select v-model="filter.search.field" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field">
                    <option value="nombre_cuenta">Nombre</option>
                    <option value="cta_contable">Código</option>
                </select>
                <input v-model="filter.search.value" class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar"
                       type="text">
                <b-button variant="info" @click="filter.page = 1;obtenerCuentasContables();"
                          v-b-tooltip.hover.top="'Buscar!'">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Nivel</th>
                    <th>Tipo</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Permite Movimientos</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center table-number">Editar</th>
                    <th class="text-center table-number">Agregar SubCuentas</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="cuenta_contable in cuentas_contables">
                    <tr :key="cuenta_contable.id_cuenta_contable">
                        <td>{{ cuenta_contable.cuenta_nivel.descripcion }}</td>
                        <td>{{ cuenta_contable.cuenta_tipo.descripcion}}</td>
                        <td>{{ cuenta_contable.cta_contable}}</td>
                        <td>{{ cuenta_contable.nombre_cuenta}}</td>
                        <template v-if="cuenta_contable.permite_movimiento">
                            <td class="text-center">
                                <span v-b-tooltip="'Permite'" class="bullet bullet-sm mr-1 bullet-success"></span>
                            </td>
                        </template>
                        <template v-else>
                            <td class="text-center">
                                <span v-b-tooltip="'No Permite'" class="bullet bullet-sm mr-1 bullet-danger"></span>
                            </td>
                        </template>
                        <td class="text-center">
                            <div v-if="cuenta_contable.estado===1">
                                <b-badge variant="success"> Activo</b-badge>
                            </div>
                            <div v-else>
                                <b-badge variant="danger"> Desactivado</b-badge>
                            </div>
                        </td>
                        <td class="text-center">
                            <template v-if="[1].indexOf(cuenta_contable.id_nivel_cuenta) < 0">
                                <router-link v-b-tooltip="'Actualizar Cuenta Contable'"
                                             :to="{name: 'contabilidad-catalogos-contables-actualizar', params: {id_cuenta_contable: cuenta_contable.id_cuenta_contable}}"
                                             tag="a">
                                    <feather-icon icon="EditIcon"></feather-icon>
                                </router-link>
                            </template>
                        </td>
                        <td class="text-center">
                            <template v-if="[6,7].indexOf(cuenta_contable.id_nivel_cuenta) < 0">
                                <router-link v-b-tooltip="'Registrar SubCuenta Contable'"
                                             :to="{name: 'contabilidad-catalogos-contables-registrar-subcuenta-padre', params: {id_cuenta_contable_padre: cuenta_contable.id_cuenta_contable}}"
                                >
                                    <feather-icon icon="Edit3Icon"></feather-icon>
                                </router-link>
                            </template>
                        </td>

                    </tr>

                </template>
                <tr v-if="!cuentas_contables.length">
                    <td class="text-center" colspan="7">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination @changePage="changePage" @changeLimit="changeLimit" :items="cuentas_contables" :total="total"
                        :page="filter.page" :limitOptions="filter.limitOptions" :limit="filter.limit"></pagination>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
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
        BFormCheckbox,
        BFormGroup,
        BBadge,
        BTab,
        BTabs,
    } from 'bootstrap-vue'
    import cuenta_contable from '../../../api/contabilidad/cuentas-contables'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    export default {
        components: {
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, BFormCheckbox, BFormGroup,
            BBadge,
            BTab,
            BTabs
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
                    limit: 20,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: 'nombre_cuenta',
                        value: '',
                        status: 0
                    }
                },
                cuentas_contables: [],
                total: 0,
            }
        },
        methods: {

            /*downloadItem (extension) {

                var self = this;
                console.log(self.descargando);
                if(!self.descargando) {
                    let url = 'contabilidad/cuentas-contables/reporte/'+extension;
                    self.descargando = true;
                    self.loading = true;
                    alertify.success("Descargando datos, espere un momento.....", 5);
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'});

                            extension === 'xls' ? blob =  new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'ReporteCuentasContables.'+extension;
                            link.click()
                            this.descargando = false;
                            self.loading = false;
                        }).catch(function (error) {
                        alertify.error("Error Descargando archivo.....", 5);
                        self.descargando = false;
                        self.loading = false;
                    })


                }else{
                    alertify.warning("Espere a que se complete la descarga anterior",5);
                }
            },*/

            obtenerCuentasContables() {
                var self = this;
                self.loading = true;
                cuenta_contable.obtenerCuentasContables(
                    self.filter,
                    data => {
                        self.cuentas_contables = data.rows;
                        self.total = data.total;
                        self.loading = false;
                    },
                    err => {
                        self.loading = false;
                        console.log(err);
                        this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Ha ocurrido un problema al cargar los datos',
                                variant: 'Warning',
                                position: 'bottom-right'
                            }
                        },{
                            position:'bottom-right'
                        })
                    }
                )
            },

            changeLimit(limit) {
                this.filter.page = 1;
                this.filter.limit = limit;
                this.obtenerCuentasContables();
            },
            changePage(page) {
                this.filter.page = page;
                this.obtenerCuentasContables();
            },
        },
        /* components: {
           pagination: Pagination
         },*/
        mounted() {
            this.obtenerCuentasContables();
        }
    }
</script>
<style lang="scss" scoped>
    @import '@core/scss/vue/libs/vue-select.scss';
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
