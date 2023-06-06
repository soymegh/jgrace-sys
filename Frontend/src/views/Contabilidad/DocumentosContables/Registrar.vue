<template>
    <b-card>
        <b-row>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Tipo Documento</label>
                    <v-select
                            :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                            :options="tipos_documentos"
                            label="descripcion"
                            v-model="form.tipoDocumento"
                            v-on:change="cambiarFoco()"
                            v-on:input="obtenerNumeroDocumento"
                    />
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.tipoDocumento"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Numero Documento <small>(Automático)</small></label>
                    <input class="form-control" readonly type="text" v-model="form.num_documento">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.num_documento"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Fecha Emision</label>
                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            @input="onDateSelect()"
                            @keyup.enter="$refs.moneda.focus"
                            class="mb-0"
                            local="es"
                            placeholder="F. Emision"
                            ref="fecha"
                            selected-variant="primary"
                            v-model="fechax"

                    >

                    </b-form-datepicker>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.fecha_emision"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>T/C</label>
                    <input class="form-control" disabled type="text" v-model="form.t_cambio">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.t_cambio"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
        </b-row>
        <b-row>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for>Concepto</label>
                    <input class="form-control" ref="concepto" type="text"
                           v-model="form.concepto">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.concepto"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
        </b-row>

        <div v-if="!form.tipoDocumento">
            <div class="alert alert-info">
                <span>Se requiere que seleccione un tipo de documento para continuar.</span>
            </div>
            <hr>
        </div>


        <b-row>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for>Cuenta Contable</label>
                    <!--<typeahead
                            :initial="detalleForm.cuentaContable"
                            :trim="80" :url="cuentasBusquedaURL" @input="seleccionarCuentaContable"
                            ref="cuenta_contable" style="width: 100%;"></typeahead>-->
                    <v-select :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'" :filterable="false"
                              :options="paginated" @search="onSearch" label="nombre_cuenta_completo"
                              v-model="detalleForm.cuentaContable" v-on:input="seleccionarCuentaContable">
                        <li class="d-flex" slot="list-footer">
                            <b-button :disabled="!hasPrevPage" @click="offset -= limit" class="flex-grow-1"
                                      style="cursor: pointer" variant="secondary">Prev
                            </b-button>
                            <b-button :disabled="!hasNextPage" @click="offset += limit" class="flex-grow-1"
                                      style="cursor: pointer" variant="secondary">Next
                            </b-button>
                        </li>
                    </v-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.cuentaContablex"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <!--<div class="col-sm-6">
                <div class="form-group">
                    <label for>Centro de Costo</label>
                    <v-select
                            :disabled="centro_costo_deshabilitado"
                            :options="centros_costos_ingresos"
                            label="descripcion"
                            ref="centro_costo_ingreso"
                            v-model="detalleForm.centro_costo_ingreso"
                            v-on:change="centroCostoEvento()"
                    ></v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.centro_costo_ingresox"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>-->


            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Concepto</label>
                    <input @keyup.enter="revisarConceptoMov" class="form-control" ref="concepto_mov"
                           type="text" v-model="detalleForm.concepto">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.conceptox"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Moneda</label>
                    <v-select
                            :options="monedas"
                            label="descripcion"
                            ref="moneda_x"
                            v-model="detalleForm.moneda_x"
                    ></v-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.moneda_x"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Debe</label>
                    <input @focus="$event.target.select()" @keyup.enter="$refs.haber.focus"
                           class="form-control" min="0" ref="debe" type="number" v-model.number="detalleForm.debe">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.debex"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Haber</label>
                    <input @focus="$event.target.select()" @keyup.enter="$refs.agregar.focus"
                           class="form-control" min="0" ref="haber" type="number"
                           v-model.number="detalleForm.haber">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.haberx"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>

                </div>
            </div>


            <div class="col-sm-2">
                <label for=""></label>
                <div class="form-group">
                    <label for>&nbsp;</label>
                    <b-button @click="agregarDetalle" @keyup.enter="agregarEvento" ref="agregar"
                              variant="info">Agregar Línea
                    </b-button>
                </div>
            </div>
        </b-row>

        <b-row>
            <div class="col-sm-12">
                <ul class="error-messages">
                    <li
                            :key="message"
                            v-for="message in errorMessages.detalleMovimientos"
                            v-text="message"
                    ></li>
                </ul>
                <table class="table table-bordered table-hover table-responsive">
                    <thead>
                    <tr>
                        <!--                        <th style="min-width:200px">Centro C/I</th>-->
                        <th style="min-width:200px">Cuenta Contable</th>
                        <th style="min-width:200px">Concepto</th>
                        <th style="min-width:200px">Moneda Original</th>
                        <th style="min-width:200px">Debe Moneda Original</th>
                        <th style="min-width:200px">Haber Moneda Original</th>
                        <th style="min-width:200px">Debe C$</th>
                        <th style="min-width:200px">Haber C$</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in form.detalleMovimientos">
                        <tr>
                            <!--                            <td style="width: 20%">
                                                            <v-select
                                                                    v-model="item.centro_costo_ingreso"
                                                                    label="descripcion"
                                                                    :options="centros_costos_ingresos"
                                                                    :disabled="item.centro_costo_ingreso_deshabilitada"
                                                            ></v-select>
                                                            <ul class="error-messages">
                                                                <li
                                                                        v-for="message in errorMessages[`detalleMovimientos.${index}.centro_costo_ingreso.id_centro`]"
                                                                        :key="message"
                                                                        v-text="message">
                                                                </li>
                                                            </ul>
                                                        </td>-->


                            <td style="width: 25%">
                                <input class="form-control" disabled type="text"
                                       v-model="item.cuentaContable.nombre_cuenta_completo">
                                <!--<v-select disabled
                                  v-model="item.cuentaContable"
                                  label="nombre_cuenta_completo"

                                  :options="cuentas_contables"
                                ></v-select>-->
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages[`detalleMovimientos.${index}.cuentaContable.id_cuenta_contable`]"
                                            v-text="message">
                                    </li>
                                </ul>
                            </td>
                            <td style="width: 21%">
                                <input class="form-control" type="text" v-model="item.concepto">
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages[`detalleMovimientos.${index}.concepto`]"
                                            v-text="message">
                                    </li>
                                </ul>
                            </td>

                            <td style="width: 20%">
                                <v-select
                                        :options="monedas"
                                        label="descripcion"
                                        v-model="item.monedaMov"
                                ></v-select>
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages[`detalleMovimientos.${index}.monedaMov.id_moneda`]"
                                            v-text="message">
                                    </li>
                                </ul>
                            </td>

                            <td style="width: 16%">
                                <input :disabled="!(item.tipo === 1)"
                                       @change="item.partida_valida = ((item.debeORG === 0 &&  item.haberORG > 0)||(item.haberORG === 0 &&  item.debeORG > 0))"
                                       class="form-control" min="0"
                                       type="number"
                                       v-model.number="item.debe">
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages[`detalleMovimientos.${index}.debe`]"
                                            v-text="message">
                                    </li>
                                </ul>
                            </td>

                            <td style="width: 16%">
                                <input :disabled="!(item.tipo === 2)"
                                       @change="item.partida_valida = ((item.haberORG === 0 &&  item.debeORG > 0)||(item.debeORG === 0 &&  item.haberORG > 0))"
                                       class="form-control" min="0"
                                       type="number"
                                       v-model.number="item.haber">
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages[`detalleMovimientos.${index}.haber`]"
                                            v-text="message">
                                    </li>
                                </ul>
                            </td>

                            <td>
                                <strong>{{monto_debe_cordobas(item)}}</strong>
                            </td>

                            <td>
                                <strong>{{monto_haber_cordobas(item)}}</strong>
                            </td>

                            <td style="width: 2%">
                                <b-button @click="eliminarLinea(item, index)" class="btn-icon" variant="flat-warning">
                                    <feather-icon icon="Trash2Icon"></feather-icon>
                                </b-button>
                            </td>
                        </tr>
                        <tr></tr>
                    </template>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2" style="width: 15%">
                        </td>
                        <td class="text-right" colspan="3" style="width: 30%">Totales</td>
                        <td class="item-footer" colspan="1" style="width: 20%">
                            <strong class="item-dark form-control">{{total_debe | formatMoney(2)}}</strong>
                        </td>
                        <td class="item-footer" colspan="1" style="width: 20%">
                            <strong class="item-dark form-control">{{total_haber | formatMoney(2)}}</strong>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </b-row>

        <b-card-footer>
            <div class="text-right">
                <router-link :to="{ name: 'documentos-contables' }" class="mx-1">
                    <b-button variant="secondary">Cancelar</b-button>
                </router-link>
                <b-button :disabled="btnAction !== 'Registrar' ? true : false"
                          @click="registrarDocumento"
                          ref="registrar"
                          variant="primary"
                >{{ btnAction }}
                </b-button>
            </div>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>
