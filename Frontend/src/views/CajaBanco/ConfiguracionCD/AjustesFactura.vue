<template>
    <b-card>
        <form>
            <b-row>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for=""> Tipo de configuración:</label>
                        <b-form-select @change="cargar()"
                                       v-model.number="filter.id_tipo_configuracion">
                            <option value="1">Contado</option>
<!--                            <option value="3">contado C$ + Bonificacion</option>-->
<!--                            <option value="4">Contado U$ + Descuento</option>-->
                            <option value="6">Credito</option>
                            <!--                            <option value="6">Credito + Bonificacion</option>-->
                            <!--                            <option value="7">Ajuste garantia parcial</option>-->
                            <!--                            <option value="8">Ajuste garantía total</option>-->
                            <!--                            <option value="9">Venta consignacion</option>-->
                            <!--                            <option value="10">Venta usado nacional</option>-->
                            <!--                            <option value="11">Venta usado exportacion</option>-->
                            <option value="7">Anticipo</option>

                        </b-form-select>
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.id_tipo_configuracion"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </b-row>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th class="text-left">Descripción</th>
                        <th class="text-left">Nombre Cuenta</th>
                        <th class="text-left">Concepto</th>
                        <th class="text-center table-number">Debe</th>
                        <th class="text-center table-number">Haber</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr :key="configuracion.id_configuracion_factura" v-for="configuracion in form.ajustes">

                        <td class="text-left">
                            {{configuracion.concepto}}
                        </td>

                        <td class="text-left">
                            <v-select :options="cuentas_contables" label="nombre_cuenta_completo"
                                      v-model="configuracion.configuracion_facturacuenta_contable"></v-select>
                        </td>

                        <td class="text-left">
                            <b-form-input class=""
                                          v-model="configuracion.descripcion_movimiento"></b-form-input>
                        </td>
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
                        <td class="text-center" colspan="4">Sin datos</td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </form>
        <b-card-footer class=" text-lg-right text-center">
            <router-link :to="{ name: 'home' }">
                <b-button class="mx-lg-1 mx-0" type="button" variant="secondary">Cancelar</b-button>
            </router-link>
            <b-button
                    :disabled="btnAction !== 'Guardar' ? true : false"
                    @click="registrar"
                    class="mx-lg-1 mx-0"
                    type="button" variant="primary"
            >{{ btnAction }}
            </b-button>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">

    import importacionConf from '../../../api/CajaBanco/facturas-config'
    import {
        BAlert,
        BBadge,
        BButton,
        BCard,
        BCardFooter,
        BFormCheckbox,
        BFormCheckboxGroup,
        BFormDatepicker,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BListGroup,
        BListGroupItem,
        BModal,
        BPaginationNav,
        BRow,
        VBModal,
        VBTooltip
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import Ripple from 'vue-ripple-directive'
    import ToastificationContent from '../../../@core/components/toastification/ToastificationContent'
    import loadingImage from '../../../assets/images/loader/block50.gif'

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
            BFormSelect,
            BModal,
            VBModal,
            BListGroup,
            BListGroupItem,
            BBadge,
            BFormInput,
        },
        directives: {
            'b-tooltip': VBTooltip,
            'b-modal': VBModal,
            Ripple,
        },
        data() {
            return {
                cuentas_contables: [],
                form: {
                    ajustes: [],
                },
                loading: true,
                msg: 'Cargando el contenido espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                filter: {
                    page: 1,
                    limit: 500,
                    limitOptions: [5, 10, 15, 20],
                    id_tipo_configuracion: 1,
                },
                btnAction: "Guardar",
                errorMessages: []
            };
        },
        methods: {

            cargar() {
                var self = this
                self.loading = true;
                importacionConf.obtener(self.filter, data => {
                    self.form.ajustes = data.rows;
                    self.total = data.total;
                    self.cuentas_contables = data.cuentas_contables;
                    self.loading = false;
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Datos cargados correctamente.',
                                variant: 'success',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }, err => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Ha ocurrido un error al cargar los datos .' + err,
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    console.log(err)
                })
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
                                    text: 'Datos actualizados correctamente.',
                                    variant: 'success',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.loading = false;
                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error al actualizar los datos .' + err,
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

        mounted() {
            this.cargar();

        }
    };
</script>
<style lang="scss">
    @import "src/@core/scss/vue/libs/vue-select";
</style>
