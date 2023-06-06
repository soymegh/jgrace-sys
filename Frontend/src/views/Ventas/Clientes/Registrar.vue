<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Tipo Persona</label>
                    <b-form-select v-model.number="form.tipo_persona">
                        <option value="1">Natural</option>
                        <option value="2">Jurídico</option>
                    </b-form-select>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Nombre Comercial</label>
                    <input class="form-control" placeholder="Digite el nombre comercial del cliente"
                           v-model="form.nombre_comercial">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.nombre_comercial" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for=""> Nombre Completo</label>
                    <input class="form-control" placeholder="Digite el nombre completo del cliente"
                           v-model="form.nombre_completo">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.nombre_comercial" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Razón Social</label>
                    <input class="form-control" placeholder="Digite la razón social del cliente"
                           v-model="form.razon_social">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.nombre_comercial" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Contacto</label>
                    <input class="form-control" placeholder="Digite el contacto del cliente" v-model="form.contacto">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.contacto" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Número RUC</label>
                    <input class="form-control" placeholder="Digite el numero ruc del cliente" v-mask="'A#############'"
                           v-model="form.numero_ruc">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.numero_ruc" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Número Cédula</label>
                    <input class="form-control" placeholder="Digite el número de cédula del cliente"
                           v-mask="'#############A'"
                           v-model="form.numero_cedula">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.numero_cedula" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Tipo Cliente</label>
                    <v-select
                            :options="tipos_clientes"
                            label="descripcion"
                            v-model="form.tipo_cliente"
                    ></v-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.tipo_cliente" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Vendedor</label>
                    <v-select :options="vendedores" label="nombre_completo" v-model="form.vendedor">
                        <template slot="no-option">No se encontraron registros</template>
                    </v-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.vendedor" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for=""> Dirección</label>
                    <input class="form-control" placeholder="Digite la dirección del cliente" v-model="form.direccion">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.direccion" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Teléfono</label>
                    <input class="form-control" placeholder="Digite el número de telefono del cliente"
                           v-model="form.telefono">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.telefono" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Correo Contacto</label>
                    <input class="form-control" placeholder="Digite el correo electronico del contacto de cliente"
                           v-model="form.correo">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.correo" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Departamento</label>
                    <v-select
                            :options="departamentos"
                            label="descripcion"
                            v-model="form.departamento"
                            v-on:input="obtenerMunicipios()"
                    >
                        <template slot="no-options">No se encontraron registros</template>
                    </v-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.departamento" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Municipio</label>
                    <v-select
                            :options="municipios"
                            label="descripcion"
                            v-model="form.municipio"
                    >
                        <template slot="no-options"> No se encontraron registros</template>
                    </v-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.municipio" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Impuesto Valor Agregado</label>
                    <v-select
                            :options="impuestos"
                            label="descripcion"
                            v-model="form.impuesto"
                    >
                        <template slot="no-options">No se encontraron registros</template>
                    </v-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.impuesto" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Tipo Contribuyente</label>
                    <select class="form-control" v-model.number="form.tipo_contribuyente">
                        <option value="1">Pequeños</option>
                        <option value="2">Especiales</option>
                        <option value="2">Grandes</option>
                    </select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.tipo_contribuyente"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Clasificación</label>
                    <select class="form-control" v-model.number="form.clasificacion">
                        <option value="1">Sector Privado</option>
                        <option value="2">Sector Público</option>
                    </select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.clasificacion" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Porcentaje Descuento</label>
                    <input class="form-control" max="70" min="0" placeholder="Porcentaje Descuento" type="number"
                           v-model.number="form.porcentaje_descuento">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.porcentaje_descuento"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <b-form-checkbox
                        v-model="form.permite_anticipo"
                        class="mx-lg-1 mb-sm-1 mt-sm-1"
                >
                    Permite anticipo
                </b-form-checkbox>
            </div>

            <div class="col-sm-3">
                <b-form-checkbox
                        class="mx-lg-1 mb-sm-1 mt-sm-1"
                        v-model="form.retencion_ir"
                >
                    Retiene IR
                </b-form-checkbox>
            </div>
            <div class="col-sm-3">
                <b-form-checkbox
                        class="mx-lg-1 mb-sm-1 mt-sm-1"
                        v-model="form.retencion_imi"
                >
                    Retiene IMI
                </b-form-checkbox>

            </div>

            <div class="col-sm-3">
                <b-form-checkbox
                        @input="validarCredito"
                        class="mx-lg-1 mb-sm-1 mt-sm-1"
                        v-model="form.permite_credito"
                >
                    Permite crédito
                </b-form-checkbox>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Plazo Máximo de Crédito</label>
                    <b-form-select :disabled="!form.permite_credito" v-model.number="form.plazo_credito">
                        <option :disabled="form.permite_credito" value="0">No permite</option>
                        <option value="8">8 días</option>
                        <option value="15">15 días</option>
                        <option value="30">30 días</option>
                        <option value="45">45 días</option>
                        <option value="60">60 días</option>
                    </b-form-select>
                    <b-alert show variant="danger"></b-alert>
                    <ul class="error-messages">
                        <li :key="message" v-for="message in errorMessages.plazo_credito" v-text="message"></li>
                    </ul>

                </div>
            </div>

            <template v-if="this.currency_id === 1">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Límite Crédito C$</label>
                        <input :disabled="!form.permite_credito" class="form-control" max="1000000"
                               min="0" placeholder="Límite de Crédito" type="number" v-model.number="form.limite_credito">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.limite_credito"
                                    v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </template>
            <template v-else>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Límite Crédito $</label>
                        <input :disabled="!form.permite_credito" class="form-control" max="1000000"
                               min="0" placeholder="Límite de Crédito" type="number" v-model.number="form.limite_credito_me">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.limite_credito_me"
                                    v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </template>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Trámite Cheque</label>
                    <input class="form-control" max="60" min="0" placeholder="Trámite Cheque" type="number"
                           v-model.number="form.tramite_cheque">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.tramite_cheque" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <!--<div class="col-sm-3">
                <div class="form-group">
                    <label class="check-label2"><input type="checkbox"
                                                       v-model="form.es_deudor"> Es un deudor</label>
                </div>
            </div>-->
            <!--<div class="col-sm-6">

                <div class="form-group">
                    <label for>Otras cuentas por cobrar</label>
                    <v-select :allow-empty="false" :options="auxiliares"
                                 :searchable="true"
                                 label="descripcion"
                                 placeholder="Selecciona un auxiliar"
                                 ref="auxiliar"
                                 track-by="id_cat_auxiliar_cxc"
                                 v-model="form.auxiliar"
                    >
						<template slot="no-options">No se encontraron registros</template>
					</v-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li
									:key="message"
									v-for="message in errorMessages.trabajadorx"
									v-text="message"
							></li>
						</ul>
					</b-alert>

                </div>
            </div>-->

            <div class="col-sm-12">
                <div class="form-group">
                    <label for=""> Observaciones</label>
                    <input class="form-control" placeholder="Observaciones" v-model="form.observaciones">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.observaciones" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

        </b-row>

        <b-card-footer class="text-lg-right text-center">

            <template v-if="form.es_deudor">
                <router-link :to="{ name: 'listado-deudores' }">
                    <b-button class="mx-lg-1 mx-0" type="button" variant="secondary">Cancelar</b-button>
                </router-link>
            </template>
            <template v-else>
                <router-link :to="{ name: 'ventas-clientes' }">
                    <b-button class="mx-lg-1 mx-0" type="button" variant="secondary">Cancelar</b-button>
                </router-link>
            </template>

            <b-button :disabled="btnAction != 'Registrar' ? true : false" @click="registrar" class="mx-lg-1 mx-0"
                      type="button"
                      variant="primary">{{ btnAction }}
            </b-button>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>

    </b-card>
