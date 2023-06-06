<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Tipo Centro:</label>
                        <b-form-select
                                @change="cambiarTipo" class="form-group" v-model.number="form.tipo_centro"
                                label = "descripcion"

                        >
                            <option value="1">Centro de Ingresos</option>
                            <option value="2">Centro de Costos</option>
                        </b-form-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.tipo_centro">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name"> Clasificación Contable:</label>
                        <b-form-select :disabled="form.tipo_centro!==2" class="form-group" v-model.number="form.clasificacion_contable">
                            <option :disabled="form.tipo_centro===2" value="0">N/A</option>
                            <option value="1">Comercialización</option>
                            <option value="2">Administración</option>
                            <option value="3">Empleado</option>
                        </b-form-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.clasificacion_contable">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Ubicación:</label>
                        <b-form-select
                                class="form-group" v-model.number="form.ubicacion"
                                label = "descripcion"
                        >
                            <option value="1">Managua</option>
                            <option value="2">Foráneo</option>
                        </b-form-select>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.ubicacion">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Descripción:</label>
                        <input type="text" class="form-control" v-model="form.descripcion"
                               placeholder="Digite la descripción del centro de costo">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'contabilidad-centros-costos'}">
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
    import {BButton,BAlert,BFormCheckbox,BFormSelect,BCard,BCardFooter} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import centro_costo from "../../../api/contabilidad/centro-costo";

    export default {
        components:{
            BButton,
            BAlert,
            BFormCheckbox,
            vSelect,
            BFormSelect,
            BCard,
            BCardFooter
        },
        data() {
            return {
                loading:false,
                msg: 'Guardando los datos, espere un momento',
                url : loadingImage,   //It is important to import the loading image then use here
                form: {
                    descripcion : '',
                    tipo_centro : 1,
                    ubicacion : 1,
                    clasificacion_contable : 0
                },
                btnAction: 'Registrar',
                errorMessages: [],

            }
        },
        methods: {

            cambiarTipo(){
              let self = this;
              if(self.form.tipo_centro === 1){
                  self.form.clasificacion_contable = 0;
              }else{
                  self.form.clasificacion_contable = 1;
              }
            },

            registrar() {
                var self = this;
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                centro_costo.registrar(self.form, data => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Datos Registrados correctamente',
                            variant: 'success',
                            position: 'top-center'
                        }
                    },
                        {
                            position : 'bottom-right'
                        });
                    this.$router.push({name: 'contabilidad-centros-costos'})
                }, err => {
                    self.loading = false;
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'BellIcon',
                            text: 'Ha ocurrido un error',
                            variant: 'warning',
                            position: 'top-center'
                        }
                    },
                    {
                        position : 'bottom-right'
                    });
                    this.$router.push({
                        name : 'contabilidad-centros-costos'
                    })
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                })
            }
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
