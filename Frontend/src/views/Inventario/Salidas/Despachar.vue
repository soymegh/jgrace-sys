<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Tipo salida</label>
                    <input class="form-control" readonly type="text" v-model="form.salida_tipo.descripcion">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li
									:key="message"
									v-for="message in errorMessages.salida_tipo"
									v-text="message"
							></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Código salida <small>(Automático)</small></label>
                    <input class="form-control" readonly type="text" v-model="form.codigo_salida">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li
									:key="message"
									v-for="message in errorMessages.codigo_salida"
									v-text="message"
							></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Bodega</label>
                    <input class="form-control" readonly type="text" v-model="form.salida_bodega.descripcion">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.salida_bodega" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>
            <template v-if="form.salida_proveedor">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Proveedor</label>

                        <input class="form-control" readonly type="text"
                               v-model="form.salida_proveedor.nombre_comercial">
						<b-alert variant="danger" show>
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.salida_proveedor" v-text="message"></li>
							</ul>
						</b-alert>



                    </div>
                </div>
            </template>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Fecha salida</label>
                    <input class="form-control" readonly type="text" v-model="form.fecha_salida">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li
									:key="message"
									v-for="message in errorMessages.fecha_salida"
									v-text="message"
							></li>
						</ul>
					</b-alert>

                </div>
            </div>
        </b-row>

        <div class="row">
            <div class="col-sm-12">
				<b-alert variant="danger" show>
					<ul class="error-messages">
						<li
								:key="message"
								v-for="message in errorMessages.salida_productos"
								v-text="message"
						></li>
					</ul>
				</b-alert>

                <div class="table-responsive mt-3">
                    <table class="table  table-bordered">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Saliente</th>
                            <th>Cantidad Despachada</th>
                            <th>Diferencia</th>
                            <!--	<th >Precio Unitario</th>
                                <th >SubTotal</th>-->

                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(item, index) in form.salida_productos">
                            <tr>
                                <td>
                                    <input class="form-control" disabled v-model="item.descripcion_producto">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`salida_productos.${index}.id_producto`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>

                                </td>


                                <td>
                                    <input class="form-control" disabled type="number"
                                           v-model.number="item.cantidad_saliente">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`salida_productos.${index}.cantidad_saliente`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>

                                </td>

                                <td>
                                    <input :disabled="(item.bodega_producto.producto_simple.id_tipo_producto===3 && item.bodega_producto.producto_simple.condicion===1&& item.bodega_producto.producto_simple.producto_detalles_baterias.id_submarca!==7)"
                                           @change="item.cantidad_despachada = Math.max(Math.min(item.cantidad_despachada, item.cantidad_saliente), 0)"
                                           class="form-control" type="number" v-model.number="item.cantidad_despachada">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`salida_productos.${index}.cantidad_despachada`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>

                                </td>

                                <td>
                                    {{calcular_diferencia(item.cantidad_saliente,item.cantidad_despachada)}}
                                </td>


                                <!--<td>
                                    <input class="form-control" disabled type="number" v-model.number="item.precio_unitario">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`salida_productos.${index}.precio_unitario`]"
                                                v-text="message">
                                        </li>
                                    </ul>
                                </td>

                                <td>
                                    <strong>C$ {{sub_total(item)| formatMoney(2)}}</strong>
                                </td>-->


                            </tr>
                            <tr></tr>
                        </template>
                        </tbody>
                        <tfoot>

                        <tr>
                            <td colspan="4"></td>
                            <!--<td>Subtotal</td>
                            <td> <strong>C$ {{total_subt | formatMoney(2)}}</strong></td>-->
                        </tr>
                        <tr>
                            <td class="item-footer"> Total Unidades</td>
                            <td class="item-footer">
                                <strong>{{total_cantidad_saliente}}</strong>
                            </td>
                            <td class="item-footer">
                                <strong>{{total_cantidad_despachada}}</strong>
                            </td>
                            <td class="item-footer">
                                <strong>{{total_cantidad_diferencia}}</strong>
                            </td>
                            <!--<td>Total</td>
                            <td> <strong>C$ {{gran_total | formatMoney(2)}}</strong></td>-->
                        </tr>

                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

        <b-card-footer class="content-box-footer text-lg-right text-center">
            <router-link :to="{ name: 'inventario-salidas' }" >
                <b-button variant="secondary" type="button" class="mx-1">Cancelar</b-button>
            </router-link>
            <b-button class="mx-1"
                    :disabled="btnAction !== 'Despachar Productos'"
                    @click="despachar"
                    variant="primary"
                    type="button"
            >{{ btnAction }}
            </b-button>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>


    </b-card>
</template>

