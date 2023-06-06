<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name">* Nivel de Cuenta:</label>
                        <b-form-input disabled class="form-group" v-model="form.cuenta_nivel.descripcion"
                               placeholder="Nivel de cuenta"
                        />


                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name">* Cuenta Padre:</label>
                        <b-form-input disabled class="form-group" v-model="form.cuenta_padre.nombre_cuenta_completo"
                        placeholder="Nombre cuenta padre"
                        />

                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="name">* Código Cuenta Padre:</label>
                        <b-form-input disabled type="text" class="form-control" v-model="form.cuenta_padre.cta_contable"
                               placeholder="Código Heredado"/>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="name">* Código nueva Cuenta</label>
                        <b-form-input disabled :maxlength="2" class="form-control"
                               @change="form.codigo_cuenta = Math.max(Math.min(Math.round(form.codigo_cuenta), form.cuenta_nivel.codigo_maximo), 1)"
                               type="number" v-model="form.codigo_cuenta"
                               placeholder="Dígite código único de cuenta"/>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.codigo_cuenta" :key="message"
                                v-text="message"></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="name">* Nuevo Código</label>
                        <b-form-input readonly class="form-control" placeholder="Código concatenado"
                               :value="(form.cuenta_nivel.id_nivel_cuenta === 2) ? niv1+form.codigo_cuenta+niv3+niv4+'-'+niv5+'-'+niv6+niv7 :
												(form.cuenta_nivel.id_nivel_cuenta === 3)? niv1+niv2+form.codigo_cuenta+niv4+'-'+niv5+'-'+niv6+niv7  :
												(form.cuenta_nivel.id_nivel_cuenta === 4)? niv1+niv2+niv3+form.codigo_cuenta+'-'+niv5+'-'+niv6+niv7  :
												(form.cuenta_nivel.id_nivel_cuenta === 5)? niv1+niv2+niv3+niv4+'-'+(form.codigo_cuenta < 10 && form.codigo_cuenta > 0 ? '0'+form.codigo_cuenta : form.codigo_cuenta )+'-'+niv6+niv7  :
												(form.cuenta_nivel.id_nivel_cuenta === 6)? niv1+niv2+niv3+niv4+'-'+niv5+'-'+((form.codigo_cuenta < 10 && form.codigo_cuenta > 0) ? '00'+form.codigo_cuenta :(form.codigo_cuenta < 100 && form.codigo_cuenta > 10) ? '0'+form.codigo_cuenta: form.codigo_cuenta )+niv7
												 :''"/>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Nombre cuenta</label>
                        <b-form-input class="form-control" v-model="form.nombre_cuenta"
                               placeholder="Dígite Nombre de la cuenta"/>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.nombre_cuenta" :key="message"
                                v-text="message"></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 form-inline mt-md-1">
                    <div class="form-group">
                        <label class="check-label2"><input v-model="form.permite_movimiento" type="checkbox" class="mx-sm-1">  Cuenta permite movimientos</label>
                    </div>
                </div>
            </div>
            <b-alert variant="success" class="mt-2 mb-2" show>
                <div class="alert-body">
                    <span>Las cuentas que permiten movimientos se utilizan directamente en la creación de documentos contables, las cuentas que no permiten movimientos (cuentas tipo SUMA) son las que solamente reflejan la sumatoria de las subcuentas que posean.</span>
                </div>

            </b-alert>

        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'contabilidad-catalogos-contables'}">
                <b-button type="submit" variant="secondary" class="mx-1">
                    Cancelar
                </b-button>
            </router-link>


            <b-button type="submit" variant="primary" @click="actualizarCuentaContable" :disabled="btnAction !== 'Actualizar'">
                {{btnAction}}
            </b-button>


        </b-card-footer>
        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-card>
</template>

