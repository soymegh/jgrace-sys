<template>
    <b-card>
        <form>
            <b-row>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Cuenta Padre</label>
                        <div class="form-group">
                            <input class="form-control" disabled placeholder="Cuenta Padre" v-model="form.nombre_padre">
                        </div>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.nombre_padre" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for=""> Código cuenta padre</label>
                        <input class="form-control" disabled placeholder="Código heredado" v-model="cta_contable">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.cta_contable" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="">Código nueva Cuenta</label>
                        <input disabled :maxlength="3"
                               @change="form.codigo_cuenta = Math.max(Math.min(Math.round(form.codigo_cuenta), form.nivel_cuenta.codigo_maximo), 1)"
                               class="form-control" placeholder="Dígite código único de cuenta" type="number"
                               v-model="form.codigo_cuenta">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.codigo_cuenta" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>


                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="codigo_nuevo"> Nuevo Código</label>
                        <input id="codigo_nuevo" v-model="form.cta_contable" class="form-control" placeholder="Código concatenado"
                               >
                        <!--<input id="codigo_nuevo" :value="(form.nivel_cuenta.id_nivel_cuenta === 2) ? niv1+form.codigo_cuenta+niv3+niv4+'-'+niv5+'-'+niv6+niv7 :
												(form.nivel_cuenta.id_nivel_cuenta === 3)? niv1+niv2+form.codigo_cuenta+niv4+'-'+niv5+'-'+niv6+niv7  :
												(form.nivel_cuenta.id_nivel_cuenta === 4)? niv1+niv2+niv3+form.codigo_cuenta+'-'+niv5+'-'+niv6+niv7  :
												(form.nivel_cuenta.id_nivel_cuenta === 5)? niv1+niv2+niv3+niv4+'-'+(form.codigo_cuenta < 10 && form.codigo_cuenta > 0 ? '0'+form.codigo_cuenta : form.codigo_cuenta )+'-'+niv6+niv7  :
												(form.nivel_cuenta.id_nivel_cuenta === 6)? niv1+niv2+niv3+niv4+'-'+niv5+'-'+((form.codigo_cuenta < 10 && form.codigo_cuenta > 0) ? '00'+form.codigo_cuenta :(form.codigo_cuenta < 100 && form.codigo_cuenta > 10) ? '0'+form.codigo_cuenta: form.codigo_cuenta )+niv7
												 :''" class="form-control" placeholder="Código concatenado"
                               >-->
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.cta_contable" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <!--<div class="col-sm-2">
        <div class="form-group">
            <label for=""> Nuevo Código</label>
            <input disabled class="form-control" placeholder="Código concatenado" :value="cta_contable+ (form.codigo_cuenta < 10 && form.codigo_cuenta > 0  && form.nivel_cuenta.id_nivel_cuenta > 2 ? '0'+form.codigo_cuenta : form.codigo_cuenta )">
            <ul class="error-messages">
                <li v-for="message in errorMessages.cta_contable" :key="message" v-text="message"></li>
            </ul>
        </div>
        </div>-->

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for=""> Nombre cuenta</label>
                        <input class="form-control" placeholder="Dígite Nombre de la cuenta"
                               v-model="form.nombre_cuenta">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.nombre_cuenta" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="check-label2"><input type="checkbox"
                                                           v-model="form.permite_movimiento"> Cuenta permite movimientos</label>
                    </div>
                </div>
            </b-row>

            <div>
                <b-alert variant="success" show>
                    <div class="alert-body">
                        <span>Las cuentas que permiten movimientos se utilizan directamente en la creación de documentos contables, las cuentas que no permiten movimientos (cuentas tipo SUMA) son las que solamente reflejan la sumatoria de las subcuentas que posean</span>
                    </div>
                </b-alert>

                <hr>
            </div>

        </form>
        <div class="content-box-footer text-right">
            <router-link :to="{ name: 'contabilidad-catalogos-contables' }" >
                <b-button variant="secondary" type="button" class="mx-lg-1">Cancelar</b-button>
            </router-link>
            <b-button :disabled="btnAction !== 'Registrar'" @click="guardarCuentaContable"
                      variant="primary" type="button" class="mx-lg-1">{{ btnAction }}
            </b-button>
        </div>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script>
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BButton, BAlert, BFormCheckbox, BFormSelect, BCard, BCardFooter, BRow} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import cuentas_contables from "../../../api/contabilidad/cuentas-contables.js";
    import nivel_cuenta from "../../../api/contabilidad/niveles_cuentas";
    import axios from "axios";

    export default {
        components: {
            BButton,
            BAlert,
            BFormCheckbox,
            vSelect,
            BFormSelect,
            BCard,
            BCardFooter,
            BRow,
        },
        data() {
            return {
                loading: false,
                msg: 'Guardando los datos, espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                cuentasBusqueda: {},
                cuentasBusquedaURL: '/contabilidad/contabilidad-catalogos-contables/buscar',
                cta_contable: '',
                niv1: "",
                niv2: "",
                niv3: "",
                niv4: "",
                niv5: "",
                niv6: "",
                niv7: "",
                cuentas_contables: [],
                niveles_cuenta: [],
                tipos_cuenta: [],
                form: {
                    nombre_cuenta: "",
                    codigo_cuenta: "",
                    cta_contable: "",
                    cuenta_padre: {},
                    tipo_cuenta: "",
                    nivel_cuenta: "",
                    permite_movimiento: 0

                },
                btnAction: 'Registrar',
                errorMessages: [],

            }
        },
        computed: {},
        methods: {

            obtenerNivelCuenta() {
                var self = this
                nivel_cuenta.obtenerNivelCuenta({
                    id_nivel_cuenta: self.id_nivel_cuenta_n
                }, data => {
                    self.loading=false;
                    self.form.nivel_cuenta = data
                }, err => {
                    self.loading=false;
                    alertify.error(err.id_nivel_cuenta[0],5);
                })
            },

            obtenerCuentaContablePadre() {
                const self = this;
                let padre = '';
                cuentas_contables.obtenerCuentaContableV(
                    {
                        id_cuenta_contable: this.$route.params.id_cuenta_contable_padre
                    },
                    data => {
                        self.form.cuenta_padre = data;
                        padre = data;
                        self.form.nombre_padre=self.form.cuenta_padre.nombre_cuenta;
                        self.cta_contable = self.form.cuenta_padre.cta_contable;
                        self.niv1 = self.form.cuenta_padre.niv1;
                        self.niv2 = self.form.cuenta_padre.niv2;
                        self.niv3 = self.form.cuenta_padre.niv3;
                        self.niv4 = self.form.cuenta_padre.niv4;
                        self.niv5 = self.form.cuenta_padre.niv5;
                        self.niv6 = self.form.cuenta_padre.niv6;
                        self.niv7 = self.form.cuenta_padre.niv7;
                        self.form.tipo_cuenta = self.form.cuenta_padre.cuenta_tipo;
                        self.form.codigo_cuenta = self.form.cuenta_padre.codigo_siguiente;
                        self.id_nivel_cuenta_n=Number(self.form.cuenta_padre.id_nivel_cuenta)+1;
                        //Number(form.cuenta_padre.cuenta_nivel.id_nivel_cuenta)+1
                        this.obtenerNivelCuenta();

                    },
                    err => {
                        //console.log(err);
                        this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'CheckIcon',
                                text: 'Ha ocurrido un error. ' + err,
                                variant: 'success',
                                position: 'bottom-right'
                            }
                        }, {
                            position: 'bottom-right'
                        });
                        this.$router.push({
                            name: "contabilidad-catalogos-contables"
                        });
                    }
                );

            },

            guardarCuentaContable() {
                const self = this;
                self.btnAction = 'Registrando, espere....';

/*                self.form.cta_contable =
                    (self.form.nivel_cuenta.id_nivel_cuenta === 2) ? self.niv1+self.form.codigo_cuenta+self.niv3+self.niv4+'-'+self.niv5+'-'+self.niv6+self.niv7 :
                        (self.form.nivel_cuenta.id_nivel_cuenta === 3)? self.niv1+self.niv2+self.form.codigo_cuenta+self.niv4+'-'+self.niv5+'-'+self.niv6+self.niv7  :
                            (self.form.nivel_cuenta.id_nivel_cuenta === 4)? self.niv1+self.niv2+self.niv3+self.form.codigo_cuenta+'-'+self.niv5+'-'+self.niv6+self.niv7  :
                                (self.form.nivel_cuenta.id_nivel_cuenta === 5)? self.niv1+self.niv2+self.niv3+self.niv4+'-'+(self.form.codigo_cuenta < 10 && self.form.codigo_cuenta > 0 ? '0'+self.form.codigo_cuenta : self.form.codigo_cuenta )+'-'+self.niv6+self.niv7  :
                                    (self.form.nivel_cuenta.id_nivel_cuenta === 6)? self.niv1+self.niv2+self.niv3+self.niv4+'-'+self.niv5+'-'+((self.form.codigo_cuenta < 10 && self.form.codigo_cuenta > 0) ? '00'+self.form.codigo_cuenta :(self.form.codigo_cuenta < 100 && self.form.codigo_cuenta > 10) ? '0'+self.form.codigo_cuenta: self.form.codigo_cuenta )+self.niv7
                                        :'';*/

                cuentas_contables.guardarCuentaContable(self.form, data => {
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'CheckIcon',
                            text: `Cuenta contable registrada correctamente`,
                            variant: 'success',
                            position: 'bottom-right'
                        }
                    }, {
                        position: 'bottom-right'
                    });
                    this.$router.push({
                        name: 'contabilidad-catalogos-contables'
                    })
                }, err => {
                    self.errorMessages = err;
                    self.btnAction = 'Registrar'
                })
            },

            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode === 46) {
                    evt.preventDefault();
                } else {
                    return true;
                }
            }
        },
        mounted() {
            this.obtenerCuentaContablePadre();

        }
    }
</script>

<style lang="scss">
    @import 'src/@core/scss/vue/libs/vue-select';
</style>
