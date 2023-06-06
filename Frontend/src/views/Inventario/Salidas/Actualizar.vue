<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Tipo salida</label>
                    <v-select
                            :options="tipos_salida"
                            label="descripcion"
                            v-model="form.salida_tipo"
                            placeholder="Seleccione un tipo de salida"
                    >
                        <template slot="no-options">No se encontraron registros</template>
                    </v-select>
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
                    <v-select
                            :options="bodegas"
                            label="descripcion"
                            v-model="form.salida_bodega"
                            v-on:input="obtenerProductosBodega()"
                            placeholder="Seleccione una bodega"
                    >
                        <template slot="no-options">No se encontraron registros</template>
                    </v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.salida_bodega"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

<!--            <div class="col-sm-4">-->
<!--                <div class="form-group">-->
<!--                    <label for>Proveedor</label>-->
<!--                    <v-select-->
<!--                            :options="proveedores"-->
<!--                            label="nombre_comercial"-->
<!--                            v-model="form.salida_proveedor"-->
<!--                    >-->
<!--                        <template slot="no-options">No se encontraron registros</template>-->
<!--                    </v-select>-->
<!--                    <b-alert variant="danger" show>-->
<!--                        <ul class="error-messages">-->
<!--                            <li :key="message" v-for="message in errorMessages.salida_proveedor"-->
<!--                                v-text="message"></li>-->
<!--                        </ul>-->
<!--                    </b-alert>-->

<!--                </div>-->
<!--            </div>-->

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Fecha salida</label>

                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            local="es"
                            @selected="onDateSelect"
                            v-model="form.fecha_salida"
                            selected-variant="primary"
                            class="mb-0"
                            placeholder="f.entrada"
                    >

                    </b-form-datepicker>
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

            <div class="col-sm-8">
                <div class="form-group">
                    <label for>Observaciones salida</label>
                    <input class="form-control" type="text" v-model="form.descripcion_salida">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.descripcion_salida"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
        </b-row>

        <div v-if="!form.salida_bodega">
            <b-alert variant="info" show class="mt-2 mb-2">
                <div class="alert-body">
                    <span>Se requiere que seleccione una bodega para continuar.</span>
                </div>
                <hr>
            </b-alert>

        </div>

        <b-alert variant="success" show class="mb-2 mt-2">
            <div class="alert-body">
                <span><strong>Detalle de la salida</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for>Producto</label>
                    <v-select
                            :options="productos"
                            label="text"
                            v-model="detalleForm.productox"
                            v-on:input="cargar_detalles_producto()"
                            ref="producto"
                    >
                        <template slot="no-options">No se encontraron registros</template>
                    </v-select>
                    <!---->
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.productox"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Cantidad</label>
                    <input @change="detalleForm.cantidad = Math.max(Math.min(Math.round(detalleForm.cantidad), detalleForm.productox.cantidad_disponible), 1)"
                           class="form-control" ref="cantidad" type="number"
                           v-model.number="detalleForm.cantidad">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.cantidadx"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <!--<div class="col-sm-3">
                <div class="form-group">
                    <label for>Precio Unitario</label>
                    <div class="input-group">
                        <input class="form-control" type="number"
                               v-model.number="detalleForm.precio_unitario">
                    </div>

                    <ul class="error-messages">
                        <li
                                :key="message"
                                v-for="message in errorMessages.precio_unitariox"
                                v-text="message"
                        ></li>
                    </ul>
                </div>
            </div>-->

            <div class="col-sm-3">
                <label for=""></label>
                <div class="form-group">
                    <label for>&nbsp;</label>
                    <b-button @click="agregarDetalle" variant="info" ref="agregar">Agregar Producto
                    </b-button>
                </div>
            </div>

        </b-row>

        <b-row>
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
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Cantidad Saliente</th>
                            <!-- <th>Cantidad Despachada</th>-->
                            <!--                        <th >Precio Unitario</th>-->
                            <!--                        <th >SubTotal</th>-->

                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(item, index) in form.salida_productos">
                            <tr>
                                <td style="width: 2%">
                                    <b-button @click="eliminarLinea(item, index)" v-b-tooltip.hover.top="'Eliminar línea'" variant="danger">
                                        <feather-icon icon="TrashIcon" ></feather-icon>
                                    </b-button>
                                </td>
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
                                    <input @change="item.cantidad_saliente = Math.max(Math.min(Math.round(item.cantidad_saliente), item.bodega_producto.cantidad), 1)"
                                           class="form-control" type="number"
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


                                <!--                            	<td>-->
                                <!--                                    <input  class="form-control" type="number" v-model.number="item.precio_unitario">-->
                                <!--                                    <ul class="error-messages">-->
                                <!--                                        <li-->
                                <!--                                                :key="message"-->
                                <!--                                                v-for="message in errorMessages[`salida_productos.${index}.precio_unitario`]"-->
                                <!--                                                v-text="message">-->
                                <!--                                        </li>-->
                                <!--                                    </ul>-->
                                <!--                                </td>-->

                                <!--                                <td>-->
                                <!--                                    <strong>C$ {{sub_total(item)| formatMoney(2)}}</strong>-->
                                <!--                                </td>-->

                            </tr>
                            <tr></tr>
                        </template>
                        </tbody>
                        <tfoot>

                        <!--                    <tr>-->
                        <!--                        <td colspan="3"></td>-->
                        <!--                        <td>Subtotal</td>-->
                        <!--                        <td><strong>C$ {{total_subt | formatMoney(2)}}</strong></td>-->
                        <!--                    </tr>-->
                        <tr>
                            <td class="item-footer" colspan="2"> Total Unidades</td>
                            <td class="item-footer">
                                <strong>{{total_cantidad_saliente}}</strong>
                            </td>
                            <!--                        <td class="item-footer">-->
                            <!--                            <strong>{{total_cantidad_despachada}}</strong>-->
                            <!--                        </td>-->
                            <!--<td>Total</td>
                            <td> <strong>C$ {{gran_total | formatMoney(2)}}</strong></td>-->
                        </tr>

                        </tfoot>
                    </table>
                </div>

            </div>
        </b-row>

        <b-card-footer class="content-box-footer text-lg-right text-center">
            <router-link :to="{ name: 'inventario-salidas' }" >
                <b-button type="button" variant="secondary" class="mx-1">Cancelar</b-button>
            </router-link>
            <b-button
                    :disabled="btnAction != 'Actualizar' ? true : false"
                    @click="actualizar"
                    variant="primary"
                    type="button"
                    class="mx-1"
            >{{ btnAction }}
            </b-button>
        </b-card-footer>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>

    </b-card>
