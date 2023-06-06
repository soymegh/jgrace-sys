<template>
    <b-card>
        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Bodega</label>
                    <v-select
                            label="descripcion"
                            v-model="form.entrada_bodega"
                            :options="bodegas"
                    ></v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.entrada_bodega" v-text="message"></li>
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
                            v-model="form.fecha_entrada"
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
           <!-- <div class="col-sm-4">
                <div class="form-group">
                    <label for>Fecha recepción</label>
                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            local="es"
                            @selected="onDateSelectRecep"
                            selected-variant="primary"
                            class="mb-0"
                            placeholder="F. Recepción"
                            v-model="fechax"
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
            </div>-->
        </b-row>
        <div v-if="!form.entrada_bodega">
            <b-alert variant="info" show>
                <span>Se requiere que seleccione una bodega entrante para continuar.</span>
            </b-alert>
            <hr>
        </div>

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

                    <v-select :options="productos"
                                 label="text"
                                 placeholder="Selecciona un producto"
                                 ref="producto"
                                 track-by="id_producto"
                                 v-model="detalleForm.productox"
                                 v-on:input="seleccionarProducto"
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

            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Cantidad</label>
                    <input class="form-control" ref="cantidad" type="number" min="1" v-model.number="detalleForm.cantidad">
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
                    <b-button @click="agregarDetalle" variant="info" ref="agregar">Agregar Producto</b-button>
                </div>
            </div>
        </b-row>

        <b-row>
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

                <table class="table table-responsive-md table-bordered"  >
                    <thead>
                    <tr>
                        <th></th>
                        <th >Producto</th>
                        <th >Cantidad</th>

                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in form.entrada_productos">
                        <tr>
                            <td style="width: 2%">
                                <b-button @click="eliminarLinea(item, index)" variant="danger">
                                    <feather-icon icon="TrashIcon"></feather-icon>
                                </b-button>
                            </td>
                            <td>
                                <input class="form-control" disabled v-model="item.entrada_producto.descripcion">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`entrada_productos.${index}.productox.id_producto`]"
                                                v-text="message">
                                        </li>
                                    </ul>
                                </b-alert>

                            </td>


                            <td>
                                <input  class="form-control" type="number" min="1" v-model.number="item.cantidad">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`entrada_productos.${index}.cantidad`]"
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
                        <td  class="item-footer">
                            <strong>{{total_cantidad}}</strong>
                        </td>
                    </tr>


                    </tfoot>
                </table>
            </div>
        </b-row>

        <b-card-footer>
            <div class="content-box-footer text-lg-right text-center">
                <router-link :to="{ name: 'inventario-entrada-inicial' }" >
                    <b-button class="mx-1" variant="secondary" type="button">Cancelar</b-button>
                </router-link>
                <b-button class="mx-1"
                        :disabled="btnAction !== 'Actualizar Entrada'"
                        @click="confirmar"
                        variant="success"
                        type="button"
                >{{ btnAction }}</b-button>
                <b-button class="mx-1"
                        :disabled="btnActionEmi !== 'Emitir Entrada'"
                        @click="confirmarEmision"
                        variant="success"
                        type="button"
                >{{ btnActionEmi }}</b-button>
            </div>
        </b-card-footer>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
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
    import conteox from "../../../api/Inventario/entrada_inicial";
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
                loading:true,
                url : loadingImage,

                areas: [],
                productos: [],
                sucursales:[],
                bodegas:[],
                fechax: new Date(),

                detalleForm:{
                    productox: '',
                    cantidad: 1,
                },

                form: {
                    hora_inicio: "",
                    hora_fin: "",
                    f_inventario: moment(new Date()).format("YYYY-MM-DD"),
                    fecha_recepcion: moment(new Date()).format("YYYY-MM-DD"),
                    conteo_sucursal: "",
                    entrada_bodega:"",
                    conteo_area: "",
                    entrada_productos: [],
                    es_borrador:false
                },

                btnAction: "Actualizar Entrada",
                btnActionEmi: "Emitir Entrada",
                errorMessages: []
            };
        },
        computed: {

            total_cantidad() {
                return this.form.entrada_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad))
                }, 0)
            },
        },
        methods: {
            confirmar(){
                this.$swal.fire({
                    title: 'Guardar entrada',
                    text: "¿Está seguro? Es posible editar la entrada luego de guardada.",
                    icon: 'warning',
                    showCancelButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger ml-1',
                    },
                    buttonsStyling: false,
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText:'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.form.es_borrador=true;
                        this.actualizar();
                    }
                })
            },

            confirmarEmision(){
                this.$swal.fire({
                    title: 'Emitir entrada',
                    text: "¿Está seguro? Una vez emitida la entrada no se puede editar",
                    icon: 'warning',
                    showCancelButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger ml-1',
                    },
                    buttonsStyling: false,
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText:'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.form.es_borrador=false;
                        this.actualizar();
                    }
                })
            },

            seleccionarProducto() {
                var self = this
                if (self.detalleForm.productox)
                    self.detalleForm.cantidad = 1;
                self.detalleForm.precio_info = self.detalleForm.productox.costo_estandar;
                // self.$refs.cantidad.focus();
            },

            onDateSelect(date) {
                //console.log(date); //
                this.form.fecha_entrada = moment(date).format("YYYY-MM-DD"); //
            },
            onDateSelectRecep(date) {
                //console.log(date); //
                this.form.fecha_recepcion = moment(date).format("YYYY-MM-DD"); //
            },


            agregarDetalle() {
                let self = this;
                if(this.detalleForm.productox){
                    if(this.detalleForm.cantidad>0 /*&& this.detalleForm.precio_info > 0*/){


                        let i = 0;
                        let keyx = 0;
                        if(self.form.entrada_productos){
                            self.form.entrada_productos.forEach((productox, key) => {
                                //console.log('ID_PRODUCTO ',self.detalleForm.productox.id_producto);
                                if(self.detalleForm.productox.id_producto===productox.id_producto){
                                    i++;
                                    keyx = key;
                                }
                            });
                        }

                        if(i === 0){
                            this.form.entrada_productos.push({
                                entrada_producto: this.detalleForm.productox,
                                id_producto: this.detalleForm.productox.id_producto,
                                codigo_barra: this.detalleForm.productox.codigo_barra,
                                descripcion: this.detalleForm.productox.text,
                                cantidad: this.detalleForm.cantidad,
                            });
                            this.detalleForm.productox='';
                            this.detalleForm.cantidad=0;

                        }else{
                            this.$swal.fire({
                                title: 'El producto ya existe en el detalle, desea sumar la nueva cantidad al monto existente?',
                                text: "También se sobreescribirá el costo unitario",
                                icon: 'warning',
                                showCancelButton: true,
                                customClass: {
                                    confirmButton: 'btn btn-primary',
                                    cancelButton: 'btn btn-danger ml-1',
                                },
                                buttonsStyling: false,
                                confirmButtonText: 'Si, confirmar',
                                cancelButtonText:'Cancelar'
                            }).then((result) => {
                                if (result.value) {
                                    self.form.entrada_productos[keyx].cantidad = Number(self.form.entrada_productos[keyx].cantidad) + self.detalleForm.cantidad;
                                    this.detalleForm.productox='';
                                    this.detalleForm.cantidad=0;
                                }
                            })
                        }

                    }else{
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
                }else{
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
                if (this.form.entrada_productos.length >= 1) {
                    this.form.entrada_productos.splice(index, 1);

                }else{
                    this.form.entrada_productos=[];
                }
            },

            obtenerConteo() {
                var self = this
                conteox.obtenerEntrada({
                    id_entrada_inicial: this.$route.params.id_entrada_inicial,
                    cargar_dependencias: true,
                }, data => {
                    self.bodegas = data.bodegas;
                    self.productos= data.productos;
                    self.form = data.entrada_inicial;
                    self.form.fecha_entrada = new Date(data.entrada_inicial.fecha_entrada);
                    self.loading=false;
                    if(self.form.estado === 2){
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'BellIcon',
                                    text: 'Esta entrada inicial ya no puede ser editada',
                                    variant: 'danger',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        this.$router.push({
                            name: "inventario-entrada-inicial"
                        });
                    }
                })
            },

            actualizar() {
                var self = this;
                if(self.form.estado !== 2 ){
                    self.btnAction = "Registrando, espere....";
                    self.btnActionEmi = "Registrando, espere....";
                    self.loading=true;
                    conteox.actualizarManual(
                        self.form,
                        data => {
                            if(self.form.es_borrador){
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'BellIcon',
                                            text: 'Actualización de inventario inicial con éxito',
                                            variant: 'success',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                            }else{
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'BellIcon',
                                            text: 'La entrada inicial ha sido actualizada y EMITIDA con éxito',
                                            variant: 'success',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                            }
                            self.loading=false;
                            this.$router.push({
                                name: "inventario-entrada-inicial"
                            });
                        },
                        err => {
                            self.loading=false;
                            self.errorMessages = err;
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'BellIcon',
                                        text: err,
                                        variant: 'danger',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            self.btnAction = "Actualizar Entrada";
                            self.btnActionEmi = "Emitir Entrada";
                        }
                    );
                }else{
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'BellIcon',
                                text: 'La entrada inicial ya no puede ser actualizada',
                                variant: 'danger',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            }
        },
        mounted() {
            this.obtenerConteo();
        }
    }
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
