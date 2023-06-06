<template>
    <div class="main">
        <b-card>
            <b-row>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Tipo entrada</label>
                        <input class="form-control" readonly type="text" v-model="form.entrada_tipo.descripcion">
						<b-alert variant="danger" show>
							<ul class="error-messages">
								<li
										:key="message"
										v-for="message in errorMessages.entrada_tipo"
										v-text="message"
								></li>
							</ul>
						</b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Código entrada <small>(Automático)</small></label>
                        <input class="form-control" readonly type="text" v-model="form.codigo_entrada">
						<b-alert variant="danger" show>
							<ul class="error-messages">
								<li
										:key="message"
										v-for="message in errorMessages.codigo_entrada"
										v-text="message"
								></li>
							</ul>
						</b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Bodega</label>
                        <input class="form-control" readonly type="text" v-model="form.entrada_bodega.descripcion">
						<b-alert variant="danger" show>
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.entrada_bodega" v-text="message"></li>
							</ul>
						</b-alert>

                    </div>
                </div>
                <template v-if="form.entrada_proveedor">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for>Proveedor</label>
                            <input class="form-control" readonly type="text"
                                   v-model="form.entrada_proveedor.nombre_comercial">
							<b-alert variant="danger" show>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.entrada_proveedor"
										v-text="message"></li>
								</ul>
							</b-alert>

                        </div>
                    </div>
                </template>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Fecha entrada</label>
                        <input class="form-control" readonly type="text" v-model="form.fecha_entrada">
						<b-alert variant="danger" show>
							<ul class="error-messages">
								<li
										:key="message"
										v-for="message in errorMessages.fecha_entrada"
										v-text="message"
								></li>
							</ul>
						</b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Fecha recepción</label>
						<b-form-datepicker
								:date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
								local="es"
								@selected="onDateSelect"
								v-model="form.fecha_recepcion"
								selected-variant="primary"
								class="mb-0"
								placeholder="f.recepcion"
						>

						</b-form-datepicker>
						<b-alert variant="danger" show>
							<ul class="error-messages">
								<li
										:key="message"
										v-for="message in errorMessages.fecha_entrada"
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
									v-for="message in errorMessages.entrada_productos"
									v-text="message"
							></li>
						</ul>
					</b-alert>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad Solicitada</th>
                            <th>Cantidad Recibida</th>
                            <th>Diferencia</th>
                            <!--<th >Precio Unitario</th>
                            <th >SubTotal</th>-->
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(item, index) in form.entrada_productos">
                            <tr>
                                <td>
                                    <input class="form-control" disabled v-model="item.descripcion_producto">
									<b-alert variant="danger" show>
										<ul class="error-messages">
											<li
													:key="message"
													v-for="message in errorMessages[`entrada_productos.${index}.id_producto`]"
													v-text="message">
											</li>
										</ul>
									</b-alert>

                                </td>


                                <td>
                                    <input class="form-control" disabled type="number"
                                           v-model.number="item.cantidad_solicitada">
									<b-alert variant="danger" show>
										<ul class="error-messages">
											<li
													:key="message"
													v-for="message in errorMessages[`entrada_productos.${index}.cantidad_solicitada`]"
													v-text="message">
											</li>
										</ul>
									</b-alert>

                                </td>

                                <td>
                                    <input :disabled="(item.bodega_producto.producto_simple.id_tipo_producto===3  && item.bodega_producto.producto_simple.condicion===1&& item.bodega_producto.producto_simple.producto_detalles_baterias.id_submarca!==7)"
                                           @change="item.cantidad_recibida = Math.max(Math.min(item.cantidad_recibida, item.cantidad_solicitada), 0)"
                                           class="form-control" type="number" v-model.number="item.cantidad_recibida">
									<b-alert variant="danger" show>
										<ul class="error-messages">
											<li
													:key="message"
													v-for="message in errorMessages[`entrada_productos.${index}.cantidad_recibida`]"
													v-text="message">
											</li>
										</ul>
									</b-alert>

                                </td>

                                <td>
                                    {{calcular_diferencia(item.cantidad_solicitada,item.cantidad_recibida)}}
                                </td>


                                <!--<td>
                                    <input class="form-control" disabled type="number" v-model.number="item.precio_unitario">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`entrada_productos.${index}.precio_unitario`]"
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
                                <strong>{{total_cantidad_solicitada}}</strong>
                            </td>
                            <td class="item-footer">
                                <strong>{{total_cantidad_recibida}}</strong>
                            </td>
                            <td></td>
                            <!--<td>Total</td>
                            <td> <strong>C$ {{gran_total | formatMoney(2)}}</strong></td>-->
                        </tr>

                        </tfoot>
                    </table>
                </div>
            </div>
            <br>

            <b-card-footer class="text-lg-right text-center">
                <router-link :to="{ name: 'inventario-entradas' }" >
                    <b-button class="mx-lg-2" variant="secondary" type="button">Cancelar</b-button>
                </router-link>
                <b-button
                        :disabled="btnAction !== 'Recibir Productos'"
                        @click="confirmar"
                        variant="primary"
                        type="button"
                >{{ btnAction }}
                </b-button>
            </b-card-footer>

            <template v-if="loading">
                <BlockUI  :url="url"></BlockUI>
            </template>

        </b-card>
    </div>
