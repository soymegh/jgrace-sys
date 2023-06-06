<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Bodega Origen</label>
                    <v-select
                            label="descripcion"
                            v-model="form.bodega_saliente"
                            :options="bodegas"
                            v-on:input="obtenerProductosBodega()"
                            placeholder="Selecciona una bodega de origen"
                    ></v-select>
                  <b-alert variant="danger" show>
                    <ul class="error-messages">
                      <li :key="message" v-for="message in errorMessages.bodega_saliente"
                          v-text="message"></li>
                    </ul>
                  </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Bodega Destino</label>
                    <v-select
                            label="descripcion"
                            v-model="form.bodega_entrante"
                            :options="bodegas_entrantes"
                    ></v-select>
                  <b-alert variant="danger" show>
                    <ul class="error-messages">
                      <li :key="message" v-for="message in errorMessages.bodega_entrante"
                          v-text="message"></li>
                    </ul>
                  </b-alert>

                </div>
            </div>


<!--            <div class="col-sm-3">-->
<!--                <div class="form-group">-->
<!--                    <label for="">Condición productos:</label>-->
<!--                    <select @change="cambiarCondicion" class="form-control" v-model.number="form.condicion">-->
<!--                        <option value="1">Nuevos</option>-->
<!--                        <option value="8">Recuperados</option>-->
<!--                        <option value="6">Obsoletos</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Fecha salida</label>
                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            local="es"
                            @selected="onDateSelect"
                            selected-variant="primary"
                            class="mb-0"
                            placeholder="f.salida"
                            v-model="form.fecha_salida"
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

            <div class="col-sm-12">
                <div class="form-group">
                    <label for>Observaciones Traslado</label>
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

        <div v-if="!form.bodega_saliente">
          <b-alert variant="success" show class="mb-2 mt-2">
            <div class="alert-body">
              <span>Se requiere que seleccione una bodega saliente y una bodega entrante para continuar.</span>
            </div>
          </b-alert>

        </div>

        <b-alert variant="success" class="mb-2 mt-2">
            <span><strong>Detalle de la salida</strong></span>
        </b-alert>
        <div class="row">
            <div class="col-sm-7">
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
                    <!---->
                  <b-alert variant="danger" show></b-alert>
                    <ul class="error-messages">
                        <li
                                :key="message"
                                v-for="message in errorMessages.productox"
                                v-text="message"
                        ></li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label for>Cantidad</label>
                    <input v-if="form.condicion === 1"
                           @change="detalleForm.cantidad = Math.max(Math.min(Math.round(detalleForm.cantidad), detalleForm.productox.cantidad_disponible), 0)"
                           class="form-control" ref="cantidad" type="number"
                           v-model.number="detalleForm.cantidad">
                    <input v-if="form.condicion === 8"
                           @change="detalleForm.cantidad = Math.max(Math.min(Math.round(detalleForm.cantidad), detalleForm.productox.cantidad_disponible_recuperadas), 0)"
                           class="form-control" ref="cantidad" type="number"
                           v-model.number="detalleForm.cantidad">
                    <input v-if="form.condicion === 6"
                           @change="detalleForm.cantidad = Math.max(Math.min(Math.round(detalleForm.cantidad), detalleForm.productox.cantidad_disponible_obsoletas), 0)"
                           class="form-control" ref="cantidad" type="number"
                           v-model.number="detalleForm.cantidad">
                  <b-alert variant="danger" show></b-alert>

                    <ul class="error-messages">
                        <li
                                :key="message"
                                v-for="message in errorMessages.cantidadx"
                                v-text="message"
                        ></li>
                    </ul>
                </div>
            </div>

            <!--  <div class="col-sm-3">
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

        </div>

        <div class="row">
            <div class="col-sm-12">
              <b-alert variant="danger" show></b-alert>
                <ul class="error-messages">
                    <li
                            :key="message"
                            v-for="message in errorMessages.detalleProductos"
                            v-text="message"
                    ></li>
                </ul>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Producto</th>
