<template>
    <b-card>
        <b-row>
            <template v-if="filter.search.es_deudor">
                <div class="col-md-2 sm-text-center">
                    <router-link class="btn btn-success" tag="button"
                                 :to="{ name: 'registrar-deudor', params:{deudor:true} }">
                        <feather-icon icon="PlusIcon"></feather-icon> Registrar Deudor
                    </router-link>
                </div>
            </template>
            <template v-else>
                <div class="col-md-2 sm-text-center">
                    <router-link class="btn btn-success" tag="button"
                                 :to="{ name: 'ventas-clientes-registrar', params:{deudor:false} }">
                      <feather-icon icon="PlusIcon"></feather-icon> Registrar
                    </router-link>
                </div>
            </template>
            <div @keyup.enter="filter.page = 1;obtener();" class="col-md-10 sm-text-center form-inline justify-content-end">

                <b-form-checkbox
                        v-model="filter.search.status"
                        class="mx-lg-1 mb-sm-1 mt-sm-1"
                >
                    Mostrar todos
                </b-form-checkbox>

                <b-form-checkbox
                        v-model="filter.search.credit"
                        class="mx-lg-1 mb-sm-1 mt-sm-1"
                >
                    Crédito
                </b-form-checkbox>
                <b-form-select
                        v-model="filter.search.field"
                        class=" mb-1 mr-sm-1 mb-sm-0 search-field"
                >
                    <option value="nombre_comercial">Nombre</option>
                    <option value="contacto">Contacto</option>
                    <option value="numero_ruc">Número RUC</option>
                    <option value="numero_cedula">Número Cedula</option>
                </b-form-select>
                <input
                        v-model="filter.search.value"
                        class="form-control mb-1 mr-sm-1 mb-sm-0"
                        placeholder="Buscar"
                        type="text"
                >
                <b-button v-b-tooltip.hover.top="'Buscar'" @click="filter.page = 1;obtener();" class="btn btn-info">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
                <a :disabled="descargando" @click.prevent="downloadItem('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button variant="danger" :disabled="descargando" class="btn btn-danger"><feather-icon icon="DownloadCloudIcon" aria-hidden="true"></feather-icon></b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItem('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button variant="success" :disabled="descargando" class="btn btn-success"><feather-icon icon="DownloadCloudIcon" aria-hidden="true"></feather-icon>
                    </b-button>
                </a>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre/Razon social</th>
                    <th>Identificación</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="cliente in clientes" :key="cliente.id_cliente">
                    <td>{{ cliente.codigo }}</td>
                    <template v-if="cliente.tipo_persona === 1">
                        <td>{{ cliente.nombre_completo }}</td>
                    </template>
                    <template v-if="cliente.tipo_persona === 2">
                        <td>{{ cliente.razon_social }}</td>
                    </template>
                    <template v-if="cliente.tipo_persona === 1">
                        <td>{{ cliente.numero_cedula }}</td>
                    </template>
                    <template v-if="cliente.tipo_persona === 2">
                        <td>{{ cliente.numero_ruc }}</td>
                    </template>
                    <td class="text-center">
                        <div v-if="cliente.estado">
                            <span class="badge badge-success">Activo</span>
                        </div>
                        <div v-else>
                            <span class="badge badge-danger">Desactivado</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <!--<template v-if="[1].indexOf(cliente.id_cliente) < 0">-->
                        <router-link
                                tag="a"
                                :to="{ name: 'ventas-clientes-actualizar', params: { id_cliente: cliente.id_cliente } }"
                        >
                            <feather-icon v-b-tooltip.hover.top="'Editar cliente'" icon="EditIcon"></feather-icon>
                        </router-link>
                        <!-- </template>-->
                    </td>
                </tr>
                <tr v-if="!clientes.length">
                    <td class="text-center" colspan="4">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
          <pagination
                  @changePage="changePage"
                  @changeLimit="changeLimit"
                  :items="clientes"
                  :total="total"
                  :page="filter.page"
                  :limitOptions="filter.limitOptions"
                  :limit="filter.limit"
          ></pagination>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>

    </b-card>

</template>

<script type="text/ecmascript-6">
    import cliente from "../../../api/Ventas/clientes";
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
        BFormSelect,
    } from 'bootstrap-vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'
    import axios from "axios";
    //import Pagination from "../layout/Pagination";

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
                    limit: 25,
                    limitOptions: [25, 30, 45, 60],
                    search: {
                        field: "nombre_comercial",
                        value: "",
                        status: 0,
                        credit: 0,
                        es_deudor: false,
                    }
                },
                clientes: [],
                total: 0,
                descargando: false,
            };
        },
        methods: {
            obtener() {
                const self = this;
                self.loading = true;
                // self.filter.search.es_deudor = this.$route.meta.deudores;
                cliente.obtener(self.filter,
                    data => {
                        self.clientes = data.rows;
                        self.total = data.total;
                        self.loading = false;
                    },
                    err => {
                        self.loading = false;
                        this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'CheckIcon',
                                text: 'Se produjo un error al cargar los clientes.' + err.result,
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                        console.log(err);
                    }
                );
            },
            downloadItem(ext) {
                var self = this;
                if (!this.descargando) {
                    this.descargando = true;
                    self.loading = true;
                    alertify.success("Descargando datos, espere un momento.....", 5);
                    axios.get('ventas/clientes/' + ext, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})
                            ext === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});
                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'ListadoClientes.' + ext;
                            link.click()
                            this.descargando = false;
                            self.loading = false;
                        }).catch(function (error) {
                        alertify.error("Error Descargando archivo.....", 5);
                        self.descargando = false;
                        self.loading = false;
                    })
                } else {
                    alertify.warning("Espere a que se complete la descarga anterior", 5);
                }
            },
            changeLimit(limit) {
                this.filter.page = 1;
                this.filter.limit = limit;
                this.obtener();
            },
            changePage(page) {
                this.filter.page = page;
                this.obtener();
            },
        },
        /*components: {
          pagination: Pagination
        },*/
        mounted() {
            this.obtener();
        }
    };
</script>

<style lang="scss" scoped>
    @import "src/@core/scss/vue/libs/vue-select";
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
