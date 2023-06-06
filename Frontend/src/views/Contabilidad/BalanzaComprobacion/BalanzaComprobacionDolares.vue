<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Tipos Cuentas:</label>
                    <v-select
                            :options="niveles_cuenta"
                            label="descripcion"
                            v-model="filter.nivel_cuenta">
                    </v-select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Moneda:</label>
                    <b-form-select v-model.number="filter.currency">
                        <option value="NIO">Córdobas</option>
                        <option value="USD">Dolares</option>
                    </b-form-select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Fecha inicial:</label>
                    <b-form-datepicker
                            :date-format-options="{ day: 'numeric', month: 'numeric', year: 'numeric'  }"
                            @change="onDateSelect"
                            class="mb-0"
                            local="es"
                            placeholder="F. inicial"
                            selected-variant="primary"
                            v-model="filter.fecha_inicial"
                    />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="label-search">Fecha final:</label>
                    <b-form-datepicker
                            :date-format-options="{ day: 'numeric', month: 'numeric', year: 'numeric'  }"
                            @selected="onDateSelect"
                            class="mb-0"
                            local="es"
                            placeholder="F. final"
                            selected-variant="primary"
                            v-model="filter.fecha_final"
                    />
                </div>
            </div>
            <div class="col-md-1 mr-1">
                <div class="form-group">
                    <b-button @click="obtenerBalanzaComprobacion()" variant="success"><i class="pe-7s-search"></i> Generar
                    </b-button>
                </div>
            </div>

            <div class="col-md-1 ">
                <div class="form-group">
                    <b-button @click="descargarReporte(filter.nivel_cuenta.id_nivel_cuenta,filter.fecha_inicial, filter.fecha_final,filter.currency, 'xls')" variant="info"><i class="DownloadIcon"></i> Descargar
                    </b-button>
                </div>
            </div>
        </b-row>
        <div class="table table-responsive">
            <table class="table table-responsive-md ">
            <thead>
            <tr>
                <th colspan="3"></th>
                <th class="text-center table-number" colspan="1">Saldo Anterior</th>
                <th class="text-center table-number" colspan="2">Movimientos</th>
                <th class="text-center table-number" colspan="2">Saldo Final</th>
            </tr>
            <tr>
                <th></th>
                <th class="text-center table-number">Código Cuenta</th>
                <th class="text-left table-number">Nombre Cuenta</th>
<!--                <th class="text-center table-number">Debe</th>
                <th class="text-center table-number">Haber</th>-->
                <th class="text-center table-number">Saldo</th>
                <!--<th class="text-center table-number">saldo</th>-->
                <th class="text-center table-number">Debe</th>
                <th class="text-center table-number">Haber</th>
<!--                <th class="text-center table-number">Debe</th>
                <th class="text-center table-number">Haber</th>-->
                <th class="text-center table-number">saldo</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(cuentaContable,key) in cuentasContables">
                <tr :key="cuentaContable.id_cierre_mensual">
                    <td class="detail-link" style="padding-left: 12px; width: 5%">
                        <div @click="mostrarCentros(key)" class="link"
                             v-if="[4,5,6].indexOf(cuentaContable.id_tipo_cuenta) >= 0"></div>
                    </td>
                    <td class="text-center" style="width: 15%">{{ cuentaContable.cta_contable }}</td>
                    <td class="text-left" style="width: 30%" v-if="cuentaContable.id_nivel_cuenta === 1">{{
                        cuentaContable.nombre_cuenta }}
                    </td>
                    <td class="text-left" style="padding-left:2em; width: 30%"
                        v-if="cuentaContable.id_nivel_cuenta === 2">{{ cuentaContable.nombre_cuenta }}
                    </td>
                    <td class="text-left" style="padding-left:4em; width: 30%"
                        v-if="cuentaContable.id_nivel_cuenta === 3">{{ cuentaContable.nombre_cuenta }}
                    </td>
                    <td class="text-left" style="padding-left:6em; width: 30%"
                        v-if="cuentaContable.id_nivel_cuenta === 4">{{ cuentaContable.nombre_cuenta }}
                    </td>
                    <td class="text-left" style="padding-left:8em; width: 30%"
                        v-if="cuentaContable.id_nivel_cuenta === 5">{{ cuentaContable.nombre_cuenta }}
                    </td>
                    <td class="text-left" style="padding-left:10em; width: 30%"
                        v-if="cuentaContable.id_nivel_cuenta === 6">{{ cuentaContable.nombre_cuenta }}
                    </td>
                    <td class="text-left" style="padding-left:12em; width: 30%"
                        v-if="cuentaContable.id_nivel_cuenta === 7">{{ cuentaContable.nombre_cuenta }}
                    </td>
