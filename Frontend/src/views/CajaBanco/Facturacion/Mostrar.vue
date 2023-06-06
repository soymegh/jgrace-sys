<template>
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">Factura Código: {{form.codigo_factura}}</span>
            <div class="btn-group">
                <a @click="$router.go(-1)" style="margin-right: 10px;"> <i class="fa fa-arrow-left"></i> Regresar</a>
                <a :disabled="descargando" @click.prevent="downloadItem('ventas/facturas/reporte/'+form.id_factura)">  <i class="fa fa-download"></i> Descargar</a>
                <button  class="btn btn-info" @click="printDiv('facturaX')"><span class="fa fa-print"></span></button>
            </div>
        </div>

        <div class="panel-body">
            <div id="facturaX" class="factura">
               <table class="jrPage" cellpadding="0" cellspacing="0" border="0" style="empty-cells: show; width: 792px; border-collapse: collapse; background-color: white;">
                    <tr valign="top" style="height:0">
                        <td style="width:20px"></td>
                        <td style="width:1px"></td>
                        <td style="width:49px"></td>
                        <td style="width:30px"></td>
                        <td style="width:60px"></td>
                        <td style="width:40px"></td>
                        <td style="width:1px"></td>
                        <td style="width:29px"></td>
                        <td style="width:3px"></td>
                        <td style="width:7px"></td>
                        <td style="width:150px"></td>
                        <td style="width:3px"></td>
                        <td style="width:3px"></td>
                        <td style="width:4px"></td>
                        <td style="width:10px"></td>
                        <td style="width:30px"></td>
                        <td style="width:20px"></td>
                        <td style="width:29px"></td>
                        <td style="width:11px"></td>
                        <td style="width:40px"></td>
                        <td style="width:10px"></td>
                        <td style="width:6px"></td>
                        <td style="width:2px"></td>
                        <td style="width:1px"></td>
                        <td style="width:1px"></td>
                        <td style="width:10px"></td>
                        <td style="width:17px"></td>
                        <td style="width:13px"></td>
                        <td style="width:27px"></td>
                        <td style="width:3px"></td>
                        <td style="width:30px"></td>
                        <td style="width:50px"></td>
                        <td style="width:10px"></td>
                        <td style="width:50px"></td>
                        <td style="width:1px"></td>
                        <td style="width:21px"></td>
                    </tr>
                    <tr valign="top" style="height:20px">
                        <td colspan="36">
                        </td>
                    </tr>
                    <tr valign="top" style="height:21px">
                        <td colspan="3">
                        </td>
                        <td colspan="5" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125; font-weight: bold;">Managua</span></td>
                        <td rowspan="2" style="text-indent: 0px; text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">| | | | | |</span></td>
                        <td colspan="3" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125; font-weight: bold;">Carretera Sur</span></td>
                        <td rowspan="2" style="text-indent: 0px; text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">| | | | | |</span></td>
                        <td colspan="9" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125; font-weight: bold;">Carretera a Masaya</span></td>
                        <td colspan="2" rowspan="2" style="text-indent: 0px; text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">| | | | | |</span></td>
                        <td>
                        </td>
                        <td colspan="8" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125; font-weight: bold;">Matagalpa</span></td>
                        <td colspan="3">
                        </td>
                    </tr>
                    <tr valign="top" style="height:58px">
                        <td colspan="3">
                        </td>
                        <td colspan="5" style="text-indent: 0px; text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Centro de servicio 27 de mayo: de la CST. 1c abajo 1 c al Sur Tel. 22582316</span></td>
                        <td colspan="3" style="text-indent: 0px; text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Frente al Hospital Psiquiátrico, km 5 Tel. 22501328</span></td>
                        <td colspan="9" style="text-indent: 0px; text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Plaza Guanacaste Km 9.5 Tel. 22244944</span></td>
                        <td>
                        </td>
                        <td colspan="8" style="text-indent: 0px; text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Dir. Escuela Pablo A. Cuadra 1C al norte 10 Varas al Oeste Cel. 83963759</span></td>
                        <td colspan="3">
                        </td>
                    </tr>
                    <tr valign="top" style="height:1px">
                        <td colspan="36">
                        </td>
                    </tr>
                    <tr valign="top" style="height:20px">
                        <td>
                        </td>
                        <td colspan="5" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">RUC: J0310000011869</span></td>
                        <td colspan="4">
                        </td>
                        <td colspan="6" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 12px; line-height: 1.2578125; font-weight: bold;">Factura de Contado</span></td>
                        <td colspan="4" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Plazo 0 dias</span></td>
                        <td colspan="8">
                        </td>
                        <td colspan="6" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">No. C-3</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:20px">
                        <td>
                        </td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Cliente:</span></td>
                        <td colspan="12" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">YYADER MEMBREÑO ARANA</span></td>
                        <td colspan="3" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Código:</span></td>
                        <td colspan="9" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">10832600005</span></td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Fecha:</span></td>
                        <td colspan="2">
                        </td>
                        <td colspan="3" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">18/08/2020</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:20px">
                        <td>
                        </td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Dirección:</span></td>
                        <td colspan="12" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                        </td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Tel:</span></td>
                        <td colspan="6" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">0000</span></td>
                        <td colspan="2">
                        </td>
                        <td colspan="5" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">RUC/CEDULA:</span></td>
                        <td colspan="4" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">2810711940000N</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:19px">
                        <td>
                        </td>
                        <td colspan="3" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">CODIGO</span></td>
                        <td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">CANTIDAD</span></td>
                        <td colspan="9" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">DESCRIPCIÓN</span></td>
                        <td colspan="5" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">P.LISTA U$</span></td>
                        <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">DESC. %</span></td>
                        <td colspan="9" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">DESC. U$</span></td>
                        <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">P.UNIT. U$</span></td>
                        <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Valor U$</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:19px">
                        <td>
                        </td>
                        <td colspan="3" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">111100068</span></td>
                        <td style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">1</span></td>
                        <td colspan="9" style="text-indent: 0px;  vertical-align: middle;text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">BATERIA L-NS40-320</span></td>
                        <td colspan="5" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1; *line-height: normal;">4880.19</span></td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">35</span></td>
                        <td colspan="9" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">1708.07</span></td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">3172.12</span></td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">3172.12</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:20px">
                        <td>
                        </td>
                        <td colspan="29" style="border: 1px solid #000000; text-indent: 0px; text-align: left;">
                        </td>
                        <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">SUB-TOTAL U$</span></td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">3172.12</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:20px">
                        <td>
                        </td>
                        <td colspan="29" rowspan="2" style="border: 1px solid #000000; text-indent: 0px; text-align: left;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">Nota: ..</span></td>
                        <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">I.V.A U$</span></td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">475.82</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:20px">
                        <td>
                        </td>
                        <td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">TOTAL U$</span></td>
                        <td colspan="2" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">3647.94</span></td>
                        <td colspan="2">
                        </td>
                    </tr>
                    <tr valign="top" style="height:10px">
                        <td colspan="36">
                        </td>
                    </tr>
                    <tr valign="top" style="height:39px">
                        <td colspan="2">
                        </td>
                        <td colspan="5" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">CLAUDIA IVETH SOLANO GALO</span></td>
                        <td colspan="4" style="text-indent: 0px; text-align: left;">
                        </td>
                        <td colspan="15" style="text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 10px; line-height: 1.2578125;">YYADER MEMBREÑO ARANA</span></td>
                        <td colspan="9" style="text-indent: 0px; text-align: left;">
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr valign="top" style="height:30px">
                        <td colspan="2">
                        </td>
                        <td colspan="5" style="border-top: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 12px; line-height: 1.2578125; font-weight: bold;">Vendido por</span></td>
                        <td colspan="4" style="border-top: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 12px; line-height: 1.2578125; font-weight: bold;">Firma</span></td>
                        <td colspan="15" style="border-top: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 12px; line-height: 1.2578125; font-weight: bold;">Firma / Nombre</span></td>
                        <td colspan="9" style="border-top: 1px solid #000000; text-indent: 0px;  vertical-align: middle;text-align: center;">
                            <span style="font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #000000; font-size: 12px; line-height: 1.2578125; font-weight: bold;">Cédula</span></td>
                        <td>
                        </td>
                    </tr>
                    <tr valign="top" style="height:275px">
                        <td colspan="36">
                        </td>
                    </tr>
                </table>
                <!--<div class="document-heading">
                    <div class="row">
                        <div class="col col-12 text-center">
                            <div class="company-name">LUBRICANTES DE NICARAGUA, S. A.</div>
                            <div class="company-address">{{form.factura_sucursal.descripcion}}</div>
                            <div class="company-address">{{form.factura_sucursal.telefonos}}</div>
                            <div class="company-address">{{form.factura_sucursal.direccion}}</div>
                            <div class="company-address">{{ajustes[2]?ajustes[2].valor:"N/D"}}</div>
                            <div class="company-address">{{form.factura_sucursal.numero_autorizacion}}</div>
                            <div class="company-address"><strong>FACTURA OFICIAL</strong></div>
                        </div>
                    </div>
                    <br>-->
                    <!--<div class="row">

                        <div class="col col-4">
                            <p>
                                <strong>Proveedor:</strong>
                            </p>
                            <template v-if="form.factura_cliente">
                            <router-link style="color: #000000;" :to="{ name: 'actualizar-proveedor', params: { id_cliente: form.id_cliente } }" target="_blank">
                                <span>{{form.factura_cliente.nombre_comercial}}</span>
                                <br>
                                <span>{{form.factura_cliente.numero_ruc}}</span>
                                <br>
                                <span>{{form.factura_cliente.direccion}}</span>
                                <br>
                                <pre>{{form.factura_cliente.contacto_proveedor}}</pre>
                            </router-link>
                            </template>
                            <template v-else>
                                <span>N/A</span>
                            </template>
                        </div>

                        <div class="col col-4">
                            <p>
                                <strong>Bodega Saliente:</strong>
                            </p>
                            <router-link style="color: #000000;" :to="{ name: 'actualizar-bodega', params: { id_bodega: form.id_bodega } }"  target="_blank">
                                <span>{{form.factura_bodega.descripcion}}</span>
                                <br>
                            </router-link>
                            <p>
                                <strong>Tipo de factura:</strong>
                            </p>
                            <span>{{form.id_tipo}}</span>
                            <br>
                        </div>
                        <div class="col col-4">
                            <table class="document-summary">
                                <tbody>
                                <tr>
                                    <td>Estado actual</td>
                                    <td>
                                        <div v-if="form.estado===0">
                                            <span class="badge badge-danger" style="font-size: 100%;">Cancelada</span>
                                        </div>

                                        <div v-if="form.estado===1">
                                            <span class="badge badge-primary" style="font-size: 100%;">Emitida</span>
                                        </div>

                                        <div v-if="form.estado===2">
                                            <span class="badge badge-success" style="font-size: 100%;">Despachada</span>
                                        </div>

                                    </td>
                                </tr>
                                <tr>
                                    <td>Código factura:</td>
                                    <td>{{form.codigo_factura}}</td>
                                </tr>
                                <tr>
                                    <td>Fecha Emisión:</td>
                                    <td>{{ formatDate(form.f_factura)}}</td>
                                </tr>
                                <tr>
                                    <td>Fecha Despacho:</td>
                                    <td>{{ formatDate(form.fecha_despacho)}}</td>
                                </tr>
                                <tr>
                                    <td>Moneda:</td>
                                    <td>Córdobas</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>-->
                <!-- <div class="document-body">
                     <table class="document-table">
                         <thead>
                         <tr>
                             <th>Código producto</th>
                             <th>Descripción</th>
                             <th>Unidad de medida</th>
                             <th class="align-center">Costo unitario C$</th>
                             <th class="align-center">Cantidad</th>
                             <th class="align-center">Total</th>
                         </tr>
                         </thead>
                         <tbody>
                         <template v-for="item in form.factura_productos" >
                             <tr :key="item.id_factura_producto">
                                 <td class="width-2">
                                     {{item.codigo_producto}}
                                 </td>
                                 <td class="width-4">

                                     <pre>{{item.descripcion_producto}}</pre>

                                 </td>
                                 <td class="width-2">
                                     {{item.unidad_medida}}
                                 </td>
                                 <td
                                         class="width-2 align-right"
                                 >{{item.precio_unitario | formatMoney(2)}}</td>
                                 <td class="width-1 align-center">{{item.cantidad_despachada}}</td>
                                 <td
                                         class="width-2 align-right"
                                 >{{(item.cantidad_despachada * item.precio_unitario) | formatMoney(2)}}</td>
                             </tr>
                         </template>
                         </tbody>
                         <tfoot>
                         <tr>
                             <td colspan="3"></td>
                             <td colspan="2">Sub Total</td>
                             <td class="align-right">C$ {{total_subt | formatMoney(2)}}</td>
                         </tr>
                         <tr>
                             <td colspan="3"></td>
                             <td colspan="2">
                                 <strong>Total</strong>
                             </td>
                             <td class="align-right">
                                 <strong>C$ {{gran_total | formatMoney(2)}}</strong>
                             </td>
                         </tr>
                         </tfoot>
                     </table>
                 </div>-->
            </div>
            </div>
            <template v-if="loading">
                <BlockUI :message="msg" :url="url"></BlockUI>
            </template>
        </div>
    </div>

