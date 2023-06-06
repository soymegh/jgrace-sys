<template>
    <b-card>
        <b-row>
            <div class="col-md-6 sm-text-center">
                <router-link :to="{ name: 'admon-usuarios-registrar' }" class="btn btn-success" tag="button" v-b-tooltip.hover.top="'Registrar nuevo usuario!'">
                    <feather-icon icon="FilePlusIcon"></feather-icon> Registrar
                </router-link>
            </div>
            <div @keyup.enter="filter.page = 1;obtenerUsuarios();" class="col-md-6 sm-text-center form-inline justify-content-end">
                <select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
                    <option value="name">Nombre</option>
                </select>
                <input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text" v-model="filter.search.value">
                <b-button class="btn btn-info mx-1" style="margin-right: .5rem!important;" v-b-tooltip.hover.top="'Buscar usuario!'"
                          @click="filter.page = 1; obtenerUsuarios();"> <feather-icon icon="SearchIcon"></feather-icon></b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <!--<th>Sucursal</th>-->
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <tr :key="usuario.id_usuario" v-for="usuario in usuarios">
                    <template>
                        <td>{{ usuario.name }}</td>
                        <td>{{ usuario.rol.descripcion }}</td>
                        <!--<template v-if="usuario.id_sucursal>0">
                        <td>{{ usuario.sucursal.descripcion }}</td>
                        </template>
                        <template v-else>
                            <td>{{ "Todas las sucursales" }}</td>
                        </template>-->
                        <td class="text-center">
                            <div v-if="usuario.estado===1">
                                <span class="badge badge-success">Activo</span>
                            </div>
                            <div v-else>
                                <span class="badge badge-danger">Desactivado</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <router-link tag="a" :to="{ name: 'admon-usuarios-actualizar', params: { id: usuario.id } }" v-b-tooltip.hover.top="'Editar registro!'"><feather-icon icon="EditIcon"></feather-icon></router-link>
                        </td>
                    </template>
                <tr v-if="!usuarios.length">
                    <td class="text-center" colspan="8">Sin registros</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination :items="usuarios" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page" :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>
        </b-card-footer>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,VBTooltip} from 'bootstrap-vue'
    import usuarios from '../../../api/admon/usuarios'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";

    var fecha_actual = new Date();
    fecha_actual.setHours(23, 59, 59, 999);
    export default {
        components:{
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton
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
                    limit: 5,
                    limitOptions: [5, 10, 15, 20],
                    search: {
                        field: 'name',
                        value: ''
                    }
                },
                usuarios: [],
                total: 0
            }
        },
        methods: {
            obtenerUsuarios() {
                var self = this
                self.loading = true;
                usuarios.obtenerUsuarios(self.filter, data => {
                    self.usuarios = data.rows
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
                this.obtenerUsuarios()
            },
            changePage(page) {
                this.filter.page = page
                this.obtenerUsuarios()
            },
        },
        /*components: {
            'pagination': Pagination
        },*/
        mounted() {
            this.obtenerUsuarios()
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
