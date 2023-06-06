<template>
    <b-card>
        <b-row>

            <template v-if="this.currency_id === 1">
                <div class="col-sm-2 sm-text-center form-inline justify-content-start">
                    <router-link :to="{ name: 'cajabanco-facturas-registrar' }" class="btn btn-success" tag="button">
                        <feather-icon icon="PlusIcon"></feather-icon>
                        Registrar
                    </router-link>
                </div>
            </template>
            <template v-else>
                <div class="col-sm-2 sm-text-center form-inline justify-content-start">
                    <router-link :to="{ name: 'cajabanco-facturas-registrar-dol' }" class="btn btn-success"
                                 tag="button">
                        <feather-icon icon="PlusIcon"></feather-icon>
                        Registrar
                    </router-link>
                </div>
            </template>


            <div
                    @keyup.enter="filter.page = 1;obtenerFacturas();"
                    class="col-sm-10 sm-text-center form-inline justify-content-end"
            >
                <b-form-select
                        class="mb-1 mr-sm-1 mb-sm-0 search-field"
                        v-model="filter.search.field"
                >
                    <option value="no_documento">No. Documento</option>
                </b-form-select>
                <input
                        class="form-control mb-1 mr-sm-1 mb-sm-0"
                        placeholder="Buscar"
                        type="text"
                        v-model="filter.search.value"
                >
                <b-button @click="filter.page = 1;obtenerFacturas();" v-b-tooltip.hover.top="'Buscar!'" variant="info">
                    <feather-icon icon="SearchIcon"></feather-icon>
                </b-button>
            </div>
        </b-row>
        <div class="table-responsive mt-3">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>No. Factura</th>
                    <th>Fecha</th>
                    <th>Vendedor</th>
                    <th>Sucursal</th>
                    <!--                    <th>Bodega</th>-->
                    <th>Cliente</th>
                    <th class="text-center table-number">Estado</th>
                    <th class="text-center table-number" colspan="2">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(factura,key) in facturas">
                    <tr :key="factura.id_factura">
                        <td class="detail-link">
                            <div @click="mostrarProductos(key)" class="link"
                                 v-b-tooltip.hover.top="'Mostrar Detalle'"></div>
                        </td>
                        <td>{{ factura.no_documento }}</td>
                        <td>{{ factura.f_factura }}</td>
                        <td>{{ factura.factura_vendedor.nombre_completo }}</td>
                        <td>{{ factura.factura_sucursal? factura.factura_sucursal.descripcion:'N/A' }}</td>
                        <!--                        <td>{{ factura.factura_bodega.descripcion }}</td>-->
                        <td>{{ factura.factura_cliente.nombre_comercial }}</td>
                        <td class="text-center">
                            <div v-if="factura.estado===0">
                                <span class="badge badge-danger">Anulada</span>
                            </div>
                            <div v-if="factura.estado===1">
                                <span class="badge badge-info">Facturada</span>
                            </div>
                            <div v-if="factura.estado===2">
                                <span class="badge badge-success">Despachada</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <a :disabled="descargando" @click.prevent="downloadItem('pdf',factura.id_factura)"
                               v-b-tooltip.hover.top="'Descargar factura'">
                                <feather-icon aria-hidden="true" icon="DownloadIcon"
                                              style="color: #0a91ff"></feather-icon>
                            </a>

                            <!--<router-link
                                    v-tooltip="'Mostrar Detalles de Factura'"
                                    :to="{ name: 'mostrar-factura', params: { id_factura: factura.id_factura } }"
                                    tag="a"
                            >
                              <i aria-hidden="true" class="fa fa-eye"></i>
                            </router-link>-->

                        </td>
                        <td class="text-center">
                            <a @click.prevent="anular(factura.id_factura)" v-b-tooltip.hover.top="'Anular Factura'"
                               v-if="factura.estado === 1">
                                <feather-icon icon="TrashIcon" style="color: red"></feather-icon>
                            </a>
                        </td>
                    </tr>
                    <tr :key="factura.codigo_factura" v-if="factura.mostrar">
                        <td></td>
                        <td colspan="7">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center">Código producto</th>
                                    <th>Descripción producto</th>
                                    <th>Unidad de medida</th>
                                    <th>Precio</th>
                                    <th>Cantidad Solicitada</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                        :key="productosDetalle.id_factura_producto"
                                        v-for="productosDetalle in factura.factura_productos">
                                    <td>{{ productosDetalle.codigo_producto }}</td>
                                    <td>{{ productosDetalle.descripcion_producto }}</td>
                                    <td>{{ productosDetalle.unidad_medida }}</td>
                                    <td>{{ productosDetalle.precio}}</td>
                                    <td>{{ productosDetalle.cantidad }}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="item-footer" colspan="3"></td>
                                    <td>Total Unidades</td>
                                    <td class="item-footer">
                                        <strong>{{factura.tot_unidades}}</strong>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </td>
                        <td></td>
                    </tr>
                </template>
                <tr v-if="!facturas.length">
                    <td class="text-center" colspan="10">Sin datos</td>
                </tr>
                </tbody>
            </table>
        </div>

        <b-card-footer>
            <pagination
                    :items="facturas"
                    :limit="filter.limit"
                    :limitOptions="filter.limitOptions"
                    :page="filter.page"
                    :total="total"
                    @changeLimit="changeLimit"
                    @changePage="changePage"
            ></pagination>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>

    </b-card>
