<template>
    <b-card>
        <b-row>
            <div class="col-md-6 sm-text-center">
                <router-link v-b-tooltip.hover.top="'Registrar conteo fisico'" :to="{ name: 'inventario-conteo-fisico-registrar' }" class="btn btn-success" tag="button">
                    <feather-icon icon="PlusIcon"></feather-icon> Registrar
                </router-link>
            </div>
        </b-row>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <!--<th></th>-->
                    <th>Fecha Conteo</th>
                    <th>Area</th>
                    <th>Sucursal</th>
                    <th>Bodega</th>

                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(conteox,key) in inventario_conteos">
                    <tr :key="conteox.id_inventario_fisico">
                        <!--<td class="detail-link">
                          <div  v-tooltip="'Mostrar Productos'" @click="mostrarProductos(key)" class="link"></div>
                        </td>-->
                        <td>{{ conteox.f_inventario }}</td>
                        <td>{{ conteox.conteo_area? conteox.conteo_area.descripcion : 'N/A' }}</td>
                        <td>{{ conteox.conteo_sucursal? conteox.conteo_sucursal.descripcion : 'N/A' }}</td>
                        <td>{{ conteox.conteo_bodega? conteox.conteo_bodega.descripcion : 'N/A' }}</td>

                        <td class="text-center">
                            <div v-if="conteox.estado===0">
                                <span class="badge badge-danger">Anulado</span>
                            </div>
                            <div v-if="conteox.estado===1">
                                <span class="badge badge-success">Completado</span>
                            </div>
                            <div v-if="conteox.estado===99">
                                <span class="badge badge-dark">Borrador</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <router-link
                                    v-b-tooltip.hover.top="'Vista previa'"
                                    :to="{ name: 'inventario-conteo-fisico-mostrar', params: { id_inventario_fisico: conteox.id_inventario_fisico } }"
                                    tag="a" class="mx-lg-1"
                                    target="_blank"
                            >
                                <feather-icon icon="EyeIcon" aria-hidden="true"></feather-icon>
                            </router-link>
                            <template v-if="conteox.estado===99">
                                <router-link v-b-tooltip.hover.top="'Actualizar'"
                                             :to="{ name: 'inventario-conteo-fisico-actualizar', params: { id_inventario_fisico: conteox.id_inventario_fisico } }"
                                             tag="a" class="mx-lg-1"><feather-icon icon="EditIcon"></feather-icon></router-link>
                            </template>
                            <template v-if="conteox.estado===1">
                                <a v-b-tooltip.hover.buttom="'Descargar'"
                                   @click.prevent="downloadItem('pdf',conteox.id_inventario_fisico,)" class="mx-lg-1"> <feather-icon icon="DownloadCloudIcon"></feather-icon> </a>
                            </template>
                        </td>
                    </tr>
                </template>
                <tr v-if="!inventario_conteos.length">
                    <td class="text-center" colspan="11">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
      <b-card-footer>
        <pagination
                :items="inventario_conteos"
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
    import conteo from "../../../api/Inventario/conteo-fisico";
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import axios from "axios";
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
        BFormTimepicker,
        BInputGroup,
        BFormInput,
        BInputGroupAppend,
    } from 'bootstrap-vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'
    //import Pagination from "../layout/Pagination";
    //inventario/conteo-fisico/reporte-comparativo/
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
            BFormTimepicker,
            BInputGroup,
            BFormInput,
            BInputGroupAppend,
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
                        field: "hora_inicio",
                        value: ""
                    }
                },
                inventario_conteos: [],
                total: 0
            };
        },
        methods: {
            downloadItem(extension, id_inventario_fisico) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/conteo-fisico/reporte-comparativo/' + extension + '/' + id_inventario_fisico;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoConteoFisico.' + extension;
                            link.click()
                            //this.descargando = false;
                            self.loading = false;
                            self.descargando = false;
                            this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'Su archivo se ha descargado correctamente',
                                    variant: 'success',
                                }
                            },{
                                position:'bottom-right'
                            });
                        }).catch(function (error) {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'AlertTriangleIcon',
                                    text: 'Error descargando el archivo ' + error,
                                    variant: 'warning',
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.descargando = false;
                        self.loading = false;
                    })
                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'AlertCircleIcon',
                                text: 'Espere a que se complete la descarga anterior',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }

                /*}else{
                    alertify.warning("Espere a que se complete la descarga anterior",5);
                }*/
            },
            obtener() {
                var self = this;
                self.loading = true;
                conteo.obtener(
                    self.filter,
                    data => {

                        self.inventario_conteos = data.rows;
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
