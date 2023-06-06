<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name">* Nivel de Cuenta:</label>
                        <v-select v-model="form.nivel_cuenta"
                                       label="descripcion"
                                       :options="niveles_cuenta"

                        >
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.nivel_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name">* Cuenta Padre:</label>
                        <v-select style="width: 100%;" v-model="form.cuenta_padre" :filterable="false"
                                  @search="onSearch" @input="seleccionarCuentaPadre" :options="cuentas_contables" label="text" > <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select-->
                            <template slot="no-options">
                                Escriba para buscar una cuenta contable
                            </template>
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.nivel_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

            </div>
            <b-row>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="name">* Código Cuenta Padre:</label>
                        <input type="text" class="form-control" v-model="cta_contable"
                               placeholder="Código Heredado">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.cta_contable">{{ message }}</li>
                            </ul>
                        </b-alert>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="name">* Código nueva Cuenta</label>
                        <input class="form-control"
                               @change="form.codigo_cuenta = Math.max(Math.min(Math.round(form.codigo_cuenta), form.nivel_cuenta.codigo_maximo), 1)"
                               type="number" v-model="form.codigo_cuenta" :maxlength="3"
                               placeholder="Dígite código único de cuenta">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.codigo_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="name">* Nuevo Código</label>
                        <input readonly class="form-control" placeholder="Código concatenado"
                               :value="(form.nivel_cuenta.id_nivel_cuenta === 2) ? niv1+form.codigo_cuenta+niv3+niv4+'-'+niv5+'-'+niv6+niv7 :
												(form.nivel_cuenta.id_nivel_cuenta === 3)? niv1+niv2+form.codigo_cuenta+niv4+'-'+niv5+'-'+niv6+niv7  :
												(form.nivel_cuenta.id_nivel_cuenta === 4)? niv1+niv2+niv3+form.codigo_cuenta+'-'+niv5+'-'+niv6+niv7  :
												(form.nivel_cuenta.id_nivel_cuenta === 5)? niv1+niv2+niv3+niv4+'-'+(form.codigo_cuenta < 10 && form.codigo_cuenta > 0 ? '0'+form.codigo_cuenta : form.codigo_cuenta )+'-'+niv6+niv7  :
												(form.nivel_cuenta.id_nivel_cuenta === 6)? niv1+niv2+niv3+niv4+'-'+niv5+'-'+((form.codigo_cuenta < 10 && form.codigo_cuenta > 0) ? '00'+form.codigo_cuenta :(form.codigo_cuenta < 100 && form.codigo_cuenta > 10) ? '0'+form.codigo_cuenta: form.codigo_cuenta )+niv7
												 :''">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.codigo_nuevo">{{ message }}</li>
                            </ul>
                        </b-alert>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="name">* Nombre cuenta</label>
                        <input class="form-control" v-model="form.nombre_cuenta"
                               placeholder="Dígite Nombre de la cuenta">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.nombre_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>
                    </div>
                </div>

                <div class="col-lg-3 form-inline mt-md-1">
                    <div class="form-group">
                        <label class="check-label2"><input v-model="form.permite_movimiento" type="checkbox" class="mx-sm-1">  Cuenta permite movimientos</label>
                    </div>
                </div>
            </b-row>
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


            <b-button type="submit" variant="primary" @click="guardarCuentaContable" :disabled="btnAction !== 'Registrar'">
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
    import {BButton,BAlert,BFormCheckbox,BFormSelect,BCard,BCardFooter, BRow} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import cuentas_contables from "../../../api/contabilidad/cuentas-contables";
    import nivel_cuenta from "../../../api/contabilidad/niveles_cuentas";
    import axios from 'axios'

    export default {
        components:{
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
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                cuentasBusqueda: {},
                cuentasBusquedaURL: '/contabilidad/cuentas-contables/buscar',
                cta_contable: '',
                niv1: "",
                niv2: "",
                niv3: "",
                niv4: "",
                niv5: "",
                niv6: "",
                niv7: "",
                cuentas_contables : [],
                niveles_cuenta : [],
                tipos_cuenta : [],
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
        computed:{

        },
        methods: {
            onSearch(search,loading){
                if (search.length){
                    loading(true)
                    this.search(loading,search,this);
                }

            },
            select(e) {
                this.$emit('input', {
                    target: {
                        value: result
                    }
                })
                this.onEsc()
            },
            search: _.debounce((loading,search,vm) => {
                let self = this;
                axios.post(`/contabilidad/cuentas-contables/buscar?q=${escape(search)}&id_nivel_cuenta=${vm.form.nivel_cuenta.id_nivel_cuenta}`).then((res)=>{
                    vm.options = res.data.results;
                     vm.cuentas_contables = res.data.results;
                    loading(false);
                })
            },250),

            seleccionarCuentaPadre(e){
              var self = this;
              //self.form.cuenta_padre = cuentaP;
                self.form.tipo_cuenta = '';
                self.cta_contable = '';
                if (self.form.cuenta_padre !== null && self.form.cuenta_padre !== undefined && self.form.cuenta_padre !== '') {
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
                }
            },

            guardarCuentaContable: function () {
                var self = this;
                self.btnAction = 'Registrando, espere....';
                self.loading = true;
                self.form.cta_contable =
                    (self.form.nivel_cuenta.id_nivel_cuenta === 2) ? self.niv1 + self.form.codigo_cuenta + self.niv3 + self.niv4 + '-' + self.niv5 + '-' + self.niv6 + self.niv7 :
                        (self.form.nivel_cuenta.id_nivel_cuenta === 3) ? self.niv1 + self.niv2 + self.form.codigo_cuenta + self.niv4 + '-' + self.niv5 + '-' + self.niv6 + self.niv7 :
                            (self.form.nivel_cuenta.id_nivel_cuenta === 4) ? self.niv1 + self.niv2 + self.niv3 + self.form.codigo_cuenta + '-' + self.niv5 + '-' + self.niv6 + self.niv7 :
                                (self.form.nivel_cuenta.id_nivel_cuenta === 5) ? self.niv1 + self.niv2 + self.niv3 + self.niv4 + '-' + (self.form.codigo_cuenta < 10 && self.form.codigo_cuenta > 0 ? '0' + self.form.codigo_cuenta : self.form.codigo_cuenta) + '-' + self.niv6 + self.niv7 :
                                    (self.form.nivel_cuenta.id_nivel_cuenta === 6) ? self.niv1 + self.niv2 + self.niv3 + self.niv4 + '-' + self.niv5 + '-' + ((self.form.codigo_cuenta < 10 && self.form.codigo_cuenta > 0) ? '00' + self.form.codigo_cuenta : (self.form.codigo_cuenta < 100 && self.form.codigo_cuenta > 10) ? '0' + self.form.codigo_cuenta : self.form.codigo_cuenta) + self.niv7
                                        : '';
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
                    self.loading = false;
                    this.$router.push({
                        name: 'contabilidad-catalogos-contables'
                    })
                }, err => {
                    this.$toast({
                        component: ToastificationContent,
                        props: {
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Ha ocurrido un error, intentelo de nuevo',
                            variant: 'danger',
                            position: 'bottom-right'
                        }
                    }, {
                        position: 'bottom-right'
                    });
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                })
            },

            obtenerTodosNivelesCuenta(){
                var self = this;
                nivel_cuenta.obtenerTodosNivelesCuenta(
                    data => {
                        self.niveles_cuenta = data;
                        self.loading = false;
                    },
                    err => {
                        self.loading = true;
                        console.log(err);
                    }
                );
            },

            isNumber(){
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode === 46) {
                    evt.preventDefault();
                    ;
                } else {
                    return true;
                }

            },


        },
        mounted() {
            this.obtenerTodosNivelesCuenta();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