<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BButton,BAlert,BFormCheckbox,BFormSelect,BCard,BCardFooter,BFormInput} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import cuentas_contables from "../../../api/contabilidad/cuentas-contables";

    export default {
        components:{
            BButton,
            BAlert,
            BFormCheckbox,
            vSelect,
            BFormSelect,
            BCard,
            BCardFooter,
            BFormInput
        },
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                cta_contable: '',
                niv1: "",
                niv2: "",
                niv3: "",
                niv4: "",
                niv5: "",
                niv6: "",
                niv7: "",
                cuentas_contables: [],
                //	anexos:[],
                tipos_cuenta: [],
                form: {
                    codigo_auxiliar:'No requiere',
                    centros_costo:[],
                    centros_ingreso:[],
                    centro_costo:[],
                    centro_ingreso:[],
                    nombre_cuenta: '',
                    codigo_cuenta: '',

                    cuenta_padre: '',
                    cuenta_nivel: '',
                    cta_contable: "",
                    permite_movimiento: 0,
                    requiere_aux : 0,
                },
                btnAction: 'Actualizar',
                errorMessages: [],

            }
        },
        methods: {

            obtenerCuentaContable(){
                var self = this;
                this.loading=true;
                if ([1,49,92,99,108].indexOf(Number(this.$route.params.id_cuenta_contable)) < 0) {
                    cuentas_contables.obtenerCuentaContable(
                        {
                            id_cuenta_contable: this.$route.params.id_cuenta_contable
                        },data =>{
                        self.form = data;
                        self.niv1 = self.form.cuenta_padre.niv1;
                        self.niv2 = self.form.cuenta_padre.niv2;
                        self.niv3 = self.form.cuenta_padre.niv3;
                        self.niv4 = self.form.cuenta_padre.niv4;
                        self.niv5 = self.form.cuenta_padre.niv5;
                        self.niv6 = self.form.cuenta_padre.niv6;
                        self.niv7 = self.form.cuenta_padre.niv7;
                        self.loading = false;
                    },err=>{
                        self.loading = false;
                        // console.log(err);
                        this.$toast({
                            component : ToastificationContent,
                            props : {
                                title : 'Notificación',
                                icon : 'InfoIcon',
                                text : 'Ha ocurrido un error al cargar los datos'+ err,
                                variant : 'warning',
                                position : 'bottom-rigth'
                            }
                        },{
                            position:'bottom-right'
                        })
                        this.$router.push({
                            name : 'contabilidad-catalogos-contables'
                        })
                    })
                } else{
                    this.$toast({
                        component : ToastificationContent,
                        props : {
                            title: 'Notificacion',
                            icon : 'AlertCircleIcon',
                            variant : 'warning',
                            text : 'Este dato se encuentra protegido, contacte a los desarrolladores realizar este cambio',
                            position : 'bottom-rigth'
                        }
                    },{
                        position:'bottom-right'
                    })
                    this.$router.push({
                        name : 'contabilidad-catalogos-contables'
                    })
                }
            },
            actualizarCuentaContable() {
                var self = this;
                self.btnAction = "Guardando, espere....";
                this.loading = true;


                self.form.cta_contable =
                    (self.form.cuenta_nivel.id_nivel_cuenta === 2) ? self.niv1 + self.form.codigo_cuenta + self.niv3 + self.niv4 + '-' + self.niv5 + '-' + self.niv6 + self.niv7 :
                        (self.form.cuenta_nivel.id_nivel_cuenta === 3) ? self.niv1 + self.niv2 + self.form.codigo_cuenta + self.niv4 + '-' + self.niv5 + '-' + self.niv6 + self.niv7 :
                            (self.form.cuenta_nivel.id_nivel_cuenta === 4) ? self.niv1 + self.niv2 + self.niv3 + self.form.codigo_cuenta + '-' + self.niv5 + '-' + self.niv6 + self.niv7 :
                                (self.form.cuenta_nivel.id_nivel_cuenta === 5) ? self.niv1 + self.niv2 + self.niv3 + self.niv4 + '-' + (self.form.codigo_cuenta < 10 && self.form.codigo_cuenta > 0 ? '0' + self.form.codigo_cuenta : self.form.codigo_cuenta) + '-' + self.niv6 + self.niv7 :
                                    (self.form.cuenta_nivel.id_nivel_cuenta === 6) ? self.niv1 + self.niv2 + self.niv3 + self.niv4 + '-' + self.niv5 + '-' + ((self.form.codigo_cuenta < 10 && self.form.codigo_cuenta > 0) ? '00' + self.form.codigo_cuenta : (self.form.codigo_cuenta < 100 && self.form.codigo_cuenta > 10) ? '0' + self.form.codigo_cuenta : self.form.codigo_cuenta) + self.niv7
                                        : '';
                cuentas_contables.actualizarCuentaContable(
                    self.form,
                    data => {
                        self.loading = false;
                        this.$toast({
                            component : ToastificationContent,
                            props : {
                                title : 'Notificación',
                                icon : 'CheckIcon',
                                text : 'Cuenta contable actualizada correctamente',
                                variant : 'success',
                            }
                        },{
                            position:'bottom-right'
                        });
                        this.$router.push({
                            name: "contabilidad-catalogos-contables"
                        });
                    },
                    err => {
                        this.loading = false;
                        self.errorMessages = err;
                        this.$toast({
                            component : ToastificationContent,
                            props : {
                                title : 'Notificación',
                                icon : 'InfoIcon',
                                text : 'Ha ocurrido un problema al actualizar la cuenta contable',
                                variant : 'danger',
                                position : 'bottom-rigth'
                            }
                        },{
                            position:'bottom-right'
                        })
                        this.$router.push({
                            name : 'contabilidad-catalogos-contables'
                        })
                        self.btnAction = "Actualizar";
                    }
                );
            },
            desactivarCuentaContable(cuentaId) {
                var self = this
                if ([1, 2, 3, 4, 5, 225].indexOf(cuentaId) < 0) {
                    cuentas_contables.desactivarCuentaContable(
                        {
                            id_cuenta_contable: cuentaId
                        },
                        data => {
                            this.$toast({
                                component : ToastificationContent,
                                props : {
                                    title : 'Notificación',
                                    icon : 'BellIcon',
                                    text : 'Cuenta contable desactivada correctamente',
                                    variant : 'succsess',
                                    position : 'bottom-rigth'
                                }
                            })
                            this.$router.push({
                                name: "contabilidad-catalogos-contables"
                            });
                        },
                        err => {
                            console.log(err);
                            this.$toast({
                                component : ToastificationContent,
                                props : {
                                    title : 'Notificación',
                                    icon : 'InfoIcon',
                                    text : 'Ha ocurrido un error al desactivar la cuenta contable',
                                    variant : 'warning',
                                    position : 'bottom-rigth'
                                }
                            })
                            this.$router.push({
                                name : 'contabilidad-catalogos-contables'
                            })
                        }
                    )
                } else {
                    this.$toast({
                        component : ToastificationContent,
                        props : {
                            title: 'Notificacion',
                            icon : 'AlertCircleIcon',
                            variant : 'warning',
                            text : 'Este dato se encuentra protegido, contacte a los desarrolladores realizar este cambio',
                            position : 'bottom-rigth'
                        }
                    })
                    this.$router.push({
                        name : 'contabilidad-catalogos-contables'
                    })
                }
            },
            activarCuentaContable(cuentaId) {
                var self = this;
                var self = this
                if ([1, 2, 3, 4, 5, 225].indexOf(cuentaId) < 0) {
                    cuentas_contables.activarCuentaContable(
                        {
                            id_cuenta_contable: cuentaId
                        },
                        data => {
                            this.$toast({
                                component : ToastificationContent,
                                props : {
                                    title : 'Notificación',
                                    icon : 'BellIcon',
                                    text : 'Cuenta contable activada correctamente',
                                    variant : 'success',
                                    position : 'bottom-rigth'
                                }
                            })
                            this.$router.push({
                                name: "contabilidad-catalogos-contables"
                            });
                        },
                        err => {
                            console.log(err);
                            this.$toast({
                                component : ToastificationContent,
                                props : {
                                    title : 'Notificación',
                                    icon : 'InfoIcon',
                                    text : 'Ha ocurrido un error al activar la cuenta contable',
                                    variant : 'warning',
                                    position : 'bottom-rigth'
                                }
                            })
                            this.$router.push({
                                name : 'contabilidad-catalogos-contables'
                            })
                        }
                    )
                } else {
                    this.$toast({
                        component : ToastificationContent,
                        props : {
                            title: 'Notificacion',
                            icon : 'AlertCircleIcon',
                            variant : 'warning',
                            text : 'Este dato se encuentra protegido, contacte a los desarrolladores realizar este cambio',
                            position : 'bottom-rigth'
                        }
                    })
                    this.$router.push({
                        name: "contabilidad-catalogos-contables"
                    });
                }
            },

        },
        mounted() {
            this.obtenerCuentaContable();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