</template>

<script type="text/ecmascript-6">
    import cliente from '../../../api/Ventas/clientes'
    import {
        BAlert,
        BButton,
        BCard,
        BCardFooter,
        BFormCheckbox,
        BFormCheckboxGroup,
        BFormDatepicker,
        BFormGroup,
        BFormSelect,
        BPaginationNav,
        BRow,
        VBTooltip,
    } from 'bootstrap-vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

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
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: false,
                msg: 'Guardando los datos, espere un momento',
                claseBotonNoSolicitado: 'btn mb-2 btn-md-width btn-default btn-rounded',
                claseBotonSolicitado: 'btn mb-2 btn-md-width btn-success btn-rounded',
                claseBotonAprobado: 'btn mb-2 btn-md-width btn-info btn-rounded',

                url: loadingImage,   //It is important to import the loading image then use here
                departamentos: [],
                municipios: [],
                zonas: [],
                impuestos: [],
                tipos_clientes: [],
                vendedores: [],
                auxiliares: [],
                currency_id: 0,
                form: {
                    tipo_persona: 1,
                    nombre_comercial: '',
                    nombre_completo: '',
                    razon_social: '',
                    contacto: '',
                    numero_ruc: '',
                    numero_cedula: '',
                    direccion: '',
                    tipo_cliente: '',
                    auxiliar: '',
                    telefono: '',
                    correo: '',
                    municipio: '',
                    giro_negocio: '',
                    zona: '',
                    impuesto: '',
                    tipo_contribuyente: 1,
                    retencion_ir: true,
                    retencion_imi: true,
                    clasificacion: 1,
                    permite_credito: false,
                    permite_anticipo: false,
                    plazo_credito: 0,
                    limite_credito: 0,
                    limite_credito_me: 0,
                    porcentaje_descuento: 0,
                    observaciones: '',
                    permite_consignacion: false,
                    tramite_cheque: 15,
                    es_deudor: false,
                    numero_contrato_consignacion: '',
                    plazo_consignacion: 0,
                    aprobacion_consignacion: 0,
                    monto_max_consignacion: 0
                },
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods: {

            validarCredito() {

                let self = this;
                self.form.limite_credito = 0;
                self.form.plazo_credito = 0;

            },
            obtenerMunicipios() {
                var self = this;
                self.loading = true;
                self.form.municipio = null;
                if (self.form.departamento.municipios_departamento)
                    self.municipios = self.form.departamento.municipios_departamento
                self.loading = false;
            },
            nuevo() {
                var self = this;
                self.loading = true;
                cliente.nuevo({}, data => {
                    self.impuestos = data.impuestos;
                    self.form.impuesto = self.impuestos[1]
                    self.tipos_clientes = data.tipos_cliente;
                    self.form.tipo_cliente = self.tipos_clientes[0];
                    self.departamentos = data.departamentos;
                    self.form.departamento = self.departamentos[9]
                    self.municipios = self.form.departamento.municipios_departamento
                    self.form.municipio = self.municipios[5]
                    self.zonas = data.zonas;
                    self.vendedores = data.vendedores;
                    self.auxiliares = data.auxiliares;
                    self.currency_id = data.currency_id;
                    self.loading = false;
                }, err => {
                    self.loading = false;
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Ha ocurrido un error al cargar los datos: ' + err,
                                variant: 'warning',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    console.log(err)
                })
            },

            solicitarAutorizacion() {
                let self = this;
                if (self.form.aprobacion_consignacion === 0) {
                    self.form.aprobacion_consignacion = 1;
                } else if (self.form.aprobacion_consignacion === 1) {
                    self.form.aprobacion_consignacion = 0;
                }
            },

            registrar() {
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                cliente.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Cliente registrado correctamente',
                                variant: 'success',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    this.$router.push({
                        name: 'ventas-clientes'
                    })
                }, err => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Ha ocurrido un error, revise los datos faltantes',
                                variant: 'danger',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    self.loading = false;
                    self.errorMessages = err;
                    self.btnAction = 'Registrar'
                })
            }
        },
        mounted() {
            /*this.obtenerTodosTipos();
            this.obtenerTodosImpuestos()
            this.obtenerTodasZonas()*/
            this.nuevo();
        }
    }
</script>

<style lang="scss">
    @import "src/@core/scss/vue/libs/vue-select";

    .check-label2 {
        margin-top: 30px;
    }
</style>
