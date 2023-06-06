<template>
    <b-card>
        <b-row>
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="check-label2"><input type="checkbox" v-on:change="cambiarBodega"
                                                       v-model="todas_bodegas"> Todos las bodegas</label>
                </div>
            </div>

            <div v-if="!todas_bodegas" class="col-sm-3">
                <div class="form-group">
                    <label> Bodega</label>
                    <div class="form-group">
                        <v-select style="width: 100%;" v-model="form.bodega" :filterable="false"
                                  @search="onSearch" @input="seleccionarBodega" :options="bodegas" label="text" > <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select-->
                            <template slot="no-options">
                                No se encontraron registros
                            </template>
                        </v-select>
                    </div>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.bodega" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div v-if="todas_bodegas" class="col-sm-3">
                <div class="form-group">
                    <label> Bodega</label>
                    <div class="form-group">
                        <input type="text" class="form-control" disabled>
                    </div>
                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Producto</label>
                    <v-select style="width: 100%;" v-model="form.productoB" :filterable="false"
                              @search="onSearchP"  :options="productos" label="text" placeholder="Escriba para buscar un producto"> <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select  || @input="seleccionarProducto"-->
                        <template slot="no-options">
                            No se encontraron registros
                        </template>
                    </v-select>

					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.productoB"
								v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>
            <div class="col-sm-4">
                <label for=""></label>
                <div class="form-group">
                    <b-button v-b-tooltip.hover.top="'Buscar'" variant="info"  @click="obtenerMovimientos()" class="mx-lg-1"><feather-icon icon="SearchIcon"></feather-icon>
                    </b-button>
                    <b-button v-b-tooltip.hover.top="'Descargar el reporte en formato PDF'" :disabled="descargando" variant="danger"
                             @click="downloadItem('pdf', form.bodega.id_bodega, form.productoB.id_producto)" class="mx-lg-1"><feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                    <b-button v-b-tooltip.hover.top="'Descargar el reporte en formato EXCEL'" :disabled="descargando" variant="success"
                             @click="downloadItem('xls', form.bodega.id_bodega, form.productoB.id_producto)" class="mx-lg-1"><feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </div>
            </div>

        </b-row>
        <br>
		<b-alert variant="success" show class="mb-2 mt-2">
			<div class="alert-body">
				<span><strong>Detalle de kardex</strong></span>
			</div>
		</b-alert>

        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for=""> Nombre producto</label>
                    <input readonly class="form-control" placeholder="Nombre producto"
                           v-model="detalle_productox.descripcion">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for=""> Código</label>
                    <input readonly class="form-control" placeholder="Código producto"
                           v-model="detalle_productox.codigo">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for=""> Total Inventario</label>
                    <input readonly class="form-control" placeholder="Unidades" v-model="total_unidadesx">
                </div>
            </div>
        </b-row>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="text-center table-number">Fecha</th>
                    <th class="text-center table-number">Bodega</th>
                    <th class="text-center table-number">Concepto</th>
                    <th class="text-center table-number">Entradas</th>
                    <th class="text-center table-number">Salidas</th>
                    <th class="text-center table-number">Saldo</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(movimiento, index) in movimientos" :key="movimiento.id_movimiento">
                    <td class="text-center">{{ movimiento.fecha_movimiento }}</td>
                    <td class="text-center">{{ movimiento.bodega }}</td>
                    <td class="text-center">
                        <template v-if="movimiento.id_tipo_mov===1">
                            <router-link target="_blank"
                                         :to="{ name: 'inventario-entradas-mostrar', params: { id_entrada: movimiento.identificador_origen_movimiento } }">
                                <feather-icon icon="EyeIcon"></feather-icon> {{ movimiento.descripcion_movimiento}}
                            </router-link>
                        </template>

                        <template v-if="movimiento.id_tipo_mov===2">
                            <router-link target="_blank"
                                         :to="{ name: 'inventario-salidas-mostrar', params: { id_salida: movimiento.identificador_origen_movimiento } }">
								<feather-icon icon="EyeIcon"></feather-icon> {{ movimiento.descripcion_movimiento}}
                            </router-link>
                        </template>
                    </td>
                    <td class="text-center">{{ movimiento.unidades_entrada > 0 ? movimiento.unidades_entrada : '-'}}
                    </td>
                    <td class="text-center">{{ movimiento.unidades_salida > 0 ? movimiento.unidades_salida : '-'}}</td>
                    <td class="text-center"><strong>{{calcular_unidades(movimiento,index)}}</strong></td>
                </tr>
                <tr v-if="!movimientos.length">
                    <td class="text-center" colspan="12">Sin datos</td>
                </tr>
                </tbody>
                <tfoot>

                <tr>
                    <td class="text-left" colspan="3"><strong>{{'Inventario Final de '+total_unidadesx + ' unidades'}}</strong></td>
                    <td class="text-center">
                        <!--<b-button variant="success" @click="obtenerListaCodigos()"><feather-icon icon="SearchIcon"></feather-icon> Ver
                            Detalle Códigos
                        </b-button>-->
                    </td>
                    <td class="text-center">Saldo Final</td>
                    <td class="text-center">
                        <strong>{{consolidar_unidades | formatMoney(2) }}</strong>
                    </td>
                </tr>

                </tfoot>
            </table>
        </div>
        <pagination :items="lista_codigos" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page"
                    :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>


        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import movimientos_productos from '../../../api/Inventario/kardex'
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
    } from 'bootstrap-vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'
    import axios from "axios";

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
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: false,
                msg: 'Cargando el contenido espere un momento',
                url: loadingImage,
                bodegasBusquedaURL: "/bodegas/buscar",
                todas_bodegas: true,
                descargando: false,
                productosBusqueda: {},
                productosBusquedaURL: "/inventario/productos/buscar",
                detalle_productox: [],
                lista_codigos: [],
                form: {
                    productoB: '',
                    bodega: {
                        id_bodega: 0,
                        text:''
                    },
                },
                movimientos: [],
                bodegas: [],
                productos: [],
                total: 0,
                total_unidadesx: 0,
                total_saldox: 0,
                costo_promediox: 0,
                errorMessages: [],
                filter: {
                    page: 1,
                    limit: 20,
                    limitOptions: [20, 40, 60, 100],
                },
            }
        },
        computed: {

            consolidar_unidades() {
                if (this.movimientos) {
                    this.total_unidadesx = this.movimientos.reduce((carry, item) => {
                        this.total_unidadesx = (carry + Number(item.cantidad_movimiento))
                        return this.total_unidadesx;
                    }, 0);

                    return this.total_unidadesx
                } else {
                    return 0;
                }

            },
        },
        methods: {
            //Busqueda de bodega
            onSearch(search,loading){
                if (search.length){
                    loading(true)
                    this.search(loading,search,this);
                }

            },
            select(e) {
                this.$emit('input', {
                    target: {
                        value: result
                    }
                })
                this.onEsc()
            },
            search: _.debounce((loading,search,vm) => {
                let self = this;
                axios.get(`/bodegas/buscar?q=${escape(search)}`).then((res)=>{
                    vm.options = res.data.results;
                    vm.bodegas = res.data.results;
                    loading(false);
                })
            },150),

            //Busqueda de producto
            onSearchP(searchP,loading){
                if (searchP.length){
                    loading(true)
                    this.searchP(loading,searchP,this);
                }

            },
            select(e) {
                this.$emit('input', {
                    target: {
                        value: result
                    }
                })
                this.onEsc()
            },
            searchP: _.debounce((loading,search,vm) => {
                let self = this;
                axios.post(`/inventario/productos/buscar?q=${escape(search)}`).then((res)=>{
                    vm.options = res.data.results;
                    vm.productos = res.data.results;
                    loading(false);
                })
            },150),
            changeLimit(limit) {
                this.filter.page = 1
                this.filter.limit = limit
                this.obtenerListaCodigos()
            },
            changePage(page) {
                this.filter.page = page
                this.obtenerListaCodigos()
            },
            cambiarBodega() {
                var self = this;
                if (self.todas_bodegas) {
                    self.form.bodega = {};
                    self.form.bodega.id_bodega = 0;
                }
            },

            seleccionarBodega(e) {
                const bodegaS = e.target.value;
                var self = this;
                self.form.bodega = bodegaS;
            },

            downloadItem(extension, id_bodega, id_producto) {
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/kardex-movimiento-productos/' + extension + '/' + id_bodega + '/' + id_producto;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoKardex.' + extension;
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
                                    icon: 'CheckIcon',
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
                                icon: 'CheckIcon',
                                text: 'Espere a que se complete la descarga anterior',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },

            calcular_unidades(itemX, keyX) {
                //console.log('unidd')
                let self = this;
                let unidadesx = 0;
                self.movimientos.forEach((movimiento, key) => {
                    if (key <= keyX) {
                        unidadesx = unidadesx + Number(movimiento.cantidad_movimiento);
                    }
                });

                return Number(unidadesx);
            },

            calcular_saldo(itemX, keyX) {
                let self = this;
                let saldox = 0;

                let unidadesx = 0;
                self.movimientos.forEach((movimiento, key) => {
                    if (key <= keyX) {
                        unidadesx = unidadesx + Number(movimiento.cantidad_movimiento);
                    }
                });

                if (unidadesx > 0) {
                    self.movimientos.forEach((movimiento, key) => {
                        if (key <= keyX) {
                            saldox = Number((Number(saldox) + Number(movimiento.total_movimiento)).toFixed(2));
                        }
                    });
                } else {
                    saldox = 0;
                }
                return Number(saldox);
            },

            calcular_costo_promedio(itemX, keyX) {
                let self = this;
                let cppx = 0;
                let saldox = 0;
                let unidadesx = 0;
                self.movimientos.forEach((movimiento, key) => {
                    if (key <= keyX) {
                        saldox = Number(saldox) + Number(movimiento.total_movimiento);
                        unidadesx = Number(unidadesx) + Number(movimiento.cantidad_movimiento);
                    }
                });
                if (unidadesx > 0) {
                    cppx = Number((saldox / unidadesx).toFixed(2));
                } else {
                    cppx = 0;
                }


                return cppx;
            },

            seleccionarProducto(e) {
                const producto = e.target.value;
                var self = this;
                self.form.productoB = producto;
            },
            obtenerListaCodigos() {
                var self = this;
                self.loading = true;
                movimientos_productos.obtenerListaCodigos(
                    {
                        productoB: self.form.productoB,
                        bodega: self.form.bodega,
                        limit: self.filter.limit,
                        page: self.filter.page,
                    }, data => {
                        self.lista_codigos = data.rows
                        self.total = data.total
                        self.loading = false;
                    }, err => {
                        self.loading = false;
                        console.log(err)
                    })
            },
            obtenerMovimientos() {
                var self = this
                self.loading = true;
                this.filter.page = 1;
                movimientos_productos.obtenerMovimientos(self.form, data => {
                    self.movimientos = data
                    self.lista_codigos = []
                    self.errorMessages = []
                    self.loading = false;
                }, err => {
                    console.log(err)
                    self.loading = false;
                    self.errorMessages = err
                })
                self.detalle_productox.codigo = self.form.productoB.codigo_sistema;
                self.detalle_productox.descripcion = self.form.productoB.descripcion;
            }
        },
    }
</script>

<style lang="scss">
	@import '@core/scss/vue/libs/vue-select.scss';
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

    .btn-generar {
        margin-top: 33px;
    }

    .check-label2 {
        margin-top: 40px;
    }
</style>
