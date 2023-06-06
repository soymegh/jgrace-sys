<template>
    <b-card>
        <b-row>
            <div class="col-md-6 sm-text-center">
                <router-link class="btn btn-success" tag="button" :to="{ name: 'contabilidad-periodos-contables-registrar' }">
                    <feather-icon icon="PlusCircleIcon"></feather-icon> Registrar
                </router-link>
            </div>
            <div @keyup.enter="filter.page = 1;obtener();" class="col-md-6 sm-text-center form-inline justify-content-end">
                <select v-model="filter.search.field" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field">
                    <option value="descripcion">Descripción</option>
                </select>
                <input v-model="filter.search.value" class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text">
                <b-button variant="info" @click="filter.page = 1;obtener();" v-b-tooltip.hover.top="'Buscar!'"><feather-icon icon="SearchIcon"></feather-icon> </b-button>
            </div>
        </b-row>
        <b-row>
            <div class="col-md-6 mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-groups">
                        <thead>
                        <tr>
                            <th>Periodo</th>
                            <th>Descripcion Periodo</th>
                            <th class="text-center table-number">Estado</th>
                            <th class="text-center action">Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr :class="periodoActivoClass(periodo)" :key="periodo.id_periodo_fiscal" @click="seleccionarPeriodo(periodo)" v-for="periodo in periodos">
                            <td>{{ periodo.periodo }}</td>
                            <td>{{ periodo.descripcion}}</td>
                            <td class="text-center">
                                <div v-if="periodo.estado===0">
                                    <span class="badge badge-success">En progreso</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-warning">Completado</span>
                                </div>
                            </td>
                            <td class="text-center" >
                                <router-link v-b-tooltip.hover.top="'Editar!'" :to="{ name: 'contabilidad-periodos-contables-actualizar', params: { id_periodo_fiscal: periodo.id_periodo_fiscal } }" tag="a"><feather-icon icon="EditIcon"></feather-icon></router-link>
                            </td>
                        </tr>
                        <tr v-if="!periodos.length">
                            <td class="text-center" colspan="4">Sin datos</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th  class="text-center" colspan="4"><b>Meses del periodo</b></th>
                        </tr>
                        </thead>
                        <thead>
                        <tr>
                            <th class="text-center">Mes</th>
                            <th class="text-center table-number">Estado</th>
                            <th colspan="2" class="text-center"> Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr :key="mes.id_periodo_fiscal_mes" v-for="mes in meses">
                            <td>{{ mes.descripcion}}</td>
                            <td class="text-center">
                                <div v-if="mes.estado===1">
                                    <span class="badge badge-success">Abierto</span>
                                </div>
                                <div v-if="mes.estado===2">
                                    <span class="badge badge-warning">Cerrado</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <a v-if="mes.estado === 1"  v-b-tooltip.hover.top="'Cerrar mes!'" @click.prevent="cerrar_periodo(mes.id_periodo_fiscal,mes.id_periodo_mes, 1)">  <feather-icon icon="CheckCircleIcon"></feather-icon> Cerrar</a>
                            </td>

                            <td class="text-center">
                                <a v-if="mes.estado === 1"  v-b-tooltip.hover.top="'Cerrar mes!'" @click.prevent="cerrar_periodo(mes.id_periodo_fiscal,mes.id_periodo_mes, 2)">  <feather-icon icon="LoaderIcon"></feather-icon> Procesar</a>
                            </td>
                        </tr>
                        <tr v-if="!meses.length">
                            <td class="text-center" colspan="3">Seleccione un periodo</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </b-row>
        <b-card-footer>
            <pagination @changePage="changePage" @changeLimit="changeLimit" :items="periodos" :total="total" :page="filter.page" :limitOptions="filter.limitOptions" :limit="filter.limit"></pagination>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,VBTooltip,BFormCheckbox,BFormGroup} from 'bootstrap-vue'
    import periodo from '../../../api/contabilidad/periodos'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
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
                periodos: [],
                total: 0,
                periodoActivo: 0,
                meses: []
            }
        },
        methods: {
            cerrar_periodo(id_periodo,id_mes,modo){ // modo 1 -> Cerrar mes modo 2 -> Procesador datos sin cerrar mes
                let txt, txtTitle;
                if (modo === 1) {
                    txtTitle = 'Esta seguro de cerrar este mes?';
                    txt = 'Se procederá a consolidar los movimientos contables y cerrar el mes'
                } else {
                    txtTitle= 'Está seguro de procesar los datos?';
                    txt= 'Se procederá a consolidar los movimientos contables para este mes, '
                }

                this.$swal.fire({
                    title: txtTitle,
                    text: txt,
                    showCancelButton: true,
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, consolidar'
                }).then((result) => {
                    if (result.value) {
                        periodo.cerrar(
                            {
                                id_periodo : id_periodo,
                                id_mes:id_mes,
                                modo:modo
                            },
                            data => {
                                this.$swal.fire(
                                    'Proceso completado',
                                    'Se ha completado el proceso de consolidación',
                                    'success'
                                );
                                this.obtener();
                            },
                            err => {
                                this.$swal.fire(
                                    'Hubo un error al cerrar el mes!',
                                    err,
                                    'warning'
                                )
                            }
                        );



                    }
                })

            },
            periodoActivoClass(periodo) {
                if (this.periodoActivo === periodo.id_periodo_fiscal) {
                    return {
                        'text-right': true,
                        'active': true
                    }
                } else {
                    return {
                        'text-right': true
                    }
                }
            },
            seleccionarPeriodo(periodo) {
                this.periodoActivo = periodo.id_periodo_fiscal
                //this.obtenerPermisos(periodo.id_periodo_fiscal)
                this.meses = periodo.meses_periodo;
            },

            obtener() {
                var self = this;
                self.loading = true;
                self.periodoActivo= 0;
                self.meses = '';
                periodo.obtener(self.filter, data => {
                    self.periodos = data.rows;
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
                this.obtener()
            },
            changePage(page) {
                this.filter.page = page
                this.obtener()
            },
        },
        /*components: {
            'pagination': Pagination
        },*/
        mounted() {
            this.obtener()
        }
    }
</script>
<style lang="scss">
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
            width: 100px;
        }
    }

    .table-groups {
        tr {
            &.active {
                color: #fff;
                background: rgba(29, 39, 94, 0.72);
            }
            .group-list {
                display: -webkit-box;
                display: -moz-box;
                display: -ms-flexbox;
                display: -webkit-flex;
                display: flex;
                i {
                    margin-top: auto;
                    margin-bottom: auto;
                    margin-left: auto;
                }
            }
        }
    }
</style>