</template>

<script type="text/ecmascript-6">
  import factura from "../../../api/CajaBanco/facturas";
  import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
  import axios from 'axios'
  import {
    BAlert,
    BButton,
    BCard,
    BCardFooter,
    BFormCheckbox,
    BFormCheckboxGroup,
    BFormDatepicker,
    BFormGroup,
    BFormSelect,
    BPaginationNav,
    BRow,
    VBTooltip,
  } from 'bootstrap-vue'
  import loadingImage from '../../../assets/images/loader/block50.gif'
  import vSelect from 'vue-select'


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
      BFormDatepicker,
      BAlert,
      BFormSelect,
    },
    directives: {
      'b-tooltip': VBTooltip,
    },
    data() {
      return {
        descargando: false,
        loading: true,
        url: loadingImage,   //It is important to import the loading image then use here
        filter: {
          page: 1,
          limit: 5,
          limitOptions: [5, 10, 15, 20],
          search: {
            field: "no_documento",
            value: ""
          }
        },
        facturas: [],
        total: 0,
        currency_id: 0,
      };
    },
    methods: {

      anular(id_factura) {

        this.$swal.fire({
          title: 'Esta seguro de anular esta factura?',
          text: "Digite la causa de la anulación de la factura",
          input: 'text',
          icon: 'warning',
          inputAttributes: {
            autocapitalize: 'off'
          },
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, anular factura'
        }).then((result) => {
          if (result.value) {
            factura.cancelar(
                {
                  id_factura: id_factura,
                  causa_anulacion: result.value
                },
                data => {
                  this.$swal.fire(
                      'Anulada',
                      'Los documentos vinculados con esta factura han sido anulados',
                      'success'
                  )
                  this.obtenerFacturas();
                },
                err => {
                  this.$swal.fire(
                      'No se puede anular factura!',
                      err,
                      'warning'
                  )
                }
            );


          }
        })

      },

      mostrarProductos(key) {
        if (this.facturas[key].mostrar) {
          this.facturas[key].mostrar = 0;
        } else {
          this.facturas[key].mostrar = 1;
        }
      },
      obtenerFacturas() {
        var self = this;
        self.loading = true;
        factura.obtenerFacturas(
            self.filter,
            data => {
              data.rows.forEach((facturas, key) => {
                data.rows[key].mostrar = 0;
              });
              self.facturas = data.rows;
              self.total = data.total;
              self.currency_id = Number(data.currency_id);
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
        this.obtenerFacturas();
      },
      changePage(page) {
        this.filter.page = page;
        this.obtenerFacturas();
      },
      downloadItem(extension, id_factura) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'inventario/facturas/reporte/' + extension + '/' + id_factura;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'FormatoFactura.' + extension;
                link.click()
                //this.descargando = false;
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
                    this.$toast({
                          component: ToastificationContent,
                          props: {
                            title: 'Notificación',
                            icon: 'CheckIcon',
                            text: 'Error descargando el archivo ' + error,
                            variant: 'warning',
                          }
                        },
                        {
                          position: 'bottom-right'
                        });
            self.descargando = false;
            self.loading = false;
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
        }
      },


    },
    /*components: {
      pagination: Pagination
    },*/
    mounted() {
      this.obtenerFacturas();
    }
  };
</script>
<style lang="scss">
    @import 'src/@core/scss/vue/libs/vue-select';

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
