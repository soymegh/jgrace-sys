<template>
    <b-card>
        <b-row>
            <div class="col-lg-4 col-md-12 sm-text-center">
                <router-link class="btn btn-success" tag="button" :to="{ name: 'inventario-productos-registrar' }">
                    <i class="pe-7s-plus"></i> Registrar
                </router-link>
            </div>
            <div @keyup.enter="filter.page = 1;obtenerProductos();" class="col-lg-8 col-md-12 sm-text-center form-inline justify-content-end">
                <div class="form-group check-label">
<!--                    <label class="check-label   mr-sm-1 mb-sm-0"><input class="form-control  mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox">Mostrar todo</label>-->
                    <b-form-checkbox
                            v-model="filter.search.status"
                            class="mx-1"
                    >
                        Mostrar todos
                    </b-form-checkbox>
                </div>
                <select v-model="filter.search.field" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field">
                    <option value="descripcion">Descripcion</option>
                    <option value="codigo_barra">Código Barras</option>
                </select>
                <input v-model="filter.search.value" class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text">
                <p class="mb-1 mr-sm-1 mb-sm-0">
                    <b-button variant="info" @click="filter.page = 1;obtenerProductos();" v-b-tooltip.hover.top="'Buscar!'"><feather-icon icon="SearchIcon"></feather-icon></b-button>
                    <!--<a  :disabled="descargando" @click.prevent="downloadItem('pdf')" style="color: #ffffff;padding-left: 2px" ><b-button v-b-tooltip.hover.top="'PDF!'" :disabled="descargando" variant="danger"><feather-icon icon="ArrowDownCircleIcon"></feather-icon></b-button></a>
                    <a  :disabled="descargando" @click.prevent="downloadItem('xls')" style="color: #ffffff;padding-left: 2px" ><b-button v-b-tooltip.hover.top="'XLS!'" :disabled="descargando" variant="success"><feather-icon icon="ArrowDownCircleIcon"></feather-icon></b-button></a>-->
                </p>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Unidad de medida</th>
                    <th>Marca</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(producto,key) in productos">
                    <tr>
                        <td>{{ producto.codigo_sistema }}</td>
                        <td>{{ producto.descripcion }}</td>
                        <td>{{ producto.unidad_medida.descripcion+' ('+producto.unidad_medida.siglas+')' }}</td>
                        <td>{{producto.marca ? producto.marca.descripcion : 'N/D'}}</td>
                        <!--<td>{{ producto.tipo_producto ===1 ? 'Producto' : producto.tipo_producto ===2? 'Servicio':producto.tipo_producto ===4? 'Bienes':'Otro'}}</td>-->
                        <td class="text-center">
                            <div v-if="producto.estado === 1">
                                <span class="badge badge-success">Activo</span>
                            </div>
                            <div v-else>
                                <span class="badge badge-danger">Desactivado</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <router-link tag="a" :to="{ name: 'inventario-productos-actualizar', params: { id_producto: producto.id_producto } }"><feather-icon icon="EditIcon"></feather-icon></router-link>
                        </td>
                    </tr>
                </template>
                <tr v-if="!productos.length">
                    <td class="text-center" colspan="8">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <pagination @changePage="changePage" @changeLimit="changeLimit" :items="productos" :total="total" :page="filter.page" :limitOptions="filter.limitOptions" :limit="filter.limit"></pagination>


        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    //import Pagination from '../layout/Pagination'
    import {BPaginationNav, BFormCheckbox, BFormGroup, BCard, BCardFooter, VBTooltip,BRow,BButton,BFormCheckboxGroup} from 'bootstrap-vue'
    //import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import vSelect from 'vue-select'
    import producto from "../../../api/Inventario/productos";

    export default {
        components:{
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
                loading:true,
                msg: 'Cargando el contenido espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 5,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: 'descripcion',
                        value: '',
                        status:false
                    }
                },
                productos: [],
                total: 0,
                descargando:false,
            }
        },
        methods: {
            downloadItem (extension) {

                var self = this;
                console.log(self.descargando);
                if(!self.descargando) {
                    let url = 'productos/ps/reporte/'+extension;
                    self.descargando = true;
                    self.loading = true;
                    alertify.success("Descargando datos, espere un momento.....", 5);
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'});

                            extension === 'xls' ? blob =  new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'ReporteProductosServicios.'+extension;
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
            },
            obtenerProductos() {
                var self = this;
                self.loading = true;
                producto.obtenerProductos(self.filter, data => {
                    self.productos = data.rows;
                    self.total = data.total;
                    self.loading = false;
                }, err => {
                    self.loading = false;
                    console.log(err)
                })
            },
            changeLimit(limit) {
                this.filter.page = 1
                this.filter.limit = limit
                this.obtenerProductos()
            },
            changePage(page) {
                this.filter.page = page
                this.obtenerProductos()
            },
        },
        mounted() {
            this.obtenerProductos()
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
