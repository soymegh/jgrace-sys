<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Tipo Cuenta:</label>
                        <select
                                class="form-control" v-model.number="form.tipo_cuenta"
                        >
                            <option value="1">Cuenta Corriente</option>
                            <option value="2">Déposito a plazos</option>
                            <option value="3">Cuenta de Ahorro</option>
                        </select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.tipo_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Banco:</label>
                        <v-select style="width: 100%" v-model="form.banco" :options="bancos" label="descripcion">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.banco">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Moneda:</label>
                        <v-select style="width: 100%" v-model="form.moneda" :options="monedas" label="descripcion">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.moneda">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Número Cuenta:</label>
                        <input type="text" class="form-control" v-model="form.numero_cuenta" placeholder="Dígite el número de cuenta">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.numero_cuenta">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Número Cuenta:</label>
                        <input type="number" min="0" class="form-control" v-model="form.numeracion_chequera" placeholder="Dígite la numeracion actual de la chequera">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.numeracion_chequera">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="name">* Cuenta Contable:</label>
                        <v-select style="width: 100%" v-model="form.cuenta_contable" :options="cuentas_contables" label="nombre_cuenta_completo">
                        </v-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.cuenta_contable">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'contabilidad-cuentas-bancarias'}">
                <b-button type="submit" variant="secondary" class="mx-1">
                    Cancelar
                </b-button>
            </router-link>


            <b-button type="submit" variant="primary" @click="registrar" :disabled="btnAction !== 'Registrar'">
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
    import cuenta_bancaria from "../../../api/contabilidad/cuentas-bancarias";
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
                monedas : [],
                cuentas_contables : [],
                bancos : [],
                form: {
                    numeracion_chequera : 0,
                    numero_cuenta : '',
                    nombre_cuenta_bancaria: '',
                    tipo_cuenta:  1,
                    cuenta_contable: ''
                },
                btnAction: 'Registrar',
                errorMessages: [],

            }
        },

        methods: {

            nueva(){
              var self = this;
              cuenta_bancaria.nueva({
              }, data =>{
                  self.monedas = data.monedas;
                  self.cuentas_contables = data.cuentas_contables;
                  self.bancos = data.bancos;
                  self.loading = false;
              }, err =>{
                  console.log(err);
                  this.$toast({
                          component: ToastificationContent,
                          props: {
                              title : 'Notificación',
                              icon : 'InfoIcon',
                              text : 'Ha ocurrido un error al cargar los datos, intentelo de nuevo',
                              variant : 'warning',

                          }
                      },
                      {
                          position : 'bottom-right'
                      });
                  /*this.$router.push({
                      name : 'contabilidad-cuentas-bancarias'
                  });*/
              })
            },

            registrar(){
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                cuenta_bancaria.registrar(self.form, data =>{
                        self.loading = false;
                        this.$toast({
                                component : ToastificationContent,
                                props: {
                                    title : 'Notificación',
                                    icon : 'CheckIcon',
                                    text : 'Datos registrados correctamente.',
                                    variant : 'success',
                                }
                            },
                            {
                                position : 'bottom-right'
                            });
                        this.$router.push({
                            name : 'contabilidad-cuentas-bancarias'
                        })
                    },
                    err =>{
                        self.loading = false;
                        self.errorMessages = err
                        self.btnAction = 'Registrar'
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title : 'Notificación',
                                    icon : 'InfoIcon',
                                    text : 'Ha ocurrido un error, intentelo de nuevo',
                                    variant : 'warning',

                                }
                            },
                            {
                                position : 'bottom-right'
                            });
                        /*this.$router.push({
                            name : 'inventario-tipos-proveedores'
                        })*/
                    })
            },
            onSearch(paginate) {
                this.search = paginate.paginated()
                this.offset = 10
            },
        },
        mounted() {
            this.nueva();
        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';

    .pagination {
        display: flex;
        margin: 0.25rem 0.25rem 0;
    }
    .pagination button {
        flex-grow: 1;
    }
    .pagination button:hover {
        cursor: pointer;
    }

</style>
