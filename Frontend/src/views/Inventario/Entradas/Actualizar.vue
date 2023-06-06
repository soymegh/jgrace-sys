<template>
    <b-card>
        <validation-observer ref="entradaValidation">
            <b-row>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Tipo entrada</label>
                        <v-select
                                :options="tipos_entrada"
                                label="descripcion"
                                placeholder="Selecciona un tipo de entrada"
                                v-model="form.entrada_tipo"
                        >
                            <template slot="no-options">
                                No se han encontrado registros
                            </template>
                        </v-select>
                        <b-alert show variant="danger">
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

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Código entrada <small>(Automático)</small></label>
                        <input class="form-control" readonly type="text" v-model="form.codigo_entrada">
                        <b-alert show variant="danger">
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

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Bodega</label>
                        <v-select
                                :options="bodegas"
                                label="descripcion"
                                v-model="form.entrada_bodega"
                                v-on:input="actualizar_listado_productos()"
                        >
                            <template slot="no-options">
                                No se han encontrado registros
                            </template>
                        </v-select>
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.entrada_bodega"
                                    v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Proveedor</label>
                        <v-select
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :options="proveedores"
                                @input="mapProveedores"
                                label="nombre_comercial"
                                multiple
                                placeholder="Selecciona un proveedor"
                                v-model="prove"
                        >
                            <template slot="no-options">
                                No se han encontrado registros
                            </template>
                        </v-select>
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.proveedor" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Fecha entrada</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @input="onDateSelect()"
                                class="mb-0"
                                local="es"
                                placeholder="f.entrada"
                                selected-variant="primary"
                                v-model="form.fecha_entrada_x"
                        >

                        </b-form-datepicker>
                        <b-alert show variant="danger">
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
                <div class="col-sm-3">
                    <div class="form-group">
                        <validation-provider
                                #default="{ errors }"
                                name="numero_documento"
                                rules="required"
                        >
                            <label for>Número documento de entrada</label>
                            <b-form-input :class="errors.length > 0 ? 'is-invalid':null" class="form-control"
                                          type="text" v-model="form.numero_documento"></b-form-input>
                            <small class="text-danger">{{ errors[0] }}</small>
                        </validation-provider>

                        <b-alert show variant="danger">
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
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for>Observaciones entrada</label>
                        <input class="form-control" type="text" v-model="form.descripcion_entrada">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.descripcion_entrada"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </b-row>

            <div v-if="!form.entrada_bodega">
                <b-alert class="mt-1 mb-1" variant="danger">
                    <span>Se requiere que seleccione una bodega para continuar.</span>
                </b-alert>
                <hr>
            </div>

            <div class="alert alert-success">
                <b-alert class="mt-2 mb-2" show variant="success">
                    <div class="alert-body">
                        <span>Detalle de la entrada.</span>
                    </div>

                </b-alert>
            </div>

            <template v-if="errorMessages.entrada_productos">
                <div class="alert alert-danger">
                    <b-alert class="mt-2 mb-2" show variant="danger">
                        <div class="alert-body">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.entrada_productos"
                                        v-text="message"
                                ></li>
                            </ul>
                        </div>
                    </b-alert>
                </div>
            </template>

            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Producto</label>
                        <v-select
                                :get-option-label="labelproducto"
                                :options="productos"
                                label="descripcion"
                                placeholder="Selecciona un producto"
                                ref="producto"
                                v-model="detalleForm.productox"
                                v-on:keydown.enter.native="cargar_detalles_producto()"
                        >
                            <template slot="no-options">
                                No se han encontrado registros
                            </template>
                        </v-select>
                        <!---->
                        <b-alert class="mt-1 mb-1" variant="danger">
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
                        <input @keydown.enter="cambiarFocoCantidad" class="form-control" min="0" ref="cantidad"
                               type="number" v-model.number="detalleForm.cantidad_solicitada">
                        <b-alert class="mt-1 mb-1" variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.cantidad_solicitadax"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>


                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Precio Unitario</label>
                        <div class="input-group">
                            <input @keydown.enter="cambiarFocoPrecioUnitario" class="form-control" min="0"
                                   ref="precio_unitario" type="number"
                                   v-model.number="detalleForm.precio_unitario_me">
                        </div>
                        <b-alert class="mt-1 mb-1" variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.precio_unitario_me"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>


                    </div>
                </div>

                <div class="col-sm-3">
                    <label></label>
                    <div class="form-group">
                        <label for>&nbsp;</label>
                        <b-button @click="agregarDetalle" ref="agregar" variant="info">Agregar
                            Producto
                        </b-button>
                    </div>
                </div>

            </div>


            <div class="col-sm-12 table-responsive">
                <b-alert class="mt-1 mb-1" variant="danger">
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
                        <th></th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>SubTotal</th>
                        <th>Estado</th>

                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in form.entrada_productos">
                        <tr>
                            <td style="width: 2%">
                                <b-button @click="eliminarLinea(item, index)" v-b-tooltip.hover.top="'Eliminar línea!'"
                                          variant="danger">
                                    <feather-icon icon="TrashIcon"></feather-icon>
                                </b-button>
                            </td>
                            <td>
                                <!--                                <input class="form-control" disabled v-model="item.descripcion_producto">-->
                                {{"(" + item.codigo_producto + ") " + item.descripcion_producto}}
                                <b-alert class="mt-1 mb-1" variant="danger">
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
                                <validation-provider
                                        #default="{ errors }"
                                        name="cantidad_solicitada"
                                        rules="required"
                                >
                                    <b-form-input :state="errors.length > 0 ? false:null" class="form-control"
                                                  min="0"
                                                  type="number"
                                                  v-model.number="item.cantidad_solicitada"></b-form-input>

                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>

                                <b-alert class="mt-1 mb-1" variant="danger">
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
                                <validation-provider
                                        #default="{ errors }"
                                        name="precio_unitario_me"
                                        rules="required"
                                >
                                    <b-form-input :state="errors.length > 0 ? false:null" class="form-control"
                                                  min="0"
                                                  type="number" v-model.number="item.precio_unitario_me"></b-form-input>
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>

                                <b-alert class="mt-1 mb-1" variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`entrada_productos.${index}.precio_unitario_me`]"
                                                v-text="message">
                                        </li>
                                    </ul>
                                </b-alert>

                            </td>

                            <template v-if="form.currency_id === 1">
                                <td>
                                    <strong>C$ {{sub_total(item)| formatMoney(2)}}</strong>
                                </td>
                            </template>
                            <template v-else>
                                <td>
                                    <strong>$ {{sub_total(item)| formatMoney(2)}}</strong>
                                </td>
                            </template>


                            <td class="text-center">
                                <div v-if="item.registrada">
                                    <feather-icon aria-hidden="true" icon="CheckIcon"
                                                  style="color: green" v-b-tooltip.hover.top="'Producto registrado'">
                                    </feather-icon>
                                    Guardado
                                </div>
                                <div v-else>
                                    <feather-icon @click="autosave(item)"
                                                  aria-hidden="true"
                                                  icon="SaveIcon" style="color:blue; cursor: pointer"
                                                  v-b-tooltip.hover.top="'Guardar producto manualmente'">

                                    </feather-icon>
                                    {{item.guardadoEnProgreso?'Guardando....':' Pendiente'}}
                                </div>
                            </td>


                        </tr>


                        <tr></tr>
                    </template>
                    </tbody>
                    <tfoot>
                    <template v-if="form.currency_id === 1">
                        <tr>
                            <td colspan="3"></td>
                            <td>Subtotal</td>
                            <td><strong>C$ {{total_subt | formatMoney(2)}}</strong></td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td colspan="3"></td>
                            <td>Subtotal</td>
                            <td><strong>$ {{total_subt | formatMoney(2)}}</strong></td>
                        </tr>
                    </template>

                    <template v-if="form.currency_id === 1">
                        <tr>
                            <td class="item-footer" colspan="2"> Total Unidades</td>
                            <td class="item-footer">
                                <strong>{{total_cantidad_solicitada}}</strong>
                            </td>
                            <td>Total</td>
                            <td><strong>C$ {{gran_total | formatMoney(2)}}</strong></td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td class="item-footer" colspan="2"> Total Unidades</td>
                            <td class="item-footer">
                                <strong>{{total_cantidad_solicitada}}</strong>
                            </td>
                            <td>Total</td>
                            <td><strong>$ {{gran_total | formatMoney(2)}}</strong></td>
                        </tr>
                    </template>
                    </tfoot>
                </table>


            </div>


            <b-card-footer class="text-lg-right text-center">
                <router-link :to="{ name: 'inventario-entradas' }">
                    <b-button class="mx-lg-1" type="button" variant="secondary">Cancelar</b-button>
                </router-link>
                <b-button
                        :disabled="btnActionDraft !== 'Guardar Borrador'"
                        @click="form.es_borrador=true;actualizar()"
                        class="mx-lg-1"
                        type="button"
                        variant="dark"
                >{{ btnActionDraft }}
                </b-button>
                <b-button
                        :disabled="btnAction !== 'Actualizar y Emitir'"
                        @click="form.es_borrador=false;actualizar()"
                        type="button"
                        variant="primary"
                >{{ btnAction }}
                </b-button>
            </b-card-footer>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </validation-observer>
    </b-card>