</template>

<script type="text/ecmascript-6">
    import producto from "../../../api/Inventario/productos";
    import bodega from "../../../api/Inventario/bodegas";
    import tipo from "../../../api/Inventario/tipos_salidas.js";
    import proveedor from "../../../api/Inventario/proveedores";
    import salida from "../../../api/Inventario/salidas";
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
                loading: true,
                url: loadingImage,   //It is important to import the loading image then use here
                es: es,
                format: "dd-MM-yyyy",
                bodegas: [],
                proveedores: [],
                tipos_salida: [],
                productos: [],


                detalleForm: {
                    productox: "",
                    cantidad_saliente: 1,
                    precio_unitario: 0,
                    subtotal: 0,
                    total: 0,
                },

                form: {
                    codigo_salida: "",
                    descripcion_salida: "",
                    fecha_salida: moment(new Date()).format("YYYY-MM-DD"),
                    salida_tipo: "",
                    salida_proveedor: "",
                    salida_bodega: "",
                    salida_productos: []
                },
                btnAction: "Actualizar",
                errorMessages: []
            };
        },
        computed: {

            total_cantidad_saliente() {
                return this.form.salida_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_saliente))
                }, 0)
            },

            total_subt() {
                return this.form.salida_productos.reduce((carry, item) => {
                        return (carry + Number(item.precio_unitario).toFixed(2));
                    }
                    , 0)
            },
            gran_total() {
                return this.form.salida_productos.reduce((carry, item) => {
                        return (carry + Number(item.total).toFixed(2));
                    }
                    , 0)
            },
        },
        methods: {
            nueva() {
                var self = this;
                self.loading = true;
                salida.nueva({}, data => {
                        self.tipos_salida = data.tipos_salida;
                        //self.proveedores = data.proveedores;
                        //self.form.proveedor = self.proveedores[0];

                        self.bodegas = data.bodegas;

                        self.productos = [];
                        if (self.form.salida_bodega) {
                            self.productos = [];
                            self.form.salida_bodega.productos_bodega.forEach((bodega_producto, key) => {
                                self.productos.push({
                                    codigo_sistema: bodega_producto.producto.codigo_sistema,
                                    costo_promedio: Number(bodega_producto.producto.costo_promedio),
                                    descripcion: bodega_producto.producto.descripcion,
                                    id_producto: bodega_producto.producto.id_producto,
                                    id_bodega_producto: self.form.salida_bodega.productos_bodega[key].id_bodega_producto,
                                    nombre_comercial: bodega_producto.producto.nombre_comercial,
                                    cantidad_disponible: Number(self.form.salida_bodega.productos_bodega[key].cantidad),
                                    unidad_medida: bodega_producto.producto.unidad_medida,
                                    text: bodega_producto.producto.text,
                                    no_documento : bodega_producto.producto.no_documento
                                });
                            });
                        }

                        self.loading = false;

                    },
                    err => {
                        console.log(err);
                    })

            },
            obtenerSalida() {
                var self = this;
                self.loading = true;
                salida.obtenerSalida({
                    id_salida: this.$route.params.id_salida
                }, data => {
                    self.form = data.salida;
                    self.proveedores = data.proveedores;
                    self.bodegas = data.bodegas;
                    self.tipos_salida = data.tipos_salida;
                    self.loading = false;
                });

                if (self.form.salida_bodega) {
                    self.productos = [];
                    self.form.salida_bodega.productos_bodega.forEach((bodega_producto, key) => {
                        self.productos.push({
                            codigo_sistema: bodega_producto.producto.codigo_sistema,
                            costo_promedio: Number(bodega_producto.producto.costo_promedio),
                            descripcion: bodega_producto.producto.descripcion,
                            id_producto: bodega_producto.producto.id_producto,
                            id_bodega_producto: self.form.salida_bodega.productos_bodega[key].id_bodega_producto,
                            nombre_comercial: bodega_producto.producto.nombre_comercial,
                            cantidad_disponible: Number(self.form.salida_bodega.productos_bodega[key].cantidad),
                            unidad_medida: bodega_producto.producto.unidad_medida,
                            text: bodega_producto.producto.text,
                            no_documento : bodega_producto.producto.no_documento
                        });
                    });
                }


            },
            sub_total(itemX) {
                itemX.subtotal = Number((Number(itemX.precio_unitario) * Number(itemX.cantidad_saliente))).toFixed(2);
                itemX.total = itemX.subtotal;
                if (!isNaN(itemX.total)) {
                    return itemX.total;
                } else return 0;
            },


            onDateSelect(date) {
                //console.log(date); //
                this.form.fecha_salida = moment(date).format("YYYY-MM-DD"); //
            },
            obtenerTodosProveedores() {
                var self = this;
                proveedor.obtenerTodosProveedores(
                    data => {
                        self.proveedores = data;
                    },
                    err => {
                        console.log(err);
                    }
                );
            },
            cargar_detalles_producto() {
                var self = this
                if (self.detalleForm.productox)
                    self.detalleForm.cantidad = 0;
                self.detalleForm.precio_unitario = self.detalleForm.productox.costo_promedio;
            },
            obtenerProductosBodega() {
                var self = this;
                self.productos = [];

                self.form.salida_bodega.productos_bodega.forEach((bodega_producto, key) => {
                    self.productos.push({
                        codigo_sistema: bodega_producto.producto.codigo_sistema,
                        costo_promedio: Number(bodega_producto.producto.costo_promedio),
                        descripcion: bodega_producto.producto.descripcion,
                        id_producto: bodega_producto.producto.id_producto,
                        id_bodega_producto: self.form.salida_bodega.productos_bodega[key].id_bodega_producto,
                        nombre_comercial: bodega_producto.producto.nombre_comercial,
                        cantidad_disponible: Number(self.form.salida_bodega.productos_bodega[key].cantidad),
                        unidad_medida: bodega_producto.producto.unidad_medida,
                        text: bodega_producto.producto.text,
                        no_documento : bodega_producto.producto.no_documento
                    });

                });

            },
            obtenerTodosTiposSalida() {
                var self = this;
                tipo.obtenerTodosTiposSalida(
                    data => {
                        self.tipos_salida = data;
                    },
                    err => {
                        console.log(err);
                    }
                );
            },
            agregarDetalle() {
                var self = this;
                if (self.detalleForm.productox) {
                    if (self.detalleForm.cantidad > 0 && self.detalleForm.precio_unitario > 0) {
                        let i = 0;
                        let keyx = 0;
                        if (self.form.salida_productos) {
                            self.form.salida_productos.forEach((productox, key) => {
                                if (self.detalleForm.productox.id_bodega_producto === productox.id_bodega_producto) {
                                    i++;
                                    keyx = key;
                                }
                            });
                        }
                        if (i === 0) {
                            self.form.salida_productos.unshift({
                                productox: self.detalleForm.productox,
                                id_bodega_producto: self.detalleForm.productox.id_bodega_producto,
                                descripcion_producto: self.detalleForm.productox.descripcion,
                                codigo_producto: self.detalleForm.productox.codigo_sistema,
                                unidad_medida: self.detalleForm.productox.unidad_medida,
                                no_documento: self.detalleForm.productox.no_documento,
                                text: self.detalleForm.productox.text,
                                cantidad_saliente: self.detalleForm.cantidad,
                                precio_unitario: self.detalleForm.precio_unitario,
                                subtotal: 0,
                                total: 0,
                            });

                        } else {
                            let nuevo_total = self.form.salida_productos[keyx].cantidad_saliente + self.detalleForm.cantidad;
                            if (nuevo_total <= self.form.salida_productos[keyx].bodega_producto.cantidad) {
                                self.form.salida_productos[keyx].cantidad_saliente = self.form.salida_productos[keyx].cantidad_saliente + self.detalleForm.cantidad;
                            } else {
                                self.form.salida_productos[keyx].cantidad_saliente = self.form.salida_productos[keyx].bodega_producto.cantidad;
                            }
                        }

                        self.detalleForm.productox = '';
                        self.detalleForm.cantidad = 0;
                        self.detalleForm.precio_unitario = 0;
                        self.detalleForm.subtotal = 0;
                        self.detalleForm.total = 0;

                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Los valores para cantidad y precio unitario deber ser mayores a cero.',
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                    }
                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Debe seleccionar un producto.',
                                variant: 'danger',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },

            /*agregarDetalle() {

                if(this.detalleForm.productox){
                    if(this.detalleForm.cantidad_saliente>0 && this.detalleForm.precio_unitario > 0){
                        this.form.salida_productos.push({
                            id_bodega_producto: 0,
                            id_producto: this.detalleForm.productox.id_producto,
                            codigo_producto: this.detalleForm.productox.codigo_sistema,
                            descripcion_producto: this.detalleForm.productox.descripcion,
                            unidad_medida: this.detalleForm.productox.unidad_medida.descripcion,
                            cantidad_saliente: this.detalleForm.cantidad_saliente,
                            precio_unitario: this.detalleForm.precio_unitario,
                            subtotal: 0,
                            total: 0,
                        });
                        this.detalleForm.productox='';
                        this.detalleForm.cantidad_saliente=0;
                        this.detalleForm.precio_unitario=0;
                        this.detalleForm.subtotal=0;
                        this.detalleForm.total=0;
                    }else{
                        alertify.warning("Los valores para cantidad_saliente y precio unitario deben ser mayores que cero",5);
                    }
                }else{
                    alertify.warning("Debe seleccionar un producto",5);
                }
            },*/
            eliminarLinea(item, index) {
                if (this.form.salida_productos.length >= 1) {
                    this.form.salida_productos.splice(index, 1);

                } else {
                    this.form.salida_productos = [];
                }
            },

            actualizar() {
                var self = this;
                self.btnAction = "Registrando, espere....";

                self.$swal.fire({
                    title: 'Esta seguro de guardar la salida?',
                    text: "Es posible actualizar la salida luego de guardar",
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
                            self.loading = true;
                            salida.guardar(
                                self.form,
                                data => {
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'InfoIcon',
                                                text: 'Salida actualizada correctamente.',
                                                variant: 'success',
                                                position: 'bottom-right'
                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                    this.$router.push({
                                        name: "inventario-salidas"
                                    });
                                },
                                err => {
                                    self.loading = false;
                                    self.errorMessages = err;
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'InfoIcon',
                                                text: 'Ha ocurrido un error.',
                                                variant: 'danger',
                                                position: 'bottom-right'
                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                    self.btnAction = "Actualizar";
                                }
                            );
                        }else {
                            this.loading = false;
                            self.btnAction = "Actualizar";
                        }
                });


            }
        },
        mounted() {
            //this.obtenerTodasBodegasProductos();
            //this.obtenerTodosProveedores();
            //this.obtenerTodosTiposSalida();
            this.obtenerSalida();
            this.nueva();

        }
    };
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';

    .btn-agregar {
        margin-top: 33px;
    }
</style>
