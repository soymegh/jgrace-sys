<template>
    <b-card>
        <b-row>
            <div class="col-md-3 sm-text-center">
                <router-link class="btn btn-success" tag="button"
                             :to="{ name: 'admon-zonas-registrar' }">
                    <feather-icon icon="PlusCircleIcon"></feather-icon>
                    Registrar
                </router-link>
            </div>

            <div @keyup.enter="filter.page = 1;obtener();"
                 class="col-md-9 sm-text-center form-inline justify-content-end">
                <b-form-checkbox v-model="filter.search.status" class="custom-control-primary mr-1">
                    Mostrar Todos
                </b-form-checkbox>
                <select v-model="filter.search.field" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field">
                    <option value="zonas">Descripción</option>
                    <option value="departamentos">Departamento</option>
                    <option value="municipios">Municipio</option>
                    <option value="codigo">Código postal</option>
                </select>
                <input v-model="filter.search.value" class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar"
                       type="text">
                <b-button variant="info" @click="filter.page = 1;obtener();"
                          v-b-tooltip.hover.top="'Buscar!'">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
                <a :disabled="descargando" @click.prevent="downloadItem('pdf')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" variant="danger" v-b-tooltip.hover.top="'PDF'"><feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
                <a :disabled="descargando" @click.prevent="downloadItem('xls')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" variant="success" v-b-tooltip.hover.top="'XLS'"><feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
            </div>


        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>

                    <!--<th>Departamento</th>
                    <th>Municipio</th>-->
                    <th>Código</th>
                    <th>Descripción</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center table-number">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="zona in zonas">
                    <tr :key="zona.id_zona">
                        <!--<td>{{ zona.zona_municipio.departamento_municipio.descripcion }}</td>
                        <td>{{ zona.zona_municipio.descripcion }}</td>-->
                        <td>{{ zona.codigo }}</td>
                        <td>{{ zona.descripcion}}</td>
                        <td class="text-center">
                            <div v-if="zona.estado===1">
                                <b-badge variant="success"> Activo</b-badge>
                            </div>
                            <div v-else>
                                <b-badge variant="danger"> Desactivado</b-badge>
                            </div>
                        </td>
                        <td class="text-center">
                            <template>
                                <router-link v-b-tooltip="'Actualizar Zona'"
                                             :to="{name: 'admon-zonas-actualizar', params: {id_zona: zona.id_zona}}"
                                             tag="a">
                                    <feather-icon icon="EditIcon"></feather-icon>
                                </router-link>
                            </template>
                        </td>

                    </tr>

                </template>
                <tr v-if="!zonas.length">
                    <td class="text-center" colspan="7">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination @changePage="changePage" @changeLimit="changeLimit" :items="zonas" :total="total"
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
    import zona from '../../../api/admon/zonas'
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
                        field: 'zonas',
                        value: '',
                        status: 0
                    }
                },
                zonas: [],
                total: 0,
                descargando : false
            }
        },
        methods: {

            obtener() {
                var self = this;
                self.loading = true;
                zona.obtener(
                    self.filter,
                    data => {
                        self.zonas = data.rows;
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
                this.obtener();
            },
            changePage(page) {
                this.filter.page = page;
                this.obtener();
            },
            downloadItem (ext) {
                this.$toast({
                    component: ToastificationContent,
                    props: {
                        title: 'Notificación',
                        icon: 'XIcon',
                        text: 'Esta sección se encuentra en desarrollo',
                        variant: 'danger',
                    }
                },{
                    position: 'bottom-right'
                })
                /*var self = this;
                self.msg= 'Descargando datos, espere un momento.....'
                self.loading = true;
                //if(!this.descargando){
                //	this.descargando=true;
                //alertify.success("Descargando datos, espere un momento.....",5);

                axios.get('unidad-medida/reporte/'+ext, { responseType: 'blob' })
                    .then(({ data }) => {
                        let blob = new Blob([data], { type: 'application/pdf' })
                        ext === 'xls' ? blob = new Blob([data], { type: 'application/octet-stream' }) : blob = new Blob([data], { type: 'application/pdf' });
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob)
                        link.download = 'Reporte_UnidadMedida.'+ext;
                        link.click()
                        //this.descargando=false;
                        self.loading = false;
                        self.msg= 'Cargando el contenido espere un momento'
                    }).catch(function (error) {
                    alertify.error("Error Descargando archivo.....", 5);
                    //self.descargando = false;
                    self.loading = false;
                    self.msg= 'Cargando el contenido espere un momento'
                })*/
                /*}else{
                    alertify.warning("Espere a que se complete la descarga anterior",5);
                }*/
            },
        },
        /* components: {
           pagination: Pagination
         },*/
        mounted() {
            this.obtener();
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
