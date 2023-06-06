<template>
  <b-card>
    <form enctype="multipart/form-data">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label for="name">* Nombre del sistema:</label>
            <input type="text" class="form-control" v-model="form.app_title"
                   placeholder="Digite el nombre del sistema">
            <b-alert variant="danger" show>
              <ul class="error-messages">
                <li v-for="message in errorMessages.app_title">{{ message }}</li>
              </ul>
            </b-alert>

          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="website">URL Sitio web:</label>
            <input type="text" class="form-control" v-model="form.company_website"
                   placeholder="Digite la URL de la pagina web de la empresa">
            <ul class="error-messages">
              <li v-for="message in errorMessages.company_website">{{ message }}</li>
            </ul>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="name_company">* Nombre de la empresa:</label>
            <input type="text" class="form-control" v-model="form.company_name"
                   placeholder="Digite el nombre de la empresa">
            <ul class="error-messages">
              <li v-for="message in errorMessages.company_name">{{ message }}</li>
            </ul>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="name">Correo electronico:</label>
            <input type="text" class="form-control" v-model="form.company_email"
                   placeholder="Digite el correo electronico de la empresa">
            <ul class="error-messages">
              <li v-for="message in errorMessages.company_email">{{ message }}</li>
            </ul>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="name">* Telefono:</label>
            <input type="number" min="0" class="form-control" v-model="form.company_telephone"
                   placeholder="Digite el telefono de la empresa">
            <ul class="error-messages">
              <li v-for="message in errorMessages.company_telephone">{{ message }}</li>
            </ul>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="name">* Número RUC:</label>
            <input type="text" class="form-control" v-model="form.ruc_company" v-mask="'A#############'"
                   placeholder="Digite el numero ruc de la empresa">
            <ul class="error-messages">
              <li v-for="message in errorMessages.ruc_company">{{ message }}</li>
            </ul>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label for="name">* Moneda:</label>
            <b-form-select
                class="" v-model.number="form.moneda"
                label="descripcion"
            >
              <option value="1">Córdobas</option>
              <option value="2">Dolares</option>
            </b-form-select>
            <b-alert variant="danger" show>
              <ul class="error-messages">
                <li v-for="message in errorMessages.moneda">{{ message }}</li>
              </ul>
            </b-alert>

          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label for="discount_max">* Porcentaje descuento máximo:</label>
            <input type="number" id="discount_max" min="0" class="form-control" v-model.number="form.discount_max"
                   placeholder="Digite el descuento máximo para facturación">
            <b-alert variant="danger" show>
              <ul class="error-messages">
                <li v-for="message in errorMessages.discount_max">{{ message }}</li>
              </ul>
            </b-alert>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label for="direccion">* Dirección de la empresa:</label>
            <input type="text" class="form-control" v-model="form.company_address"
                   placeholder="Digite la direccion de la empresa">
            <ul class="error-messages">
              <li v-for="message in errorMessages.company_address">{{ message }}</li>
            </ul>
          </div>
        </div>
