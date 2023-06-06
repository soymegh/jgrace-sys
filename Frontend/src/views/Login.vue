<template>
  <div class="auth-wrapper auth-v2">
    <b-row class="auth-inner m-0">


      <!-- Left Text-->
      <b-col
          class="d-none d-lg-flex align-items-center p-5"
          lg="8"
      >
        <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
          <b-img
              :src="imgUrl"
              alt="Login V2"
              fluid
          />
        </div>
      </b-col>
      <!-- /Left Text-->

      <!-- Login-->
      <b-col
          class="d-flex align-items-center auth-bg px-2 p-lg-5"
          lg="4"
      ><!-- Brand logo-->
        <b-link class="brand-logo">
          <b-img fluid-grow :src="appLogo"/>
          <h2 class="brand-text text-primary ml-1">

          </h2>
        </b-link>
        <!-- /Brand logo-->
        <b-col
            class="px-xl-2 mx-auto"
            lg="12"
            md="6"
            sm="8"
        >
          <b-card-title
              class="font-weight-bold mb-1"
              title-tag="h2" style="margin-top: 5rem"
          >
            Bienvenido! 👋
          </b-card-title>
          <b-card-text class="mb-2">
            Inicia sesión para acceder al sistema
          </b-card-text>

          <!-- form -->
          <validation-observer ref="loginValidation">
            <b-form
                @submit.prevent="login"
                class="auth-login-form mt-2"
            >
              <!-- email -->
              <b-form-group
                  label="Email"
                  label-for="login-email"
              >
                <validation-provider
                    #default="{ errors }"
                    name="Email"
                    rules="required|email"
                >
                  <b-form-input
                      :state="errors.length > 0 ? false:null"
                      id="login-email"
                      name="login-email"
                      placeholder="john@example.com"
                      v-model="userEmail"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group>
                <div class="d-flex justify-content-between">
                  <label for="login-password">Password</label>
                  <b-link :to="{name:'forgot-password'}">
                    <small>Olvide mi contraseña</small>
                  </b-link>
                </div>
                <validation-provider
                    #default="{ errors }"
                    name="Password"
                    rules="required"
                >
                  <b-input-group
                      :class="errors.length > 0 ? 'is-invalid':null"
                      class="input-group-merge"
                  >
                    <b-form-input
                        :state="errors.length > 0 ? false:null"
                        :type="passwordFieldType"
                        class="form-control-merge"
                        id="login-password"
                        name="login-password"
                        placeholder="············"
                        v-model="password"
                    />
                    <b-input-group-append is-text>
                      <feather-icon
                          :icon="passwordToggleIcon"
                          @click="togglePasswordVisibility"
                          class="cursor-pointer"
                      />
                    </b-input-group-append>
                  </b-input-group>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
              <!-- checkbox -->
              <b-form-group>
                <b-form-checkbox
                    id="remember-me"
                    name="checkbox-1"
                    v-model="remember_me"
                >
                  Recuerdame
                </b-form-checkbox>
              </b-form-group>

              <!--Attemtps timer-->
              <template v-if="form.count >= 5 && form.timer > 0">
                <b-form-group>
                  <b-alert show variant="danger">
                    <div class="text-center p-t-12">
                      <ul class="error-messages">
                        <li>{{ 'Limite de intentos superados, por favor espere ' + form.timer + 'segundos' }}
                        </li>
                      </ul>
                    </div>
                  </b-alert>

                </b-form-group>
              </template>
              <!-- submit buttons -->
              <b-button
                  :disabled="btnAction !== 'Inicia sesión'"
                  block
                  type="submit"
                  variant="primary"

              >
                {{ btnAction }}
              </b-button>
            </b-form>
          </validation-observer>


          <!-- social buttons -->

        </b-col>
      </b-col>
      <!-- /Login-->
    </b-row>
  </div>
</template>

