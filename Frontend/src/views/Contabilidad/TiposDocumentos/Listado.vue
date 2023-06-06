<template>
    <div class="main">
        <b-card>
            <div class="row">
                <div class="col-md-3 sm-text-center">
                    <router-link class="btn btn-success" tag="button" :to="{name:'contabilidad-tipos-documentos-registrar'}">
                        <i class="pe-7s-plus"></i> Registrar
                    </router-link>
                </div>
                <div @keyup.enter="filter.page = 1;obtenerTiposDocumento();" class="col-md-9 sm-text-center form-inline justify-content-end">
                    <div class="form-group">

                        <!--                            <input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox">-->
                        <b-form-checkbox v-model="filter.search.status" class="custom-control-primary">
                            Mostrar Todos
                        </b-form-checkbox>


                    </div>

                    <select v-model="filter.search.field" class="form-control mb-1 mr-sm-1 mb-sm-0 mx-2 search-field">
                        <option value="descripcion">Descripción</option>
                    </select>


                    <input v-model="filter.search.value" class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text">
                    <button class="btn btn-info" @click="filter.page = 1;obtenerTiposDocumento();"><i class="pe-7s-search"></i> Buscar</button>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">Descripción</th>
                        <th class="text-center">Prefijo</th>
                        <th class="text-center">Registro Manual</th>
                        <th class="text-center table-number">Estado</th>
                        <th class="text-center action">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="tipo_documento in tipos_documentos" :key="tipo_documento.id_tipo_doc">
                        <td class="text-center">{{ tipo_documento.descripcion}}</td>
                        <td class="text-center">{{ tipo_documento.prefijo}}</td>
                        <td class="text-center">
                            <div v-if="tipo_documento.permite_registro_manual">
                                <span class="badge badge-success">Permitido</span>
                            </div>
                            <div v-else>
                                <span class="badge badge-danger">No permitido</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div v-if="tipo_documento.estado">
                                <span class="badge badge-success">Activo</span>
                            </div>
                            <div v-else>
                                <span class="badge badge-danger">Desactivado</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <router-link  :to="{ name: 'contabilidad-tipos-documentos-actualizar', params: { id_tipo_doc: tipo_documento.id_tipo_doc}}"><feather-icon icon="EditIcon"></feather-icon></router-link>
                        </td>
                    </tr>
                    <tr v-if="!tipos_documentos.length">
                        <td class="text-center" colspan="5">Sin datos</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <b-card-footer>
                <pagination class="pagination b-pagination mb-0" @changePage="changePage" @changeLimit="changeLimit" :items="tipos_documentos" :total="total" :page="filter.page" :limitOptions="filter.limitOptions" :limit="filter.limit"></pagination>
            </b-card-footer>

            <template v-if="loading">
                <BlockUI  :url="url"></BlockUI>
            </template>


        </b-card>
    </div>
</template>

<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    //import Pagination from '../layout/Pagination'
    import { BPaginationNav,BFormCheckbox,BFormGroup,BCard, BCardFooter } from 'bootstrap-vue'
    //import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import vSelect from 'vue-select'
    import tipos_documentos from "../../../api/contabilidad/tipos_documentos";

    export default {
        components:{
            BCard,
            BCardFooter,
            BPaginationNav,
            BFormCheckbox,
            BFormGroup,
            vSelect,
        },
        data() {
            return {
                loading:true,
                url : loadingImage,   //It is important to import the loading image then use here
                currentPage:1,
                filter: {
                    page: 1,
                    limit: 5,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: 'descripcion',
                        value: '',
                        status:0
                    }
                },
                tipos_documentos: [],
                total:0
            }
        },
        methods: {
            linkGen(pageNum) {
                return pageNum === 1 ? '?' : `?page=${pageNum}`
            },
            obtenerTiposDocumento() {
                var self = this;
                self.loading = true;
                tipos_documentos.obtener(self.filter, data => {
                    self.tipos_documentos = data.rows
                    self.total = data.total
                    self.loading = false;
                }, err => {
                    self.loading = false;
                    console.log(err)
                })
            },
            changeLimit(limit) {
                this.filter.page = 1;
                this.filter.limit = limit;
                this.obtenerTiposDocumento()
            },
            changePage(page) {
                this.filter.page = page;
                this.obtenerTiposDocumento()
            },
        },
        /*components: {
            'pagination': Pagination
        },*/
        mounted() {
            this.obtenerTiposDocumento()
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