<!--        -->
        <div class="col-sm-4">
          <div class="form-group">
            <label for=""> Imágen logo</label>
            <div class="uploader">
              <b-form-file @change="handleFileObject" accept="image/jpeg, image/png, image/gif" id="customFile"
                           ref="file"/>
            </div>
            <b-alert show variant="danger">
              <ul class="error-messages">
                <li
                    :key="message"
                    v-for="message in errorMessages.imagen"
                    v-text="message"
                ></li>
              </ul>
            </b-alert>
          </div>
        </div>
        <div class="col-sm-2">
          <template v-if="form.login_img">
            <b-container fluid class="p-1 bg-dark">
              <b-row>
                <b-col>
                  <b-img thumbnail fluid-grow :src="get_avatar()" alt="Logo"/>
                </b-col>
              </b-row>
            </b-container>
          </template>
        </div>
       <!--Solicita el icono-->
        <div class="col-sm-4">
          <div class="form-group">
            <label for="">Icono</label>
            <div class="uploader">
              <b-form-file @change="handleFileObjects" accept="image/jpeg, image/png, image/gif" id="customIcon"
                           ref="files"/>
            </div>
            <b-alert show variant="danger">
              <ul class="error-messages">
                <li
                    :key="message"
                    v-for="message in errorMessages.imagen"
                    v-text="message"
                ></li>
              </ul>
            </b-alert>
          </div>
        </div>
        <div class="col-sm-2">
          <template v-if="form.uploaded_icon">
            <b-container fluid class="p-1 bg-dark">
              <b-row>
                <b-col>
                  <b-img thumbnail fluid-grow :src="get_icono()" alt="Logo"/>
                </b-col>
              </b-row>
            </b-container>
          </template>

        </div>
        <!--<div class="col-sm-6">
            <div class="form-group">
                <label for="name">* Moneda:</label>
                <b-form-group>
                    <v-select
                            v-model="selected"
                            label="title"
                            :options="option"
                    />
                </b-form-group>
            </div>
        </div>-->
        <!--<div class="col-sm-4">
            <div class="form-group">
                <label for="name">Logo</label>
                &lt;!&ndash; Styled &ndash;&gt;
                <div class="uploader">
                    <b-form-file
                            v-model="file"
                            placeholder="Choose a file or drop it here..."
                            drop-placeholder="Drop file here..."
                            id="uploader-button"
                            v-on:change="initUploader"
                            ref="logo_uploaded"
                    />
                </div>

                <template v-if="form.preview_logo_loaded">
                    <img class="imagePreviewWrapper" :src="form.preview_logo_loaded">
                </template>


                <b-card-text class="my-1">
                    Archivo seleccionado: <strong>{{ file ? file.name : '' }}</strong>
                </b-card-text>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="name">Favicon</label>
                &lt;!&ndash; Styled &ndash;&gt;
                <div class="uploader">
                    <b-form-file
                            v-model="fileIcon"
                            placeholder="Choose a file or drop it here..."
                            drop-placeholder="Drop file here..."
                            id="uploader-button-fav"
                            v-on:change="initUploaderFavicon"
                            ref="favicon_uploaded"
                    />
                </div>

                <template v-if="form.preview_icon_loaded">
                    <img class="imagePreviewWrapper" :src="form.preview_icon_loaded">
                </template>


                <b-card-text class="my-1">
                    Archivo seleccionado: <strong>{{ fileIcon ? fileIcon.name : '' }}</strong>
                </b-card-text>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="name">Imagen login</label>
                &lt;!&ndash; Styled &ndash;&gt;
                <div class="uploader">
                    <b-form-file
                            v-model="fileLogin"
                            placeholder="Choose a file or drop it here..."
                            drop-placeholder="Drop file here..."
                            id="uploader-button-login"
                            v-on:change="initUploaderLogin"
                            ref="logo_login_uploaded"
                    />
                </div>

                <template v-if="form.preview_login_loaded">
                    <img class="imagePreviewWrapper" :src="form.preview_login_loaded">
                </template>


                <b-card-text class="my-1">
                    Archivo seleccionado: <strong>{{ fileLogin ? fileLogin.name : '' }}</strong>
                </b-card-text>
            </div>
        </div>-->
      </div>
    </form>
    <b-card-footer class="text-right">

      <router-link :to="{name: 'home'}">
        <b-button type="submit" variant="secondary" class="mx-1">
          Cancelar
        </b-button>
      </router-link>


      <b-button type="submit" variant="primary" @click="registrar" :disabled="btnAction !== 'Guardar'">
        {{ btnAction }}
      </b-button>


    </b-card-footer>
    <template v-if="loading">
      <BlockUI :url="url"></BlockUI>
    </template>
  </b-card>
</template>

