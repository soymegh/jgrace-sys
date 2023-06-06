<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Area Solicitante</label>
                    <v-select
                            label="descripcion"
                            v-model="form.conteo_area"
                            :options="areas"
                            disabled
                    >
                      <template slot="no-options">No se encontraron registros</template>
                    </v-select>
                  <b-alert variant="danger" show>
                    <ul class="error-messages">
                      <li :key="message" v-for="message in errorMessages.conteo_area"
                          v-text="message"></li>
                    </ul>
                  </b-alert>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label> Sucursal</label>
                    <div class="form-group">

                        <v-select :allow-empty="false" :options="sucursales"
                                     :searchable="true"
                                     label="descripcion"
                                     placeholder="Selecciona una sucursal"
                                     track-by="id_sucursal"
                                     v-model="form.conteo_sucursal"
                                     v-on:input="seleccionarSucursal"
                        >
                          <template slot="no-options"> No se encontraron registros</template>
                        </v-select>
                    </div>
                  <b-alert variant="danger" show>
                    <ul class="error-messages">
                      <li :key="message" v-for="message in errorMessages.conteo_sucursal"
                          v-text="message"></li>
                    </ul>
                  </b-alert>

                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Bodega</label>
                    <v-select
                            label="descripcion"
                            v-model="form.conteo_bodega"
                            :options="bodegas"
                            v-on:input="seleccionarBodega"
                            placeholder="Seleccione una bodega"
                    >
                      <template slot="no-options"> No se encontraron registros</template>
                    </v-select>
                  <b-alert variant="danger" show>
                    <ul class="error-messages">
                      <li :key="message" v-for="message in errorMessages.conteo_bodega"
                          v-text="message"></li>
                    </ul>
                  </b-alert>

                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Fecha Conteo</label>
                  <b-form-datepicker
                          :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                          local="es"
                          @selected="onDateSelect"
                          v-model="form.f_inventario"
                          selected-variant="primary"
                          class="mb-0"
                          placeholder="f. conteo"
                  >
                  </b-form-datepicker>
                  <b-alert variant="danger" show>
                    <ul class="error-messages">
                      <li
                              :key="message"
                              v-for="message in errorMessages.f_inventario"
                              v-text="message"
                      ></li>
                    </ul>
                  </b-alert>

                </div>
            </div>


            <div class="col-sm-4">
                    <label for="hour-inicio-input">Hora Inicio</label>
                    <b-input-group class='mb-1'>
                        <b-form-input id='hour-inicio-input' v-mask="'##:##'" v-model='form.hora_inicio' type='text' placeholder='HH:mm' />
                        <b-input-group-append>
                            <b-form-timepicker button-variant="outline-primary" size="sm" v-model='form.hora_inicio'  button-only right  locale='en' aria-controls='hour-inicio-input' />
                        </b-input-group-append>
                    </b-input-group>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.hora_inicio"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Hora Finalización</label>
                    <b-input-group class='mb-1'>
                        <b-form-input id='hour-fin-input' v-mask="'##:##'" v-model='form.hora_fin' type='text' placeholder='HH:mm' />
                        <b-input-group-append>
                            <b-form-timepicker button-variant="outline-primary" size="sm" v-model='form.hora_fin'  button-only right  locale='es' aria-controls='hour-fin-input' />
                        </b-input-group-append>
                    </b-input-group>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.hora_fin"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

        </b-row>

        <div v-if="!form.conteo_bodega">
            <b-alert variant="info" show class="mb-2 mt-2">
                <div class="alert-body">
                    <span>Se requiere que seleccione una bodega.</span>
                </div>
            </b-alert>
        </div>

        <b-alert variant="success" show class="mb-2 mt-2">
            <div class="alert-body">
                <span><strong>Detalle de conteo</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Producto</label>

                    <!--<typeahead style="width: 100%;" :initial="detalleForm.productox" :trim="80" :url="productosBusquedaURL" @input="seleccionarProducto"></typeahead>-->

                    <v-select :allow-empty="false" :options="productos"
                              :searchable="true"
                              label="text"
                              placeholder="Selecciona un producto"
                              ref="producto"
                              track-by="id_producto"
                              v-model="detalleForm.productox"
                              v-on:keydown.enter.native="seleccionarProducto"
                    >
                        <template slot="no-options">No se encontraron registros</template>
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

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Cantidad</label>
                    <input class="form-control" ref="cantidad" type="number" min="1"
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


            <div class="col-sm-4">
                <label for=""></label>
                <div class="form-group">
                    <label for>&nbsp;</label>
                    <b-button @click="agregarDetalle"  variant="info" ref="agregar">Agregar
                        Producto
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
                                v-for="message in errorMessages.conteo_productos"
                                v-text="message"
                        ></li>
                    </ul>
                </b-alert>

                <div class="table-responsive mt-3">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Cantidad</th>

                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(item, index) in form.conteo_productos">
                            <tr>
                                <td style="width: 2%">
                                    <b-button @click="eliminarLinea(item, index)" variant="danger">
                                        <feather-icon icon="TrashIcon"></feather-icon>
                                    </b-button>
                                </td>
                                <td>
                                    <input class="form-control" disabled v-model="item.productox.descripcion">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`conteo_productos.${index}.productox.id_producto`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>

                                </td>


                                <td>
                                    <input class="form-control" type="number" min="1"
                                           v-model.number="item.cantidad">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`conteo_productos.${index}.cantidad`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>

                                </td>

                            </tr>
                            <tr></tr>
                        </template>
                        </tbody>
                        <tfoot>


                        <tr>
                            <td class="item-footer" colspan="2"> Total Unidades</td>
                            <td class="item-footer">
                                <strong>{{total_cantidad}}</strong>
                            </td>
                        </tr>


                        </tfoot>
                    </table>
                </div>

            </div>
        </b-row>

        <b-card-footer class="content-box-footer text-lg-right text-center">
            <router-link  :to="{ name: 'inventario-conteo-fisico' }">
                <b-button type="button" variant="secondary" class="mx-lg-1">Cancelar</b-button>
            </router-link>
            <b-button
                    :disabled="btnActionDraft !== 'Guardar Borrador'"
                    @click="form.es_borrador=true;registrar()"
                    variant="dark"
                    type="button" class="mx-lg-1"
            >{{ btnActionDraft }}
            </b-button>
            <b-button
                    :disabled="btnAction !== 'Registrar'"
                    @click="form.es_borrador=false;registrar()"
                    variant="primary"
                    type="button" class="mx-lg-1"
            >{{ btnAction }}
            </b-button>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>

    </b-card>