<script>
/* eslint-disable global-require */
import {ValidationObserver, ValidationProvider} from 'vee-validate'
import VuexyLogo from '@core/layouts/components/Logo.vue'
import {
  BAlert,
  BButton,
  BCardText,
  BCardTitle,
  BCol,
  BForm,
  BFormCheckbox,
  BFormGroup,
  BFormInput,
  BImg,
  BInputGroup,
  BInputGroupAppend,
  BLink,
  BRow,
} from 'bootstrap-vue'
import {email, required} from '@validations'
import {togglePasswordVisibility} from '@core/mixins/ui/forms'
import store from '@/store/index'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import {mapActions} from "vuex";
import ajustes_generales from "@/api/admon/ajustes_generales";

export default {
  components: {
    BRow,
    BCol,
    BLink,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
    VuexyLogo,
    ValidationProvider,
    ValidationObserver,
    BAlert
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      remember_me: false,
      password: '',
      userEmail: '',
      sideImg: require('@/assets/images/pages/login-v2.svg'),
      // appLogo: require('/public/logotipo.png'),
      appLogo : '',
      // validation rulesimport store from '@/store/index'
      required,
      email,
      btnAction: "Inicia sesión",
      form: {
        count: 0,
        timer: 60,
      },
      maxAttempts: [],
      recursos: []

    }
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    imgUrl() {
      if (store.state.appConfig.layout.skin === 'dark') {
        // eslint-disable-next-line vue/no-side-effects-in-computed-properties
        this.sideImg = require('@/assets/images/pages/login-v2-dark.svg')
        return this.sideImg
      }
      return this.sideImg
    },
  },
  methods: {
    ...mapActions({
      login: 'auth/login'
    }),
    countDownTimer() {
      if (this.form.timer > 0) {
        setTimeout(() => {
          this.form.timer -= 1;
          this.btnAction = 'Por favor espere...';
          this.countDownTimer()
        }, 1000)
      } else if (this.form.timer === 0) {
        this.btnAction = 'Inicia sesión'
        this.form.count = 0;
      }
    },
    login() {
      this.$refs.loginValidation.validate().then(success => {
        this.btnAction = 'Validando credenciales...';
        if (success) {
          this.$store.dispatch('login', {
            email: this.userEmail,
            password: this.password,
            remember_me: this.remember_me
          })
              .then((response) => {
                // console.log('Status response login', this.$store.state.auth.status);

                if (this.$store.state.auth.status === 401) {
                  this.$toast({
                    component: ToastificationContent,
                    position: 'top-right',
                    props: {
                      title: 'Error',
                      icon: 'CoffeeIcon',
                      variant: 'danger',
                      text: 'Algo ha ido mal, por favor verifica tus datos.!',
                    },
                  });
                  this.btnAction = 'Inicia sesión'
                }
                if (this.$store.state.auth.status === 200) {
                  this.$router.push(sessionStorage.getItem('redirectPath') || 'home'); // push to this route
                  sessionStorage.removeItem('redirectPath');
                  this.$toast({
                    component: ToastificationContent,
                    position: 'top-right',
                    props: {
                      title: 'Bienvenido',
                      icon: 'CoffeeIcon',
                      variant: 'success',
                      text: 'Ha iniciado de sesión correctamente, bienvenido!',
                    },
                  });
                }
              }).catch(res => {
            this.btnAction = 'Inicia sesión';
            this.form.count = this.form.count + 1;
            if (this.form.count === 5) {
              this.countDownTimer();
            }
            this.maxAttempts = ['Número de intentos superados, porfavor espere ']
            this.$toast({
              component: ToastificationContent,
              position: 'top-right',
              props: {
                title: 'Failed',
                icon: 'CoffeeIcon',
                variant: 'danger',
                text: 'Algo ha ido mal, por favor verifica tus datos.!',
              },
            })
          })
          /* }).then(res => console.log(res)) */
        } else {
          this.btnAction = 'Inicia sesión'
        }
      })
    },
    cargarAjustes() {
      let self = this;

      ajustes_generales.cargarRecursos(data => {

            this.recursos = data;
            this.appLogo =  this.recursos.host + this.recursos.logo_login.valor;


          },
          err => {

            console.log(err);
          });
      },
  },


  mounted() {
    this.cargarAjustes()
  }
}
</script>

<style lang="scss">
@import '@core/scss/vue/pages/page-auth.scss';
</style>