</template>

<script type="text/ecmascript-6">
    import loadingImage from '../../assets/images/block50.gif'
    import factura from "../../api/facturas";
    import axios from "axios";

    export default {
        data() {
            return {
                loading:true,
                msg: 'Cargando el contenido espere un momento',
                url : '/public'+loadingImage,
                format: "dd-MM-yyyy",
                descargando:false,
                ajustes:[],
                form: {
                    codigo_factura: "",
                    f_factura: "",
                    id_tipo_factura: "",
                    id_cliente: 0,
                    id_bodega: 0,
                    detalleProductos: [],
                    factura_cliente: [],
                    factura_bodega : [],
                    factura_productos: [],
                    factura_sucursal:[],
                    factura_tipo:[],
                    log_registro:[],
                    total: 0,
                    sub_total: 0,
                },
                btnAction: "Registrar",
                errorMessages: []
            };
        },
        methods: {
            printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                // document.body.style.marginTop="-45px";
                window.print();
                document.body.innerHTML = originalContents;
            },
            downloadItem (url) {
                if(!this.descargando){
                    this.descargando=true;
                    alertify.success("Descargando datos, espere un momento.....",5);
                    axios.get(url, { responseType: 'blob' })
                        .then(({ data }) => {
                            let blob = new Blob([data], { type: 'application/pdf' })
                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'ReporteFactura.pdf';
                            link.click()
                            this.descargando=false;
                        }) .catch(function (error) {
                        alertify.error("Error Descargando archivo.....",5);
                        this.descargando=false;
                    })
                }else{
                    alertify.warning("Espere a que se complete la descarga anterior",5);
                }
            },
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
            obtenerFactura() {
                var self = this;
                factura.obtenerFactura(
                    {
                        id_factura: this.$route.params.id_factura,
                        cargar_dependencias:true
                    },
                    data => {
                        self.loading =false;
                        self.form = data.factura;
                        self.ajustes = data.ajustes_basicos;
                    },
                    err => {
                        self.loading =false;
                        console.log(err);
                    }
                );
            },
        },
        computed: {

            total_cantidad_despachada() {
                return this.form.factura_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_despachada))
                }, 0)
            },

            total_subt() {
                return this.form.factura_productos.reduce((carry, item) => {
                        return(carry + Number((Number(item.cantidad_despachada * item.precio_unitario).toFixed(2))));
                    }
                    , 0)
            },
            gran_total() {
                return this.form.factura_productos.reduce((carry, item) => {
                        return(carry + Number((Number(item.cantidad_despachada * item.precio_unitario).toFixed(2))));
                    }
                    , 0)
            },
        },
        mounted() {
            this.obtenerFactura();
        }
    };
</script>
