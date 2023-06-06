<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="name">* Descripción:</label>
                        <input type="text" class="form-control" v-model="form.descripcion" placeholder="Dígite el tipo de entrada">
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

            <router-link  :to="{name: 'inventario-tipos-entradas'}">
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
    import tipo_entrada from "../../../api/Inventario/tipos_entradas";
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
                    descripcion : ''

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
                tipo_entrada.registrar(self.form, data =>{
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
                            name : 'inventario-tipos-entradas'
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
                        this.$router.push({
                            name : 'inventario-tipos-entradas'
                        })
                    })
            },


        }

    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
