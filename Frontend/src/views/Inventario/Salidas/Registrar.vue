<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Tipo salida</label>
                    <v-select
                            :options="tipos_salida"
                            label="descripcion"
                            v-model="form.tipo_salida"
                            placeholder="Selecciona un tipo de salida"
                    >
                        <template slot="no-options">No se encontraron registros.</template>
                    </v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.tipo_salida"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <!--  <div class="col-sm-4">
                <div class="form-group">
                  <label for>Código salida <small>(Automático)</small></label>
                  <input class="form-control" type="text" v-model="form.codigo_salida">
                  <ul class="error-messages">
                    <li
                            :key="message"
                            v-for="message in errorMessages.codigo_salida"
                            v-text="message"
                    ></li>
                  </ul>
                </div>
              </div>-->

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Bodega</label>
                    <v-select
                            label="descripcion"
                            v-model="form.bodega"
                            :options="bodegas"
                            v-on:input="obtenerProductosBodega()"
                            placeholder="Selecciona una bodega"
                    >
                        <template slot="no-options">No se encontraron registros</template>
                    </v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.bodega" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <!--<div class="col-sm-4">
                <div class="form-group">
                    <label for>Proveedor</label>
                    <v-select
                            :options="proveedores"
                            label="nombre_comercial"
                            v-model="form.proveedor"
                    ></v-select>
                    <ul class="error-messages">
                        <li :key="message" v-for="message in errorMessages.proveedor" v-text="message"></li>
                    </ul>
                </div>
            </div>-->

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Fecha salida</label>

                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            local="es"
                            @selected="onDateSelect"
                            selected-variant="primary"
                            class="mb-0"
                            placeholder="f.salida"
                            v-model="fechax"
                    >

                    </b-form-datepicker>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    v-for="message in errorMessages.fecha_salida"
                                    :key="message"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Número documento de salida</label>
                    <input type="text" class="form-control" v-model="form.numero_documento">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.numero_documento"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-9">
                <div class="form-group">
                    <label for>Observaciones salida</label>
                    <input type="text" class="form-control" v-model="form.descripcion_salida">
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

        <div v-if="!form.bodega">
            <b-alert variant="success" show>
                <div class="alert alert-info">
                    <span>Se requiere que seleccione una bodega para continuar.</span>
                </div>
            </b-alert>

            <hr>
        </div>

        <b-alert variant="success" show class="mt-2 mb-2">
            <div class="alert-body">
                <span><strong>Detalle de la salida</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for>Producto</label>
                    <v-select v-model="detalleForm.productox" :options="productos"
                              track-by="id_producto"
                              placeholder="Selecciona un producto"
                              label="nombre_full"
                              ref="producto"
                              v-on:keydown.enter.native="cargar_detalles_producto()"
                              :filterable="true"
                    >
                        <template slot="no-options">
                            No se han encontrado registros
                        </template>
                    </v-select>
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
                    <input @change="detalleForm.cantidad = Math.max(Math.min(detalleForm.cantidad, detalleForm.productox.cantidad_disponible), 0)"
                           class="form-control" ref="cantidad" type="number" @keydown.enter="cambiarFocoCantidad"
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


            <!--<div class="col-sm-2">
              <div class="form-group">
                <label for>Costo Promedio</label>
                <div class="input-group">
                  <div class="input-group-addon">C$
                  </div>
                  <input class="form-control" readonly type="number" v-model.number="detalleForm.precio_unitario">
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
                                v-for="message in errorMessages.detalleProductos"
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
                            <th>Cantidad</th>
                            <!--  <th >Costo Unitario</th>

                               <th >SubTotal</th>-->

                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(item, index) in form.detalleProductos">
                            <tr>
                                <td style="width: 2%">
                                    <b-button variant="danger" @click="eliminarLinea(item, index)" v-b-tooltip.hover.top="'Eliminar fila'">
                                        <feather-icon icon="TrashIcon"></feather-icon>
                                    </b-button>
                                </td>
                                <td>
                                    <input class="form-control" disabled v-model="item.productox.nombre_full">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`detalleProductos.${index}.productox.id_producto`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>

                                </td>


                                <td>
                                    <input @change="item.cantidad = Math.max(Math.min(Math.round(item.cantidad), item.productox.cantidad_disponible), 1)"
                                           class="form-control" type="number" v-model.number="item.cantidad">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`detalleProductos.${index}.cantidad`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>

                                </td>


                                <!-- <td>
                                   <input class="form-control" readonly type="number" v-model.number="item.precio_unitario">
                                   <ul class="error-messages">
                                     <li
                                             :key="message"
                                             v-for="message in errorMessages[`detalleProductos.${index}.precio_unitario`]"
                                             v-text="message">
                                     </li>
                                   </ul>
                                 </td>-->

                                <!--<td>
                                     <strong>C$ {{sub_total(item)| formatMoney(2)}}</strong>
                                   </td>-->


                            </tr>
                            <tr></tr>
                        </template>
                        </tbody>
                        <tfoot>

                        <tr>
                            <td colspan="3"></td>
                            <!--<td>Subtotal</td>
                            <td> <strong>C$ {{total_subt | formatMoney(2)}}</strong></td>-->
                        </tr>
                        <tr>
                            <td class="item-footer" colspan="2"> Total Unidades</td>
                            <td class="item-footer">
                                <strong>{{total_cantidad}}</strong>
                            </td>
                            <!-- <td>Total</td>
                             <td> <strong>C$ {{gran_total | formatMoney(2)}}</strong></td>-->
                        </tr>

                        </tfoot>
                    </table>
                </div>

            </div>
        </b-row>

        <b-card-footer class="content-box-footer text-lg-right text-center">
            <router-link  :to="{ name: 'inventario-salidas' }">
                <b-button type="button" variant="secondary" class="mx-1">Cancelar</b-button>
            </router-link>
            <b-button
                    :disabled="btnAction != 'Registrar' ? true : false"
                    @click="registrar"
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
    import bodega from "../../../api/Inventario/bodegas";
    import tipo from "../../../api/Inventario/tipos_salidas";

    import proveedor from "../../../api/Inventario/proveedores";
    import salida from "../../../api/Inventario/salidas";
    //import Datepicker from "vuejs-datepicker";
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
                es: es,
                format: "dd-MM-yyyy",
                bodegas: [],
                proveedores: [],
                tipos_salida: [],
                productos: [],
                fechax: new Date(),

                detalleForm: {
                    productox: "",
                    cantidad: 1,
                    precio_unitario: 0,
                    subtotal: 0,
                    total: 0,
                },

                form: {
                    codigo_salida: "",
                    descripcion_salida: "",
                    fecha_salida: moment(new Date()).format("YYYY-MM-DD"),
                    tipo_salida: "",
                    proveedor: "",
                    bodega: "",
                    numero_documento: '',
                    detalleProductos: []
                },
                btnAction: "Registrar",
                errorMessages: []
            };
        },
        computed: {

            total_cantidad() {
                return this.form.detalleProductos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad))
                }, 0)
            },
            total_subt() {
                return this.form.detalleProductos.reduce((carry, item) => {
                        return (carry + Number(item.subtotal.toFixed(2)));
                    }
                    , 0)
            },
            gran_total() {
                return this.form.detalleProductos.reduce((carry, item) => {
                        return (carry + Number(item.total.toFixed(2)));
                    }
                    , 0)
            },
        },
        methods: {
            cambiarFocoCantidad() {
                var self = this;
                self.$refs.agregar.focus()
            },
            sub_total(itemX) {
                itemX.subtotal = Number((Number(itemX.precio_unitario) * Number(itemX.cantidad)).toFixed(2));
                itemX.total = itemX.subtotal;
                if (!isNaN(itemX.total)) {
                    return itemX.total;
                } else return 0;
            },
            cargar_detalles_producto() {
                var self = this
                if (self.detalleForm.productox)
                    self.detalleForm.cantidad = 1;
                self.detalleForm.precio_unitario = self.detalleForm.productox.costo_promedio;
                self.$refs.cantidad.focus();
            },
            onDateSelect(date) {
                this.form.fecha_salida = moment(date).format("YYYY-MM-DD"); //
            },
            obtenerTodasBodegasProductos() {
                var self = this;
                bodega.obtenerTodasBodegasProductos(
                    data => {
                        self.bodegas = data.bodegas;
                        self.form.bodega = self.bodegas[0];

                        self.productos = [];
                        self.form.bodega.productos_bodega.forEach((bodega_producto, key) => {
                            self.productos.push({
                                codigo_sistema: bodega_producto.producto.codigo_sistema,
                                costo_promedio: Number(bodega_producto.producto.costo_promedio),
                                costo_promedio_me: Number(bodega_producto.producto.costo_promedio_me),
                                descripcion: bodega_producto.producto.descripcion,
                                id_producto: bodega_producto.producto.id_producto,
                                id_bodega_producto: self.form.bodega.productos_bodega[key].id_bodega_producto,
                                nombre_comercial: bodega_producto.producto.nombre_comercial,
                                cantidad_disponible: Number(self.form.bodega.productos_bodega[key].cantidad),
                                unidad_medida: bodega_producto.producto.unidad_medida,
                                nombre_full: bodega_producto.producto.descripcion + ' - ' + bodega_producto.no_documento,
                                no_documento: bodega_producto.no_documento
                            });
                        });

                    },
                    err => {
                        console.log(err);
                    }
                );
            },

            obtenerProductosBodega() {
                var self = this;
                self.form.detalleProductos = [];
                self.productos = [];

                self.form.bodega.productos_bodega.forEach((bodega_producto, key) => {

                    self.productos.push({
                        codigo_sistema: bodega_producto.producto.codigo_sistema,
                        costo_promedio: Number(bodega_producto.producto.costo_promedio),
                        costo_promedio_me: Number(bodega_producto.producto.costo_promedio_me),
                        descripcion: bodega_producto.producto.descripcion,
                        id_producto: bodega_producto.producto.id_producto,
                        id_bodega_producto: self.form.bodega.productos_bodega[key].id_bodega_producto,
                        nombre_comercial: bodega_producto.producto.nombre_comercial,
                        cantidad_disponible: Number(self.form.bodega.productos_bodega[key].cantidad),
                        unidad_medida: bodega_producto.producto.unidad_medida,
                        nombre_full: bodega_producto.producto.descripcion + ' - ' + bodega_producto.no_documento,
                        no_documento: bodega_producto.no_documento
                    });

                });

            },
            agregarDetalle() {
                var self = this;
                if (self.detalleForm.productox) {
                    if (self.detalleForm.cantidad > 0 /*&& self.detalleForm.precio_unitario > 0*/) {
                        let i = 0;
                        let keyx = 0;
                        if (self.form.detalleProductos) {
                            self.form.detalleProductos.forEach((productox, key) => {
                                if (self.detalleForm.productox.id_bodega_producto === productox.productox.id_bodega_producto) {
                                    i++;
                                    keyx = key;
                                }
                            });
                        }
                        if (i === 0) {
                            self.form.detalleProductos.push({
                                productox: self.detalleForm.productox,
                                cantidad: self.detalleForm.cantidad,
                                precio_unitario: self.detalleForm.precio_unitario,
                                subtotal: 0,
                                total: 0,
                            });

                        } else {
                            let nuevo_total = self.form.detalleProductos[keyx].cantidad + self.detalleForm.cantidad;
                            if (nuevo_total <= self.form.detalleProductos[keyx].cantidad_disponible) {
                                self.form.detalleProductos[keyx].cantidad = self.form.detalleProductos[keyx].cantidad + self.detalleForm.cantidad;
                            } else {
                                self.form.detalleProductos[keyx].cantidad = self.form.detalleProductos[keyx].productox.cantidad_disponible;
                            }
                        }

                        self.detalleForm.productox = '';
                        self.detalleForm.cantidad = 0;
                        self.detalleForm.precio_unitario = 0;
                        self.detalleForm.subtotal = 0;
                        self.detalleForm.total = 0;
                        this.$refs.producto.$refs.search.focus();

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
            eliminarLinea(item, index) {
                if (this.form.detalleProductos.length >= 1) {
                    this.form.detalleProductos.splice(index, 1);

                } else {
                    this.form.detalleProductos = [];
                }
            },

            registrar() {
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
                        salida.registrar(
                            self.form,
                            data => {
                                self.loading = false;
                                this.$swal.fire(
                                    'Salida Registrada!',
                                    'La salida fue actualizada correctamente.',
                                    'success'
                                );
                                this.$router.push({
                                    name: "inventario-salidas"
                                });
                            },
                            err => {
                                self.loading = false;
                                this.$swal.fire(
                                    'Ha ocurrido un error!',
                                    err.messages,
                                    'warning'
                                );
                                self.errorMessages = err;
                                self.btnAction = "Registrar";
                            }
                        );
                    }else{
                        this.loading = false;
                        self.btnAction = "Registrar";
                    }
                });

            },

            nueva() {
                var self = this;
                self.loading = true;
                salida.nueva({}, data => {
                        self.tipos_salida = data.tipos_salida;
                        self.form.tipo_salida = self.tipos_salida[0];
                        //self.proveedores = data.proveedores;
                        //self.form.proveedor = self.proveedores[0];

                        self.bodegas = data.bodegas;
                        self.form.bodega = self.bodegas[0];

                        self.productos = [];
                        self.form.bodega.productos_bodega.forEach((bodega_producto, key) => {
                            self.productos.push({
                                codigo_sistema: bodega_producto.producto.codigo_sistema,
                                costo_promedio: Number(bodega_producto.producto.costo_promedio),
                                costo_promedio_me: Number(bodega_producto.producto.costo_promedio_me),
                                descripcion: bodega_producto.producto.descripcion,
                                id_producto: bodega_producto.producto.id_producto,
                                id_bodega_producto: self.form.bodega.productos_bodega[key].id_bodega_producto,
                                nombre_comercial: bodega_producto.producto.nombre_comercial,
                                cantidad_disponible: Number(self.form.bodega.productos_bodega[key].cantidad),
                                unidad_medida: bodega_producto.producto.unidad_medida,
                                nombre_full: bodega_producto.producto.descripcion + ' - ' + bodega_producto.no_documento,
                                no_documento: bodega_producto.no_documento
                            });
                        });

                        self.loading = false;

                    },
                    err => {
                        console.log(err);
                    })

            },


        },
        mounted() {
            this.nueva()

            //this.obtenerTodasBodegasProductos();
            //this.obtenerTodosProveedores();
            //this.obtenerTodosTiposSalida();
        }
    };
</script>
<style lang="scss" >
    @import '@core/scss/vue/libs/vue-select.scss';
    .btn-agregar {
        margin-top: 33px;
    }
</style>