</template>

<script type="text/ecmascript-6">
    import conteox from "../../../api/Inventario/conteo-fisico";
    import bodega from "../../../api/Inventario/bodegas";
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
        BFormTimepicker,
        BInputGroup,
        BFormInput,
        BInputGroupAppend,
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
            BFormTimepicker,
            BInputGroup,
            BFormInput,
            BInputGroupAppend,
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        /*components: {
          Datepicker
        },*/
        data() {
            return {
                loading: true,
                url:  loadingImage,
                es: es,
                format: "dd-MM-yyyy",
                areas: [],
                productos: [],
                productos_nuevos: [],
                productos_usados: [],
                sucursales: [],
                bodegas: [],
                fechax: new Date(),

                detalleForm: {
                    productox: '',
                    cantidad: 1,
                },

                form: {
                    hora_inicio: "",
                    hora_fin: "",
                    f_inventario: moment(new Date()).format("YYYY-MM-DD"),
                    conteo_sucursal: "",
                    conteo_bodega: "",
                    conteo_area: "",
                    conteo_productos: [],
                    es_borrador: false
                },
                btnAction: "Registrar",
                btnActionDraft: "Guardar Borrador",
                errorMessages: []
            };
        },
        computed: {

            total_cantidad() {
                return this.form.conteo_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad))
                }, 0)
            },
        },
        methods: {


            seleccionarBodega() {
                var self = this;
                //self.loading = true;
                self.productos = [];
                if (self.form.conteo_bodega) {
                    if (self.form.conteo_bodega.id_tipo_bodega === 3) {
                        self.productos_usados.forEach((producto, key) => {
                            self.productos.push(producto)
                        });
                    } else {
                        self.productos_nuevos.forEach((producto, key) => {
                            self.productos.push(producto)
                        });
                    }
                }
            },

            seleccionarSucursal() {

                var self = this;
                self.bodegas = [];
                self.form.conteo_bodega = [];

                if (self.form.conteo_sucursal.sucursal_bodegas_todas.length) {
                    self.bodegas = self.form.conteo_sucursal.sucursal_bodegas_todas;
                    self.form.conteo_bodega = self.bodegas[0];
                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'La sucursal seleccionada no tiene bodegas asignadas.',
                                variant: 'danger',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },

            seleccionarProducto() {
                var self = this
                if (self.detalleForm.productox){
                    self.detalleForm.cantidad = 1;
                    self.detalleForm.precio_info = 1;
                    self.$refs.cantidad.focus();
                }

            },

            onDateSelect(date) {
                //console.log(date); //
                this.form.f_inventario = moment(date).format("YYYY-MM-DD"); //
            },


            agregarDetalle() {
                let self = this;
                if (this.detalleForm.productox) {
                    if (this.detalleForm.cantidad >0) {


                        let i = 0;
                        let keyx = 0;
                        if (self.form.conteo_productos) {
                            self.form.conteo_productos.forEach((productox, key) => {
                                //console.log('ID_PRODUCTO ',self.detalleForm.productox.id_producto);
                                if (self.detalleForm.productox.id_producto === productox.productox.id_producto) {
                                    i++;
                                    keyx = key;
                                }
                            });
                        }

                        if (i === 0) {
                            this.form.conteo_productos.push({
                                productox: this.detalleForm.productox,
                                cantidad: this.detalleForm.cantidad,
                            });
                            this.detalleForm.productox = '';
                            this.detalleForm.cantidad = 0;

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
                                    self.form.conteo_productos[keyx].cantidad = Number(self.form.conteo_productos[keyx].cantidad) + self.detalleForm.cantidad;
                                    this.detalleForm.productox = '';
                                    this.detalleForm.cantidad = 0;
                                }
                            })
                        }

                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'El valor para cantidad y precio unitario debe ser mayor a cero.',
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
                if (this.form.conteo_productos.length >= 1) {
                    this.form.conteo_productos.splice(index, 1);

                } else {
                    this.form.conteo_productos = [];
                }
            },

            nuevo() {
                var self = this;
                conteox.nuevo(
                    data => {
                        self.sucursales = data.sucursales;
                        //self.bodegas = data.bodegas;
                        //self.form.conteo_bodega = self.bodegas[0];
                        //self.form.conteo_sucursal = self.sucursales[0];
                        self.areas = data.areas;
                        self.form.conteo_area = data.area_actual;
                        self.productos_usados = data.productos_usados;
                        self.productos_nuevos = data.productos;
                        self.productos = data.productos;
                        self.loading = false;
                    },
                    err => {
                        console.log(err);
                    }
                );
            },

            registrar() {
                var self = this;
                self.loading = true;
                self.btnAction = "Registrando, espere....";
                self.btnActionDraft = "Guardando, espere....";
                conteox.registrar(
                    self.form,
                    data => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Registro guardado con éxito.',
                                    variant: 'success',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        this.$router.push({
                            name: "inventario-conteo-fisico"
                        });
                        self.loading = false;
                    },
                    err => {
                        self.loading = false;
                        self.errorMessages = err;
                        self.btnAction = "Registrar";
                        self.btnActionDraft = "Guardar Borrador";
                    }
                );
            }
        },
        mounted() {
            this.nuevo();
        }
    };
</script>
<style lang="scss">
    @import "src/@core/scss/vue/libs/vue-select";
    .btn-agregar {
        margin-top: 33px;
    }
</style>
