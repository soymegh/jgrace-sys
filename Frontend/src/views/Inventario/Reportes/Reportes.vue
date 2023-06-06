<template>
    <b-row>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Articulos y Servicios
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemArticulosServicios('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemArticulosServicios('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Bodegas
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemBodegas('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemBodegas('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Tipo Entradas
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemTipoEntradas('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemTipoEntradas('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Unidades de Medida
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemUnidadMedida('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemUnidadMedida('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Tipo Salidas
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemTipoSalidas('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemTipoSalidas('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Marcas
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemMarcas('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemMarcas('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Tipo Bodegas
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemTipoBodegas('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemTipoBodegas('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Tipo Proveedores
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemTipoProveedores ('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemTipoProveedores ('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Proveedores
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemProveedores ('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemProveedores ('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Tipo Productos
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemTipoProductos ('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemTipoProductos ('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Existencia Articulos por bodega
                </h3>
                <b-row class="mb-2">
                    <b-form-checkbox class="custom-control-primary ml-1 mt-2" v-model="todas_bodgas_articulos"
                                     v-on:change="cambiarBodegaArticulo">
                        Todas las Bodegas
                    </b-form-checkbox>
                    <div class="col-md-6 ml-1 mt-1">

                        <v-select :options="bodegas" label="descripcion"
                                  v-if="!todas_bodgas_articulos"
                                  v-model="form.bodega"

                        ></v-select>
                    </div>
                </b-row>
                <a :disabled="descargando"
                   @click.prevent="downloadItemArticulosxBodega ('pdf',todas_bodgas_articulos === true ? id_bodega = 0: form.bodega.id_bodega )"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando"
                   @click.prevent="downloadItemArticulosxBodega ('xls', todas_bodgas_articulos === true ? id_bodega = 0 : form.bodega.id_bodega)"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Existencia por Marcas
                </h3>
                <b-row>
                    <!--<div class="col-md-6">
                        <b-form-checkbox v-model="todas_marcas" v-on:change="cambiarBodegaArticulo" class="custom-control-primary ml-1 mt-2 ">
                            Todas las Marcas
                        </b-form-checkbox>
                    </div>
                    <div class="col-md-6">
                        <b-form-checkbox v-model="todas_bodegas_por_marca" v-on:change="cambiarBodegaArticulo" class="custom-control-primary ml-1 mt-2 ">
                            Todas las Bodegas
                        </b-form-checkbox>
                    </div>-->
                    <div class="col-md-6">
                        <label for="name">* Marcas:</label>
                        <v-select :options="marcas" label="descripcion"
                                  v-if="!todas_marcas"
                                  v-model="formMarcas.marca"

                        ></v-select>
                    </div>
                    <div class="col-md-6">
                        <label for="name">* Bodegas:</label>
                        <v-select :options="bodegas"
                                  label="descripcion"
                                  v-model="formMarcas.bodega"

                        ></v-select>
                    </div>
                </b-row>

                <a :disabled="descargando"
                   @click.prevent="downloadItemExistenciasxMarcas ('pdf',formMarcas.bodega.id_bodega,formMarcas.marca.id_marca)"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando"
                   @click.prevent="downloadItemExistenciasxMarcas ('xls',formMarcas.bodega.id_bodega,formMarcas.marca.id_marca)"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>

        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Entrada por bodegas
                </h3>
                <b-row>
                    <div class="col-md-5">
                        <label for="name">* Bodegas:</label>
                        <v-select :options="bodegas"
                                  label="descripcion"
                                  v-model="formEntradaBodega.bodega"

                        ></v-select>
                    </div>
                    <div class="col-md-3.5 mr-1">
                        <label for="name">* Fecha Inicial:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.inicial"
                                selected-variant="primary"
                                v-model="formEntradaBodega.f_inicial"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-3.5">
                        <label for="name">* Fecha Final:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.final"
                                selected-variant="primary"
                                v-model="formEntradaBodega.f_final"
                        ></b-form-datepicker>
                    </div>
                </b-row>

                <a :disabled="descargando"
                   @click.prevent="downloadItemEntradaxBodegas ('pdf',formEntradaBodega.bodega.id_bodega, formEntradaBodega.f_inicial,formEntradaBodega.f_final)"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando"
                   @click.prevent="downloadItemEntradaxBodegas ('xls',formEntradaBodega.bodega.id_bodega, formEntradaBodega.f_inicial,formEntradaBodega.f_final)"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Salida por bodegas
                </h3>
                <b-row>
                    <div class="col-md-5">
                        <label for="name">* Bodegas:</label>
                        <v-select :options="bodegas"
                                  label="descripcion"
                                  v-model="formSalidaBodega.bodega"

                        ></v-select>
                    </div>
                    <div class="col-md-3.5 mr-1">
                        <label for="name">* Fecha Inicial:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.inicial"
                                selected-variant="primary"
                                v-model="formSalidaBodega.f_inicial"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-3.5">
                        <label for="name">* Fecha Final:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.final"
                                selected-variant="primary"
                                v-model="formSalidaBodega.f_final"
                        ></b-form-datepicker>
                    </div>
                </b-row>

                <a :disabled="descargando"
                   @click.prevent="downloadItemSalidasxBodegas ('pdf',formSalidaBodega.bodega.id_bodega, formSalidaBodega.f_inicial, formSalidaBodega.f_final)"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando"
                   @click.prevent="downloadItemSalidasxBodegas ('xls,formSalidaBodega.bodega.id_bodega, formSalidaBodega.f_inicial, formSalidaBodega.f_final')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Kardex consolidado con rango de fechas
                </h3>
                <b-row>
                    <div class="col-md-5">
                        <label for="name">* Bodegas:</label>
                        <v-select :options="bodegas"
                                  label="descripcion"
                                  v-model="formKardexConsol.bodega"

                        ></v-select>
                    </div>
                    <div class="col-md-3.5 mr-1">
                        <label for="name">* Fecha Inicial:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.inicial"
                                selected-variant="primary"
                                v-model="formKardexConsol.f_inicial"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-3.5">
                        <label for="name">* Fecha Final:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.final"
                                selected-variant="primary"
                                v-model="formKardexConsol.f_final"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-12 mt-1">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemKardexConsolidado ('pdf',formKardexConsol.bodega.id_bodega, formKardexConsol.f_inicial, formKardexConsol.f_final)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemKardexConsolidado ('xls',formKardexConsol.bodega.id_bodega, formKardexConsol.f_inicial, formKardexConsol.f_final)"
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
                    Kardex por bodega y productos
                </h3>
                <b-row>
                    <div class="col-md-5">
                        <label for="name">* Bodegas:</label>
                        <v-select :options="bodegas"
                                  label="descripcion"
                                  v-model="formKardexFecha.bodega"

                        ></v-select>
                    </div>
                    <div class="col-md-3.5 mr-1">
                        <label for="name">* Fecha Inicial:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.inicial"
                                selected-variant="primary"
                                v-model="formKardexFecha.f_inicial"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-3.5">
                        <label for="name">* Fecha Final:</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                @selected="onDateSelect"
                                class="mb-0"
                                local="es"
                                placeholder="f.final"
                                selected-variant="primary"
                                v-model="formKardexFecha.f_final"
                        ></b-form-datepicker>
                    </div>
                    <div class="col-md-6">
                        <label for="name">* Productos:</label>
                        <v-select :options="productos"
                                  label="descripcion"
                                  v-model="formKardexFecha.producto"

                        ></v-select>
                    </div>
                    <div class="col-md-6 mt-1">
                        <a :disabled="descargando"
                           @click.prevent="downloadItemKardexConsolidadoF ('pdf', formKardexFecha.bodega.id_bodega, formKardexFecha.producto.id_producto, formKardexFecha.f_inicial,formKardexFecha.f_final)"
                           style="color: #ffffff;padding-left: 2px">
                            <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                                      variant="danger"> PDF
                                <feather-icon icon="DownloadCloudIcon"></feather-icon>
                            </b-button>
                        </a>
                        <a :disabled="descargando"
                           @click.prevent="downloadItemKardexConsolidadoF ('xls', formKardexFecha.bodega.id_bodega, formKardexFecha.producto.id_producto, formKardexFecha.f_inicial,formKardexFecha.f_final)"
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
                    Reporte General de Artículos
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemProductos ('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemProductos ('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte General de Servicios
                </h3>
                <a :disabled="descargando" @click.prevent="downloadItemServicios ('pdf')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1 mr-1" v-b-tooltip.hover.top="'Descargar PDF'"
                              variant="danger"> PDF
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
                <a :disabled="descargando" @click.prevent="downloadItemServicios ('xls')"
                   style="color: #ffffff;padding-left: 2px">
                    <b-button :disabled="descargando" class="mt-1" v-b-tooltip.hover.top="'Descargar XLS'"
                              variant="success"> XLS
                        <feather-icon icon="DownloadCloudIcon"></feather-icon>
                    </b-button>
                </a>
            </b-card>
        </div>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>
    </b-row>

</template>

<script>
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import bodega from "../../../api/Inventario/bodegas";
    import producto from "../../../api/Inventario/productos";
    import axios from "axios";
    import loadingImage from '../../../assets/images/loader/block50.gif'
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
    import vSelect from 'vue-select'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";


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
                form: {
                    bodega: ''
                },
                formMarcas: {
                    bodega: '',
                    marca: ''
                },
                formEntradaBodega: {
                    bodega: '',
                    f_inicial: moment(new Date()).format("YYYY-MM-DD"),
                    f_final: moment(new Date()).format("YYYY-MM-DD")
                },
                formSalidaBodega: {
                    bodega: '',
                    f_inicial: moment(new Date()).format("YYYY-MM-DD"),
                    f_final: moment(new Date()).format("YYYY-MM-DD")
                },
                formKardexConsol: {
                    bodega: '',
                    f_inicial: moment(new Date()).format("YYYY-MM-DD"),
                    f_final: moment(new Date()).format("YYYY-MM-DD")
                },
                formKardexFecha: {
                    bodega: '',
                    producto: '',
                    f_inicial: moment(new Date()).format("YYYY-MM-DD"),
                    f_final: moment(new Date()).format("YYYY-MM-DD")
                },
                todas_bodgas_articulos: false,
                todas_marcas: false,
                bodega_reporteExistenciaArticulo: {
                    id_bodega: 0
                },
                bodegas: [],
                marcas: [],
                productos: [],
                total: 0
            };
        },
        methods: {

            onDateSelect(date) {
                this.filter.search.fecha_inicial = moment(date).format("YYYY-MM-DD"); //
            },

            cambiarBodegaArticulo() {
                var self = this;
                if (self.todas_bodgas_articulos) {
                    self.bodega_reporteExistenciaArticulo = {};
                    self.bodega_reporteExistenciaArticulo.id_bodega = 0;
                }
            },

            downloadItemArticulosServicios(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'productos/ps/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'ReporteArticulosServicios.' + extension;
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
            downloadItemBodegas(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'bodegas/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'});

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'ListadoDeBodega.' + extension;
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
                            self.loading=false;
                            self.descargando = false;
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
                    self.loading=false;
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
            downloadItemTipoEntradas(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/tipos-entradas/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoTipoEntradas.' + extension;
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
            downloadItemUnidadMedida(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'unidad-medida/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoUnidadesMedida.' + extension;
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
            downloadItemTipoSalidas(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/tipos-salidas/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoTipoSalidas.' + extension;
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
            downloadItemMarcas(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/marcas/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoMarcas.' + extension;
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

            downloadItemTipoBodegas(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/tipos-bodegas/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoTipoBodegas.' + extension;
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

            downloadItemTipoProveedores(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/tipo-proveedor/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoTipoProveedores.' + extension;
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

            downloadItemProveedores(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/proveedores/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoProveedores.' + extension;
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

            downloadItemTipoProductos(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'tipo-producto/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoTipoProductos.' + extension;
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

            obtenerTodasBodegas() {
                var self = this;
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
                        console.log(err);
                    }
                );
            },
            downloadItemArticulosxBodega(extension, id_bodega) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'bodegas/reporte-articulos-bodega/' + extension + '/' + id_bodega;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoArticulosBodega.' + extension;
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
            obtenerMarca() {
                var self = this;
                producto.nuevo({}, data => {
                        // self.proveedores = data.proveedores;
                        self.marcas = data.marcas;
                        self.loading = false;
                    },
                    err => {
                        self.loading = false;
                        self.descargando = false;
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
            obtenerProducto() {
                var self = this;
                producto.obtenerProductosValidos(
                    data => {
                        self.productos = data;
                        self.loading = false;
                    },
                    err => {
                        self.loading = false;
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error al cargar los datos.' + err,
                                    variant: 'danger',
                                    position: 'bottom-right'
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        console.log(err);
                    }
                );


            },
            downloadItemExistenciasxMarcas(extension, id_bodega, id_marca) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/marcas/reporte-exitencia/' + extension + '/' + id_bodega + '/' + id_marca;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoExistenciaMarca.' + extension;
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

            downloadItemEntradaxBodegas(extension, id_bodega, f_inicial, f_final) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'entradas/reporte-entrada-bodega/' + extension + '/' + id_bodega + '/' + f_inicial + '/' + f_final;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoEntradaporBodega.' + extension;
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

            downloadItemSalidasxBodegas(extension, id_bodega, f_inicial, f_final) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/tipos-salidas-bodegas/reporte/' + extension + '/' + id_bodega + '/' + f_inicial + '/' + f_final;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoSalidaporBodega.' + extension;
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

            downloadItemKardexConsolidado(extension, id_bodega, f_inicial, f_final) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/kardex-consolidado/' + extension + '/' + id_bodega + '/' + f_inicial + '/' + f_final;
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

            downloadItemProductos(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'productos/ps/reporte-articulos/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoGeneralArticulos.' + extension;
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

            downloadItemServicios(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'productos/ps/reporte-servicios/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoGeneralServicios.' + extension;
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

            downloadItemKardexConsolidadoF(extension, id_bodega, id_producto, f_inicial, f_final) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'inventario/kardex-consolidado-fecha/' + extension + '/' + id_bodega + '/' + id_producto + '/' + f_inicial + '/' + f_final;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatokardexporRangoFecha.' + extension;
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
        },
        /*components: {
          pagination: Pagination
        },*/
        mounted() {
            this.obtenerTodasBodegas();
            this.obtenerMarca();
            this.obtenerProducto();
        }
    };
</script>

<style scoped>

</style>