</template>

<script type="text/ecmascript-6">
  import {ValidationObserver, ValidationProvider} from 'vee-validate'
  import producto from "../../../api/Inventario/productos";
  import bodega from "../../../api/Inventario/bodegas";
  import tipo from "../../../api/Inventario/tipos_entradas";
  import proveedor from "../../../api/Inventario/proveedores";
  import entrada from "../../../api/Inventario/entradas";
  //import Datepicker from "vuejs-datepicker";
  import es from 'vuejs-datepicker/dist/locale/translations/es'
  import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
  import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
  import {required} from '@validations'
  import {
    BAlert,
    BButton,
    BCard,
    BCardFooter,
    BFormCheckbox,
    BFormCheckboxGroup,
    BFormDatepicker,
    BFormGroup,
    BFormInput,
    BPaginationNav,
    BRow,
    VBTooltip
  } from 'bootstrap-vue'

  import loadingImage from '../../../assets/images/loader/block50.gif'
  import vSelect from "vue-select";
  import tc from "../../../api/contabilidad/tasas-cambio";

  export default {
    components: {
      BCard,
      BCardFooter,
      BPaginationNav,
      BFormCheckbox,
      BFormGroup,
      BFormInput,
      vSelect,
      BRow,
      BButton,
      BFormCheckboxGroup,
      BFormDatepicker,
      BAlert,
      ValidationProvider,
      ValidationObserver,
    },
    directives: {
      'b-tooltip': VBTooltip,
    },
    data() {
      return {
        required,
        loading: false,
        registrandoBateria: false,
        total_pendientes: 0,
        url: loadingImage,   //It is important to import the loading image then use here
        es: es,
        format: "dd-MM-yyyy",
        bodegas: [],
        //
        proveedores: [],
        tipos_entrada: [],
        productos: [],
        consolidadoProductos: [],
        prove: '',

        detalleForm: {
          productox: "",
          cantidad_solicitada: 1,
          precio_unitario: 0,
          subtotal: 0,
          total: 0,
        },

        form: {
          es_borrador: true,
          codigo_entrada: "",
          descripcion_entrada: "",
          fecha_entrada: moment(new Date()).format("YYYY-MM-DD"),
          fecha_entrada_x: new Date(),
          entrada_tipo: "",
          entrada_proveedor: '',
          entrada_bodega: "",
          entrada_productos: [],
          numero_documento: '',
          currency_id: 2,
          mapProveedor: 0,
          t_cambio: 0
        },
        btnAction: "Actualizar y Emitir",
        btnActionDraft: "Guardar Borrador",
        errorMessages: []
      };
    },
    computed: {

      total_cantidad_solicitada() {
        return this.form.entrada_productos.reduce((carry, item) => {
          return (carry + Number(item.cantidad_solicitada))
        }, 0)
      },

      total_subt() {
        return this.form.entrada_productos.reduce((carry, item) => {
          return (carry + Number(item.cantidad_solicitada) * Number(item.precio_unitario_me))
        }, 0)
      },
      gran_total() {
        return this.form.entrada_productos.reduce((carry, item) => {
          return (carry + Number(item.cantidad_solicitada) * Number(item.precio_unitario_me))
        }, 0)
      },

    },
    methods: {
      mapProveedores() {
        if (this.prove) {
          return this.form.mapProveedor = this.prove.map((proveedor) => [proveedor.id_proveedor]).join(",");
        }
        return this.form.mapProveedor = '';
      },
      cambiarFocoAgregar() {
        const self = this;
        this.$refs.producto.$refs.search.focus();
      },
      cambiarFocoCantidad() {
        const self = this;
        self.$refs.precio_unitario.focus();
      },
      cambiarFocoPrecioUnitario() {
        const self = this;
        self.$refs.agregar.focus()
      },
      obtenerEntrada() {
        const self = this;
        self.loading = true;
        entrada.obtenerEntrada({
          id_entrada: this.$route.params.id_entrada
        }, data => {

          data.entrada.entrada_productos.forEach((entrada, key) => {
            data.entrada.entrada_productos[key].registrada = true;
            data.entrada.entrada_productos[key].guardadoEnProgreso = false;
          });
          self.form = data.entrada;
          self.form.currency_id = data.currency_id;
          self.form.fecha_entrada = data.entrada.fecha_entrada;
          self.form.t_cambio = Number(data.t_cambio.tasa);

          self.agruparProductos();
          self.loading = false;
          if (self.form.estado !== 99) {
            self.loading = false;
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'InfoIcon',
                    text: 'La entrada ya no puede ser actualizada',
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
        itemX.subtotal = Number((Number(itemX.precio_unitario_me) * Number(itemX.cantidad_solicitada)).toFixed(2));
        itemX.total = itemX.subtotal;
        if (!isNaN(itemX.total)) {
          return itemX.total;
        } else return 0;
      },
      cargar_detalles_producto() {
        var self = this
        //  console.log(self.detalleForm.productox)
        if (self.detalleForm.productox) {
          self.detalleForm.precio_unitario = Number(self.detalleForm.productox.precio_compra);
          self.$refs.cantidad.focus();
        }
      },
      actualizar_listado_productos() {
        var self = this;
        self.form.entrada_productos.forEach((producto, key) => {
          producto.id_bodega_producto = 0;
        });
      },

      onDateSelect(date) {
        //console.log(date); //
        this.form.fecha_entrada = this.form.fecha_entrada_x; //
        this.obtenerTC();
      },
      obtenerTC() {
        // console.log('ejecutando obtener tc con fecha: ' + this.form.fecha_entrada_x);
        var self = this;
        tc.obtenerTC({
          fecha: self.form.fecha_entrada
        }, data => {
          if (data.tasa !== null) {
            self.form.t_cambio = Number(data.tasa);
          } else {
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'InfoIcon',
                    text: 'No se encuentran tasas de cambia para esta fecha.',
                    variant: 'warning',

                  }
                },
                {
                  position: 'bottom-right'
                });
            self.form.t_cambio = 0;
          }
          self.loading = false;
        }, err => {
          if (err.fecha) {
            self.form.t_cambio = 0;
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'InfoIcon',
                    text: 'No se encuentran tasas de cambia para esta fecha.',
                    variant: 'warning',

                  }
                },
                {
                  position: 'bottom-right'
                });
            self.loading = false;
          }
        })
      },
      obtenerTodosProveedores() {
        var self = this;
        self.loading = true;
        proveedor.obtenerTodosProveedores(
            data => {
              self.proveedores = data;
              self.loading = false;
            },
            err => {
              self.loading = false;
              this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'InfoIcon',
                      text: 'Ha ocurrido un error al cargar los datos.' + err,
                      variant: 'danger',
                      position: 'bottom-right'
                    }
                  },
                  {
                    position: 'bottom-right'
                  });
              console.log(err);
            }
        );
      },
      obtenerTodasBodegas() {
        var self = this;
        self.loading = true;
        bodega.obtenerTodasBodegas(
            data => {
              self.bodegas = data;
              self.loading = false;
            },
            err => {
              self.loading = false;
              this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'InfoIcon',
                      text: 'Ha ocurrido un error al cargar los datos.' + err,
                      variant: 'danger',
                      position: 'bottom-right'
                    }
                  },
                  {
                    position: 'bottom-right'
                  });
              console.log(err);
            }
        );
      },
      obtenerTodosTiposEntrada() {
        var self = this;
        self.loading = true;
        tipo.obtenerTodosTiposEntrada(
            data => {
              self.tipos_entrada = data;
              self.loading = false;
            },
            err => {
              self.loading = false;
              this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'InfoIcon',
                      text: 'Ha ocurrido un error al cargar los datos.' + err,
                      variant: 'danger',
                      position: 'bottom-right'
                    }
                  },
                  {
                    position: 'bottom-right'
                  });
              console.log(err);
            }
        );
      },
      obtenerProductosValidos() {
        var self = this;
        self.loading = true;
        producto.obtenerProductosValidos(
            data => {
              self.productos = data;
              self.loading = false;
            },
            err => {
              self.loading = false;
              this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'InfoIcon',
                      text: 'Ha ocurrido un error al cargar los datos.' + err,
                      variant: 'danger',
                      position: 'bottom-right'
                    }
                  },
                  {
                    position: 'bottom-right'
                  });
              console.log(err);
            }
        );
      },

      agregarDetalle() {
        let self = this;
        if (this.detalleForm.productox) {
          if (this.detalleForm.cantidad_solicitada > 0 && this.detalleForm.precio_unitario_me > 0) {

            let i = 0;
            let keyx = 0;
            if (self.form.entrada_productos) {
              self.form.entrada_productos.forEach((productox, key) => {
                //console.log('ID_PRODUCTO ',self.detalleForm.productox.id_producto);
                if (self.detalleForm.productox.id_producto === productox.bodega_producto.id_producto) {
                  i++;
                  keyx = key;
                }
              });
            }

            if (i === 0) {
              this.form.entrada_productos.unshift({
                id_bodega_producto: 0,
                id_producto: this.detalleForm.productox.id_producto,
                bodega_producto: this.detalleForm.productox,
                codigo_producto: this.detalleForm.productox.codigo_sistema,
                descripcion_producto: this.detalleForm.productox.descripcion,
                unidad_medida: this.detalleForm.productox.unidad_medida.descripcion,
                cantidad_solicitada: this.detalleForm.cantidad_solicitada,
                precio_unitario: this.detalleForm.precio_unitario,
                precio_unitario_me: this.detalleForm.precio_unitario_me,
                subtotal: 0,
                total: 0,
                registrada: false,
                guardadoEnProgreso: false,
                estado: 1,
                id_entrada_producto: null
              });

              this.detalleForm.productox = '';
              this.detalleForm.cantidad_solicitada = 0;
              this.detalleForm.precio_unitario = 0;
              this.detalleForm.subtotal = 0;
              this.detalleForm.total = 0;
              this.$refs.producto.$refs.search.focus();

              if (self.cantidad_sin_registrar() >= 3) {
                self.form.entrada_productos.forEach((productox, key) => {
                  if (!productox.registrada) {
                    self.autosave(productox)
                  }
                });
              }
              self.cantidad_sin_registrar();
              self.agruparProductos();

            } else {
              this.$swal.fire({
                title: 'El producto ya existe en el detalle, desea sumar la nueva cantidad al monto existente?',
                text: "También se sobreescribirá el costo unitario",
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
                  self.form.entrada_productos[keyx].cantidad_solicitada = Number(self.form.entrada_productos[keyx].cantidad_solicitada) + Number(self.detalleForm.cantidad_solicitada);
                  self.form.entrada_productos[keyx].precio_unitario = Number(self.detalleForm.precio_unitario) + Number(self.form.entrada_productos[keyx].precio_unitario);
                  this.detalleForm.productox = '';
                  this.detalleForm.cantidad_solicitada = 0;
                  this.detalleForm.precio_unitario = 0;
                  this.detalleForm.subtotal = 0;
                  this.detalleForm.total = 0;
                  this.$refs.producto.$refs.search.focus();
                }
              })
            }


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
        if (this.form.entrada_productos.length >= 1) {
          this.form.entrada_productos[index].estado = 0;
          this.form.entrada_productos[index].registrada = false;
          this.agruparProductos();
          // this.form.entrada_productos.splice(index, 1);

        } else {
          this.form.entrada_productos = [];
        }
      },

      actualizar() {
        this.$refs.entradaValidation.validate().then(success => {
          if (success) {
            // Se ejecuta si todos los campos requeridos en el formulario han sido llenados
            var self = this;
            self.btnAction = "Registrando, espere....";
            self.btnActionDraft = "Guardando, espere....";

            if (self.form.es_borrador === false) {
              if (self.form.t_cambio > 0) {
                self.$swal.fire({
                  title: 'Esta seguro de guardar y emitir la entrada?',
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
                    self.loading = true;
                    entrada.actualizar(
                        self.form,
                        data => {
                          self.loading = false;
                          this.$swal.fire(
                              'Entrada Emitida!',
                              'La entrada fue actualizada y emitida correctamente.',
                              'success'
                          )

                          this.$router.push({
                            name: "inventario-entradas"
                          });
                        },
                        err => {
                          self.loading = false;
                          self.errorMessages = err.result;
                          this.$toast({
                                component: ToastificationContent,
                                props: {
                                  title: 'Notificación',
                                  icon: 'InfoIcon',
                                  text: err.messages,
                                  variant: 'danger',
                                  position: 'bottom-right'
                                }
                              },
                              {
                                position: 'bottom-right'
                              });
                          self.btnAction = "Actualizar y Emitir";
                          self.btnActionDraft = "Guardar Borrador";
                        }
                    );
                  } else {
                    self.loading = false;
                    self.btnAction = "Actualizar y Emitir";
                    self.btnActionDraft = "Guardar Borrador";
                  }
                })
              } else {
                self.loading = false;
                self.btnAction = "Actualizar y Emitir";
                self.btnActionDraft = "Guardar Borrador";
                this.$toast({
                      component: ToastificationContent,
                      props: {
                        title: 'Notificación',
                        icon: 'InfoIcon',
                        text: 'Las tasas de cambio no se encuentran actualizadas, favor revise',
                        variant: 'danger',
                        position: 'bottom-right'
                      }
                    },
                    {
                      position: 'bottom-right'
                    });
              }
            } else {
              if (self.form.t_cambio > 0) {
              self.loading = true;
              entrada.actualizar(
                  self.form,
                  data => {
                    self.loading = false;
                    this.$swal.fire(
                        'Borrador registrado!',
                        'Registro actualizado correctamente.',
                        'success'
                    );
                    this.$router.push({
                      name: "inventario-entradas"
                    });
                  },
                  err => {
                    self.loading = false;
                    this.$toast({
                          component: ToastificationContent,
                          props: {
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: err.messages,
                            variant: 'danger',
                            position: 'bottom-right'
                          }
                        },
                        {
                          position: 'bottom-right'
                        });
                    self.errorMessages = err.result;
                    self.btnAction = "Actualizar y Emitir";
                    self.btnActionDraft = "Guardar Borrador";
                  }
              );
              }else{
                self.loading = false;
                self.btnAction = "Actualizar y Emitir";
                self.btnActionDraft = "Guardar Borrador";
                this.$toast({
                      component: ToastificationContent,
                      props: {
                        title: 'Notificación',
                        icon: 'InfoIcon',
                        text: 'Las tasas de cambio no se encuentran actualizadas, favor revise',
                        variant: 'danger',
                        position: 'bottom-right'
                      }
                    },
                    {
                      position: 'bottom-right'
                    });
              }
            }

          } else {
            //Ejecutar en caso de que existan campos sin llenar
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'InfoIcon',
                    text: 'Revise los campos faltantes',
                    variant: 'danger',
                    position: 'bottom-right'
                  }
                },
                {
                  position: 'bottom-right'
                });
            this.btnAction = "Actualizar y Emitir";
            this.btnActionDraft = "Guardar Borrador";
          }
        })

      },
      agruparProductos() {
        let self = this;

        var counts = self.form.entrada_productos.reduce((p, c) => {
          var name = c.descripcion_producto;

          if (!p.hasOwnProperty(name)) {
            p[name] = 0;
          }
          if (c.estado === 1) {
            p[name]++;
          }

          return p;
        }, {});

        self.consolidadoProductos = Object.keys(counts).map(k => {
          return {nombre_producto: k, cantidad: counts[k]};
        });

      },
      cantidad_sin_registrar() {
        var self = this;
        if (self.form.entrada_productos) {
          let i = 0;

          self.form.entrada_productos.forEach((productox, key) => {
            if (!productox.registrada) {
              i++;
            }
          });

          self.total_pendientes = i;

          return self.total_pendientes
        } else {
          return 0;
        }
      },
      labelproducto(item) {
        let cod_producto = item.codigo_sistema == null ? 'N/A' : item.codigo_sistema;
        // return `${item.descripcion} - ${cod_barra}`
        return `( ${cod_producto}) - ${item.descripcion}`
      },
      autosave(producto) {
        var self = this;
        if (!self.registrandoProducto && !producto.registrada /*&& bateria.estado===1*/) {
          //self.registrandoBateria = true;
          producto.guardadoEnProgreso = true;
          entrada.autosaveEntradaProducto(
              {
                id_entrada: self.form.id_entrada,
                id_entrada_producto: producto.id_entrada_producto,
                id_producto: producto.id_producto,
                codigo_sistema: producto.codigo_producto,
                descripcion: producto.descripcion_producto,
                unidad_medida: producto.unidad_medida,  //$producto['productox']['unidad_medida']['descripcion']
                precio_unitario: producto.precio_unitario,
                cantidad_solicitada: producto.cantidad_solicitada,
                estado: producto.estado,
                id_bodega: this.form.id_bodega,
              },
              data => {
                //self.form = data.orden;
                producto.registrada = true;
                producto.id_entrada_producto = data.id_entrada_producto;
                //self.registrandoBateria = false;
                producto.guardadoEnProgreso = false;
              },
              err => {
                producto.registrada = false;
                // console.log(err);
                //self.registrandoBateria = false;
                producto.guardadoEnProgreso = false;
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
              }
          );
        }
      },
      obtenerProveedores() {
        let self = this;
        self.loading = true;
        entrada.obtenerProveedores({
              id_entrada: this.$route.params.id_entrada
            }, data => {
              self.loading = false;
              self.prove = data.proveedores;
              if (this.prove) {
                return this.form.mapProveedor = this.prove.map((proveedor) => [proveedor.id_proveedor]).join(",");
              }
            },
            err => {
              self.loading = false;
              console.log(err);
            }
        )
      },
    },
    mounted() {
      this.obtenerTodasBodegas();
      this.obtenerTodosProveedores();
      this.obtenerTodosTiposEntrada();
      this.obtenerProductosValidos();
      this.obtenerEntrada();
      this.obtenerProveedores();
    }
  };
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';

    .btn-agregar {
        margin-top: 33px;
    }
</style>