<script type="text/ecmascript-6">
    import Vue from 'vue'
    import salida from "../../../api/Inventario/salidas";
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'

    import {
        BAlert,
        BButton,
        BCard,
        BCardFooter,
        BFormCheckbox,
        BFormCheckboxGroup, BFormDatepicker,
        BFormGroup,
        BPaginationNav,
        BRow, VBTooltip
    } from "bootstrap-vue";

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
                url: loadingImage,   //It is important to import the loading image then use here
                bateriasBusquedaURL: "/productos/baterias/buscar",
                es: es,
                format: "dd-MM-yyyy",
                detalleForm: {
                    productox: {},
                },
                productos: [],
                detalle_baterias: [],
                form: {
                    contiene_baterias: false,
                    codigo_salida: "",
                    fecha_salida: moment(new Date()).format("YYYY-MM-DD"),
                    salida_tipo: "",
                    salida_proveedor: "",
                    salida_bodega: "",
                    salida_productos: []
                },
                btnAction: "Despachar Productos",
                errorMessages: []
            };
        },
        computed: {

            total_cantidad_saliente() {
                return this.form.salida_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_saliente))
                }, 0)
            },
            total_cantidad_despachada() {
                return this.form.salida_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_despachada))
                }, 0)
            },
            total_cantidad_diferencia() {
                return this.form.salida_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_saliente - item.cantidad_despachada))
                }, 0)
            },
            total_subt() {
                return this.form.salida_productos.reduce((carry, item) => {
                        return (carry + Number(item.subtotal.toFixed(2)));
                    }
                    , 0)
            },
            gran_total() {
                return this.form.salida_productos.reduce((carry, item) => {
                        return (carry + Number(item.total.toFixed(2)));
                    }
                    , 0)
            },
        },
        methods: {
            /*seleccionarProducto(e) {
                const productoP = e.target.value;
                var self = this;
                self.detalleForm.productox = productoP;
                this.$refs.fecha_activacion.focus();
            },*/


            eliminarLinea(item, index) {
                var self = this;
                if (this.detalle_baterias.length >= 1) {

                    let keyx = 0;
                    if (self.form.salida_productos) {
                        self.form.salida_productos.forEach((productox, key) => {
                            if (item.productox.id_producto === productox.bodega_producto.id_producto) {
                                keyx = key;
                            }
                        });
                    }
                    self.form.salida_productos[keyx].cantidad_despachada--;
                    this.detalle_baterias.splice(index, 1);

                } else {
                    this.detalle_baterias = [];
                }
            },


            calcular_diferencia(solicitado, recibido) {
                let diff = solicitado - recibido;
                if (!isNaN(diff) && diff > 0) {
                    return diff;
                } else return 0;
            },
            obtenerSalida() {
                var self = this
                self.loading = true;
                salida.obtenerSalida({
                    id_salida: this.$route.params.id_salida
                }, data => {
                    self.form = data.salida
                    if (data.productos.length) {
                        self.form.contiene_baterias = true;
                    } else {
                        self.form.contiene_baterias = false;
                    }

                    if (self.form.estado !== 1) {
						this.$toast({
									component: ToastificationContent,
									props: {
										title: 'Notificación',
										icon: 'InfoIcon',
										text: 'La salida ya no puede ser despachada.',
										variant: 'danger',
										position: 'bottom-right'
									}
								},
								{
									position: 'bottom-right'
								});
                        this.$router.push({
                            name: "inventario-salidas"
                        });
                    }
                    this.loading = false;
                });
            },
            sub_total(itemX) {
                itemX.subtotal = Number((Number(itemX.precio_unitario) * Number(itemX.cantidad_saliente)).toFixed(2));
                itemX.total = itemX.subtotal;
                if (!isNaN(itemX.total)) {
                    return itemX.total;
                } else return 0;
            },

            despachar() {
                var self = this;
                if (self.total_cantidad_despachada === self.total_cantidad_saliente) {
                    self.form.detalle_baterias = self.detalle_baterias;
                    self.btnAction = "Registrando, espere....";
                    if (self.form.estado === 1) {


                        self.$swal.fire({
                            title: 'Esta seguro de confirmar el despacho de la salida?',
                            text: "Esta acción no se puede deshacer",
                            icon: 'warning',
                            showCancelButton: true,
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-danger ml-1',
                            },
                            confirmButtonText: 'Si, confirmar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.value) {
                                salida.despachar(
                                    self.form,
                                    data => {
                                        this.$swal.fire(
                                            'Salida ha sido despachada exitosamente!',
                                            'La Salida fue despachada correctamente.',
                                            'success'
                                        )
                                        this.$router.push({
                                            name: "inventario-salidas"
                                        });
                                    },
                                    err => {
                                        self.errorMessages = err;
                                        self.btnAction = "Despachar Productos";
                                    }
                                );
                            } else {
                                self.btnAction = "Despachar Productos";
                            }
                        })

                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'La salida ya no puede ser despachada.',
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        this.$router.push({
                            name: "inventario-salidas"
                        });
                    }
                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Se debe despachar la misma cantidad que fue solicitada.',
                                variant: 'danger',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            }
        },
        mounted() {
            //this.loading=true;
            this.obtenerSalida();

        },
    };
</script>
<style>
    .btn-agregar {
        margin-top: 33px;
    }
</style>