<script>
import {
  BCard,
  BCardText,
  BLink,
  BFormGroup,
  BFormFile,
  BButton,
  BAlert,
  BCardFooter,
  BFormSelect,
  BContainer,
  BRow,
  BCol,
  BImg
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import ajustes_generales from "../../api/admon/ajustes_generales";
import loader from '../../assets/images/loader/block50.gif'
import ToastificationContent from "../../@core/components/toastification/ToastificationContent";
import $ from 'jquery'


export default {
  components: {
    BCard,
    BCardText,
    BLink,
    BFormGroup,
    vSelect,
    BFormFile,
    BButton,
    ToastificationContent,
    BAlert,
    BCardFooter,
    BFormSelect,
    BContainer,
    BRow,
    BCol,
    BImg,
  },
  data() {
    return {
      loading: false,
      msg: 'Cargando el contenido espere un momento',
      url: loader,
      file: null,
      fileIcon: null,
      fileLogin: null,
      imagen_nueva: false,
      icono_nuevo: false,
      form: {
        ajustes: [],
        app_title: "",
        company_address: "",
        company_email: "",
        company_name: "",
        company_telephone: "",
        company_website: "",
        currency_id: 1,
        uploaded_logo: "",
        preview_logo_loaded: null,
        uploaded_logo_name: "",
        uploaded_icon: "",
        uploaded_icon_name: "",
        preview_icon_loaded: null,
        login_img: "",
        login_img_name: '',
        preview_login_loaded: null,
        ruc_company: "",
        moneda: '',
        discount_max: '',
        imagen_nueva: false,
        icono_nuevo: false,
        avatarlogin: "",
        avataricon: "",
        avatarLoginFile:"",
        avatarIconFile:"",
      },
      btnAction: "Guardar",
      errorMessages: [],
      monedas: [],
      recursos: [],
      host: "",
      avatarlogin_name:"",
      avatricon_name:"",


    }
  },
  methods: {
    handleFileObject(e) {
      this.form.avatarLoginFile = e.target.files[0];


      let file = e.target.files[0];
      let reader = new FileReader();

      if (file['size'] < 2111775) {
        reader.onloadend = (file) => {
          //console.log('RESULT', reader.result)
          this.form.avatarlogin = reader.result;
          if (this.form.login_img !== this.form.avatarlogin ) {
            this.imagen_nueva = true;
            this.form.imagen_nueva = true;

          }


        }
        reader.readAsDataURL(file);
      } else {
        alert('El tamaño del archivo no puede ser superior a 2 MB')
      }
    },

    handleFileObjects(e) {
      this.form.avatarIconFile = e.target.files[0];

      let files = e.target.files[0];
      let readers = new FileReader();

      if (files['size'] < 2111775) {
        readers.onloadend = (files) => {
          //console.log('RESULT', reader.result)
          this.form.avataricon = readers.result;
          if (this.form.uploaded_icon !== this.form.avataricon ) {
            this.icono_nuevo = true;
            this.form.icono_nuevo = true;

          }
        }
        readers.readAsDataURL(files);
      } else {
        alert('El tamaño del archivo no puede ser superior a 2 MB')
      }
    },





    //For getting Instant Uploaded Photo
    get_avatar() {
      let self = this;
      if (self.imagen_nueva === true) {

        // let photo = (this.form.avatar.length > 100) ? this.form.avatar : "http://localhost:8001/img/products/"+ this.form.avatar;
        let photo = (this.form.avatarlogin.length > 100) ? this.form.avatarlogin : this.host + this.form.avatarlogin;
        // let photo = (this.form.avatar.length > 100) ? this.form.avatar : "https://backend.capital.software/img/products/"+ this.form.avatar;
        return photo;

      } else {
        // let photo = (this.form.imagen.length > 100) ? this.form.imagen : "http://localhost:8001/img/products/"+ this.form.imagen;
        this.avatarlogin_name = (this.form.login_img.length > 100) ? this.form.login_img : this.host + this.form.login_img;
        let photo =  this.avatarlogin_name;
        // let photo = (this.form.imagen.length > 100) ? this.form.imagen : "https://backend.capital.software/img/products/"+ this.form.imagen;
        return photo;
      }

      // let photo = (this.form.imagen.length > 100) ? this.form.imagen : "http://css.capital.software:8043/img/products/"+ this.form.imagen;

    },



    //For getting Instant Uploaded Icono
    get_icono() {
      let self = this;
      if (self.icono_nuevo === true) {

        // let photo = (this.form.avatar.length > 100) ? this.form.avatar : "http://localhost:8001/img/products/"+ this.form.avatar;
        let icon = (this.form.avataricon.length > 100) ? this.form.avataricon : this.host + this.form.avataricon;
        // let photo = (this.form.avatar.length > 100) ? this.form.avatar : "https://backend.capital.software/img/products/"+ this.form.avatar;
        return icon;

      } else {
        // let photo = (this.form.imagen.length > 100) ? this.form.imagen : "http://localhost:8001/img/products/"+ this.form.imagen;
        this.avatricon_name  = (this.form.uploaded_icon.length > 100) ? this.form.uploaded_icon : this.host + this.form.uploaded_icon;
        let icon =  this.avatricon_name;
        // let photo = (this.form.imagen.length > 100) ? this.form.imagen : "https://backend.capital.software/img/products/"+ this.form.imagen;
        return icon;
      }

      // let photo = (this.form.imagen.length > 100) ? this.form.imagen : "http://css.capital.software:8043/img/products/"+ this.form.imagen;

    },
    cargarAjustes() {
      var self = this;
      self.loading = true;
      ajustes_generales.cargar(data => {
            this.form.ajustes = data.ajustes;
            this.form.moneda = data.ajustes[0].valor;
            this.form.app_title = data.ajustes[1].valor;
            this.form.company_name = data.ajustes[3].valor;
            this.form.company_address = data.ajustes[4].valor;
            this.form.company_telephone = data.ajustes[5].valor;
            this.form.company_email = data.ajustes[6].valor;
            this.form.company_website = data.ajustes[7].valor;
            this.form.discount_max = data.ajustes[11].valor;
            this.form.uploaded_icon = data.ajustes[8].valor;
            this.form.avataricon = this.form.uploaded_icon;
            this.form.uploaded_logo = data.ajustes[2].valor;
            this.form.login_img = data.ajustes[9].valor;
            this.form.avatarlogin = this.form.login_img;
            this.form.ruc_company = data.ajustes[10].valor;
            this.monedas = data.monedas;
            this.host = data.host;
            self.loading = false;
          },
          err => {
            self.loading = false;
            console.log(err);
          });
    },
    initUploader(e) {
      let self = this;
      self.form.uploaded_logo = e.target.files[0];
      self.form.uploaded_logo_name = e.target.files[0].name;
      let reader = new FileReader();
      reader.readAsDataURL(self.form.uploaded_logo);
      reader.onload = e => {
        self.form.preview_logo_loaded = e.target.result;

      }
    },
    initUploaderFavicon(e) {
      let self = this;
      self.form.uploaded_icon = e.target.files[0];
      self.form.uploaded_icon_name = e.target.files[0].name;
      let reader = new FileReader();
      reader.readAsDataURL(self.form.uploaded_icon);
      reader.onload = e => {
        self.form.preview_icon_loaded = e.target.result;

      }
    },
    initUploaderLogin(e) {
      let self = this;
      self.form.login_img = e.target.files[0];
      self.form.login_img_name = e.target.files[0].name;
      let reader = new FileReader();
      reader.readAsDataURL(self.form.login_img);
      reader.onload = e => {
        self.form.preview_login_loaded = e.target.result;

      }
    },
    registrar() {
      const self = this;
      self.loading = true;
      self.btnAction = "Guardando, espere...";
      ajustes_generales.registrar(
          self.form,
          data => {

            self.btnAction = "Guardar";
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Notificación',
                icon: 'InfoIcon',
                text: 'Registros actualizado correctamente',
                variant: 'success',
                position: 'top-center'
              }
            });
            this.$router.go(0);
            // alertify.success("Datos Actualizados Correctamente!", 5);
            self.loading = false;

          },
          err => {
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Notificación',
                icon: 'BellIcon',
                text: 'Ha ocurrido un error',
                variant: 'warning',
                position: 'top-center'
              }
            });
            self.errorMessages = err;
            self.btnAction = "Guardar";
            self.loading = false;

          }
      );
    },

  },
  mounted() {
    this.cargarAjustes();
    this.get_avatar();
    this.get_icono();
  }
}

</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';

.imagePreviewWrapper {
  width: 150px;
  height: 150px;
  display: block;
  cursor: pointer;
  margin: 0 auto 30px;
  background-size: cover;
  background-position: center center;
  margin-top: 2rem;
}
</style>
