<template>
    <b-card>
        <b-row>
            <div class="col-md-12 sm-text-start">
                <router-link class="btn btn-success" tag="button" :to="{ name: 'inventario-entrada-inicial-registrar' }">
                    <feather-icon icon="PlusCircleIcon"></feather-icon>
                    Registrar Inventario Inicial
                </router-link>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Fecha Entrada</th>
                    <th>Responsable</th>
                    <th>Bodega</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(entrada,key) in entradas">
                    <tr :key="entrada.id_entrada_inicial">
                        <td>{{ formatDate(entrada.fecha_entrada) }}</td>
                        <td>{{ entrada.usuario_registra }}
                        </td>
                        <td>{{ entrada.entrada_bodega? entrada.entrada_bodega.descripcion:'N/A' }}</td>
                        <td class="text-center">
                            <div v-if="entrada.estado===0">
                                <span class="badge badge-danger">Anulada</span>
                            </div>
                            <div v-if="entrada.estado===99">
                                <span class="badge badge-grey">Borrador</span>
                            </div>
                            <div v-if="entrada.estado===1">
                                <span class="badge badge-info">Registrada</span>
                            </div>
                            <div v-if="entrada.estado===2">
                                <span class="badge badge-success">Emitida</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <template v-if="([1,99].indexOf(entrada.estado) >= 0)&&entrada.tipo_productos===2">
                                <router-link v-b-tooltip="'Editar o Emitir Entrada Inicial'" tag="a"
                                             :to="{ name: 'inventario-entrada-inicial-actualizar', params: { id_entrada_inicial: entrada.id_entrada_inicial } }">
                                    <feather-icon icon="EditIcon"></feather-icon>
                                </router-link>
                            </template>
                            <router-link
                                    v-b-tooltip="'Vista previa'"
                                    :to="{ name: 'inventario-entrada-inicial-mostrar', params: { id_entrada_inicial: entrada.id_entrada_inicial } }"
                                    tag="a"
                                    target="_blank"
                            >
                                <feather-icon icon="EyeIcon"></feather-icon>
                            </router-link>
                        </td>
                    </tr>
                </template>
                <tr v-if="!entradas.length">
                    <td class="text-center" colspan="10">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <pagination
                @changePage="changePage"
                @changeLimit="changeLimit"
                :items="entradas"
                :total="total"
                :page="filter.page"
                :limitOptions="filter.limitOptions"
                :limit="filter.limit"
        ></pagination>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    //import Pagination from '../layout/Pagination'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import {
        BPaginationNav,
        BFormCheckbox,
        BFormGroup,
        BCard,
        BCardFooter,
        VBTooltip,
        BRow,
        BButton,
        BFormCheckboxGroup
    } from 'bootstrap-vue'
    //import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import vSelect from 'vue-select'
    import entrada from "../../../api/Inventario/entrada_inicial";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

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
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: true,
                url : loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 20,
                    limitOptions: [20, 40, 60, 80],
                    search: {
                        field: "id_entrada_inicial",
                        value: ""
                    }
                },
                entradas: [],
                total: 0
            };
        },
        methods: {
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
            mostrarProductos(key) {
                if (this.entradas[key].mostrar) {
                    this.entradas[key].mostrar = 0;
                } else {
                    this.entradas[key].mostrar = 1;
                }
            },
            obtenerEntradas() {
                var self = this;
                entrada.obtener(
                    self.filter,
                    data => {
                        data.rows.forEach((entradas, key) => {
                            data.rows[key].mostrar = 0;
                        });
                        self.entradas = data.rows;
                        self.total = data.total;
                        self.loading = false;
                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props:{
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error al cargar los datos.',
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        console.log(err);
                    }
                );
            },
            changeLimit(limit) {
                this.filter.page = 1;
                this.filter.limit = limit;
                this.obtenerEntradas();
            },
            changePage(page) {
                this.filter.page = page;
                this.obtenerEntradas();
            },
        },
        /*components: {
          pagination: Pagination
        },*/
        mounted() {
            this.obtenerEntradas();
        }
    }
</script>

<style lang="scss" scoped>


    @import '@core/scss/vue/libs/vue-select.scss';

    .search-field {
        min-width: 125px;
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
