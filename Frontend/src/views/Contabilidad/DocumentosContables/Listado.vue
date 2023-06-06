<template>
    <b-card>
        <b-row>
            <div class="col-md-6 sm-text-center">
                <router-link :to="{ name: 'documentos-contables-registrar' }" class="btn btn-success" tag="button">
                    <feather-icon icon="PlusCircleIcon"></feather-icon>
                    Registrar
                </router-link>
            </div>
            <div @keyup.enter="filter.page = 1;obtener();"
                 class="col-md-6 sm-text-center form-inline justify-content-end">
                <select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
                    <option value="num_documento">Código</option>
                </select>
                <input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text"
                       v-model="filter.search.value">
                <b-button @click="filter.page = 1;obtener();" v-b-tooltip.hover.top="'Buscar!'" variant="info">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th>Numero documento</th>
                    <th>Tipo Documento</th>
                    <th>Fecha documento</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center action">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(documento_contable,key) in documentos_contables">
                    <tr :key="documento_contable.id_documento">
                        <td class="detail-link">
                            <div @click="mostrarMovimientos(key)" class="link"
                                 v-b-tooltip.hover.top="'Mostrar detalles!'"></div>
                        </td>
                        <td>{{ documento_contable.num_documento }}</td>
                        <td>{{ documento_contable.documento_tipo.descripcion}}</td>
                        <td>{{ formatDate(documento_contable.fecha_emision) }}</td>
                        <td class="text-center">
                            <div v-if="documento_contable.estado===0">
                                <b-badge variant="danger"> Cancelado</b-badge>
                            </div>
                            <div v-if="documento_contable.estado===1">
                                <b-badge variant="info"> Emitido</b-badge>
                            </div>
                            <div v-if="documento_contable.estado===2">
                                <b-badge variant="success"> Aprobado</b-badge>
                            </div>
                        </td>
                        <td class="text-center">
                            <a :disabled="loading" v-b-tooltip.hover.top="'Descargar reporte de comprobante'"
                               @click.prevent="downloadItemDocumentoEspecifico('',documento_contable.id_documento)"><feather-icon icon="DownloadIcon" aria-hidden="true" style="color: #0a91ff"></feather-icon></a>
                        </td>
                    </tr>


                    <tr :key="documento_contable.num_documento" v-if="documento_contable.mostrar">
                        <td colspan="8">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <!--<th>Centro</th>-->
                                    <th colspan="2">Cuenta Contable</th>
                                    <th>Concepto</th>
                                    <th>Debe</th>
                                    <th>Haber</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr :key="movimiento.id_documento_cuenta"
                                    v-for="movimiento in documento_contable.movimientos_cuentas">

                                    <!--<td v-if="movimiento.centro_costo">{{ movimiento.centro_costo.codigo }}</td>
                                    <td v-else-if="movimiento.centro_auxiliar">{{ movimiento.centro_auxiliar.codigo }}</td>
                                    <td v-else>{{'N/A'}}</td>-->

                                    <td colspan="2">{{ movimiento.cuenta_contable.nombre_cuenta_completo }}</td>
                                    <td>{{ movimiento.concepto }}</td>
                                    <td>{{ movimiento.debe | formatMoney(2)}}</td>
                                    <td>{{ movimiento.haber| formatMoney(2)}}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="item-footer" colspan="3">Totales</td>
                                    <td class="item-footer" colspan="1">
                                        <strong class="item-dark form-control">{{total_debex(documento_contable)|formatMoney(2)}}</strong>
                                    </td>
                                    <td class="item-footer" colspan="1">
                                        <strong class="item-dark form-control">{{total_haberx(documento_contable)|formatMoney(2)}}</strong>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                </template>
                <tr v-if="!documentos_contables.length">
                    <td class="text-center" colspan="7">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>
        <b-card-footer>
            <pagination :items="documentos_contables" :limit="filter.limit" :limitOptions="filter.limitOptions"
                        :page="filter.page"
                        :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>

            <template v-if="loading">
                <BlockUI :url="url"></BlockUI>
            </template>
        </b-card-footer>
    </b-card>
</template>

