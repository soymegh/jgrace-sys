<template>
    <b-card>
        <form enctype="multipart/form-data">
            <b-row>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="tipo_producto"> Tipo:</label>
                        <b-form-select id="tipo_producto" @change="seleccionaTipo()" v-model.number="form.tipo_producto">
                            <option value="1">Producto</option>
                            <option value="2">Servicio</option>
                            <!--                        <option value="4">Bienes</option>-->
                        </b-form-select>
                    </div>
                </div>
                <template v-if="form.tipo_producto === 2">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="tipo_servicio"> Tipo de servicio:</label>
                            <b-form-select id="tipo_servicio" v-model.number="form.tipo_servicio">
                                <option value="1">Servicios de Procesamiento e Instalación</option>
                                <option value="2">Asesoría Logística e importaciones</option>
                                <!--                        <option value="4">Bienes</option>-->
                            </b-form-select>
                        </div>
                    </div>
                </template>


                <div class="col-sm-3">
                    <b-form-group>
                        <label for=""> Unidad de medida</label>
                        <v-select
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :disabled="!tipoProducto"
                                :options="ums"
                                label="descripcion"
                                v-model="form.unidad_medida"

                        />
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.unidad_medida"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </b-form-group>
                </div>

                <div class="col-sm-3">
                    <b-form-group>
                        <label for=""> Marcas</label>
                        <v-select
                                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                                :options="marcas"
                                label="descripcion"
                                v-model="form.marca"
                                v-on:input="obtenerCodigo"
                        />
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.marca"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </b-form-group>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">* Nombre {{form.tipo_producto === 1? 'Producto': 'Servicio'}}</label>
                        <input class="form-control" placeholder="Nombre" v-model="form.nombre_comercial">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.nombre_comercial"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">* Descripción</label>
                        <input class="form-control" placeholder="Descripción" v-model="form.descripcion">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.descripcion"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Código Barras</label>
                        <input :disabled="!tipoProducto" class="form-control" placeholder="Código Barras"
                               v-model="form.codigo_barra">
                        <barcode :format="formatx" :height="heightx" :marginTop="marginTopx"
                                 :textPosition="textPositionx" :width="widthx" v-bind:value="form.codigo_barra">
                            Generando Código de Barras
                        </barcode>
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.codigo_barra"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Código</label>
                        <input class="form-control" placeholder="Código Sistema" v-model="form.codigo_sistema">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.codigo_sistema"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>


                <!--<div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Impuesto</label>
                        <v-select v-model="form.impuesto_producto" label="descripcion" :options="impuestos"/>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.impuesto_producto" :key="message" v-text="message"></li>
                        </ul>
                    </div>
                </div>-->

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Existencia Mínima</label>
                        <input :disabled="!tipoProducto" class="form-control" min="0" placeholder="Existencia Mínima"
                               type="number" v-model.number="form.existencia_min">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.existencia_min"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Existencia Máxima</label>
                        <input :disabled="!tipoProducto" class="form-control" min="0"
                               placeholder="Existencia Máxima"
                               type="number" v-model.number="form.existencia_max">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.existencia_max"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Costo Inicial C$</label>
                        <input class="form-control" min="1" placeholder="Costo Estándar" type="number"
                               v-model.number="form.costo_estandar">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.costo_estandar"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Precio de compra $</label>
                        <input class="form-control" min="0" placeholder="Costo de compra" type="number"
                               v-model.number="form.precio_compra">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.precio_compra"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label for=""> Porcentaje Ganacia</label>
                  <input class="form-control" min="0" placeholder="Porcentaje Ganancia" type="number"
                         v-model.number="form.porcentaje_ganancia">
                  <b-alert show variant="danger">
                    <ul class="error-messages">
                      <li
                          :key="message"
                          v-for="message in errorMessages.porcentaje_ganancia"
                          v-text="message"
                      ></li>
                    </ul>
                  </b-alert>
                </div>

              </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Precio de venta $</label>
                      <div class="col-sm-3">
                        <strong>{{ total_precio_venta| formatMoney(2) }}</strong>
                      </div>
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.precio_sugerido"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Precio de venta a Distribuidor $</label>
                        <input class="form-control" min="1" placeholder="Precio de venta distribuidor" type="number"
                               v-model.number="form.precio_distribuidor">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.precio_distribuidor"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>
                    </div>
                </div>

                <!--<div class="col-sm-3">
                    <div class="form-group">
                        <label for=""> Inventario Inicial</label>
                        <input class="form-control" :disabled="!tipoProducto" type="number" min="0"
                               v-model.number="form.cantidad_inicial" placeholder="Inventario Inicial">
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.cantidad_inicial" :key="message" v-text="message"></li>
                        </ul>
                    </div>
                </div>-->

                <!--<div class="col-sm-3">
                    <div class="form-group">
                        <label for>Bodega</label>
                        <v-select
                                :disabled="!tipoProducto"
                                v-model="form.bodega_inicial"
                                label="descripcion"
                                :options="bodegas"
                        ></v-select>
                        <ul class="error-messages">
                            <li v-for="message in errorMessages.bodega_inicial" :key="message" v-text="message"></li>
                        </ul>
                    </div>
                </div>-->

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for=""> Imágen</label>
                        <div class="uploader">
                            <b-form-file @change="handleFileObject" accept="image/jpeg, image/png, image/gif" id="customFile"
                                         ref="file" v-model="form.imagen"/>
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
                <div class="col-sm-4">
                    <template v-if="form.imagen">
                        <b-container class="p-1 bg-dark" fluid>
                            <b-row>
                                <b-col>
                                    <b-img :src="get_avatar()" alt="imagen del producto')" fluid-grow thumbnail/>
                                </b-col>
                            </b-row>
                        </b-container>
                    </template>

                </div>
            </b-row>
        </form>
        <b-card-footer>
            <div class="text-right">
                <router-link :to="{ name: 'inventario-productos' }">
                    <b-button class="mx-1" type="button" variant="secondary">Cancelar</b-button>
                </router-link>
                <b-button :disabled="btnAction !== 'Registrar' ? true : false" @click="registrarProducto"
                          variant="primary">{{ btnAction }}
                </b-button>
            </div>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>
