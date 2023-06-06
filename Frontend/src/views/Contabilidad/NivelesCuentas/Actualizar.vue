<template>
    <b-card class="main">

        <div class="row">
            <div class="form-group col-md-12">
                <label for=""> Descripción :</label>
                <input class="form-control" v-model="form.descripcion" placeholder="Digita nombre del nivel">
                <ul class="error-messages">
                    <li v-for="message in errorMessages.descripcion" :key="message" v-text="message"></li>
                </ul>
            </div>

            <b-card-footer class="row">

                <div class="col-md-12">
                    <div class="content-box-footer text-right align-content-md-end ">
                        <router-link  :to="{name: 'contabilidad-niveles-cuentas'}">
                            <b-button type="submit" variant="secondary" class="mx-1">
                                Cancelar
                            </b-button>
                        </router-link>


                        <b-button type="submit" variant="primary" @click="actualizarNivelCuenta" :disabled="btnAction !== 'Guardar'">
                            {{btnAction}}
                        </b-button>
                    </div>
                </div>
                <template v-if="loading">
                    <BlockUI  :url="url"></BlockUI>
                </template>
            </b-card-footer>

        </div>
    </b-card>

</template>

<script type="text/ecmascript-6">
    import rol from '../../../api/admon/roles'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {BButton,BAlert,BDropdown} from 'bootstrap-vue'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import tipos_cuenta from "../../../api/contabilidad/tipos_cuenta";
    import estados_financieros from "../../../api/contabilidad/estados_financieros";
    import niveles_cuentas from "../../../api/contabilidad/niveles_cuentas";

    export default {
        components:{
            BButton,
            BAlert,
            BDropdown
        },
        data() {
            return {
                loading:true,
                url : loadingImage,   //It is important to import the loading image then use here
                roles: [],
                form: {
                    descripcion: '',
                },
                btnAction: 'Guardar',
                errorMessages: [],
                estados_financieros: []

            }
        },
        methods: {

            obtenerNiveleCuenta(){
                var self = this;
                niveles_cuentas.obtenerNivelCuenta(
                    {
                        id_nivel_cuenta: this.$route.params.id_nivel_cuenta
                    }, data => {
                        self.form = data
                        self.loading = false;
                    }, err => {
                        alertify.error(err.id_nivel_cuenta[0], 5);
                        this.$router.push({
                            name: 'contabilidad-niveles-cuentas'
                        });
                    })
            },

            actualizarNivelCuenta() {
                var self = this
                self.loading = true;
                self.btnAction = 'Guardando, espere......'

                niveles_cuentas.actualizar(self.form, data => {
                    this.$toast({
                        component: ToastificationContent,
                        props:{
                            title: 'Notificación',
                            icon: 'InfoIcon',
                            text: 'Cuenta actualizada correctamente',
                            variant: 'success',
                            position: 'top-center'
                        }
                    });
                    this.$router.push({
                        name: 'contabilidad-niveles-cuentas'
                    })
                }, err => {
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            }
        },
        mounted() {
            this.obtenerNiveleCuenta();
        }
    }
</script>
