<template>
    <b-row>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Documentos Contables
                </h3>
                <div class="row">

                <div class="col-md-12">
                    <b-form-select
                            v-model="formDocumentosContables.type"
                            :options="formDocumentosContables.optionsType"
                            class="mt-1"
                    />
                </div>
                    <div class="col-md-6">
                        <label for="fecha_inicial">* Fecha Inicial:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelectDocumentosCInicial"
                                class="mb-0"
                                local="es"
                                placeholder="f.inicial"
                                selected-variant="primary"
                                v-model="formDocumentosContables.f_inicial"
                                id="fecha_inicial"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_final">* Fecha Final:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelectDocumentosCFinal"
                                class="mb-0"
                                local="es"
                                placeholder="f.final"
                                selected-variant="primary"
                                v-model="formDocumentosContables.f_final"
                                id="fecha_final"
                        ></b-form-datepicker>
                    </div>

                    <a :disabled="descargando" @click.prevent="downloadItemDocumentosContables('pdf',formDocumentosContables.type,formDocumentosContables.f_inicial,formDocumentosContables.f_final)"
                       style="color: #ffffff;padding-left: 2px">
                        <b-button :disabled="descargando" class="mt-1 mr-1 ml-1" v-b-tooltip.hover.top="'PDF'" variant="danger">
                            PDF
                            <feather-icon icon="DownloadCloudIcon"></feather-icon>
                        </b-button>
                    </a>


                    <a :disabled="descargando" @click.prevent="downloadItemDocumentosContables('xls',formDocumentosContables.type,formDocumentosContables.f_inicial,formDocumentosContables.f_final)"
                       style="color: #ffffff;padding-left: 2px">
                        <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'XLS'" variant="success">
                            XLS
                            <feather-icon icon="DownloadCloudIcon"></feather-icon>
                        </b-button>
                    </a>

                </div>
            </b-card>
        </div>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Catálogos Contables
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemCatalogosContables('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'PDF'" variant="danger">
                        Decargar PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemCatalogosContables('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'XLS'" variant="success">
                        Decargar XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Tipo Cuentas
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemTipoCuentas('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'PDF'" variant="danger">
                        Decargar PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemTipoCuentas('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'XLS'" variant="success">
                        Decargar XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Niveles Cuentas
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemNivelesCuentas('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'PDF'" variant="danger">
                        Decargar PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemNivelesCuentas('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'XLS'" variant="success">
                        Decargar XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Tipos Documentos
                </h3>
                <div class="col-md-12 mt-2 mb-1">
                    <a :disabled="descargando" @click.prevent="downloadItemTipoDocumentos('pdf')"
                       style="color: #ffffff;padding-left: 2px">
                        <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'PDF'"
                                  variant="danger"> Decargar PDF
                            <feather-icon icon="DownloadCloudIcon"></feather-icon>
                        </b-button>
                    </a>
                    <a :disabled="descargando" @click.prevent="downloadItemTipoDocumentos('xls')"
                       style="color: #ffffff;padding-left: 2px">
                        <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'XLS'" variant="success">
                            Decargar XLS
                            <feather-icon icon="DownloadCloudIcon"></feather-icon>
                        </b-button>
                    </a>
                </div>
            </b-card>
        </div>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Tasas de Cambio
                </h3>
                <b-row>
                    <div class="col-md-2.5 mt-1">
                        <label for="">Periodo</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="filter.search.anio"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-2.5 mt-1">
                        <label class="ml-1" for="">Mes</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="filter.search.mes">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemTasasCambio('pdf',filter.search.anio.periodo,filter.search.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemTasasCambio('xls',filter.search.anio.periodo,filter.search.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Libro Diario
                </h3>
                <b-row>
                    <div class="col-md-2.5 mt-1">
                        <label for="">Periodo</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="form.anio"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-2.5 mt-1">
                        <label class="ml-1" for="">Mes</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'" label="descripcion"
                                  v-model="form.mes">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemLibroDiario('pdf',form.anio.periodo,form.anio.id_periodo_fiscal,form.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemLibroDiario('xls',form.anio.periodo,form.anio.id_periodo_fiscal,form.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte Libro Mayor
                </h3>
                <b-row>
                    <div class="col-md-2.5 mt-1">
                        <label for="">Periodo</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="formLibroMayor.anio"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-2.5 mt-1">
                        <label class="ml-1" for="">Mes</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="formLibroMayor.mes">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemLibroMayor('pdf',formLibroMayor.anio.periodo,formLibroMayor.anio.id_periodo_fiscal,formLibroMayor.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemLibroMayor('xls',formLibroMayor.anio.periodo,formLibroMayor.anio.id_periodo_fiscal,formLibroMayor.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>
        <div class="col-lg-12 col-md-12 ">
            <b-card>
                <h3>
                    Estado Resultado Comparativo
                </h3>
                <b-row>
                    <div class="col-md-3 mt-1">
                        <label for="">Periodo Actual</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="formEstadoResultado.anioActual"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="ml-1" for="">Mes Actual</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="formEstadoResultado.mesActual">
                        </v-select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <label for="">Periodo Anterior</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="formEstadoResultado.anioAnterior"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="ml-1" for="">Mes Anterior</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="formEstadoResultado.mesAnterior">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemEstadoResultado('pdf',formEstadoResultado.anioActual.id_periodo_fiscal,formEstadoResultado.mesActual.mes,formEstadoResultado.anioAnterior.id_periodo_fiscal,formEstadoResultado.mesAnterior.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemEstadoResultado('xls',formEstadoResultado.anioActual.id_periodo_fiscal,formEstadoResultado.mesActual.mes,formEstadoResultado.anioAnterior.id_periodo_fiscal,formEstadoResultado.mesAnterior.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>

        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Estado Cambio Patrimonio
                </h3>
                <b-row>
                    <div class="col-md-2.5 mt-1">
                        <label for="">Periodo</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="formCambioPatrimonio.anio"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-2.5 mt-1">
                        <label class="ml-1" for="">Mes</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="formCambioPatrimonio.mes">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemCambioPatrimonio('pdf',formCambioPatrimonio.anio.id_periodo_fiscal,formCambioPatrimonio.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemCambioPatrimonio('xls',formCambioPatrimonio.anio.id_periodo_fiscal,formCambioPatrimonio.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>

        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Balanza de Comprobación Anual
                </h3>
                <b-row>
                    <div class="col-md-4 mt-1">
                        <label for="">Nivel Cuenta</label>
                        <v-select
                                :options="niveles"
                                label="descripcion"
                                v-model="formBalanza.nivel_cuenta"
                        ></v-select>
                    </div>
                    <div class="col-md-2.5 mt-1">
                        <label for="">Periodo</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="formBalanza.anio"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-2.5 mt-1">
                        <label class="ml-1" for="">Mes</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'" label="descripcion"
                                  v-model="formBalanza.mes">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemBalanzaComprobacion('pdf',formBalanza.nivel_cuenta.id_nivel_cuenta,formBalanza.anio.id_periodo_fiscal,formBalanza.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemBalanzaComprobacion('xls',formBalanza.nivel_cuenta.id_nivel_cuenta,formBalanza.anio.id_periodo_fiscal,formBalanza.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>
        <div class="col-lg-12 col-md-12 ">
            <b-card>
                <h3>
                    Comparativo de Razones Financieras
                </h3>
                <b-row>
                    <div class="col-md-3 mt-1">
                        <label for="">Periodo Actual</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="formRazonesFinancieras.anioActual"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="ml-1" for="">Mes Actual</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="formRazonesFinancieras.mesActual">
                        </v-select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <label for="">Periodo Anterior</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="formRazonesFinancieras.anioAnterior"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="ml-1" for="">Mes Anterior</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="formRazonesFinancieras.mesAnterior">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemRazonesFinancieras('pdf',formRazonesFinancieras.anioActual.id_periodo_fiscal,formRazonesFinancieras.mesActual.mes,formRazonesFinancieras.anioAnterior.id_periodo_fiscal,formRazonesFinancieras.mesAnterior.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemRazonesFinancieras('xls',formRazonesFinancieras.anioActual.id_periodo_fiscal,formRazonesFinancieras.mesActual.mes,formRazonesFinancieras.anioAnterior.id_periodo_fiscal,formRazonesFinancieras.mesAnterior.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte de inventario con montos
                </h3>
                <b-row>
                    <div class="col-md-5">
                        <label for="bodega">* Bodegas:</label>
                        <v-select :options="bodegas"
                                  label="descripcion"
                                  v-model="formInventarioMonto.bodega"

                        ></v-select>
                    </div>
                    <div class="col-md-3.5 mr-1">
                        <label for="fecha_inicial">* Fecha Inicial:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelectRMSInicial"
                                class="mb-0"
                                local="es"
                                placeholder="f.inicial"
                                selected-variant="primary"
                                v-model="formInventarioMonto.f_inicial"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-3.5">
                        <label for="fecha_final">* Fecha Final:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelectRMSFinal"
                                class="mb-0"
                                local="es"
                                placeholder="f.final"
                                selected-variant="primary"
                                v-model="formInventarioMonto.f_final"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-12 mt-1">
                        <a :disabled="descargando"
                           @click.prevent="downloadReporteMovimientosSaldos ('pdf',formInventarioMonto.bodega.id_bodega, formInventarioMonto.f_inicial, formInventarioMonto.f_final)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadReporteMovimientosSaldos ('xls',formInventarioMonto.bodega.id_bodega, formInventarioMonto.f_inicial, formInventarioMonto.f_final)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>
      <!-- Reporte de listado de Factura-->
      <div class="col-sm-3 col-md-6">
        <b-card>
          <h3>
            Reporte de listado de Facturas
          </h3>
          <b-row>
            <div class="col-md-12">
              <label for="id_cliente">* Clientes:</label>
              <v-select :options="formListadoFactura.clientes"
                        label="nombre_comercial"
                        v-model="formListadoFactura.clienteSelecionado"
                        id="id_cliente"

              ></v-select>
            </div>
            <div class="col-md-12">
              <label for="id_vendedor">* Vendedores:</label>
              <v-select :options="formListadoFactura.vendedores"
                        label="nombre_completo"
                        v-model="formListadoFactura.vendedorSelecionado"
                        id="id_vendedor"

              ></v-select>
            </div>
            <div class="col-md-12 mr-1">
              <label for="fecha_inicial">* Fecha Inicial:</label>
              <b-form-datepicker
                  :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                  @selected="onDateSelectRLFInicial"
                  class="mb-0"
                  local="es"
                  placeholder="f.inicial"
                  selected-variant="primary"
                  v-model="formListadoFactura.f_inicial"
              ></b-form-datepicker>
            </div>
            <div class="col-md-12">
              <label for="fecha_final">* Fecha Final:</label>
              <b-form-datepicker
                  :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                  @selected="onDateSelectRLFFinal"
                  class="mb-0"
                  local="es"
                  placeholder="f.final"
                  selected-variant="primary"
                  v-model="formListadoFactura.f_final"
              ></b-form-datepicker>
            </div>
            <div class="col-md-12 mt-1">
              <a :disabled="descargando"
                 @click.prevent="downloadReporteListadodeFactura ('pdf',formListadoFactura.clienteSelecionado.id_cliente,formListadoFactura.vendedorSelecionado.id_vendedor, formListadoFactura.f_inicial,formListadoFactura.f_final)"
                 style="color: #ffffff;padding-left: 2px">
                <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                          variant="danger"> PDF
                  <feather-icon icon="DownloadCloudIcon"></feather-icon>
                </b-button>
              </a>
              <a :disabled="descargando"
                 @click.prevent="downloadReporteListadodeFactura ('xls',formListadoFactura.clienteSelecionado.id_cliente,formListadoFactura.vendedorSelecionado.id_vendedor, formListadoFactura.f_inicial,formListadoFactura.f_final)"
                 style="color: #ffffff;padding-left: 2px">
                <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                          variant="success"> XLS
                  <feather-icon icon="DownloadCloudIcon"></feather-icon>
                </b-button>
              </a>
            </div>
          </b-row>
        </b-card>
      </div>
        <!-- Reporte de movimientos y saldos por cuentas contables-->
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte de movimiento por cuentas
                </h3>
                <b-row>
                    <div class="col-md-12">
                        <label for="cuenta_contable">* Cuenta contable:</label>
                        <v-select :options="cuentas_contables"
                                  label="nombre_cuenta_completo"
                                  v-model="formMovimientoCuenta.cuenta_contable"
                                  id="cuenta_contable"

                        ></v-select>
                    </div>
                    <div class="col-md-12 mr-1">
                        <label for="fecha_inicial">* Fecha Inicial:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelectRSCInicial"
                                class="mb-0"
                                local="es"
                                placeholder="f.inicial"
                                selected-variant="primary"
                                v-model="formSaldoPorCuenta.f_inicial"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-12">
                        <label for="fecha_final">* Fecha Final:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelectRSCFinal"
                                class="mb-0"
                                local="es"
                                placeholder="f.final"
                                selected-variant="primary"
                                v-model="formSaldoPorCuenta.f_final"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-12 mt-1">
                        <a :disabled="descargando"
                           @click.prevent="downloadReporteMovimientosPorcuenta ('pdf',formMovimientoCuenta.cuenta_contable.id_cuenta_contable,formSaldoPorCuenta.f_inicial, formSaldoPorCuenta.f_final)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadReporteMovimientosPorcuenta ('xls',formMovimientoCuenta.cuenta_contable.id_cuenta_contable,formSaldoPorCuenta.f_inicial, formSaldoPorCuenta.f_final)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>

        <div class="col-sm-3 col-md-6">
          <b-card>
            <h3>
              Reporte de ingreso y costos por rubro
            </h3>
            <b-row>
              <div class="col-md-12 mr-1">
                <label for="fecha_inicial">* Fecha Inicial:</label>
                <b-form-datepicker
                    :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                    @selected="onDateSelectRSCInicial"
                    class="mb-0"
                    local="es"
                    placeholder="f.inicial"
                    selected-variant="primary"
                    v-model="formIngresoCostoRubro.f_inicial"
                ></b-form-datepicker>
              </div>
              <div class="col-md-12">
                <label for="fecha_final">* Fecha Final:</label>
                <b-form-datepicker
                    :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                    @selected="onDateSelectRSCFinal"
                    class="mb-0"
                    local="es"
                    placeholder="f.final"
                    selected-variant="primary"
                    v-model="formIngresoCostoRubro.f_final"
                ></b-form-datepicker>
              </div>
              <div class="col-md-12 mt-1">
                <a :disabled="descargando"
                   @click.prevent="downloadReporteIngresoCostoRubro ('pdf',formSaldoPorCuenta.f_inicial, formSaldoPorCuenta.f_final)"
                   style="color: #ffffff;padding-left: 2px">
                  <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                            variant="danger"> PDF
                    <feather-icon icon="DownloadCloudIcon"></feather-icon>
                  </b-button>
                </a>
                <a :disabled="descargando"
                   @click.prevent="downloadReporteIngresoCostoRubro ('xls',formIngresoCostoRubro.f_inicial, formIngresoCostoRubro.f_final)"
                   style="color: #ffffff;padding-left: 2px">
                  <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                            variant="success"> XLS
                    <feather-icon icon="DownloadCloudIcon"></feather-icon>
                  </b-button>
                </a>
              </div>
            </b-row>
          </b-card>
        </div>
        <!--Reporte de comprobantes descuadrados-->
        <div class="col-lg-6 col-md-6 ">
            <b-card>
                <h3>
                    Reporte de comprobantes descuadrados
                </h3>
                <b-row>
                    <div class="col-md-2.5 mt-1">
                        <label for="">Periodo</label>
                        <v-select
                                :options="periodos"
                                label="periodo"
                                v-model="filter.search.anio"
                                v-on:input="obtenerMeses"
                        ></v-select>
                    </div>
                    <div class="col-md-2.5 mt-1">
                        <label class="ml-1" for="">Mes</label>
                        <v-select :options="meses" :style="'margin-left: .5rem!important;'"
                                  label="descripcion" v-model="filter.search.mes">
                        </v-select>
                    </div>
                    <div class="col-md-5 mt-2">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemComprobantesDescuadrados('pdf',filter.search.anio.periodo,filter.search.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemComprobantesDescuadrados('xls',filter.search.anio.periodo,filter.search.mes.mes)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar'"
                                      variant="success"> XLS
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                    </div>
                </b-row>
            </b-card>
        </div>
        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-row>


    <!--<b-card-body>
        <h1>Reporte Documentos contables</h1>
    </b-card-body>-->


    <!--<b-card-footer>
        <pagination
                @changePage="changePage"
                @changeLimit="changeLimit"
                :items="facturas"
                :total="total"
                :page="filter.page"
                :limitOptions="filter.limitOptions"
                :limit="filter.limit"
        ></pagination>
    </b-card-footer>-->



</template>

<script type="text/ecmascript-6">
  import factura from "../../../api/CajaBanco/facturas";
  import nivel from "../../../api/contabilidad/niveles_cuentas";
  import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
  import axios from "axios";
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
  import tasa from "../../../api/contabilidad/tasas-cambio";
  import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
  import bodega from "../../../api/Inventario/bodegas";
  import documento_contable from "../../../api/contabilidad/documentos_contables";
  import isObjectEmpty from "../../../../../Backend/resources/app/assets/plugins/moment/src/lib/utils/is-object-empty";
  import catalogo from "../../../api/contabilidad/reportes_contabilidad";

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
        formDocumentosContables:{
          type: 1,
          optionsType:[
            {value: 1, text: 'Consolidado'},
            {value: 2, text: 'Detallado'},
          ],
          f_inicial: moment(new Date()).format("YYYY-MM-DD"),
          f_final: moment(new Date()).format("YYYY-MM-DD")
        },
        form: {
          anio: '',
          mes: ''
        },
        bodegas: [],
        cuentas_contables: [],
        formInventarioMonto: {
          bodega: '',
          f_inicial: moment(new Date()).format("YYYY-MM-DD"),
          f_final: moment(new Date()).format("YYYY-MM-DD")
        },
        formSaldoPorCuenta: {
          bodega: '',
          f_inicial: moment(new Date()).format("YYYY-MM-DD"),
          f_final: moment(new Date()).format("YYYY-MM-DD")
        },
        formMovimientoCuenta: {
          cuenta_contable: '',
        },
        formLibroMayor: {
          anio: '',
          mes: ''
        },
        formCambioPatrimonio: {
          anio: '',
          mes: ''
        },
        formBalanza: {
          anio: '',
          mes: '',
          nivel_cuenta: ''
        },
        formEstadoResultado: {
          anioActual: '',
          mesActual: '',
          anioAnterior: '',
          mesAnterior: ''
        },
        formRazonesFinancieras: {
          anioActual: '',
          mesActual: '',
          anioAnterior: '',
          mesAnterior: ''
        },
        formIngresoCostoRubro: {
          f_inicial: moment(new Date()).format("YYYY-MM-DD"),
          f_final: moment(new Date()).format("YYYY-MM-DD")
        },

        formListadoFactura: {
          clientes:[],
          vendedores: [],
          clienteSelecionado:[],
          vendedorSelecionado:[],
          f_inicial: moment(new Date()).format("YYYY-MM-DD"),
          f_final: moment(new Date()).format("YYYY-MM-DD")
        },
        periodos: [],
        meses: [],
        facturas: [],
        niveles: [],
        total: 0,
        catalogos:[]
      };
    },
    methods: {

      obtenerTodosNivelesCuenta() {
        var self = this;
        nivel.obtenerTodosNivelesCuenta(
            data => {
              self.niveles = data;
              /*self.filter.nivel_cuenta=self.niveles_cuenta[1];*/
            },
            err => {
              console.log(err);
            }
        );
      },

      obtenerTasas() {
        var self = this;
        self.loading = true;
        tasa.obtenerTasas(self.filter, data => {
          self.periodos = data.periodos
          self.tasas = data.rows
          self.total = data.total
          self.filter.search.anio = self.periodos[0]
          self.meses = self.filter.search.anio.meses_periodo
          //self.filter.search.mes = self.meses[0]
          self.loading = false;
        }, err => {
          self.loading = false;
          console.log(err)
        })
      },
      obtenerMeses() {
        let self = this;
        self.filter.search.mes = [];
        self.meses = self.filter.search.anio.meses_periodo
        self.filter.search.mes = self.meses[0]
      },

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
      downloadItemDocumentosContables(extension, type, fecha_inicial, fecha_final){
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/documentos-contables/reporte/' + extension + '/' + type + '/' + fecha_inicial + '/' + fecha_final;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'});

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'FormatoDocumentoscontables.' + extension;
                link.click();
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
      onDateSelectDocumentosCInicial(date) {
        this.formDocumentosContables.f_inicial = moment(date).format("YYYY-MM-DD"); //
      },
      onDateSelectDocumentosCFinal(date) {
        this.formDocumentosContables.f_final = moment(date).format("YYYY-MM-DD"); //
      },

      downloadItemCatalogosContables(extension) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/cuentas-contables/reporte/' + extension;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoCatalogoContable.' + extension;
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
      downloadItemTipoCuentas(extension) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/tipos-cuenta/reporte/' + extension;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoTipoCuentas.' + extension;
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

      downloadItemNivelesCuentas(extension) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/niveles-cuentas/reporte/' + extension;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoNivelesCuentas.' + extension;
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

      downloadItemTipoDocumentos(extension) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/tipos-documentos/reporte/' + extension;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoTipoDocumentos.' + extension;
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

      downloadItemTasasCambio(extension, periodo, mes) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/tasas-cambio/reporte/' + extension + '/' + periodo + '/' + mes;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoTasaCambio.' + extension;
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
      //Descargar reporte de comprobantes descuadrados
      downloadItemComprobantesDescuadrados(extension, periodo, mes) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/comprobantes-descuadrados/reporte/' + extension + '/' + periodo + '/' + mes;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'});

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'ReporteComprobantesDescuadrados.' + extension;
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
          self.loading = false;
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

      downloadItemLibroDiario(extension, periodo, id_periodo, mes) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/estados-financieros/libro-diario/reporte/' + extension + '/' + periodo + '/' + id_periodo + '/' + mes;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoLibroDiario.' + extension;
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
      downloadItemLibroMayor(extension, periodo, id_periodo, mes) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/estados-financieros/libro-mayor/reporte/' + extension + '/' + periodo + '/' + id_periodo + '/' + mes;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoLibroMayor.' + extension;
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
      downloadItemEstadoResultado(extension, id_periodo_act, mes_act, id_periodo_ant, mes_ant) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/estados-financieros/estado-resultado/reporte/' + extension + '/' + id_periodo_act + '/' + mes_act + '/' + id_periodo_ant + '/' + mes_ant;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoEstadoResultado.' + extension;
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

      downloadItemCambioPatrimonio(extension, id_periodo, mes) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/estados-financieros/cambio-patrimonio/reporte/' + extension + '/' + id_periodo + '/' + mes;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoCambioPatrimonio.' + extension;
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

      downloadItemBalanzaComprobacion(extension, id_nivel_cuenta, id_periodo, mes) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/estados-financieros/balanza-anual/reporte/' + extension + '/' + id_nivel_cuenta + '/' + id_periodo + '/' + mes;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoBalanzaComprobacion.' + extension;
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


      downloadItemRazonesFinancieras(extension, id_periodo_act, mes_act, id_periodo_ant, mes_ant) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/estados-financieros/razones-financieras-comparativo/reporte/' + extension + '/' + id_periodo_act + '/' + mes_act + '/' + id_periodo_ant + '/' + mes_ant;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoRazonesFinancieras.' + extension;
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

      downloadReporteMovimientosSaldos(extension, id_bodega, f_inicial, f_final) {
        const self = this;
        if (!this.descargando) {
          self.loading = true;
          let url = 'contabilidad/movimiento-con-saldos/' + extension + '/' + id_bodega + '/' + f_inicial + '/' + f_final;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'FormatoKardexConsolidado.' + extension;
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
            self.loading = false;
            self.descargando = false;
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'CheckIcon',
                    text: 'Ha ocurrido un error descargando el reporte',
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
          self.loading = false;
          self.descargando = false;
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
      onDateSelectRMSInicial(date) {
        this.formInventarioMonto.f_inicial = moment(date).format("YYYY-MM-DD"); //
      },
      onDateSelectRMSFinal(date) {
        this.formInventarioMonto.f_final = moment(date).format("YYYY-MM-DD"); //
      },
      obtenerTodasBodegas() {
        const self = this;
        self.loading = true;
        bodega.obtenerTodasBodegas(
            data => {
              self.loading = false;
              self.bodegas = data;
              /*self.form.bodega = self.bodegas[0];*/
            },
            err => {
              self.loading = false;
              self.descargando = false;
              this.$toast({
                    component: ToastificationContent,
                    props: {
                      title: 'Notificación',
                      icon: 'InfoIcon',
                      text: 'Ha ocurrido un error al cargar los datos.',
                      variant: 'danger',
                      position: 'bottom-right'
                    }
                  },
                  {
                    position: 'bottom-right'
                  });
            }
        );
      },

      onDateSelectRSCInicial(date) {
        this.formSaldoPorCuenta.f_inicial = moment(date).format("YYYY-MM-DD"); //
      },
      onDateSelectRSCFinal(date) {
        this.formSaldoPorCuenta.f_final = moment(date).format("YYYY-MM-DD"); //
      },
      downloadReporteMovimientosPorcuenta(extension, id_cuenta_contable, fecha_inicial, fecha_final) {
        const self = this;
        if (!this.descargando && Object.keys(self.formMovimientoCuenta.cuenta_contable).length > 0) {
          self.loading = true;
          let url = 'contabilidad/reporte/movimiento-por-cuenta/' + extension + '/' + id_cuenta_contable + '/' + fecha_inicial + '/' + fecha_final;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'MovimientoPorCtacontable.' + extension;
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
            self.loading = false;
            self.descargando = false;
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'CheckIcon',
                    text: 'Ha ocurrido un error descargando el reporte',
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
          self.loading = false;
          self.descargando = false;
          this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Notificación',
                  icon: 'CheckIcon',
                  text: 'No ha seleccionado una cuenta contable',
                  variant: 'warning',
                }
              },
              {
                position: 'bottom-right'
              });
        }
      },
      downloadReporteIngresoCostoRubro(extension, fecha_inicial, fecha_final) {
        const self = this;
          self.loading = true;
          let url = 'contabilidad/reporte/ingresocostorubro/' + extension  + '/' + fecha_inicial + '/' + fecha_final;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'ReporteIngresoCostoRubro.' + extension;
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
            self.loading = false;
            self.descargando = false;
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'CheckIcon',
                    text: 'Ha ocurrido un error descargando el reporte',
                    variant: 'warning',
                  }
                },
                {
                  position: 'bottom-right'
                });
            self.descargando = false;
            self.loading = false;
          })

      },
      obtenerCuentascontables() {
        let self = this;
        documento_contable.nuevo({}, data => {
              self.cuentas_contables = data.cuentas_contables;
              self.loading = false;
            },
            err => {
              this.$toast({
                    component : ToastificationContent,
                    props: {
                      title : 'Notificación',
                      icon : 'CheckIcon',
                      text : 'Ha ocurrido un error cargando los datos.',
                      variant : 'warning',
                    }
                  },
                  {
                    position : 'bottom-right'
                  });
            })

      },
      //Selecionar las fecha de listado de facturas
      onDateSelectRLFInicial(date) {
        this.formListadoFactura.f_inicial = moment(date).format("YYYY-MM-DD"); //
      },
      onDateSelectRLFFinal(date) {
        this.formListadoFactura.f_final = moment(date).format("YYYY-MM-DD"); //
      },

      obtenerCatalagoFactura() {
        let self = this;
        self.loading = true;
        catalogo.obtenerCatalago(
            data => {
              self.loading = false;
              self.formListadoFactura.clientes = data.clientes
              self.formListadoFactura.vendedores = data.vendedores
              //Metodo de notificacion
              this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Notificación',
                  icon: 'CheckIcon',
                  text: data.messages,
                  variant: 'success',
                }
              }, {
                position: 'bottom-right'
              });
              /*self.filter.nivel_cuenta=self.niveles_cuenta[1];*/
            },
            err => {
              self.loading = false;
              this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Notificación',
                  icon: 'CheckIcon',
                  text: err.messages,
                  variant: 'danger',
                }
              }, {
                position: 'bottom-right'
              });
            }
        );
      },
      downloadReporteListadodeFactura(extension, id_cliente,id_vendedor,fecha_inicial, fecha_final) {
        const self = this;
        if (!this.descargando ) {
          self.loading = true;
          let url = 'contabilidad/listado-de-facturas/' + extension + '/' + id_cliente + '/'+ id_vendedor +  '/' + fecha_inicial + '/' + fecha_final;
          this.descargando = true;
          axios.get(url, {responseType: 'blob'})
              .then(({data}) => {
                let blob = new Blob([data], {type: 'application/pdf'})

                extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob)
                link.download = 'ListadoDeFactura.' + extension;
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
            self.loading = false;
            self.descargando = false;
            this.$toast({
                  component: ToastificationContent,
                  props: {
                    title: 'Notificación',
                    icon: 'CheckIcon',
                    text: 'Ha ocurrido un error descargando el reporte',
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
          self.loading = false;
          self.descargando = false;
          this.$toast({
                component: ToastificationContent,
                props: {
                  title: 'Notificación',
                  icon: 'CheckIcon',
                  text: 'Espere que se descargue el Reporte anterior',
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
      this.obtenerCuentascontables();
      this.obtenerTasas();
      this.obtenerTodosNivelesCuenta();
      this.obtenerTodasBodegas();
      this.obtenerCatalagoFactura();
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
