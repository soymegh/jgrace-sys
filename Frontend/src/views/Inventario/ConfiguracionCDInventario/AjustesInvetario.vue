<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for=""> Tipo de configuración:</label>
                        <select class="form-control" v-model.number="filter.id_tipo_configuracion"
                                @change="cargar()">
<!--                            <option value="1">Entrada de productos</option>-->
                            <option value="2">Salida de productos</option>
                            <option value="6">Entrada de productos</option>
<!--                            <option value="3">Traslado de productos</option>-->
                            <!--<option value="3">Entrada proveedor local</option>
                             <option value="4">Entrada por compra de usados</option>
                             <option value="5">Entradas locales acreedores</option>
                            <option value="7">Ajuste garantía total</option>
                            <option value="8">Venta consignacion</option>
                            <option value="9">Venta usado nacional</option>
                            <option value="10">Venta usado exportacion</option>-->

                        </select>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.id_tipo_configuracion"
                                    v-text="message"
                            ></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
<!--                        <th class="text-left table-number">Centro / Codigo</th>-->
                        <th class="text-left table-number">Nombre Cuenta</th>
                        <th class="text-left table-number">Descripción Movimiento</th>
                        <th class="text-center table-number">Debe</th>
                        <th class="text-center table-number">Haber</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="configuracion in form.ajustes" :key="configuracion.id_configuracion_comprobante">

                        <!--<td>
                            <template
                                    v-if="configuracion.configuracion_importacioncuenta_contable.requiere_aux === 0">

                                <input class="form-control" v-model="form.codigo_auxiliar" readonly
                                       placeholder="No requiere un codigo auxiliar">
                                <ul class="error-messages">
                                    <li v-for="message in errorMessages.codigo_auxiliar" :key="message"
                                        v-text="message"></li>
                                </ul>

                            </template>
                            <template
                                    v-if="configuracion.configuracion_importacioncuenta_contable.requiere_aux === 1">

                                <label for>Auxiliares</label>
                                <v-select
                                        label="descripcion"
                                        v-model="configuracion.configuracion_auxiliares"
                                        :options="form.auxiliares"
                                ></v-select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.configuracion_auxiliares"
                                        v-text="message"></li>
                                </ul>


                                <div v-if="configuracion.configuracion_auxiliares">
                                    <label for=""> Codigo</label>
                                    <input class="form-control"
                                           v-model="configuracion.configuracion_auxiliares.codigo" readonly
                                           placeholder="Dígite el número auxiliar para la cuenta seleccionada">
                                    <ul class="error-messages">
                                        <li v-for="message in errorMessages.centro_costo" :key="message"
                                            v-text="message"></li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <label for=""> Codigo</label>
                                    <input class="form-control" v-model="form.notAvailable" readonly
                                           placeholder="Dígite el número auxiliar para la cuenta seleccionada">
                                    <ul class="error-messages">
                                        <li v-for="message in errorMessages.centro_costo" :key="message"
                                            v-text="message"></li>
                                    </ul>
                                </div>


                            </template>
                            <template
                                    v-if="configuracion.configuracion_importacioncuenta_contable.requiere_aux === 2">


                                <label for>Centro de costo</label>
                                <v-select
                                        label="descripcion"
                                        v-model="configuracion.configuracion_centro_costo"
                                        :options="form.centros_costo"
                                ></v-select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.configuracion_centro_costo"
                                        v-text="message"></li>
                                </ul>


                                <div v-if="configuracion.configuracion_centro_costo">
                                    <label for=""> Codigo</label>
                                    <input class="form-control"
                                           v-model="configuracion.configuracion_centro_costo.codigo" readonly
                                           placeholder="Dígite el número auxiliar para la cuenta seleccionada">
                                    <ul class="error-messages">
                                        <li v-for="message in errorMessages.centro_costo" :key="message"
                                            v-text="message"></li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <label for=""> Codigo</label>
                                    <input class="form-control" v-model="form.notAvailable" readonly
                                           placeholder="Dígite el número auxiliar para la cuenta seleccionada">
                                    <ul class="error-messages">
                                        <li v-for="message in errorMessages.centro_costo" :key="message"
                                            v-text="message"></li>
                                    </ul>
                                </div>


                            </template>
                            <template
                                    v-if="configuracion.configuracion_importacioncuenta_contable.requiere_aux === 3">

                                <label for>Centro de ingreso</label>
                                <v-select
                                        label="descripcion"
                                        v-model="configuracion.configuracion_centro_costo"
                                        :options="form.centros_ingreso"
                                ></v-select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.configuracion_centro_costo"
                                        v-text="message"></li>
                                </ul>

                                <div v-if="configuracion.configuracion_centro_costo">
                                    <label for=""> Codigo</label>
                                    <input class="form-control"
                                           v-model="configuracion.configuracion_centro_costo.codigo" readonly
                                           placeholder="Dígite el número auxiliar para la cuenta seleccionada">
                                    <ul class="error-messages">
                                        <li v-for="message in errorMessages.centro_ingreso" :key="message"
                                            v-text="message"></li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <label for=""> Codigo</label>
                                    <input class="form-control" v-model="form.notAvailable" readonly
                                           placeholder="Dígite el número auxiliar para la cuenta seleccionada">
                                    <ul class="error-messages">
                                        <li v-for="message in errorMessages.centro_costo" :key="message"
                                            v-text="message"></li>
                                    </ul>
                                </div>


                            </template>
                        </td>-->

                        <td class="text-left">
                            <v-select :options="cuentas_contables" label="nombre_cuenta_completo"
                                      v-model="configuracion.configuracion_importacioncuenta_contable"></v-select>
                        </td>

                        <td class="text-left"><input class="form-control"
                                                     v-model="configuracion.descripcion_movimiento"></td>
                        <template v-if="configuracion.debe_haber === 1">
                            <td class="text-center">
                                <span class="bullet bullet-sm mr-1 bullet-success"></span>
                            </td>
                            <td class="text-center">

                            </td>
                        </template>
                        <template v-else>
                            <td class="text-center">

                            </td>
                            <td class="text-center">
                                <span class="bullet bullet-sm mr-1 bullet-success"></span>
                            </td>
                        </template>

                    </tr>
                    <tr v-if="!form.ajustes.length">
                        <td class="text-center" colspan="5">Sin datos</td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </form>
        <b-card-footer>
            <div class="content-box-footer text-right">
                <router-link class="mx-1" :to="{ name: 'home' }">
                    <b-button  variant="secondary">Cancelar</b-button>
                </router-link>
                <b-button
                        :disabled="btnAction !== 'Guardar' ? true : false"
                        @click="registrar"
                        variant="primary"
                >{{ btnAction }}
                </b-button>
            </div>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    //import Pagination from '../layout/Pagination'
    import { BPaginationNav,BFormCheckbox,BFormGroup,BCard, BCardFooter, BRow, BButton,BBadge } from 'bootstrap-vue'
    //import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import vSelect from 'vue-select'
    import importacionConf from "../../../api/Inventario/configuracion_comprobantes";
    import cuentas_contables from "../../../api/contabilidad/cuentas-contables";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    export default {
        components:{
            BCard,
            BCardFooter,
            BPaginationNav,
            BFormCheckbox,
            BFormGroup,
            vSelect,
            BRow,
            BButton,
            BBadge
        },
        data() {
            return {
                cuentas_contables: [],
                form : {
                    auxiliares: [],
                    ajustes: [],
                    centros_costo: [],
                    centros_ingreso: [],
                    centro_costo: [],
                    centro_ingreso: [],
                    requiere_aux: 1,
                    codigo_auxiliar: 'No requiere codigo ',
                    notAvailable: 'N/A'
                },
                loading: true,
                msg: 'Cargando el contenido espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 500,
                    limitOptions: [5, 10, 15, 20],
                    id_tipo_configuracion: 2,
                },
                btnAction: "Guardar",
                errorMessages: []
            }
        },
        methods: {
            linkGen(pageNum) {
                return pageNum === 1 ? '?' : `?page=${pageNum}`
            },
            cargar() {
                var self = this
                self.loading = true;
                importacionConf.obtener(self.filter, data => {
                    self.form.ajustes = data.rows;
                    self.total = data.total;
                    self.cuentas_contables = data.cuentas_contables;
                    self.form.centros_costo = data.centro_costo;
                    self.form.centros_ingreso = data.centro_ingreso;
                    self.form.auxiliares = data.auxiliares;

                    self.form.centro_costo = data.centro_costo[0];
                    self.form.centro_ingreso = data.centro_ingreso[0];
                    self.errorMessages = [];
                    self.loading = false;
                }, err => {
                    console.log(err)
                })
            },

            obtenerTodasCuentasContables(){
                var self = this;
                cuentas_contables.obtenerTodasCuentasContables(
                    data => {
                        self.cuentas_contables = data;
                    },
                    err => {
                        console.log(err);
                    }
                )
            },

            registrar() {
                var self = this;
                self.loading = true;
                self.btnAction = "Guardando, espere...";
                importacionConf.actualizar(
                    self.form,
                    data => {
                        self.btnAction = "Guardar";
                        this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'CheckIcon',
                                text: 'Datos actualizados correctamente',
                                variant: 'success',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                        self.loading = false;
                    },
                    res => {
                        this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'CheckIcon',
                                text: 'Ha ocurrido un error inesperado',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                        self.errorMessages = res;
                        self.btnAction = "Guardar";
                        self.loading = false;
                    },
                    err => {
                        this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'CheckIcon',
                                text: 'Verifique que ningún campo esté vacío',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                        self.errorMessages = err;
                        self.btnAction = "Guardar";
                        self.loading = false;
                    }
                );
            },
        },
        /*components: {
            'pagination': Pagination
        },*/
        mounted() {

            this.cargar();
        }
    }
</script>

<style lang="scss">


    @import '@core/scss/vue/libs/vue-select.scss';

    .search-field {
        min-width: 125px;
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
</style>