<!--                    <td class="text-center" style="width: 10%">{{ cuentaContable.debe_anterior |
                        formatMoney(2)}}
                    </td>
                    <td class="text-center" style="width: 10%">{{ cuentaContable.haber_anterior |
                        formatMoney(2)}}
                    </td>-->
                    <td class="text-center" style="width: 10%">{{ cuentaContable.saldo_anterior |
                        formatMoney(2)}}
                    </td>
                    <!--<td class="text-center" style="width: 10%">{{ cuentaContable.saldo_anterior | formatMoney(2)}}</td>-->
                    <td class="text-center" style="width: 15%">{{ cuentaContable.debe | formatMoney(2)}}</td>
                    <td class="text-center" style="width: 15%">{{ cuentaContable.haber | formatMoney(2)}}</td>
                    <!--<td class="text-center" style="width: 15%">{{
                        Number(cuentaContable.debe_anterior)+Number(cuentaContable.debe) | formatMoney(2)}}
                    </td>
                    <td class="text-center" style="width: 15%">{{
                        Number(cuentaContable.haber_anterior)+Number(cuentaContable.haber) | formatMoney(2)}}
                    </td>-->
                    <!--<td class="text-center" style="width: 10%">{{ Number(cuentaContable.saldo_anterior)+Number(cuentaContable.saldox) | formatMoney(2)}}</td>-->
                    <td class="text-center" style="width: 10%">{{ cuentaContable.saldox |
                        formatMoney(2)}}
                    </td>
                </tr>

                <tr :key="cuentaContable.id_cuenta_contable" v-if="cuentaContable.mostrar">
                    <td colspan="10">
                        <table class="table table-responsive-md ">
                            <thead>
                            <tr>
                                <th class="text-left table-number">Descripción Centro Costo</th>
                                <th></th>
                                <th class="text-center table-number">Debe</th>
                                <th class="text-center table-number">Haber</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                    :key="detalle_sucursal.cta_contable"
                                    v-for="detalle_sucursal in cuentaContable.detalle_sucursales">
                                <td class="text-left" style="width: 50%">{{ detalle_sucursal.descripcion }}</td>
                                <td style="width: 10%"></td>
                                <td class="text-center" style="width: 15%">{{ detalle_sucursal.debex }}</td>
                                <td class="text-center" style="width: 15%">{{ detalle_sucursal.haberx }}</td>
                                <td style="width: 10%"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

            </template>
            </tbody>
            <tfoot>
            <!--<tr>
                <td class="text-right" colspan="3"><strong>Total Transacciones</strong></td>
                <td class="text-center"><strong>C$ {{total_debe | formatMoney(2)}}</strong></td>
                <td class="text-center"><strong>C$ {{total_haber | formatMoney(2)}}</strong></td>
                <td class="text-center"><strong>C$ {{total_debe | formatMoney(2)}}</strong></td>
                <td class="text-center"><strong>C$ {{total_haber | formatMoney(2)}}</strong></td>
                <td class="text-center"><strong>C$ {{total_debe | formatMoney(2)}}</strong></td>
                <td class="text-center"><strong>C$ {{total_haber | formatMoney(2)}}</strong></td>
            </tr>-->
            </tfoot>
        </table>
        </div>
        <template v-if="loading">
            <BlockUI  :url="url"></BlockUI>
        </template>

    </b-card>

