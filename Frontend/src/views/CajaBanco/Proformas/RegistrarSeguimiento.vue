<template>
    <b-card>
        <b-row>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for=""> No. Proforma</label>
                    <input class="form-control" :disabled="true" placeholder="" v-model="form.no_documento">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.proforma" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for>Ejecutivo de venta</label>
                    <v-select label="nombre_completo" v-model="form.proforma_vendedor"
                              :options="vendedores"></v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.vendedor" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for=""> Nombre de persona contactada</label>
                    <input class="form-control" placeholder="Dígite un nombre"
                           v-model="form.nombre_contacto">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.descripcion"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for="">Medio de contacto</label>
                    <b-form-select  v-model="form.medio_contacto">
                        <option value="1">Llamada</option>
                        <option value="2">Correo</option>
                        <option value="3">App Mensajería</option>
                        <option value="4">Visita personal</option>
                    </b-form-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.medio_contacto"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for=""> Correo</label>
                    <input class="form-control" placeholder="Dígite un correo electronico"
                           v-model="form.correo">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.correo" v-text="message"></li>
                        </ul>
                    </b-alert>


                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for=""> Cargo de persona contactada</label>
                    <input class="form-control" placeholder="Dígite un cargo" v-model="form.cargo_contacto">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.cargo_contacto"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label for=""> Telefono</label>
                    <input class="form-control" type="number" min="0"
                           placeholder="Dígite un número telefonico" v-model="form.telefono">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.telefono" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for=""> Observación</label>
                    <input class="form-control" placeholder="Dígite una observación"
                           v-model="form.nota_seguimiento">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.nota_seguimiento"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for>Proximo contacto</label>
                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            local="es"
                            :disabled="false"
                            selected-variant="primary"
                            class="mb-0"
                            placeholder="f.proximo"
                            @selected="onDateSelect2"
                            v-model="form.proximo_contacto"
                    />
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.proximo_contacto" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for>&nbsp;</label>
                    <b-button @click="agregarDetalle" class="btn-agregar" variant="info" ref="agregar">Agregar
                        detalle
                    </b-button>
                </div>
            </div>
        </b-row>

        <div class="row">
            <div class="col-sm-12">
                <b-alert variant="danger" show>
                    <ul class="error-messages">
                        <li :key="message" v-for="message in errorMessages.proforma" v-text="message"></li>
                    </ul>
                </b-alert>


                <table class="table table-bordered table-hover table-responsive">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="min-width:330px">Ejecutivo de ventas</th>
                        <th style="min-width:300px">Nombre persona contactada</th>
                        <th style="min-width:200px">Cargo persona contactada</th>
                        <th style="min-width:150px">Medio de contacto</th>
                        <th style="min-width:200px">Correo</th>
                        <th style="min-width:200px">Telefono</th>
                        <th style="min-width:350px">Observaciones</th>
                        <th style="min-width:170px">Próximo contacto</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in form.proforma">
                        <tr>
                            <td style="width: 2%">
                                <b-button v-b-tooltip.hover.top="'eliminar línea'" variant="outline-danger" @click="eliminarLinea(item, index)">
                                    <feather-icon icon="TrashIcon"></feather-icon>
                                </b-button>
                            </td>
                            <td>
                                <!--<v-select  label="text" :disabled="true" v-model="item.proforma_vendedor" :options="vendedores"></v-select>-->
                                {{item.proforma_vendedor.text}}
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`proforma.${index}.vendedor`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td>
                                <input class="form-control" type="text" :disabled="true"
                                       v-model.text="item.nombre_contacto">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`detalleSeguimiento.${index}.nombre_contacto`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td>
                                <input class="form-control" type="text" :disabled="true"
                                       v-model.text="item.cargo_contacto">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`detalleSeguimiento.${index}.cargo_contacto`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td>
                                <b-form-select  disabled="true"
                                        v-model.text="item.medio_contacto">
                                    <option value="1">Llamada</option>
                                    <option value="2">Correo</option>
                                    <option value="3">App Mensajería</option>
                                    <option value="4">Visita personal</option>
                                </b-form-select>
                                <!--<input  class="form-control" type="text" :disabled="true" v-model.text="item.medio_contacto">-->
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`detalleSeguimiento.${index}.medio_contacto`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td>
                                <input class="form-control" type="text" :disabled="true"
                                       v-model.text="item.correo">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`detalleSeguimiento.${index}.correo`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td>
                                <input class="form-control" type="text" :disabled="true"
                                       v-model.text="item.telefono">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`detalleSeguimiento.${index}.telefono`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td>
                                <input class="form-control" type="text" :disabled="true"
                                       v-model.text="item.nota_seguimiento">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`detalleSeguimiento.${index}.nota_seguimiento`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td>
                                <input class="form-control" :disabled="true"
                                       v-model="item.proximo_contacto">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message"
                                            v-for="message in errorMessages[`detalleSeguimiento.${index}.proximo_contacto`]"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </td>
                            <!--<td>
                            <strong> {{sub_total(item) | formatMoney(2)}}</strong>
                            </td>-->

                        </tr>
                        <tr></tr>
                    </template>
                    </tbody>
                    <tfoot>
                    <tr>
                        <!--<td colspan="4"></td>
                        <td>Total</td>
                        <td> <strong> {{calcular_total | formatMoney(2)}}</strong></td>-->
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <b-card-footer class="text-lg-right text-center">
            <router-link :to="{ name: 'cajabanco-proformas' }">
                <b-button variant="secondary" type="button" class="mx-lg-1">Cancelar</b-button>
            </router-link>
            <b-button :disabled="btnAction !== 'Registrar' ? true : false" @click="registrar"
                    variant="primary" type="button" class="mx-lg-1">{{ btnAction }}
            </b-button>
            <b-button :disabled="btnActionArch != 'Archivar' ? true : false" @click="archivar(5)"
                    variant="dark" type="button" class="mx-lg-1">{{ btnActionArch }}
            </b-button>
        </b-card-footer>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import proforma from '../../../api/CajaBanco/proformas-seguimiento'
    import proformaORG from '../../../api/CajaBanco/proformas'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
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
        BFormSelect, BModal, VBModal, BListGroup, BListGroupItem, BFormInput, BForm
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import Ripple from 'vue-ripple-directive'
    import roundNumber from '../../../assets/custom-scripts/Round'
    import moment from '../../../../../Backend/resources/app/assets/plugins/moment/moment'
    import ToastificationContent from '../../../@core/components/toastification/ToastificationContent'
    import loadingImage from '../../../assets/images/loader/block50.gif'

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
            BFormSelect,
            BModal,
            BListGroup,
            BListGroupItem,
            BForm,
        },
        directives: {
            'b-tooltip': VBTooltip,
            'b-modal': VBModal,
            Ripple,
        },
        data() {
            return {
                loading: false,
                msg: 'Cargando los datos, espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                es: es,
                format: "dd-MM-yyyy",
                trabajadoresBusquedaURL: "/trabajadores/buscar_trabajador",
                form: {
                    proforma: [],
                    id_proforma: '',
                    no_documento: '',
                    proforma_vendedor: [],
                    nombre_contacto: '',
                    cargo_contacto: '',
                    medio_contacto: 1,
                    correo: '',
                    telefono: '',
                    nota_seguimiento: '',
                    proximo_contacto: '',
                },
                fechax: new Date(),
                fechax2: new Date(),
                trabajador: {},
                vendedores: [],
                id_trabajador_seguimiento: '',
                btnAction: 'Registrar',
                btnActionArch: 'Archivar',
                errorMessages: []
            }
        },
        methods: {
            nueva() {
                var self = this;
                self.loading = true;
                proforma.nueva({id_proforma: this.$route.params.id_proforma}, data => {
                        self.form.proforma = data.seguimientos;
                        self.form.no_documento = data.no_documento;
                        self.form.id_proforma = data.id_proforma;
                        self.fechax2 = new Date(data.seguimientos.proximo_contacto);
                        self.vendedores = data.vendedores;
                        self.loading = false;
                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ocurrió un error al cargar los datos. ' ,
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.loading = false;
                        console.log(err);
                    })

            },
            obtenerSeguimiento() {
                var self = this
                //Array(1,2,3).includes(Number(self.$route.params.id_zona)) ? self.SoloLectura = true : self.SoloLectura = false
                proforma.obtenerProforma({
                    id_proforma: this.$route.params.id_proforma
                }, data => {
                    self.vendedores = data.vendedores;
                    self.form = data.seguimiento;
                    self.fechax2 = new Date(data.seguimiento.proforma_seguimiento.proximo_contacto);
                    self.loading = false;
                }, err => {
                    alertify.error(err.id_proforma[0], 5);
                    this.$router.push({
                        name: 'cajabanco-proformas'
                    });
                })
            },
            registrar() {
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                proforma.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Seguimiento registrado correctamente. ' ,
                                variant: 'success',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    this.$router.push({
                        name: 'cajabanco-proformas'
                    })
                }, err => {
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                })
            },
            /*
            * Auth: omorales
            * Method: archivar cotización - Cambiar a estado 5 solicitando justificación
            * date: 08/0/2021
            * */
            archivar(estado) {
                var txtAprobar = 'Justificación:';

                var self = this;

                self.$swal.fire({
                    title: '¿Está seguro de archivar esa cotización?',
                    text: txtAprobar,
                    icon: 'warning',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    self.loading = true;
                    if (result.value) {
                        //var self = this
                        proformaORG.archivar({
                            id_proforma: self.form.id_proforma,
                            estado: estado,
                            observacion: result.value
                        }, data => {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Se ha archivado la proforma correctamente. ' ,
                                        variant: 'warning',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            this.$router.push({
                                name: 'cajabanco-proformas'
                            })
                        }, err => {
                            self.loading = false;
                            console.log(err)
                        })
                    } else {
                        self.loading = false;
                    }
                })
            },
            agregarDetalle() {
                let self = this;
                if (this.form.proximo_contacto) {
                    let i = 0;
                    let keyx = 0;
                    if (self.form.proforma) {
                        self.form.proforma.forEach((fila, key) => {
                            if (self.form.proximo_contacto === fila.proximo_contacto) {
                                i++;
                                keyx = key;
                            }
                        });
                    }
                    if (i === 0) {
                        //this.detalleForm.parentescos.descripcion = this.detalleForm.parentescos.text;
                        this.form.proforma.push({
                            proforma_vendedor: this.form.proforma_vendedor,
                            nombre_contacto: this.form.nombre_contacto,
                            cargo_contacto: this.form.cargo_contacto,
                            medio_contacto: this.form.medio_contacto,
                            correo: this.form.correo,
                            telefono: this.form.telefono,
                            nota_seguimiento: this.form.nota_seguimiento,
                            proximo_contacto: this.form.proximo_contacto,
                        });
                        //this.detalleForm.fecha_viatico='';
                        this.form.trabajador = '';
                        this.form.nombre_contacto = '';
                        this.form.cargo_contacto = '';
                        this.form.medio_contacto = '';
                        this.form.correo = '';
                        this.form.telefono = '';
                        this.form.nota_seguimiento = '';
                        this.form.proximo_contacto = '';

                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ya existe un registro con los datos seleccionados. ' ,
                                    variant: 'warning',

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
                                text: 'Los campos no pueden estar vacios. ' ,
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },
            eliminarLinea(item, index) {
                if (this.form.proforma.length >= 1) {
                    this.form.proforma.splice(index, 1);

                } else {
                    this.form.proforma = [];
                }
            },
            onDateSelect2(date) {
                //console.log(date); //
                this.form.proximo_contacto = moment(date).format("YYYY-MM-DD"); //
            },
            seleccionarEmpleado(e) {
                const empleadoP = e.target.value;
                var self = this;
                self.trabajador = empleadoP;
                self.id_trabajador = self.trabajador.id_trabajador;
            },
        },
        mounted() {
            this.nueva();
            //this.obtenerSeguimiento();
        }
    }
</script>
<style lang="scss">
    @import "src/@core/scss/vue/libs/vue-select";
    .btn-agregar {
        margin-top: 1.6rem;
    }
</style>
