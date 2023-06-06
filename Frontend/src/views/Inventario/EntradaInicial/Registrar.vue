<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Bodega</label>
                    <v-select
                            v-model="form.bodega"
                            :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                            label="descripcion"
                            :options="bodegas"
                            v-on:input="seleccionarBodega"
                            placeholder="Selecciona una bodega"
                    >
                        <template slot="no-options">
                            No se han encontrado registros
                        </template>
                    </v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.bodega" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Fecha Emitida</label>
                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            local="es"
                            @selected="onDateSelect"
                            selected-variant="primary"
                            class="mb-0"
                            placeholder="F. Emision"
                    >

                    </b-form-datepicker>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.f_emitida"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Número documento de entrada</label>
                    <input type="text" class="form-control" v-model="form.no_documento" required>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.no_documento"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
        </b-row>

        <b-alert variant="success" class="mt-2 mb-2" show>
            <div class="alert-body">
                <span>Detalle de la entrada.</span>
            </div>

        </b-alert>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Producto</label>

                    <!--<typeahead style="width: 100%;" :initial="detalleForm.productox" :trim="80" :url="productosBusquedaURL" @input="seleccionarProducto"></typeahead>-->

                    <v-select :options="productos" label="text" placeholder="Selecciona un producto" ref="producto"
                              track-by="id_producto" v-model="detalleForm.productox" v-on:input="seleccionarProducto">
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
                    <b-button @click="agregarDetalle" variant="info" ref="agregar">Agregar
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
                <table class="table table-responsive-md table-bordered">
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
                                <b-button @click="eliminarLinea(item, index)" variant="danger" v-b-tooltip.hover.top="'Eliminar linea!'">
                                    <feather-icon icon="TrashIcon"></feather-icon>
                                </b-button>
                            </td>
                            <td>
                                <input class="form-control" disabled v-model="item.productox.text">
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
                                <input class="form-control" type="number" min="1" v-model.number="item.cantidad">
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
        </b-row>

        <b-card-footer>
            <div class="text-right">
                <router-link :to="{ name: 'inventario-entrada-inicial' }">
                    <b-button variant="secondary" type="button" class="mx-1">Cancelar</b-button>
                </router-link>
                <b-button
                        :disabled="btnAction !== 'Registrar Entrada Inicial'"
                        @click="confirmar"
                        variant="primary"
                        type="button"
                >{{ btnAction }}
                </b-button>
            </div>
        </b-card-footer>
        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>

