<template>
    <b-card>
        <b-row>
            <div class="col-md-4 sm-text-center">
                <router-link :to="{ name: 'admon-paises-registrar' }" class="btn btn-success" tag="button">
                    <i class="pe-7s-plus"></i> Registrar
                </router-link>
            </div>
            <div @keyup.enter="filter.page = 1;obtenerPaises();"
                 class="col-md-8 sm-text-center form-inline justify-content-end">
                <div class="form-group mx-1">
                    <b-form-checkbox v-model="filter.search.status" class="custom-control-primary">
                        Mostrar Todos
                    </b-form-checkbox>


                </div>
                <select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
                    <option value="descripcion">Descripción</option>
                </select>
                <input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text"
                       v-model="filter.search.value">
                <b-button class="btn btn-info mx-1" style="margin-right: .5rem!important;"
                          v-b-tooltip.hover.top="'Buscar país!'"
                          @click="filter.page = 1; obtenerPaises();">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <tr :key="pais.id_pais" v-for="pais in paises">
                    <td>{{ pais.codigo }}</td>
                    <td>{{ pais.descripcion }}</td>
                    <td class="text-center">
                        <div v-if="pais.estado===1">
                            <span class="badge badge-success">Activo</span>
                        </div>
                        <div v-else>
                            <span class="badge badge-danger">Desactivado</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <router-link tag="a" :to="{ name: 'admon-paises-actualizar', params: { id_pais: pais.id_pais } }" v-b-tooltip.hover.top="'Editar registro!'"><feather-icon icon="EditIcon"></feather-icon></router-link>
                    </td>
                </tr>
                <tr v-if="!paises.length">
                    <td class="text-center" colspan="4">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination :items="paises" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page"
                        :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>

            <template v-if="loading">
                <BlockUI  :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,VBTooltip,BFormCheckbox,BFormGroup} from 'bootstrap-vue'
    import pais from '../../../api/admon/paises'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";

    var fecha_actual = new Date();
    fecha_actual.setHours(23, 59, 59, 999);
    export default {
        components:{
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,BFormCheckbox,BFormGroup
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading:true,
                url : loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 10,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: 'descripcion',
                        value: '',
                        status: 0
                    }
                },
                paises: [],
                total: 0
            }
        },
        methods: {
            obtenerPaises() {
                var self = this;
                self.loading = true;
                pais.obtener(self.filter, data => {
                    self.paises = data.rows
                    self.total = data.total
                    self.loading = false;
                }, err => {
                    self.loading = false;
                    console.log(err)
                })
            },
            changeLimit(limit) {
                this.filter.page = 1
                this.filter.limit = limit
                this.obtenerPaises()
            },
            changePage(page) {
                this.filter.page = page
                this.obtenerPaises()
            },
        },
        /*components: {
            'pagination': Pagination
        },*/
        mounted() {
            this.obtenerPaises()
        }
    }
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
            width: 100px;
        }
    }
</style>