<script type="text/ecmascript-6">
  import vSelect from 'vue-select'
  import {
    BAlert,
    BButton,
    BCard,
    BCardFooter,
    BCol,
    BFormCheckbox,
    BFormDatepicker,
    BFormGroup,
    BPaginationNav,
    BRow,
    VBTooltip
  } from 'bootstrap-vue'
  import documento_contable from '../../../api/contabilidad/documentos_contables'
  import loadingImage from '../../../assets/images/loader/block50.gif'
  import es from 'vuejs-datepicker/dist/locale/translations/es'
  import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
  import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
  import tc from "../../../api/contabilidad/tasas-cambio";
  import Round from '../../../assets/custom-scripts/Round'

  export default {
    components: {
      BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, BFormCheckbox, BFormGroup, BAlert,
      vSelect
    },
    directives: {
      'b-tooltip': VBTooltip,
    },
    data() {
      return {
        search: '',
        offset: 0,
        limit: 10,
        loading: true,
        url: loadingImage,
        centro_costo_deshabilitado: true,
        cuentasBusqueda: {},
        cuentasBusquedaURL: "/contabilidad/cuentas-contables/buscar?q",
        contador: 0,
        es: es,
        format: "d MMMM yyyy",
        monedas: [],
        cuentas_contables: [],
        tipos_documentos: [],
        centros_costos_ingresos: [],
        fechax: new Date(),
        form: {
          t_cambio: 0,
          num_documento: "",
          fecha_emision: moment(new Date()).format("YYYY-MM-DD"),
          concepto: "",
          valor: 0,
          codigo_documento: 1,
          detalleMovimientos: []
        },
        detalleForm: {
          centro_costo_ingreso: "",
          debe: 0,
          haber: 0,
          debeORG: 0,
          haberORG: 0,
          concepto: "",
          centro_costo_ingreso_deshabilitada: true,
          cuentaContable: [],
          moneda_x: ""
        },
        btnAction: "Registrar",
        errorMessages: []
      }
    },
    computed: {
      total_debe() {
        let total_debe_rounded = this.form.detalleMovimientos.reduce((carry,item) =>{
          return carry + Number(item.debe)
        },0);
        return Round.round(total_debe_rounded,2)
      },
      total_haber() {
        let total_haber_rounded = this.form.detalleMovimientos.reduce((carry, item) => {
          return carry + Number(item.haber)
        },0);
        return Round.round(total_haber_rounded,2)
      },
      filtered() {
        return this.cuentas_contables.filter((nombre_cuenta) =>
            nombre_cuenta.nombre_cuenta.toLowerCase().includes(this.search.toLowerCase())
        )
      },
      paginated() {
        return this.filtered.slice(this.offset, this.limit + this.offset)
      },
      hasNextPage() {
        const nextOffset = this.offset + this.limit;
        return Boolean(
            this.filtered.slice(nextOffset, this.limit + nextOffset).length
        )
      },
      hasPrevPage() {
        const prevOffset = this.offset - this.limit;
        return Boolean(
            this.filtered.slice(prevOffset, this.limit + prevOffset).length
        )
      },
    },
    methods: {
      monto_debe_cordobas(itemX) {
        let tasa_cambio = this.form.t_cambio;
        if (itemX.monedaMov.id_moneda === 2) {
          // itemX.debeORG = Number((itemX.debe * tasa_cambio).toFixed(2));
          itemX.debeORG = Round.round(itemX.debe * tasa_cambio, 4);
        }

        if (!isNaN(itemX.debeORG)) {
          return itemX.debeORG;
        } else return 0;
      },

      monto_haber_cordobas(itemX) {
        let tasa_cambio = this.form.t_cambio;
        if (itemX.monedaMov.id_moneda === 2) {
          // itemX.haberORG = Number((itemX.haber * tasa_cambio).toFixed(2));
          itemX.haberORG = Round.round(itemX.haber * tasa_cambio, 4);
        }
        if (!isNaN(itemX.haberORG)) {
          return itemX.haberORG;
        } else return 0;
      },

      onSearch(query) {
        this.search = query;
        this.offset = 0
      },

      seleccionarCuentaContable() {
        //const cuenta = e.target.value;
        const self = this;
        // self.detalleForm.cuentaContable = cuenta;
        if ([4, 5, 6].indexOf(Number(self.detalleForm.cuentaContable.id_tipo_cuenta)) < 0) {
          self.detalleForm.centro_costo_ingreso = null;
          self.centro_costo_deshabilitado = true;
          // self.$refs.concepto_mov.focus();
        } else {
          self.centro_costo_deshabilitado = false;
          // self.$refs.centro_costo_ingreso.$refs.search.focus();
        }

      },
      onDateSelect(date) {
        this.form.fecha_emision = this.fechax;
        this.obtenerTC();
        this.obtenerCodigo();
      },
      obtenerTC() {
        // console.log('ejecutando obtener tc con fecha: ' + this.form.fecha_entrada_x);
        const self = this;
        tc.obtenerTC({
          fecha: self.form.fecha_emision
        }, data => {
          if (data.tasa !== null) {
            self.form.t_cambio = Number(data.tasa);
          } else {
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'InfoIcon',
                    text: 'No se encuentran tasas de cambia para esta fecha.',
                    variant: 'warning',

                  }
                },
                {
                  position: 'bottom-right'
                });
            self.form.t_cambio = 0;
          }
          self.loading = false;
        }, err => {
          if (err.fecha) {
            self.form.t_cambio = 0;
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'InfoIcon',
                    text: 'No se encuentran tasas de cambia para esta fecha.',
                    variant: 'warning',

                  }
                },
                {
                  position: 'bottom-right'
                });
            self.loading = false;
          }
        })
      },
      obtenerCodigo() {
        var self = this;
        self.loading = true;
        documento_contable.obtenerCodigo({
          id_tipo_doc: self.form.tipoDocumento.id_tipo_doc,
          fecha_doc: self.form.fecha_emision
        }, data => {
          //console.log(data);
          self.form.codigo_documento = data.codigo.secuencia;
          self.form.t_cambio = Number(data.t_cambio.tasa);
          self.obtenerNumeroDocumento();
          self.loading = false;
        }, err => {
          this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Notificación',
                  icon: 'InfoIcon',
                  text: 'Ha ocurrido un error ' + err,
                  variant: 'warning',
                }
              },
              {
                position: 'bottom-right'
              });
          self.loading = false;
        })
      },
      /* obtenerMonedas() {
           var self = this;
           monedas.cargarMonedas(
               data => {
                   self.monedas = data;
                   self.form.moneda = self.monedas[0];
               },
               err => {
                   console.log(err);
               }
           );
       },
       obtenerTiposDocumentosTodos() {
           var self = this;
           tipo_documento.obtenerTodos(
               data => {
                   self.tipos_documentos = data;
                   //console.log();
                   self.form.tipoDocumento = self.tipos_documentos[0];
                   self.obtenerCodigo()
               },
               err => {
                   console.log(err);
               }
           );
       },
       obtenerSucursalesTodas() {
           var self = this;
           centro_costo_ingreso.obtenerTodos(
               data => {
                   self.centros_costos_ingresos = data;
               },
               err => {
                   console.log(err);
               }
           );
       },*/

      nuevo() {
        var self = this;
        documento_contable.nuevo({}, data => {
              self.tipos_documentos = data.tipos_documentos;
              self.form.tipoDocumento = self.tipos_documentos[0];
              self.monedas = data.monedas;
              self.cuentas_contables = data.cuentas_contables;
              self.detalleForm.moneda_x = self.monedas[1];
              self.centros_costos_ingresos = data.centro_costos_ingresos;
              self.form.t_cambio = Number(data.t_cambio.tasa);
              self.obtenerCodigo()
              self.loading = false;

            },
            err => {
              console.log(err);
            })

      },

      agregarDetalle() {

        //  if(this.detalleForm.sucursal){
        //  this.errorMessages.sucursalx = ['correcto']
        if (this.detalleForm.cuentaContable) {
          if (Number(this.detalleForm.debe.toFixed(4)) > 0 || Number(this.detalleForm.haber.toFixed(4)) > 0) {
            if (Number(this.detalleForm.debe.toFixed(4)) > 0 && Number(this.detalleForm.haber.toFixed(4)) > 0) {
              this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'InfoIcon',
                      text: 'No se puede agregar monto en ambos campos',
                      variant: 'warning',
                    }
                  },
                  {
                    position: 'bottom-right'
                  });
            } else {
              let tasa_cambio = 1;
              let debe = 0;
              let debe_org = 0;
              let haber = 0;
              let haber_org = 0;
              if (this.detalleForm.moneda_x.id_moneda === 2) { // Dolares
                tasa_cambio = this.form.t_cambio;
                debe = this.detalleForm.debe;
                haber = this.detalleForm.haber;
                debe_org = Round.round(debe * tasa_cambio, 4);
                haber_org = Round.round(haber * tasa_cambio, 4);

              } else if (this.detalleForm.moneda_x.id_moneda === 1) { // cordobas

                tasa_cambio = this.form.t_cambio;
                debe_org = this.detalleForm.debe;
                haber_org = this.detalleForm.haber;
                debe = Round.round(debe_org / tasa_cambio, 4);
                haber = Round.round(haber_org / tasa_cambio, 4);

              }
              let tipox = 0;//1- tipo debe 2- tipo haber
              if (this.detalleForm.debe > this.detalleForm.haber) {
                tipox = 1;
              } else {
                tipox = 2;
              }

              this.form.detalleMovimientos.push({
                cuentaContable: this.detalleForm.cuentaContable,
                centro_costo_ingreso: this.detalleForm.centro_costo_ingreso,
                centro_costo_ingreso_deshabilitada: this.centro_costo_deshabilitado,
                monedaMov: this.detalleForm.moneda_x,
                debe: debe,
                haber: haber,
                debeORG: debe_org,
                haberORG: haber_org,
                concepto: this.detalleForm.concepto,
                tipo: tipox
              });
              this.detalleForm.cuentaContable = {};
              // this.detalleForm.sucursal=''
              this.detalleForm.debe = 0;
              this.detalleForm.haber = 0;
              this.detalleForm.debeORG = 0;
              this.detalleForm.haberORG = 0;
              //this.detalleForm.concepto = '';
              this.detalleForm.centro_costo_ingreso = null;
              this.centro_costo_deshabilitado = true;
              this.total_debe > this.total_haber ? this.form.valor_me = Round.round(this.total_debe, 2) : this.form.valor_me = Round.round(this.total_haber, 2);
              this.total_debe > this.total_haber ? this.form.valor = Round.round(this.total_debe * tasa_cambio, 2) : this.form.valor = Round.round(this.total_haber * tasa_cambio, 2);

            }
          } else {
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'InfoIcon',
                    text: 'El monto debe ser mayor a cero',
                    variant: 'warning',
                  }
                },
                {
                  position: 'bottom-right'
                });
          }

        } else {
          this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Notificación',
                  icon: 'InfoIcon',
                  text: 'Debe seleccionar una cuenta contable',
                  variant: 'warning',
                }
              },
              {
                position: 'bottom-right'
              });
        }

        /*}else{
          alertify.warning("Debe seleccionar una sucursal",5);
        }*/
      },

      eliminarLinea(item, index) {
        if (this.form.detalleMovimientos.length >= 1) {
          this.form.detalleMovimientos.splice(index, 1);

        } else {
          this.form.detalleMovimientos = [];
        }
      },

      obtenerNumeroDocumento() {
        var self = this;
        self.form.num_documento = self.form.tipoDocumento.prefijo + '-' + self.form.codigo_documento;
      },

      cambiarFoco() {
        var self = this;
        if (self.contador > 0) {
          self.$refs.moneda.$refs.search.focus();
        }
        self.contador++;
      },

      revisarConceptoMov() {
        var self = this;
        if (self.detalleForm.concepto !== '') {
          self.$refs.debe.focus();
        } else {
          self.$refs.concepto_mov.focus();
        }
      },

      centroCostoEvento() {
        var self = this;
        if (self.contador > 0) {

        }

      },


      conceptoEvento() {
        var self = this;
        if (self.contador > 0) {
          self.$refs.cuenta_contable.open();
          //self.$refs.sucursal.$refs.search.focus();
        }
        //console.log(self.$refs.cuenta_contable);
      },

      agregarEvento() {
        var self = this;
        self.$refs.cuenta_contable.open();
      },

      registrarDocumento() {
        var self = this;
        self.loading = true;
        if ((self.total_debe > 0 && self.total_haber > 0) && (Round.round(self.total_debe, 2) === Round.round(self.total_haber, 2))) {
          self.btnAction = "Registrando, espere....";
          documento_contable.registrar(
              self.form,
              data => {
                this.$toast({
                      component: ToastificationContent,
                      props: {
                        title: 'Notificación',
                        icon: 'InfoIcon',
                        text: 'Documento registrado correctamente',
                        variant: 'success',
                      }
                    },
                    {
                      position: 'bottom-right'
                    });
                self.loading = false;
                this.$router.push({
                  name: "documentos-contables"
                });
              },
              err => {
                self.errorMessages = err;
                this.$toast({
                      component: ToastificationContent,
                      props: {
                        title: 'Notificación',
                        icon: 'InfoIcon',
                        text: 'Ha ocurrido un error, revisar datos faltantes',
                        variant: 'warning',
                      }
                    },
                    {
                      position: 'bottom-right'
                    });
                self.btnAction = "Registrar";
                self.loading = false;
              }
          );
        } else {
          this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Notificación',
                  icon: 'InfoIcon',
                  text: 'La sumatoria entre las columnas debe y haber deben ser igual a cero',
                  variant: 'warning',
                }
              },
              {
                position: 'bottom-right'
              });
          self.loading = false;
        }
      }
    },
    mounted() {
      /*  this.obtenerTiposDocumentosTodos();
        this.obtenerSucursalesTodas();
        this.obtenerMonedas();*/
      this.nuevo();
      this.$refs.concepto.focus();
    }
  }
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
    @import '../../../@core/scss/vue/libs/vue-sweetalert';

    .search-field {
        min-width: 120px;
    }

    .table {
        a {
            color: #2675dc;
        }

        .table-number {
            width: 60px;
        }

        .action {
            width: 180px;
        }

        .detail-link {
            width: 60px;
            position: relative;

            .link {
                width: 10px;
                height: 4px;
                margin-left: auto;
                margin-right: auto;
                cursor: pointer;
                margin-top: 8px;
                background-color: #595959;
                border: 1px solid #595959;

                &:before {
                    content: "";
                    width: 4px;
                    height: 10px;
                    left: 0px;
                    right: 0px;
                    cursor: pointer;
                    margin: 0px auto;
                    margin-top: -4px;
                    position: absolute;
                    background-color: #595959;
                    border: 1px solid #595959;
                }
            }
        }
    }
</style>