<script type="text/ecmascript-6">
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import VueBarCode from 'vue-barcode'
    //import Pagination from '../layout/Pagination'
    import {
        BAlert,
        BButton,
        BCard,
        BCardFooter,
        BCol,
        BContainer,
        BFormCheckbox,
        BFormCheckboxGroup,
        BFormFile,
        BFormGroup,
        BImg,
        BPaginationNav,
        BRow,
        VBTooltip,
        BFormSelect
    } from 'bootstrap-vue'
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import vSelect from 'vue-select'
    import producto from "../../../api/Inventario/productos";
    import Round from "@/assets/custom-scripts/Round";

    export default {
        components: {
            BCard,
            BCardFooter,
            BPaginationNav,
            BFormCheckbox,
            BFormGroup,
            vSelect,
            BRow,
            BButton,
            BFormCheckboxGroup,
            BAlert,
            'barcode': VueBarCode,
            BFormFile,
            BImg,
            BContainer,
            BCol,
            BFormSelect,
        },
        directives: {
            'b-tooltip': VBTooltip,
        },
        data() {
            return {
                loading: true,
                msg: 'Guardando los datos, espere un momento',
                url: loadingImage,   //It is important to import the loading image then use here
                textPositionx: 'center',
                heightx: 25,
                widthx: 1,
                marginTopx: 0,
                formatx: 'CODE39',
                tipoProducto: true,
                rubros: [],
                impuestos: [],
                ums: [],
                marcas: [],
                bodegas: [],
                form: {
                    precio_compra: 0,
                    precio_distribuidor: 0,
                    codigo_sistema: '',
                    codigo_consecutivo: 0,
                    nombre_comercial: '',
                    descripcion: '',
                    costo_estandar: 0,
                    precio_sugerido: 0,
                    existencia_min: 1,
                    existencia_max: 1,
                    cantidad_inicial: 0,
                    tipo_producto: 1,
                    tipo_servicio: 1,
                    codigo_barra: '',
                    imagen: null,
                    rubro_producto: '',
                    impuesto_producto: '',
                    unidad_medida: [],
                    marca: [],
                    bodega_inicial: "",
                    avatar: '',
                    avatarName: '',
                  porcentaje_ganancia:0,
                },
                uploader: [],
                btnAction: 'Registrar',
                errorMessages: []
            }
        },
        methods: {
            handleFileObject(e) {
                let file = e.target.files[0];
                let reader = new FileReader();

                if (file['size'] < 2111775) {
                    reader.onloadend = (file) => {
                        //console.log('RESULT', reader.result)
                        this.form.avatar = reader.result;
                    }
                    reader.readAsDataURL(file);
                } else {
                    alert('El tamaño del archivo no puede ser superior a 2 MB')
                }
            },
            //For getting Instant Uploaded Photo
            get_avatar() {
                let photo = (this.form.avatar.length > 100) ? this.form.avatar : "img/products/" + this.form.avatar;
                return photo;
            },

            nueva() {
                var self = this;
                producto.nuevo({}, data => {
                        // self.proveedores = data.proveedores;
                        self.ums = data.unidades_medida;
                        self.marcas = data.marcas;
                        self.loading = false;
                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error:' + err,
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.loading = false;
                        console.log(err);
                    })

            },

            obtenerCodigo() {
                var self = this;
                producto.obtenerCodigoProducto({}, data => {
                    //console.log(data);
                    self.form.codigo_consecutivo = data.codigo_siguiente;
                    self.obtenerConcatenarCodigo();
                }, err => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: err,
                                variant: 'danger',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                })
            },
            seleccionaTipo() {
                var self = this;
                if (self.form.tipo_producto === 1) {
                    self.tipoProducto = true;
                    self.form.costo_estandar = 0;
                    self.form.precio_sugerido = 0;
                    self.form.existencia_min = 0;
                    self.form.existencia_max = 1;
                    self.form.cantidad_inicial = 0;
                    self.form.codigo_barra = 'N/A';
                    self.form.imagen = '';
                    self.form.rubro_producto = '';
                    self.form.impuesto_producto = '';
                    self.form.unidad_medida = [];
                } else if (self.form.tipo_producto === 2) {
                    self.tipoProducto = false;
                    self.form.costo_estandar = 0;
                    self.form.precio_sugerido = 0;
                    self.form.existencia_min = 1;
                    self.form.existencia_max = 1;
                    self.form.cantidad_inicial = 0;
                    self.form.codigo_barra = '';
                    self.form.imagen = '';
                    self.form.rubro_producto = '';
                    self.form.impuesto_producto = '';
                    self.form.unidad_medida = [];
                    self.ums.forEach((umx, indice) => {
                        if (umx.id_unidad_medida === 11) {
                            self.form.unidad_medida = self.ums[indice];
                        }
                    });
                } else {
                    self.tipoProducto = true;
                    self.form.costo_estandar = 0;
                    self.form.precio_sugerido = 0;
                    self.form.existencia_min = 0;
                    self.form.existencia_max = 1;
                    self.form.cantidad_inicial = 0;
                    self.form.codigo_barra = 'N/A';
                    self.form.imagen = '';
                    self.form.rubro_producto = '';
                    self.form.impuesto_producto = '';
                    self.form.unidad_medida = [];
                }
            },
            /*obtenerTodasBodegas() {
                var self = this;
                bodega.obtenerTodasBodegas(
                        data => {
                            self.bodegas = data;
                            self.form.bodega_inicial=self.bodegas[0];
                        },
                        err => {
                            console.log(err);
                        }
                );
            },*/
            obtenerConcatenarCodigo() {
                var self = this;
                self.form.codigo_sistema = 'PRD' + self.form.tipo_producto + self.form.unidad_medida.id_unidad_medida + self.form.marca.id_marca + '' + self.form.codigo_consecutivo;
            },


            /*obtenerTodosRubrosPS() {
                var self = this
                rubro.obtenerTodosRubrosPS(data => {
                    self.rubros = data
                }, err => {
                    console.log(err)
                })
            },
            obtenerTodosImpuestos() {
                var self = this
                impuesto.obtenerTodosImpuestos(data => {
                    self.impuestos = data
                }, err => {
                    console.log(err)
                })
            },
            obtenerTodosUnidadMedida() {
                var self = this
                um.obtenerTodas(data => {
                    self.ums = data
                }, err => {
                    console.log(err)
                })
            },*/
            registrarProducto() {
                var self = this
                self.btnAction = 'Registrando, espere....'
                self.loading = true;
                producto.registrarProducto(self.form, data => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'CheckIcon',
                                text: 'Producto registrado correctamente',
                                variant: 'success',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    self.loading = false;
                    this.$router.push({
                        name: 'inventario-productos'
                    })
                }, {headers: {'content-type': 'multipart/form-data'}}, err => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Ha ocurrido un error',
                                variant: 'danger',
                                position: 'bottom-right'
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Registrar'
                })
            },
            /*initSelect2() {
                $('.select2').select2()
            }*/

        },
      computed:{
          total_precio_venta(){

            this.form.precio_sugerido = Round.round(Number((Number(this.form.precio_compra) *  Number( this.form.porcentaje_ganancia/100)) + Number( this.form.precio_compra)),2) ;


            if (!isNaN(this.form.precio_sugerido)) {
              return this.form.precio_sugerido
            }
            return 0
          }
      },
        mounted() {
            //this.initUploader()
            this.nueva();
        }
    }
</script>

<style lang="scss">


    @import '@core/scss/vue/libs/vue-select.scss';

</style>
