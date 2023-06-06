<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Tipo Persona</label>
                    <b-form-select  v-model.number="form.tipo_persona">
                        <option value="1">Natural</option>
                        <option value="2">Jurídico</option>
                    </b-form-select>
                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Nombre Comercial</label>
                    <input class="form-control" placeholder="Nombre Comercial" v-model="form.nombre_comercial">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.nombre_comercial" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for=""> Nombre Completo</label>
                    <input class="form-control" placeholder="Nombre Completo" v-model="form.nombre_completo">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.nombre_comercial" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Razón Social</label>
                    <input class="form-control" placeholder="Razón Social" v-model="form.razon_social">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.nombre_comercial" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Contacto</label>
                    <input class="form-control" placeholder="Contacto" v-model="form.contacto">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.contacto" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Número RUC</label>
                    <input class="form-control" v-mask="'A#############'" placeholder="Número RUC"
                           v-model="form.numero_ruc">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.numero_ruc" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Número Cédula</label>
                    <input class="form-control" v-mask="'#############A'" placeholder="Número Cédula"
                           v-model="form.numero_cedula">
					<b-alert variant="danger" show>
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
                    >
						<template slot="no-options">No se encontraron registros</template>
					</v-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.tipo_clientex" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Vendedor</label>
                    <v-select
                            :options="vendedores"
                            label="nombre_completo"
                            v-model="form.vendedor_cliente"
                    >
						<template slot="no-options">No se encontraron registros</template>
					</v-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.vendedor_cliente" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for=""> Dirección</label>
                    <input class="form-control" placeholder="Dirección" v-model="form.direccion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.direccion" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Teléfono</label>
                    <input class="form-control" placeholder="Teléfono" v-model="form.telefono">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.telefono" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Correo Contacto</label>
                    <input class="form-control" placeholder="Correo Contacto" v-model="form.correo">
					<b-alert variant="danger" show>
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
                            v-model="form.departamento_cliente"
                            v-on:input="obtenerMunicipios()"
                    >
						<template slot="no-options"> No se encontraron registros</template>
					</v-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.departamento_cliente" v-text="message"></li>
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
                            v-model="form.municipio_cliente"
                    >
						<template slot="no-options">No se encontraron registros</template>
					</v-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.municipio_cliente" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <!--<div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Giro del Negocio</label>
                    <input class="form-control" placeholder="Giro del Negocio" v-model="form.giro_negocio">
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.giro_negocio" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>-->

           <!-- <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Zona</label>
                    <v-select
                            :options="zonas"
                            label="descripcion"
                            v-model="form.zona_cliente"
                    >
						<template slot="no-options">No se encontraron registros</template>
					</v-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.zona_cliente" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>-->

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Impuesto Valor Agregado</label>
                    <v-select
                            :options="impuestos"
                            label="descripcion"
                            v-model="form.impuesto_cliente"
                    >
						<template slot="no-options">No se encontraron registros</template>
					</v-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.impuesto_cliente" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Tipo Contribuyente</label>
                    <b-form-select  v-model.number="form.tipo_contribuyente">
                        <option value="1">Pequeños</option>
                        <option value="2">Grandes</option>
                    </b-form-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.tipo_contribuyente" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Clasificación</label>
                    <b-form-select  v-model.number="form.clasificacion">
                        <option value="1">Sector Privado</option>
                        <option value="2">Sector Público</option>
                    </b-form-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.clasificacion" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Porcentaje Descuento</label>
                    <input class="form-control" placeholder="Porcentaje Descuento" type="number" min="0" max="70"
                           v-model.number="form.porcentaje_descuento">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.porcentaje_descuento" :key="message" v-text="message"></li>
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
						v-model="form.retencion_ir"
						class="mx-lg-1 mb-sm-1 mt-sm-1"
				>
					Retiene IR
				</b-form-checkbox>
			</div>
			<div class="col-sm-3">
				<b-form-checkbox
						v-model="form.retencion_imi"
						class="mx-lg-1 mb-sm-1 mt-sm-1"
				>
					Retiene IMI
				</b-form-checkbox>

			</div>

			<div class="col-sm-3">
				<b-form-checkbox
						v-model="form.permite_credito"
						class="mx-lg-1 mb-sm-1 mt-sm-1"
				>
					Permite crédito
				</b-form-checkbox>
			</div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Plazo Máximo de Crédito</label>
                    <b-form-select :disabled="!form.permite_credito"  v-model.number="form.plazo_credito">
                        <option :disabled="form.permite_credito" value="0">No permite</option>
                        <option value="8">8 días</option>
                        <option value="15">15 días</option>
                        <option value="30">30 días</option>
                        <option value="45">45 días</option>
                        <option value="60">60 días</option>
                    </b-form-select>
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.plazo_credito" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

            <template v-if="this.currency_id === 1">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Límite Crédito C$</label>
                        <input :disabled="!form.permite_credito" class="form-control" placeholder="Límite Crédito"
                               type="number" min="0" max="1000000" v-model.number="form.limite_credito">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.limite_credito" :key="message" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </template>
            <template v-else>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Límite Crédito $</label>
                        <input :disabled="!form.permite_credito" class="form-control" placeholder="Límite Crédito"
                               type="number" min="0" max="1000000" v-model.number="form.limite_credito_me">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.limite_credito_me" :key="message" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </template>




            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Trámite Cheque</label>
                    <input class="form-control" placeholder="Trámite Cheque" type="number" min="0" max="60"
                           v-model.number="form.tramite_cheque">
					<b-alert variant="danger" show>
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
                                 placeholder="Selecciona un empleado"
                                 ref="auxiliar"
                                 track-by="id_cat_auxiliar_cxc"
                                 v-model="form.auxiliar_cliente"
                    ></v-select>
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
					<b-alert variant="danger" show>
						<ul class="error-messages">
							<li :key="message" v-for="message in errorMessages.observaciones" v-text="message"></li>
						</ul>
					</b-alert>

                </div>
            </div>

        </b-row>

        <div class="row">
            <div class="col-md-6">
                <b-card-footer class="text-lg-left text-center">
                    <template v-if="form.estado">
                        <b-button @click="desactivar(form.id_cliente)" variant="danger" class="mx-lg-1 mx-0 mt-sm-1 mt-0"><feather-icon icon="TrashIcon" ></feather-icon>Inhabilitar</b-button>
                    </template>
                    <template v-else>
                        <b-button @click="activar(form.id_cliente)" variant="success" class="mx-lg-1 mx-0 mt-sm-1 mt-0"><feather-icon icon="CheckIcon" ></feather-icon>Habilitar</b-button>
                    </template>
                </b-card-footer>
            </div>

            <div class="col-md-6">
                <b-card-footer class="text-lg-right text-center">
                    <template v-if="form.es_deudor">
                        <router-link  :to="{ name: 'ventas-clientes' }">
                            <b-button type="button" variant="secondary" class="mx-lg-1 mx-0 mt-sm-1 mt-0">Cancelar</b-button>
                        </router-link>
                    </template>
                    <template v-else>
                        <router-link  :to="{ name: 'ventas-clientes' }">
                            <b-button type="button" variant="secondary" class="mx-lg-1 mx-0 mt-sm-1 mt-0">Cancelar</b-button>
                        </router-link>
                    </template>
                    <b-button :disabled="btnAction != 'Guardar' ? true : false" @click="actualizar" class="mx-lg-1 mx-0 mt-sm-1 mt-0"
                            variant="primary" type="button">{{ btnAction }}
                    </b-button>
                </b-card-footer>
            </div>
            <template v-if="loading">
                <BlockUI  :url="url"></BlockUI>
            </template>
        </div>


        <div class="content-box-footer text-right">

        </div>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import cliente from '../../../api/Ventas/clientes'
    import tipo from '../../../api/Ventas/tipos_clientes'
    import departamento from '../../../api/admon/departamentos'
    import impuesto from '../../../api/admon/impuestos'
    import zona from '../../../api/admon/zonas'
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
        BFormSelect,
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
                loading: true,
                msg: 'Cargando contenido, espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                claseBotonNoSolicitado: 'btn mb-2 btn-md-width btn-default btn-rounded',
                claseBotonSolicitado: 'btn mb-2 btn-md-width btn-success btn-rounded',
                claseBotonAprobado: 'btn mb-2 btn-md-width btn-info btn-rounded',
                departamentos: [],
                municipios: [],
                zonas: [],
                impuestos: [],
                tipos_clientes: [],
                vendedores: [],
                auxiliares: [],
                currency_id:0,
                form: {
                    departamento_cliente: '',
                    tipo_persona: '',
                    nombre_comercial: '',
                    nombre_completo: '',
                    razon_social: '',
                    contacto: '',
                    numero_ruc: '',
                    numero_cedula: '',
                    direccion: '',
                    tipo_clientex: '',
                    tipo_cliente: '',
                    auxiliar: '',
                    telefono: '',
                    correo: '',
                    municipio: '',
                    giro_negocio: '',
                    zona: '',
                    impuesto: '',
                    tipo_contribuyente: '',
                    retencion_ir: '',
                    retencion_imi: '',
                    clasificacion: '',
                    permite_credito: '',
                    plazo_credito: '',
                    limite_credito: '',
                    limite_credito_me: '',
                    porcentaje_descuento: '',
                    observaciones: '',
                    permite_consignacion: '',
                    tramite_cheque: '',
                    es_deudor: '',
                    numero_contrato_consignacion: '',
                    plazo_consignacion: 0,
                    monto_max_consignacion: 0
                },
                btnAction: 'Guardar',
                errorMessages: []
            }
        },
        methods: {
            obtenerCliente() {
                var self = this
                //self.SoloLectura = [1].includes(Number(self.$route.params.id_cliente))
                cliente.obtenerCliente({
                    id_cliente: this.$route.params.id_cliente,
                }, data => {
                    if (data !== null) {
                        self.form = data.cliente;
                        self.currency_id = data.currency_id;
                        self.departamentos = data.departamentos;
                        self.zonas = data.zonas;
                        self.impuestos = data.impuestos;
                        self.tipos_clientes = data.tipos_clientes;
                        self.vendedores = data.vendedores;
                        self.auxiliares = data.auxiliares;
                        self.loading = false;
                        if (self.form.municipio_cliente) {
                            self.departamentos.forEach((departamentox, indice) => {
                                if (departamentox.id_departamento === self.form.municipio_cliente.id_departamento) {
                                    self.form.departamento_cliente = self.departamentos[indice];
                                    self.municipios = self.form.departamento_cliente.municipios_departamento
                                }
                            });
                        }
                    } else {
                        this.$router.push({
                            name: 'ventas-clientes'
                        })
                        this.$toast({
                                component: ToastificationContent,
                                props:{
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Lo sentimos, no encontramos el registro solicitado.',
                                    variant: 'warning',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                    }
                }, err => {
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
            actualizar() {
                var self = this
                self.loading = true;
                self.btnAction = 'Guardando, espere......'
                //if(!self.SoloLectura){
                cliente.actualizar(self.form, data => {
                    this.$toast({
                            component: ToastificationContent,
                            props:{
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Cliente actualizado satisfactoriamente.',
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
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
                /*}else{
                    this.$router.push({
                        name: 'listado-clientes'
                    })
                alertify.warning('Lo sentimos este valor esta protegido, si desea cambiar el nombre contacte a los desarrolladores');
                }*/
            },
            desactivar(clienteId) {

                var self = this;
                self.$swal.fire({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        cliente.desactivar({
                            id_cliente: clienteId
                        }, data => {
                            this.$toast({
                                    component: ToastificationContent,
                                    props:{
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Cliente inhabilitado satisfactoriamente.',
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
                                    props:{
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Ha ocurrido un error: ' + err,
                                        variant: 'warning',
                                        position: 'bottom-right'
                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            console.log(err)
                        });
                    } else {
                        self.btnAction = "Guardar";
                    }
                })
            },
            activar(clienteId) {
                var self = this;
                self.$swal.fire({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        cliente.activar({
                            id_cliente: clienteId
                        }, data => {
                            this.$toast({
                                    component: ToastificationContent,
                                    props:{
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Cliente habilitado correctamente: ',
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
                            console.log(err)
                        });
                    } else {
                        self.btnAction = "Guardar";
                    }
                })
            },
            obtenerMunicipios() {
                var self = this
                self.form.municipio_cliente = null;
                if (self.form.departamento_cliente.municipios_departamento)
                    self.municipios = self.form.departamento_cliente.municipios_departamento
            },
        },
        mounted() {
            this.obtenerCliente();
        }
    }
</script>

<style lang="scss">
    @import 'src/@core/scss/vue/libs/vue-select';
    .check-label2 {
        margin-top: 30px;
    }
</style>