</template>

<script type="text/ecmascript-6">
    import entrada from "../../../api/Inventario/entradas";
    import Vue from 'vue'
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
                detalle_baterias: [],
                form: {
                    contiene_baterias: false,
                    codigo_entrada: "",
                    fecha_entrada: moment(new Date()).format("YYYY-MM-DD"),
                    fecha_recepcion: moment(new Date()).format("YYYY-MM-DD"),
                    entrada_tipo: "",
                    entrada_proveedor: "",
                    entrada_bodega: "",
                    entrada_productos: [],
                },
                btnAction: "Recibir Productos",
                errorMessages: []
            };
        },
        computed: {

            total_cantidad_solicitada() {
                return this.form.entrada_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_solicitada))
                }, 0)
            },
            total_cantidad_recibida() {
                return this.form.entrada_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_recibida))
                }, 0)
            },
            total_subt() {
                return this.form.entrada_productos.reduce((carry, item) => {
                        return (carry + Number(item.subtotal.toFixed(2)));
                    }
                    , 0)
            },
            gran_total() {
                return this.form.entrada_productos.reduce((carry, item) => {
                        return (carry + Number(item.total.toFixed(2)));
                    }
                    , 0)
            },
        },
        methods: {

            confirmar() {
                this.$swal.fire({
                    title: 'Esta seguro de confirmar la recepción de la entrada?',
                    text: "Revise bien los datos, esta acción no se puede deshacer.",
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
                        this.recibir();
                    }
                })
            },
            obtenerEntrada() {
                var self = this
                entrada.obtenerEntrada({
                    id_entrada: this.$route.params.id_entrada
                }, data => {
                    this.loading = false;
                    self.form = data.entrada;
                    self.form.fecha_recepcion = data.entrada.fecha_entrada;
                    //console.log(data.traslados);

                    self.form.contiene_baterias = !!data.productos.length;
                    /*data.entrada.productos.forEach((productox, key) => {
                        if(self.detalleForm.productox.id_producto===productox.bodega_producto.id_producto){
                            existeProducto=true;
                        }
                    });*/

                    //self.detalle_baterias = data.entrada.entrada_productos
                    if (self.form.estado !== 1) {
                        this.$toast({
                                component: ToastificationContent,
                                props:{
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'La entrada no puede ser recibida',
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        this.$router.push({
                            name: "inventario-entradas"
                        });
                    }
                })
            },
            sub_total(itemX) {
                itemX.subtotal = Number((Number(itemX.precio_unitario) * Number(itemX.cantidad_solicitada)).toFixed(2));
                itemX.total = itemX.subtotal;
                if (!isNaN(itemX.total)) {
                    return itemX.total;
                } else return 0;
            },

            agregarDetalle() {
                var self = this;
                if (self.detalleForm.productox) {
                    let existeCodigoGarantia = false;
                    let existeProducto = false;
                    self.detalle_baterias.forEach((bateriax, key) => {
                        if (self.detalleForm.productox.id_bateria === bateriax.id_bateria) {
                            existeCodigoGarantia = true;
                        }
                    });

                    if (self.form.entrada_productos) {
                        self.form.entrada_productos.forEach((productox, key) => {
                            if (self.detalleForm.productox.id_producto === productox.bodega_producto.id_producto) {
                                existeProducto = true;
                            }
                        });
                    }

                    if (existeProducto) {
                        if (!existeCodigoGarantia) {
                            let keyx = 0;
                            if (self.form.entrada_productos) {
                                self.form.entrada_productos.forEach((productox, key) => {
                                    if (self.detalleForm.productox.id_producto === productox.bodega_producto.id_producto) {
                                        keyx = key;
                                    }
                                });
                            }

                            if ((self.form.entrada_productos[keyx].cantidad_recibida + 1) <= self.form.entrada_productos[keyx].cantidad_solicitada) {
                                self.form.entrada_productos[keyx].cantidad_recibida++;
                                this.detalle_baterias.push({
                                    //productox: this.detalleForm.productox
                                    codigo_barra: this.detalleForm.productox.codigo_barras,
                                    descripcion: this.detalleForm.productox.bateria_producto.descripcion,
                                    id_producto: this.detalleForm.productox.id_producto,
                                    id_bateria: this.detalleForm.productox.id_bateria,
                                    text: this.detalleForm.productox.codigo_garantia,
                                });
                                alertify.success("Batería agregada!", 5);
                            } else {
                                alertify.warning("Ya se registró la cantidad solicitada de esta batería", 5);
                            }
                        } else {
                            alertify.warning("Ya existe ese código de batería en el listado", 5);
                        }
                    } else {
                        alertify.warning("El código de esta batería pertenece a un producto que no existe en la entrada", 5);
                    }
                    this.detalleForm.productox = {};
                    this.$refs.bateria.focus();
                } else {
                    alertify.warning("Debe seleccionar un producto", 5);
                }
            },

            eliminarLinea(item, index) {
                var self = this;
                if (this.detalle_baterias.length >= 1) {

                    let keyx = 0;
                    if (self.form.entrada_productos) {
                        self.form.entrada_productos.forEach((productox, key) => {
                            if (item.id_producto === productox.bodega_producto.id_producto) {
                                keyx = key;
                            }
                        });
                    }
                    self.form.entrada_productos[keyx].cantidad_recibida--;
                    this.detalle_baterias.splice(index, 1);

                } else {
                    this.detalle_baterias = [];
                }
            },
            onDateSelect(date) {
                this.form.fecha_recepcion = moment(date).format("YYYY-MM-DD"); //
            },

            calcular_diferencia(solicitado, recibido) {
                let diff = solicitado - recibido;
                if (!isNaN(diff) && diff > 0) {
                    return diff;
                } else return 0;
            },
            recibir() {
                var self = this;
                if (self.total_cantidad_recibida > 0) {
                    self.btnAction = "Registrando, espere....";
                    if (self.form.estado === 1) {
                        entrada.recibir(
                            self.form,
                            data => {
                                //alertify.success("La entrada fue recibida correctamente",5);
                                this.$swal.fire(
                                    'Entrada Recibida!',
                                    'La entrada fue recibida correctamente.',
                                    'success'
                                )
                                this.$router.push({
                                    name: "inventario-entradas"
                                });
                            },
                            err => {
                                this.$toast({
                                        component: ToastificationContent,
                                        props:{
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'Ha ocurrido un error' + err,
                                            variant: 'danger',
                                            position: 'bottom-right'
                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                                self.errorMessages = err;
                                self.btnAction = "Recibir Productos";
                            }
                        );
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props:{
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'La entrada ya no puede ser recibida',
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        this.$router.push({
                            name: "inventario.entradas"
                        });
                    }
                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props:{
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'La cantidad recibida debe ser mayor a 0',
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
            this.loading = true;
            this.obtenerEntrada();
        }
    };
</script>
<style>
    .btn-agregar {
        margin-top: 33px;
    }
</style>
