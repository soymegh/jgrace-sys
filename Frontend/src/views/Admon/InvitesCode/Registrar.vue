<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="max">* Limite de usos:</label>
                        <input type="number" class="form-control" id="max" v-model.number="form.max" placeholder="Dígite el limite de uso de código">
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.max">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="valid_until">* Fecha de expiración:</label>
                        <input type="date" class="form-control" id="valid_until" v-model="form.valid_until" placeholder="Seleccione la fecha de expiración" required>
                        <b-alert variant="danger" show>
                            <ul class="error-messages">
                                <li v-for="message in errorMessages.valid_until">{{ message }}</li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </div>
        </form>
        <b-card-footer class="content-box-footer text-right mt-1">

            <router-link  :to="{name: 'admon-invites'}">
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
    import proyecto from "../../../api/admon/invites_codes";
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
                    descripcion : '',
                    max : 0,
                    valid_until : '',

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
                proyecto.registrar(self.form, data =>{
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
                            name : 'admon-invites'
                        })
                    },
                    err =>{
                        self.loading = false;
                        self.errorMessages = err;
                        self.btnAction = 'Registrar';
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


        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
