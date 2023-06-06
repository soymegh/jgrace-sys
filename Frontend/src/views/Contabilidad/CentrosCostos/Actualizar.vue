<template>
    <b-card>
        <form>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="name">* Tipo Centro:</label>
                        <b-form-select
                                class="form-group" disabled v-model.number="form.tipo_centro"
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
                        <b-form-select disabled class="form-group" v-model.number="form.clasificacion_contable">
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
                               disabled class="form-group" v-model.number="form.ubicacion"
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
        <b-card-footer class="row text-right mt-1">
            <div class="col-md-6">
                <div class="content-box-footer text-left">
                    <template v-if="form.estado">
                        <b-button @click="desactivar(form.id_centro)" class="btn btn-danger">
                            <feather-icon icon="Trash2Icon"></feather-icon>
                            Desactivar
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button @click="activar(form.id_centro)" class="btn btn-success" >
                            <feather-icon icon="CheckCircleIcon"></feather-icon>
                            Activar
                        </b-button>
                    </template>
                </div>
            </div>
            <div class="col-sm-6">
                <router-link  :to="{name: 'contabilidad-centros-costos'}">
                    <b-button type="submit" variant="secondary" class="mx-1">
                        Cancelar
                    </b-button>
                </router-link>


                <b-button type="submit" variant="primary" @click="actualizar" :disabled="btnAction !== 'Actualizar'">
                    {{btnAction}}
                </b-button>
            </div>




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

                },
                btnAction: 'Actualizar',
                errorMessages: [],

            }
        },
        methods: {

          obtenerCentro(){
              var self = this;
              centro_costo.obtenerCentro({
                  id_centro: this.$route.params.id_centro
              }, data =>{
                  self.form = data
                  self.loading = false;
              }, err =>{
                  this.$toast({
                      component : ToastificationContent,
                      props : {
                          title: 'Notificación',
                          icon: 'BellIcon',
                          text: 'Ha ocurrido un error al cargar la información',
                          variant: 'warning',
                          position: 'top-center'
                      }
                  },
                      {
                          position : 'bottom-right'
                      })
                  this.$router.push({
                      name : 'contabilidad-centros-costos'
                  })
              })
          },

          actualizar(){
              var self = this;
              self.loading = true;
              self.btnAction = 'Guardando, espere...'
              centro_costo.actualizar(self.form, data =>{
                  this.$toast({
                          component : ToastificationContent,
                          props : {
                              title: 'Notificación',
                              icon: 'BellIcon',
                              text: 'Datos actualizados correctamente',
                              variant: 'success',
                              position: 'top-center'
                          }
                      },
                      {
                          position : 'bottom-right'
                      })
                  this.$router.push({
                      name : 'contabilidad-centros-costos'
                  })
              }, err =>{
                  self.loading = false;
                  this.$toast({
                          component : ToastificationContent,
                          props : {
                              title: 'Notificación',
                              icon: 'BellIcon',
                              text: 'Ha ocurrido un error al actualizar los datos',
                              variant: 'warning',
                              position: 'top-center'
                          }
                      },
                      {
                          position : 'bottom-right'
                      })
                  this.$router.push({
                      name : 'contabilidad-centros-costos'
                  })
              })
          },
            desactivar(id_centro){
              var self = this;
              self.$swal.fire({
                  title : '¿Esta seguro de cambiar el estado?',
                  text: "",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si, confirmar',
                  cancelButtonText:'Cancelar'
              }).then((result)=>{
                  if(result.value){
                      var self = this
                      centro_costo.desactivar({
                          id_centro : id_centro
                      },
                      data =>{
                        if(self.form.tipo_centro===1){
                            this.$toast({
                                    component : ToastificationContent,
                                    props : {
                                        title: 'Notificación',
                                        icon: 'BellIcon',
                                        text: 'Centro de ingreso desactivado correctamente',
                                        variant: 'success',
                                        position: 'top-center'
                                    }
                                },
                                {
                                    position : 'bottom-right'
                                })
                        }else{
                            this.$toast({
                                    component : ToastificationContent,
                                    props : {
                                        title: 'Notificación',
                                        icon: 'BellIcon',
                                        text: 'Centro de costo desactivado correctamente',
                                        variant: 'success',
                                        position: 'top-center'
                                    }
                                },
                                {
                                    position : 'bottom-right'
                                })
                            this.$router.push({
                                name:'contabilidad-centros-costos'
                            })
                        }
                      }, err =>{
                              if(self.form.tipo_centro===1){
                                  this.$toast({
                                          component : ToastificationContent,
                                          props : {
                                              title: 'Notificación',
                                              icon: 'BellIcon',
                                              text: 'Ha ocurrido un error al desactivar el centro de ingreso',
                                              variant: 'warning',
                                              position: 'top-center'
                                          }
                                      },
                                      {
                                          position : 'bottom-right'
                                      })
                              }else {
                                  this.$toast({
                                          component: ToastificationContent,
                                          props: {
                                              title: 'Notificación',
                                              icon: 'BellIcon',
                                              text: 'Ha ocurrido un error al desactivar el centro de costo',
                                              variant: 'warning',
                                              position: 'top-center'
                                          }
                                      },
                                      {
                                          position: 'bottom-right'
                                      })
                                  this.$router.push({
                                      name: 'contabilidad-centros-costos'
                                  })
                              }
                          })
                  }
              })
            },
            activar(id_centro){
              var self = this;
              self.$swal.fire({
                  title : '¿Esta seguro de cambiar el estado?',
                  text: "",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si, confirmar',
                  cancelButtonText:'Cancelar'
              }).then((result)=>{
                  if(result.value){
                      var self = this
                      centro_costo.activar({
                          id_centro : id_centro
                      },
                      data =>{
                        if(self.form.tipo_centro===1){
                            this.$toast({
                                    component : ToastificationContent,
                                    props : {
                                        title: 'Notificación',
                                        icon: 'BellIcon',
                                        text: 'Centro de ingreso activado correctamente',
                                        variant: 'success',
                                        position: 'top-center'
                                    }
                                },
                                {
                                    position : 'bottom-right'
                                })
                        }else{
                            this.$toast({
                                    component : ToastificationContent,
                                    props : {
                                        title: 'Notificación',
                                        icon: 'BellIcon',
                                        text: 'Centro de costo activado correctamente',
                                        variant: 'success',
                                        position: 'top-center'
                                    }
                                },
                                {
                                    position : 'bottom-right'
                                })
                            this.$router.push({
                                name:'contabilidad-centros-costos'
                            })
                        }
                      }, err =>{
                              if(self.form.tipo_centro===1){
                                  this.$toast({
                                          component : ToastificationContent,
                                          props : {
                                              title: 'Notificación',
                                              icon: 'BellIcon',
                                              text: 'Ha ocurrido un error al activar el centro de ingreso',
                                              variant: 'warning',
                                              position: 'top-center'
                                          }
                                      },
                                      {
                                          position : 'bottom-right'
                                      })
                              }else {
                                  this.$toast({
                                          component: ToastificationContent,
                                          props: {
                                              title: 'Notificación',
                                              icon: 'BellIcon',
                                              text: 'Ha ocurrido un error al activar el centro de costo',
                                              variant: 'warning',
                                              position: 'top-center'
                                          }
                                      },
                                      {
                                          position: 'bottom-right'
                                      })
                                  this.$router.push({
                                      name: 'contabilidad-centros-costos'
                                  })
                              }
                          })
                  }
              })
            }

        },
        mounted() {
            this.obtenerCentro();
        }
    }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
