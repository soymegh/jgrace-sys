<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Descripción Periodo</label>
                    <input class="form-control" placeholder="Dígite la descripcion del periodo"
                           v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.descripcion"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Periodo</label>
                    <input class="form-control" type="number" v-model="form.periodo"
                           placeholder="Dígite la descripcion del periodo">

                    <ul class="error-messages">
                        <li v-for="message in errorMessages.periodo" :key="message"
                            v-text="message"></li>
                    </ul>
                </div>
            </div>

            <!--<div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Salario Mensual Techo</label>
                    <input class="form-control" type="number" v-model="form.salario_mensual_techo" placeholder="Costo Estándar">
                    <ul class="error-messages">
                        <li v-for="message in errorMessages.salario_mensual_techo" :key="message" v-text="message"></li>
                    </ul>
                </div>
                </div>

                <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Porcentaje INSS</label>
                    <input class="form-control" type="number" v-model="form.porcentaje_inss_base" placeholder="Costo Estándar">
                    <ul class="error-messages">
                        <li v-for="message in errorMessages.porcentaje_inss_base" :key="message" v-text="message"></li>
                    </ul>
                </div>
                </div>


                <div class="col-sm-3">
                <div class="form-group">
                    <label for=""> Porcentaje IR</label>
                    <input class="form-control" type="number" v-model="form.porcentaje_ir_base" placeholder="Costo Estándar">
                    <ul class="error-messages">
                        <li v-for="message in errorMessages.porcentaje_ir_base" :key="message" v-text="message"></li>
                    </ul>
                </div>
                </div>-->

            <div class="col-sm-3">
                <div class="form-group">
                    <label for="">Estado</label>
                    <select class="form-control" v-model="form.estado">
                        <option value="0">En progreso</option>
                        <option value="1">Completado</option>
                    </select>
                    <ul class="error-messages">
                        <li v-for="message in errorMessages.estado" :key="message"
                            v-text="message"></li>
                    </ul>
                </div>
            </div>
        </b-row>
        <b-card-footer>
            <div class="content-box-footer text-right">
                <router-link class="mx-1"  :to="{ name: 'contabilidad-periodos-contables' }">
                    <b-button  variant="secondary">Cancelar</b-button>
                </router-link>
                <b-button  variant="primary" @click="registrar"
                           :disabled="btnAction != 'Registrar' ? true : false">{{ btnAction }}
                </b-button>
            </div>
            <template v-if="loading">
                <BlockUI  :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>

</template>

<script type="text/ecmascript-6">
    import {BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,VBTooltip,BFormCheckbox, BFormGroup} from 'bootstrap-vue'
    import periodo from '../../../api/contabilidad/periodos'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

    export default {
        components:{
            BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton,BFormCheckbox,BFormGroup
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: false,
                url: loadingImage,   //It is important to import the loading image then use here
                form: {
                    descripcion: '',
                    periodo: 2022,
                    estado:1,
                    /*	salario_mensual_techo: 0,
                        porcentaje_inss_base: 0,
                        porcentaje_ir_base: 0,*/
                },
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods: {

            registrar() {
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                periodo.registrar(self.form, data => {
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
                    this.$router.push({name: 'contabilidad-periodos-contables'})
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
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                })
            }
        },
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
