<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-4 form-group">
                    <label for="name">* Nombre Sucursal:</label>
                    <input type="text" class="form-control" placeholder="Dígite el nombre de la sucursal" v-model="form.descripcion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.descripcion">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
                <div class="col-sm-4 form-group">
                    <label for="name">* Serie de facturación:</label>
                    <input type="text" class="form-control" placeholder="Dígite la serie para facturación" v-model="form.serie">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.serie">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
                <div class="col-sm-4 form-group">
                    <label for="name">* Numeración actual facturación:</label>
                    <input type="text" class="form-control" placeholder="Dígite la serie para facturación" v-model="form.numeracion_facturacion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.numeracion_facturacion">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
                <div class="col-sm-4 form-group">
                    <label for="name">* Télefonos:</label>
                    <input type="text" class="form-control" placeholder="Dígite los teléfonos de la sucursal" v-model="form.telefonos">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.telefonos">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
                <div class="col-sm-4 form-group">
                    <label for="name">* Dirección:</label>
                    <input type="text" class="form-control" placeholder="Dígite la dirección de la sucursal" v-model="form.direcciones">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.direcciones">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
                <div class="col-sm-4 form-group">
                    <label for="name">* No. Autorización:</label>
                    <input type="text" class="form-control" placeholder="Dígite el número de autorización" v-model="form.numero_autorizacion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.numero_autorizacion">{{ message }}</li>
                        </ul>
                    </b-alert>

                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'admon-sucursales'}">
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
    import sucursal from "../../../api/admon/sucursales";
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
                form: {
                    descripcion: '',
                    serie:'',
                    telefonos:'',
                    direcciones:'',
                    numero_autorizacion:'',
                    numeracion_facturacion:0
                },
                btnAction: 'Registrar',
                errorMessages: [],

            }
        },
        methods: {

            registrar(){
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                sucursal.registrar(self.form, data =>{
                        self.loading = false;
                        this.$toast({
                            component : ToastificationContent,
                            props: {
                                title : 'Notificación',
                                icon : 'CheckIcon',
                                text : 'Datos registrados correctamente.',
                                variant : 'success',
                            }
                        },{
                            position : 'bottom-right'
                        });
                        this.$router.push({
                            name : 'admon-sucursales'
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
                            name : 'admon-sucursales'
                        })*/
                    })
            },


        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