</template>
<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    //import Pagination from '../layout/Pagination'
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
    //import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import vSelect from 'vue-select'
    import entrada from "../../../api/Inventario/entrada_inicial";
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    var fecha_actual = new Date();
    fecha_actual.setHours(23, 59, 59, 999);

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
                es: es,
                format: "dd-MM-yyyy",
                //productoEntradaBusquedaURL: "/entradas/productos/buscar",
                loading: true,
                registrandoBateria: false,
                msg: 'Cargando el contenido espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                mascaraCodigo: 'X############',
                codigoAutomatico: false,
                total_pendientes: 0,
                contador: 1,
                productos: [],
                productos_nuevos: [],
                productos_usados: [],
                sucursales: [],
                bodegas: [],
                detalleForm: {
                    productox: '',
                    cantidad: 1,
                },
                detalle_baterias: [],
                consolidadoProductos: [],
                form: {
                    id_entrada_inicial: '',
                    contiene_baterias: true,
                    codigo_entrada: "",
                    fecha_entrada: moment(new Date()).format("YYYY-MM-DD"),
                    entrada_tipo: "",
                    entrada_proveedor: "",
                    entrada_bodega: "",
                    conteo_productos: [],
                },
                btnAction: "Registrar Entrada Inicial",
                errorMessages: []
            };
        },
        methods: {
            seleccionarProducto() {
                var self = this
                if (self.detalleForm.productox)
                    self.detalleForm.cantidad = 1;
                self.detalleForm.precio_info = self.detalleForm.productox.costo_estandar;
                // self.$refs.cantidad.focus();
            },
            seleccionarBodega() {
                var self = this;
                //self.loading = true;

                /*if(self.form.bodega){

                    if(self.form.conteo_productos.length >= 1){
                        if(self.form.bodega.id_tipo_bodega===3){
                        this.$swal.fire({
                            title: 'Al cambiar la bodega el conteo?',
                            text: "Revise bien la información",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Si, confirmar',
                            cancelButtonText:'Cancelar'
                        }).then((result) => {
                            if (result.value) {
                                self.form.conteo_productos[keyx].cantidad = Number(self.form.conteo_productos[keyx].cantidad) + self.detalleForm.cantidad;
                                this.detalleForm.productox='';
                                this.detalleForm.cantidad=0;
                            }
                        })
                    }else{

                    }*/

                self.productos = [];
                self.form.conteo_productos = [];
                if (self.form.bodega.id_tipo_bodega === 3) {
                    self.productos_usados.forEach((producto, key) => {
                        self.productos.push(producto)
                    });
                } else {
                    self.productos_nuevos.forEach((producto, key) => {
                        self.productos.push(producto)
                    });
                }

            },
            onDateSelect(date) {
                this.form.fecha_entrada = moment(date).format("YYYY-MM-DD"); //
            },


            agregarDetalle() {
                let self = this;
                if (this.detalleForm.productox) {
                    if (this.detalleForm.cantidad > 0 /*&& this.detalleForm.precio_info > 0*/) {


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
                                title: 'Revise bien la información',
                                text: "Ya existe este producto en el detalle, desea sumar la cantidad?",
                                icon: 'warning',
                                showCancelButton: true,
                                customClass: {
                                    confirmButton: 'btn btn-primary',
                                    cancelButton: 'btn btn-danger ml-1',
                                },
                                buttonsStyling: false,
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
                                    icon: 'BellIcon',
                                    text: 'Los valores para cantidad y precio unitario deben ser mayores que cero',
                                    variant: 'danger',

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
                                icon: 'BellIcon',
                                text: 'Debe seleccionar un producto',
                                variant: 'danger',

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


            confirmar() {
                this.$swal.fire({
                    title: 'Revise bien los datos',
                    text: "Esta seguro de confirmar el registro de la entrada?",
                    icon: 'warning',
                    showCancelButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger ml-1',
                    },
                    buttonsStyling: false,
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.registrar();
                    }
                })
            },

            cargar_detalles_producto() {
                var self = this
                //  console.log(self.detalleForm.productox)
                self.$refs.bateria.$refs.search.blur()
                self.$refs.codigo.focus();
                if (self.detalleForm.productox) {
                    if (self.detalleForm.productox.producto_detalles_baterias.id_submarca === 7) {///Caso motobaterias (no tiene codigo de garantia)
                        self.$refs.fecha_activacion.focus();
                        self.codigoAutomatico = true;
                        self.mascaraCodigo = 'XXXXXXXXXXXXXXXXXXXXXXX';
                        self.detalleForm.codigo_garantiax = 'Código Automático ' + self.contador.toString();
                        self.contador++;
                        let old_activacion = self.detalleForm.codigo_garantiax;
                        self.detalleForm.codigo_garantiax = '';
                        self.detalleForm.codigo_garantiax = old_activacion;
                    } else if (self.detalleForm.productox.producto_detalles_baterias.id_submarca === 3) {//Caso Cronos
                        self.$refs.codigo.focus();
                        self.detalleForm.codigo_garantiax = '';
                        self.mascaraCodigo = 'X############';
                        self.codigoAutomatico = false;

                    } else {//casos lth
                        self.$refs.codigo.focus();
                        self.detalleForm.codigo_garantiax = '';
                        self.mascaraCodigo = 'XXXXXXXXXXXXXXXXXXXXXXX';
                        self.codigoAutomatico = false;
                    }
                }
                //self.$refs.codigo.focus();
            },

            nuevo() {
                var self = this;
                entrada.nuevoProductosVarios({}, data => {
                        self.productos_usados = data.productos_usados;
                        self.productos_nuevos = data.productos;
                        self.productos = data.productos;
                        self.bodegas = data.bodegas;
                        //self.form.id_entrada_inicial =data.id_entrada_inicial;
                        //self.agruparProductos();
                        self.loading = false;
                    },
                    err => {
                        console.log(err);
                    })

            },


            registrar() {
                var self = this;
                //if(self.total_cantidad_recibida > 0){
                self.loading = true;
                self.form.detalle_baterias = self.detalle_baterias;
                self.btnAction = "Registrando, espere....";
                //if(self.form.estado === 1) {
                entrada.registrarManual(
                    self.form,
                    data => {
                        //alertify.success("La entrada fue recibida correctamente",5);
                        this.$swal.fire(
                            'Entrada Registrada!',
                            'La Entrada fue Registrada correctamente.',
                            'success'
                        );
                        self.loading = false;
                        this.$router.push({
                            name: "inventario-entrada-inicial"
                        });
                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'BellIcon',
                                    text: 'Ha ocurrido un error guardando los datos, revise bien la información',
                                    variant: 'danger',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.errorMessages = err;
                        self.btnAction = "Registrar Entrada Inicial";
                        self.loading = false;
                    }
                );
                /*}else{
                    alertify.error("La entrada ya no puede ser recibida",5);
                    this.$router.push({
                        name: "entradas-inicial"
                    });
                }*/
                /*}else{
                    alertify.error("Se debe recibir al menos un producto",5);
                }*/
            },
        },


        computed: {

            total_cantidad() {
                return this.form.conteo_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad))
                }, 0)
            },
        },
        mounted() {
            this.nuevo();
        },
    }
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