<script type="text/ecmascript-6">
  import {
    BBadge,
    BButton,
    BCard,
    BCardFooter,
    BCol,
    BFormCheckbox,
    BFormDatepicker,
    BFormGroup,
    BPaginationNav,
    BRow,
    VBModal,
    VBTooltip
  } from 'bootstrap-vue'
  import documento_contable from '../../../api/contabilidad/documentos_contables'
  import loadingImage from '../../../assets/images/loader/block50.gif'
  import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
  import axios from "axios";
  import Ripple from 'vue-ripple-directive'
  import ToastificationContent from '../../../@core/components/toastification/ToastificationContent'

  export default {
    components: {
      BFormDatepicker, BRow, BCol, BCard, BCardFooter, BPaginationNav, BButton, BFormCheckbox, BFormGroup,
      BBadge
    },
    directives: {
      'b-tooltip': VBTooltip,
      'b-modal': VBModal,
      Ripple,
    },
    data() {
      return {
        loading: true,
        url: loadingImage,   //It is important to import the loading image then use here
        filter: {
          page: 1,
          limit: 20,
          limitOptions: [5, 10, 15, 20],
          search: {
            field: 'num_documento',
            value: '',
            status: 0
          }
        },
        documentos_contables: [],
        total: 0,
      }
    },
    methods: {
      anular(id_documento) {


        this.$swal.fire({
          title: 'Esta seguro de anular este documento contable?',
          text: "Digite la causa de la anulación del documento",
          input: 'text',
          inputAttributes: {
            autocapitalize: 'off'
          },
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, anular documento'
        }).then((result) => {
          if (result.value) {
            documento_contable.anular(
                {
                  id_documento: id_documento,
                  causa_anulacion: result.value
                },
                data => {
                  this.$swal.fire(
                      'Anulado',
                      'Los registros del documento han sido anulados',
                      'success'
                  )
                  //this.obtenerFacturas();
                },
                err => {
                  this.$swal.fire(
                      'No se puede anular este documento!',
                      err,
                      'warning'
                  )
                }
            );


          }
        })

      },
      downloadItemDocumentoEspecifico(extension, id_documento) {
        const self = this;
        self.$swal.fire({
          title: 'Seleccione formato para descarga',
          showDenyButton: true,
          showConfirmButton: true,
          denyButtonText: 'PDF',
          confirmButtonText: 'EXCEL',
          icon: 'question'
        }).then((result) => {
          if (result.isConfirmed) {
            extension = 'xls';
          } else if (result.isDenied) {
            extension = 'pdf';
          }
          if (!this.descargando) {
            self.loading = true;
            let url = 'contabilidad/documentos-contables/reporte-especifico/' + extension + '/' + id_documento;
            let nameOfReport = 'DocumentoContable' + id_documento + '.';
            this.descargando = true;
            axios.get(url, {responseType: 'blob'})
                .then(({data}) => {
                  let blob = new Blob([data], {type: 'application/pdf'})

                  extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                  let link = document.createElement('a');
                  link.href = window.URL.createObjectURL(blob);
                  link.download = nameOfReport + extension;
                  link.click();
                  this.descargando = false;
                  self.loading = false;
                  self.descargando = false;
                  this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'CheckIcon',
                      text: 'Su archivo se ha descargado correctamente',
                      variant: 'success',
                    }
                  }, {
                    position: 'bottom-right'
                  });
                }).catch(function (error) {
              self.descargando = false;
              self.loading = false;
              self.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'CheckIcon',
                      text: 'Error descargando el archivo ',
                      variant: 'warning',
                    }
                  },
                  {
                    position: 'bottom-right'
                  });

            })
          } else {
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'CheckIcon',
                    text: 'Espere a que se complete la descarga anterior',
                    variant: 'warning',
                  }
                },
                {
                  position: 'bottom-right'
                });
            self.descargando = false;
            self.loading = false;
          }
        });

      },

      formatDate(date) {
        return moment(date).format('YYYY-MM-DD')
      },
      mostrarMovimientos(key) {
        if (this.documentos_contables[key].mostrar) {
          this.documentos_contables[key].mostrar = 0;
        } else {
          this.documentos_contables[key].mostrar = 1;
        }
      },
      total_debex(documentoContable) {
        return Number((documentoContable.movimientos_cuentas.reduce((carry, item) => {
              return (carry + Number(item.debe));
            }
            , 0)).toFixed(4));
      },
      total_haberx(documentoContable) {
        return Number((documentoContable.movimientos_cuentas.reduce((carry, item) => {
              return (carry + Number(item.haber));
            }
            , 0)).toFixed(4));
      },
      obtener() {
        var self = this;
        self.loading = true;
        documento_contable.obtener(
            self.filter,
            data => {
              data.rows.forEach((documentos_contables, key) => {
                data.rows[key].mostrar = 0;
              });
              self.documentos_contables = data.rows;
              self.total = data.total;
              self.loading = false;
            },
            err => {
              self.loading = false;
              console.log(err);
            }
        );
      },
      changeLimit(limit) {
        this.filter.page = 1;
        this.filter.limit = limit;
        this.obtener();
      },
      changePage(page) {
        this.filter.page = page;
        this.obtener();
      },
    },
    /* components: {
       pagination: Pagination
     },*/
    mounted() {
      this.obtener();
    }
  }
</script>
<style lang="scss" scoped>
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