</template>
<script>
  import {
    BAlert,
    BButton,
    BCard,
    BCardFooter,
    BCardText,
    BFormDatepicker,
    BFormFile,
    BFormGroup,
    BLink,
    BRow,
    BFormSelect
  } from 'bootstrap-vue'
  import vSelect from 'vue-select'
  import es from 'vuejs-datepicker/dist/locale/translations/es'
  import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
  import nivel_cuenta from '../../../api/contabilidad/niveles_cuentas'
  import reportes_financieros from '../../../api/contabilidad/reportes_financieros'
  import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
  import axios from 'axios'
  import loadingImage from '../../../assets/images/loader/block50.gif'
  //import DateTimePicker
  import flatPickr from 'vue-flatpickr-component'


  var fecha_actual = new Date();
  fecha_actual.setHours(23, 59, 59, 999);

  export default {
    components: {
      BCard,
      BCardText,
      BLink,
      BFormGroup,
      vSelect,
      BFormFile,
      BButton,
      BAlert,
      BCardFooter,
      BRow,
      BFormDatepicker,
      BFormSelect,
      flatPickr
    },
    data() {
      return {
        loading:false,
        url: loadingImage,
        descargando: false,
        niveles_cuenta: [],
        disabledDates: {
          to: '', // Disable all dates up to specific date
          from: fecha_actual, // Disable all dates after specific date
        },
        es: es,
        format: "d MMMM yyyy",
        fecha_inicialx: '',//new Date(),
        fecha_finalx: '',//new Date(),
        filter: {
          nivel_cuenta: "",
          fecha_inicial: 'Invalid date',//moment(new Date()).format("YYYY-MM-DD"),
          fecha_final: 'Invalid date',//moment(new Date()).format("YYYY-MM-DD"),
          currency: "NIO",
          formato: 'xls',
        },
        cuentasContables: [],
        errorMessages: [],
        total: 0
      }

    },
    computed: {
      total_debe() {
        return this.cuentasContables.reduce((carry, item) => {
          return (carry + (item.id_nivel_cuenta === 2 ? Number(item.debe) : 0))
        }, 0)
      },
      total_haber() {
        return this.cuentasContables.reduce((carry, item) => {
          return (carry + (item.id_nivel_cuenta === 2 ? Number(item.haber) : 0))
        }, 0)
      },
    },
    methods: {
      mostrarCentros(key) {
        console.log(key);
        console.log("mostrar centro");
        if (this.cuentasContables[key].mostrar) {
          this.cuentasContables[key].mostrar = 0;
        } else {
          this.cuentasContables[key].mostrar = 1;
        }
        //console.log(this.cuentasContables[key].mostrar);
      },
      /*seleccionarUsuario(e) {
            const client = e.target.value;
            this.usuarioBusqueda = client;
        },*/

      onDateSelect(date) {
        this.filter.fecha_inicial = moment(date).format("DD-MM-YYYY"); //
      },
      onDateSelect2(date) {
        this.filter.fecha_final = moment(date).format("DD-MM-YYYY"); //
      },
      obtenerBalanzaComprobacion(){
        const self = this;
        reportes_financieros.obtenerBalanzaComprobacion(self.filter, data => {
          data.forEach((cuenta, key) => {
            data[key].mostrar = 0;
            //console.log(JSON.parse(data[key].detalle_sucursales));
            // data[key].detalle_sucursales = JSON.parse(data[key].detalle_sucursales);
          });
          self.cuentasContables = data;
        }, err => {
          console.log(err);
          this.$toast({
            component: ToastificationContent,
            props: {
              title: '',
              icon: 'CoffeeIcon',
              variant: 'danger',
              text: err
            },
          })
        })
      },
      obtenerTodosNivelesCuenta() {
        const self = this;
        nivel_cuenta.obtenerTodosNivelesCuenta(
            data => {
              self.niveles_cuenta = data;
              self.filter.nivel_cuenta = self.niveles_cuenta[1];
            },
            err => {
              console.log(err);
            }
        );
      },
      descargarReporte(id_nivel_cuenta, fecha_inicial, fecha_final,currency, extension) {
        const self = this;
        self.loading = true;
        let url = 'contabilidad/estados-financieros/balanza-comprobacion/reporte/' + id_nivel_cuenta + '/' + fecha_inicial + '/' + fecha_final + '/' + currency + '/' + extension;
        axios.get(url,{responseType: 'blob'})
            .then(({data}) => {
              let blob = new Blob([data], {type: 'application/pdf'});
              extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});
              let link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = 'BalanzaComprobacion.' + extension;
              link.click();
              self.loading = false;
            }).catch(function (error) {
          // console.log(error);
          this.$toast({
                component : ToastificationContent,
                props: {
                  title : 'Notificación',
                  icon : 'CheckIcon',
                  text : 'Error descargando el archivo....',
                  variant : 'danger',
                }
              },
              {
                position : 'bottom-right'
              });
          self.loading = false;
        });
      },
      /*downloadItem (url) {
              if(!this.descargando){
                  this.descargando=true;
          alertify.success("Descargando datos, espere un momento.....",5);
          axios.get(url, { responseType: 'blob' })
                  .then(({ data }) => {
                      let blob = new Blob([data], { type: 'application/pdf' })
                      let link = document.createElement('a');
                      link.href = window.URL.createObjectURL(blob)
                      link.download = 'Reporte_Accesos.pdf';
                      link.click()
                      this.descargando=false;
                  })
              }else{
                  alertify.warning("Espere a que se complete la descarga anterior",5);
              }
      },*/
    },
    mounted() {
      this.obtenerTodosNivelesCuenta();
    }
  }
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '@core/scss/vue/libs/vue-flatpicker.scss';
</style>
