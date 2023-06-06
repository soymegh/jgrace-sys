<template>
    <b-card class="main">

        <div class="row">
            <div class="form-group col-md-6">
                <label for=""> Tipo :</label>
                <input class="form-control" v-model="form.descripcion" placeholder="Digita nombre del rol">
                <ul class="error-messages">
                    <li v-for="message in errorMessages.descripcion" :key="message" v-text="message"></li>
                </ul>
            </div>
            <div class="form-group col-md-6">
                <label for=""> * Abreviación:</label>
                <input class="form-control" v-model="form.tipo_abreviado" placeholder="Digita nombre del rol">
                <ul class="error-messages">
                    <li v-for="message in errorMessages.tipo_abreviado" :key="message" v-text="message"></li>
                </ul>
            </div>

            <b-card-footer class="row">


                <!--<div class="col-md-6">
                    <div class="content-box-footer text-left">
                        <template v-if="form.estado">
                            &lt;!&ndash;                            <button :disabled="form.id_rol === 1" class="btn btn-danger"&ndash;&gt;
                            &lt;!&ndash;                                    @click="desactivarRol(form.id_rol)"><i class="fa fa-trash-o"> Inhabilitar</i></button>&ndash;&gt;

                            <b-button :disabled="form.id_rol === 1" class="btn btn-danger" @click="desactivarRol(form.id_rol)">
                                <feather-icon icon="Trash2Icon"></feather-icon>
                                Inhabilitar
                            </b-button>
                        </template>
                        <template v-else>
                            <b-button :disabled="form.id_rol === 1" class="btn btn-success" @click="activarRol(form.id_rol)">
                                <feather-icon icon="CheckCircleIcon"></feather-icon>
                                Habilitar
                            </b-button>
                        </template>
                    </div>
                </div>-->

                <div class="col-md-12">
                    <div class="content-box-footer text-right align-content-md-end ">
                        <router-link  :to="{name: 'contabilidad-tipos-cuenta'}">
                            <b-button type="submit" variant="secondary" class="mx-1">
                                Cancelar
                            </b-button>
                        </router-link>


                        <b-button type="submit" variant="primary" @click="actualizarTipoCuenta" :disabled="btnAction !== 'Guardar'">
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
    import {BButton,BAlert,BDropdown,BCardFooter,BCard} from 'bootstrap-vue'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import tipos_cuenta from "../../../api/contabilidad/tipos_cuenta";
    import estados_financieros from "../../../api/contabilidad/estados_financieros";

    export default {
        components:{
            BButton,
            BAlert,
            BDropdown,
            BCardFooter,
            BCard
        },
        data() {
            return {
                loading:true,
                url : loadingImage,   //It is important to import the loading image then use here
                roles: [],
                form: {
                    tipo: '',
                    tipo_abreviado: ''
                },
                btnAction: 'Guardar',
                errorMessages: [],

            }
        },
        methods: {

            obtenerTipoCuenta(){
                var self = this;
                tipos_cuenta.obtenerTipoCuenta(
                    {
                        id_tipo_cuenta: this.$route.params.id_tipo_cuenta
                    }, data => {
                        self.form = data
                        self.loading = false;
                    }, err => {
                        this.$toast({
                            component: ToastificationContent,
                            props:{
                                title: 'Notificación',
                                icon: 'BellIcon',
                                text: 'Ha ocurrido un error',
                                variant: 'warning',
                                position: 'top-center'
                            }
                        });
                        this.$router.push({
                            name: 'contabilidad-tipos-cuenta'
                        });
                    })
            },

            actualizarTipoCuenta() {
                var self = this
                self.loading = true;
                self.btnAction = 'Guardando, espere......'

                tipos_cuenta.actualizar(self.form, data => {
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
                        name: 'contabilidad-tipos-cuenta'
                    })
                }, err => {
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            }
        },
        mounted() {
            this.obtenerTipoCuenta();
        }
    }
</script>
