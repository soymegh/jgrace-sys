<template>
    <div class="auth-wrapper auth-v1 px-2">
        <div class="auth-inner py-2">

            <!-- Forgot Password v1 -->
            <b-card class="mb-0">
                <b-link class="brand-logo">
                    <!-- logo -->
                    <!--          <vuexy-logo />-->

                    <h2 class="brand-text text-primary ml-1">
                        CapSoftSys
                    </h2>
                </b-link>

                <b-card-title class="mb-1">
                    Ha olvidado su contraseña? 🔒
                </b-card-title>
                <b-card-text class="mb-2">
                    Ingrese su correo electronico y le enviaremos instrucciones para su recuperación.
                </b-card-text>

                <!-- form -->
                <validation-observer ref="simpleRules">
                    <b-form
                            class="auth-forgot-password-form mt-2"
                            @submit.prevent="validationForm"
                    >
                        <!-- email -->
                        <b-form-group
                                label="Email"
                                label-for="forgot-password-email"
                        >
                            <validation-provider
                                    #default="{ errors }"
                                    name="Email"
                                    rules="required|email"
                            >
                                <b-form-input
                                        id="forgot-password-email"
                                        v-model="form.email"
                                        :state="errors.length > 0 ? false:null"
                                        name="forgot-password-email"
                                        placeholder="john@example.com"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>

                        <!-- submit button -->
                        <b-button
                                variant="primary"
                                block
                                type="submit"
                        >
                            Enviar enlace de recuperación
                        </b-button>
                    </b-form>
                </validation-observer>

                <b-card-text class="text-center mt-2">
                    <b-link :to="{name:'login'}">
                        <feather-icon icon="ChevronLeftIcon"/>
                        Regresar al login
                    </b-link>
                </b-card-text>

            </b-card>
            <!-- /Forgot Password v1 -->
        </div>
    </div>
</template>

<script>
    import {ValidationProvider, ValidationObserver} from 'vee-validate'
    import VuexyLogo from '@core/layouts/components/Logo.vue'
    import axios from 'axios'
    import {
        BCard, BLink, BCardText, BCardTitle, BFormGroup, BFormInput, BForm, BButton,
    } from 'bootstrap-vue'
    import {required, email} from '@validations'
    import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

    export default {
        components: {
            VuexyLogo,
            BCard,
            BLink,
            BCardText,
            BCardTitle,
            BFormGroup,
            BFormInput,
            BButton,
            BForm,
            ValidationProvider,
            ValidationObserver,
        },
        data() {
            return {
                form:{
                    email:'',
                },
                userEmail: '',
                // validation
                required,
                email,
            }
        },
        methods: {
            validationForm() {
                this.$refs.simpleRules.validate().then(success => {
                    if (success) {
                        axios.get('sanctum/csrf-cookie').then(()=>{
                            axios.post('forgot-password', this.form).then((resp) => {
                                this.$router.push({name:'login'});
                                this.$toast({
                                    component: ToastificationContent,
                                    position: 'top-right',
                                    props: {
                                        title: '',
                                        icon: 'CoffeeIcon',
                                        variant: 'success',
                                        text: 'Hemos enviado por correo electrónico el enlace para restablecer la contraseña.!',
                                    },
                                },{
                                  position:'bottom-right'
                                });
                            }).catch((error) => {
                              this.$toast({
                                component: ToastificationContent,
                                position: 'top-right',
                                props: {
                                  title: '',
                                  icon: 'CoffeeIcon',
                                  variant: 'danger',
                                  text: error.message,
                                },
                              },{
                                position:'bottom-right'
                              })
                            })
                    })
                    }
                })
            },
        },
    }
</script>

<style lang="scss">
    @import '@core/scss/vue/pages/page-auth.scss';
</style>