<!--                        <th>Condición</th>-->
                        <th>Cantidad</th>
                        <!--<th >Costo Unitario</th>

                        <th >SubTotal</th>-->

                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in form.detalleProductos">
                        <tr>
                            <td style="width: 2%">
                                <b-button variant="danger" @click="eliminarLinea(item, index)">
                                    <feather-icon icon="TrashIcon"></feather-icon>
                                </b-button>
                            </td>
                            <td>
                                <input class="form-control" disabled v-model="item.productox.descripcion">
                              <b-alert variant="danger" show></b-alert>
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages[`detalleProductos.${index}.productox.id_producto`]"
                                            v-text="message">
                                    </li>
                                </ul>
                            </td>

<!--                            <td>-->
<!--                                <select disabled class="form-control" v-model.number="item.condicion">-->
<!--                                    <option value="1">Nuevo</option>-->
<!--                                    <option value="8">Recuperado</option>-->
<!--                                    <option value="6">Obsoleto</option>-->
<!--                                </select>-->
<!--                            </td>-->

                            <td>
                                <input v-if="item.condicion === 1"
                                       @change="item.cantidad = Math.max(Math.min(Math.round(item.cantidad), item.productox.cantidad_disponible), 1)"
                                       class="form-control" type="number" v-model.number="item.cantidad">
                                <input v-if="item.condicion === 8"
                                       @change="item.cantidad = Math.max(Math.min(Math.round(item.cantidad), item.productox.cantidad_disponible_recuperadas), 1)"
                                       class="form-control" type="number" v-model.number="item.cantidad">
                                <input v-if="item.condicion === 6"
                                       @change="item.cantidad = Math.max(Math.min(Math.round(item.cantidad), item.productox.cantidad_disponible_obsoletas), 1)"
                                       class="form-control" type="number" v-model.number="item.cantidad">

                              <b-alert variant="danger" show></b-alert>
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages[`detalleProductos.${index}.cantidad`]"
                                            v-text="message">
                                    </li>
                                </ul>
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
                             </td>

                             <td>
                               <strong>C$ {{sub_total(item)| formatMoney(2)}}</strong>
                             </td>
         -->

                        </tr>
                        <tr></tr>
                    </template>
                    </tbody>
                    <tfoot>

                    <!--  <tr>
                       <td colspan="3"></td>
                      <td>Subtotal</td>
                       <td> <strong>C$ {{total_subt | formatMoney(2)}}</strong></td>
                     </tr>-->
                    <tr>
                        <td class="item-footer" colspan="2"> Total Unidades</td>
                        <td class="item-footer">
                            <strong>{{total_cantidad}}</strong>
                        </td>
                        <!--  <td>Total</td>
                          <td> <strong>C$ {{gran_total | formatMoney(2)}}</strong></td>-->
                    </tr>

                    </tfoot>
                </table>
            </div>
        </div>

        <b-card-footer class="content-box-footer text-lg-right text-center">
            <router-link  :to="{ name: 'inventario-salidas' }">
                <b-button type="button" variant="secondary" class="mx-1">Cancelar</b-button>
            </router-link>
            <b-button
                    :disabled="btnAction != 'Registrar' ? true : false"
                    @click="registrarTraslado"
                    variant="primary"
                    type="button" class="mx-1"
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
                msg: 'Cargando módulos...espere un momento',
                url: loadingImage,
                es: es,
                format: "dd-MM-yyyy",
                bodegas: [],
                bodegas_entrantes: [],
                productos: [],

                detalleForm: {
                    productox: "",
                    cantidad: 1,
                    precio_unitario: 0,
                    subtotal: 0,
                    total: 0
                },

                form: {
                    numero_documento: '',
                    codigo_salida: "",
                    descripcion_salida: "",
                    fecha_salida: moment(new Date()).format("YYYY-MM-DD"),
                    tipo_salida: "",
                    bodega_entrante: "",
                    bodega_saliente: "",
                    detalleProductos: [],
                    condicion: 1,
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
            cambiarCondicion() {

                let self = this;
                self.form.detalleProductos = [];
                self.detalleForm.productox = '';
                self.detalleForm.cantidad = 0;

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
                    self.detalleForm.cantidad = 0;
                self.detalleForm.precio_unitario = self.detalleForm.productox.costo_promedio;
                self.$refs.cantidad.focus();
            },
            onDateSelect(date) {
                this.form.fecha_salida = moment(date).format("YYYY-MM-DD"); //
            },

            obtenerTodasBodegasProductos() {
                var self = this;
                self.loading = true;
                bodega.obtenerTodasBodegasProductos(
                    data => {
                        self.bodegas = data.bodegas;
                        self.bodegas_entrantes = data.bodegas_entrantes;
                        self.form.bodega_saliente = self.bodegas[0];

                        self.productos = [];
                        self.form.bodega_saliente.productos_bodega.forEach((bodega_producto, key) => {
                            self.productos.push({
                                codigo_sistema: bodega_producto.producto.codigo_sistema,
                                costo_promedio: Number(bodega_producto.producto.costo_promedio),
                                descripcion: bodega_producto.producto.descripcion,
                                id_producto: bodega_producto.producto.id_producto,
                                id_bodega_producto: self.form.bodega_saliente.productos_bodega[key].id_bodega_producto,
                                nombre_comercial: bodega_producto.producto.nombre_comercial,
                                cantidad_disponible: Number(self.form.bodega_saliente.productos_bodega[key].cantidad),
                                cantidad_disponible_obsoletas: Number(self.form.bodega_saliente.productos_bodega[key].cantidad_obsoletas),
                                cantidad_disponible_recuperadas: Number(self.form.bodega_saliente.productos_bodega[key].cantidad_recuperadas),
                                unidad_medida: bodega_producto.producto.unidad_medida,
                                nombre_full: bodega_producto.producto.descripcion + ' - ' + bodega_producto.producto.no_documento,
                            });
                        });
                        self.loading = false;
                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error.' + err,
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        console.log(err);
                        self.loading = false;
                    }
                );
            },

            obtenerProductosBodega() {
                var self = this;
                self.loading = true;
                self.form.detalleProductos = [];
                self.productos = [];
                self.form.bodega_saliente.productos_bodega.forEach((bodega_producto, key) => {
                    self.productos.push({
                        codigo_sistema: bodega_producto.producto.codigo_sistema,
                        costo_promedio: Number(bodega_producto.producto.costo_promedio),
                        descripcion: bodega_producto.producto.descripcion,
                        id_producto: bodega_producto.producto.id_producto,
                        id_bodega_producto: self.form.bodega_saliente.productos_bodega[key].id_bodega_producto,
                        nombre_comercial: bodega_producto.producto.nombre_comercial,
                        cantidad_disponible: Number(self.form.bodega_saliente.productos_bodega[key].cantidad),
                        cantidad_disponible_obsoletas: Number(self.form.bodega_saliente.productos_bodega[key].cantidad_obsoletas),
                        cantidad_disponible_recuperadas: Number(self.form.bodega_saliente.productos_bodega[key].cantidad_recuperadas),
                        unidad_medida: bodega_producto.producto.unidad_medida,
                        nombre_full: bodega_producto.producto.descripcion + ' - ' + bodega_producto.producto.no_documento,
                    });
                });
                self.loading = false;
            },
            agregarDetalle() {
                var self = this;
                if (self.detalleForm.productox) {
                    if (self.detalleForm.cantidad > 0 /*&& self.detalleForm.precio_unitario > 0*/) {
                        let i = 0;
                        let keyx = 0;
                        if (self.form.detalleProductos) {
                            self.form.detalleProductos.forEach((productox, key) => {
                                if (self.detalleForm.productox.id_bodega_producto === productox.productox.id_bodega_producto &&
                                    (self.form.condicion === productox.condicion)) {
                                    i++;
                                    keyx = key;
                                }
                            });
                        }
                        if (i === 0) {
                            self.form.detalleProductos.push({
                                productox: self.detalleForm.productox,
                                cantidad: self.detalleForm.cantidad,
                                condicion: self.form.condicion,
                                precio_unitario: self.detalleForm.precio_unitario,
                                subtotal: 0,
                                total: 0,
                            });

                        } else {
                            let nuevo_total = self.form.detalleProductos[keyx].cantidad + self.detalleForm.cantidad;

                            let cantidad_disponiblex = self.form.detalleProductos[keyx].cantidad_disponible;
                            if (self.form.condicion === 8) {
                                cantidad_disponiblex = self.form.detalleProductos[keyx].cantidad_disponible_recuperadas;
                            }

                            if (self.form.condicion === 6) {
                                cantidad_disponiblex = self.form.detalleProductos[keyx].cantidad_disponible_obsoletas;
                            }

                            if (nuevo_total <= cantidad_disponiblex) {
                                self.form.detalleProductos[keyx].cantidad = self.form.detalleProductos[keyx].cantidad + self.detalleForm.cantidad;
                            } else {
                                //  self.form.detalleProductos[keyx].cantidad =self.form.detalleProductos[keyx].productox.cantidad_disponible;
                                self.form.detalleProductos[keyx].cantidad = self.form.detalleProductos[keyx].productox.cantidad_disponible;
                                if (self.form.condicion === 8) {
                                    self.form.detalleProductos[keyx].cantidad = self.form.detalleProductos[keyx].productox.cantidad_disponible_recuperadas;
                                }
                                if (self.form.condicion === 6) {
                                    self.form.detalleProductos[keyx].cantidad = self.form.detalleProductos[keyx].productox.cantidad_disponible_obsoletas;
                                }
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
                                text: 'Debe seleccionar al menos un producto.',
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

            registrarTraslado() {
                var self = this;
                //self.loading=true;
                if (self.form.bodega_entrante && self.form.bodega_saliente && self.form.bodega_entrante.id_bodega !== self.form.bodega_saliente.id_bodega) {
                    self.btnAction = "Registrando, espere....";

                    self.$swal.fire({
                        title: 'Esta seguro de guardar y emitir la entrada?',
                        text: "Esta acción no se puede deshacer",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, confirmar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.value) {
                            salida.registrarTraslado(
                                self.form,
                                data => {
                                    this.$router.push({
                                        name: "inventario-salidas"
                                    });
                                    //alertify.success("Salida por traslado registrada exitosamente",5);
                                    // alertify.success("Entrada por traslado registrada y en espera de confirmación de recibido",5);
                                    self.loading = false;
                                    this.$swal.fire(
                                        'Salida por traslado registrada exitosamente!',
                                        'La Salida fue Registrada correctamente.',
                                        'success'
                                    )
                                },
                                err => {
                                    self.loading = false;
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'InfoIcon',
                                                text: 'Ha ocurrido un error, verifique sus datos.' + err,
                                                variant: 'danger',
                                                position: 'bottom-right'
                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                    self.errorMessages = err;
                                    self.btnAction = "Registrar";
                                }
                            );
                        } else {
                            self.loading = false;
                            this.$swal.fire(
                                'No se han guardado los cambios!',
                                '',
                                'warning'
                            )
                            self.btnAction = "Registrar";
                        }
                    })
                } else {
                    self.loading = false;
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Debe seleccionar bodegas distintas.',
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
            this.obtenerTodasBodegasProductos();
        }
    };
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    .btn-agregar {
        margin-top: 33px;
    }
</style>
