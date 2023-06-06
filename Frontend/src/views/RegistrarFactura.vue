<template>
    <div class="main">
        <div class="row">
            <b-card>
                <b-alert
                        variant="primary"
                        show
                >
                    <div class="alert-body">
                        <span><strong>Datos del cliente</strong></span>
                    </div>
                </b-alert>
                <template v-if="proformaFormHeader.es_nuevo === true">
                    <div class="row">

                        <div class="col-sm-5">
                            <div class="form-group">
                                <label> Cliente</label>
                                <div class="form-group">
                                    <select class="form-control" v-model.number="form.tipo_identificacion">
                                        <option value="1"></option>
                                        <option value="2"></option>
                                    </select>
                                </div>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.factura_cliente"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Tipo Cliente</label>
                                <select class="form-control" v-model.number="form.tipo_identificacion">
                                    <option value="1">Natural</option>
                                    <option value="2">Jurídico</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label> Identificación</label>
                                <input class="form-control" placeholder="Número Identificación"
                                       v-model="form.identificacion">
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.identificacion"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="row">

                        <div class="col-sm-5">
                            <div class="form-group">
                                <label> Cliente</label>
                                <div class="form-group">
                                    <v-select
                                            label="nombre_completo"
                                            v-model="form.proforma_cliente"
                                            :options="vendedores"
                                            :disabled="true"
                                    ></v-select>
                                </div>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.factura_cliente"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Tipo Cliente</label>
                                <select class="form-control" v-model.number="form.proforma_cliente.id_tipo_cliente"
                                        readonly>
                                    <option value="1">Natural</option>
                                    <option value="2">Jurídico</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label> Identificación</label>
                                <input class="form-control" placeholder="Número Identificación"
                                       v-model="form.proforma_cliente.numero_cedula" readonly>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.identificacion"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>


                <b-alert
                        variant="primary"
                        show
                >
                    <div class="alert-body">
                        <span><strong>Datos de factura</strong></span>
                    </div>
                </b-alert>
                <template v-if="proformaFormHeader.es_nuevo === true">
                    <div class="row">


                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for>Bodega</label>
                                <select class="form-control" v-model.number="form.tipo_identificacion">
                                    <option value="1">Natural</option>
                                    <option value="2">Jurídico</option>
                                </select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.factura_bodega"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Vendedor</label>
                                <!-- <div class="form-group">
                                   <typeahead :initial="form.factura_vendedor" :trim="80" :url="vendedoresBusquedaURL" @input="seleccionarVendedor" style="width: 100%;"></typeahead>

                                 </div>-->
                                <select class="form-control" v-model.number="form.tipo_identificacion">
                                    <option value="1">Natural</option>
                                    <option value="2">Jurídico</option>
                                </select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.factura_vendedor"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Tipo Factura</label>
                                <select @change="seleccionarTipo" class="form-control" v-model.number="form.id_tipo">
                                    <option value="1">Contado</option>
                                    <option :disabled="!clienteCredito" value="2">Crédito</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label> Plazo Crédito</label>
                                <select @change="obtenerTCParalela" :disabled="!(form.id_tipo===2)" class="form-control"
                                        v-model.number="form.dias_credito">
                                    <option :disabled="(form.id_tipo===2)" value="0">N/A</option>
                                    <option :disabled="!(plazo_maximo_credito>=8)" value="8">8 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=15)" value="15">15 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=30)" value="30">30 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=45)" value="45">45 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=60)" value="60">60 días</option>
                                </select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.plazo_credito"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for>Fecha emisión</label>
                                <input disabled type="text" class="form-control" v-model="form.f_vencimiento">
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.f_factura"
                                            v-text="message"
                                    ></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for>T/C</label>
                                <input disabled type="text" class="form-control" v-model="form.t_cambio">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for>Observaciones</label>
                                <input type="text" class="form-control" v-model="form.observacion">
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.observacion"
                                            v-text="message"
                                    ></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label> Sucursal</label>
                                <div class="form-group">
                                    <!-- <typeahead :initial="form.factura_sucursal" :trim="80" :url="sucursalesBusquedaURL" @input="seleccionarSucursal" style="width: 100%;"></typeahead>-->
                                    <v-select
                                            label="descripcion"
                                            v-model="form.proforma_sucursal"
                                            :options="sucursales"
                                            :disabled="true"
                                    ></v-select>

                                </div>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.factura_sucursal"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Serie</label>
                                <input class="form-control" disabled placeholder="Serie"
                                       v-model="form.proforma_sucursal.serie">
                                <ul class="error-messages">
                                    <li v-for="message in errorMessages.serie" :key="message" v-text="message"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Consecutivo</label>
                                <input class="form-control" disabled placeholder="Consecutivo" v-model="no_documento">
                                <ul class="error-messages">
                                    <li v-for="message in errorMessages.no_documento" :key="message"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for>Bodega</label>
                                <v-select
                                        label="descripcion"
                                        v-model="form.proforma_bodega"
                                        :options="bodegas"
                                        v-on:input="seleccionarBodega()"
                                        :disabled="true"
                                ></v-select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.factura_bodega"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>


                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Tipo Factura</label>
                                <select @change="seleccionarTipo" class="form-control"
                                        v-model.number="proformaFormHeader.id_tipo">
                                    <option value="1">Contado</option>
                                    <option :disabled="!clienteCredito" value="2">Crédito</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label> Plazo Crédito</label>
                                <select @change="obtenerTCParalela" :disabled="!(form.id_tipo===2)" class="form-control"
                                        v-model.number="form.dias_credito">
                                    <option :disabled="(form.id_tipo===2)" value="0">N/A</option>
                                    <option :disabled="!(plazo_maximo_credito>=8)" value="8">8 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=15)" value="15">15 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=30)" value="30">30 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=45)" value="45">45 días</option>
                                    <option :disabled="!(plazo_maximo_credito>=60)" value="60">60 días</option>
                                </select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.plazo_credito"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label> Vendedor</label>
                                <!-- <div class="form-group">
                                   <typeahead :initial="form.factura_vendedor" :trim="80" :url="vendedoresBusquedaURL" @input="seleccionarVendedor" style="width: 100%;"></typeahead>

                                 </div>-->
                                <v-select
                                        label="nombre_completo"
                                        v-model="form.proforma_vendedor"
                                        :options="vendedores"
                                        :disabled="true"
                                ></v-select>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.factura_vendedor"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for>Fecha Emitida</label>
                                <datepicker :disabled="true" :format="format" :language="es"
                                            :value="new Date()"></datepicker>
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.f_factura"
                                            v-text="message"
                                    ></li>
                                </ul>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for>Fecha Vencimiento</label>
                                <input disabled type="text" class="form-control" v-model="form.f_vencimiento">
                            </div>
                        </div>


                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for>T/C</label>
                                <input disabled type="text" class="form-control" v-model="form.t_cambio">
                            </div>
                        </div>

                        <div class="col-sm-10">
                            <div class="form-group">
                                <label for>Observaciones</label>
                                <input type="text" class="form-control" v-model="proformaFormHeader.observacion">
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.observacion"
                                            v-text="message"
                                    ></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="check-label2"><input @input="deseleccionar"
                                                                   v-model="form.proforma_especifica" type="checkbox">
                                    Tiene proforma</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for=""> Proforma</label>
                                <div class="form-group">
                                    <typeahead v-if="form.proforma_especifica" :initial="form.proformasBusqueda"
                                               :trim="80" :url="proformasBusquedaURL" @input="seleccionarProforma"
                                               style="width: 100%;"></typeahead>
                                    <input v-if="!form.proforma_especifica" class="form-control" disabled>
                                </div>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.proformasBusqueda"
                                        v-text="message"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <div class="form-group">
                                    <button class="btn btn-success btn-agregar" @click="cargarProductosProforma">Cargar
                                        Productos
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>


                <b-alert
                        variant="primary"
                        show
                >
                    <div class="alert-body">
                        <span><strong>Detalle de factura</strong></span>
                    </div>
                </b-alert>
                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Producto</label>
                            <div class="form-group">
                                <v-select v-model="detalleForm.productox" :options="productos"
                                             deselect-label="No se puede eliminar este valor"
                                             track-by="id_producto"
                                             label="text"
                                             placeholder="Selecciona un producto"
                                             :searchable="true"
                                             :show-labels="false"
                                             :allow-empty="false"
                                             ref="producto"
                                             v-on:input="cargar_detalles_producto()"
                                ></v-select>
                            </div>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.productox" v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for>Cantidad</label>
                            <input @keydown.enter="cambiarFocoCantidad"
                                   @change="detalleForm.cantidad = Math.max(Math.min(Math.round(detalleForm.cantidad), (!isNaN(detalleForm.productox.cantidad_disponible))?detalleForm.productox.cantidad_disponible:0 ), 1)"
                                   class="form-control" ref="cantidad" type="number" min="0"
                                   v-model.number="detalleForm.cantidad">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.cantidadx"
                                        v-text="message"
                                ></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for>Precio </label>
                            <input readonly class="form-control" v-model.number="detalleForm.precio_sugerido">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.preciox"
                                        v-text="message"
                                ></li>
                            </ul>
                        </div>
                    </div>


                    <div class="col-sm-2 text-center mt-2">
                            <label for>&nbsp;</label>
                            <b-button @click="agregarDetalle" variant="primary" ref="agregar">Agregar
                            </b-button>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.detalleProductos"
                                    v-text="message"
                            ></li>
                        </ul>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Descripcion</th>
                                <th>Precio Unit. C$</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>{{"2099PLB"}}</td>
                                <td>{{"1"}}</td>
                                <td>{{""}}</td>
                                <td>{{"Suministro e instalacion sobre de lavamanos"}}</td>
                                <td>{{"$645"}}</td>
                                <td>{{"$645"}}</td>
                            </tr>
                            <tr></tr>

                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <br>
                <div class="row">
                    <div class="col-sm-8">

                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Método Pago</label>
                                    <div class="form-group">
                                        <v-select v-model="detalleFormPago.via_pagox" :options="vias_pago"
                                                     deselect-label="No se puede eliminar este valor"
                                                     track-by="id_via_pago"
                                                     label="descripcion"
                                                     placeholder="Selecciona un método pago"
                                                     :searchable="true"
                                                     :show-labels="false"
                                                     :allow-empty="false"
                                                     ref="via_pago"
                                        ></v-select>
                                    </div>
                                    <ul class="error-messages">
                                        <li :key="message" v-for="message in errorMessages.via_pagox"
                                            v-text="message"></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Moneda</label>
                                    <div class="form-group">
                                        <v-select v-model="detalleFormPago.moneda_x" :options="monedas"
                                                     deselect-label="No se puede eliminar este valor"
                                                     track-by="id_moneda"
                                                     label="descripcion"
                                                     placeholder="Selecciona una moneda"
                                                     :searchable="true"
                                                     :show-labels="false"
                                                     :allow-empty="false"
                                                     ref="moneda"
                                        ></v-select>
                                    </div>
                                    <ul class="error-messages">
                                        <li :key="message" v-for="message in errorMessages.moneda_x"
                                            v-text="message"></li>
                                    </ul>
                                </div>
                            </div>

                            <template v-if="detalleFormPago.via_pagox">

                                <div v-if="[1,3,5,6].indexOf(detalleFormPago.via_pagox.id_via_pago) >= 0"
                                     class="col-sm-6">
                                    <div class="form-group">
                                        <label> Banco</label>
                                        <div class="form-group">
                                            <multiselect v-model="detalleFormPago.banco_x" :options="bancos"
                                                         deselect-label="No se puede eliminar este valor"
                                                         track-by="id_banco"
                                                         label="descripcion"
                                                         placeholder="Selecciona un método pago"
                                                         :searchable="true"
                                                         :show-labels="false"
                                                         :allow-empty="false"
                                                         ref="banco"
                                            ></multiselect>
                                        </div>
                                        <ul class="error-messages">
                                            <li :key="message" v-for="message in errorMessages.moneda_x"
                                                v-text="message"></li>
                                        </ul>
                                    </div>
                                </div>

                                <div v-if="[1].indexOf(detalleFormPago.via_pagox.id_via_pago) >= 0"
                                     class="col-sm-6">
                                    <div class="form-group">
                                        <label for>Número Minuta</label>
                                        <input class="form-control" v-model="detalleFormPago.numero_minuta">
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.numero_minuta"
                                                    v-text="message"
                                            ></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-sm-6" v-if="detalleFormPago.via_pagox.id_via_pago === 3">
                                    <div class="form-group">
                                        <label for>Número Voucher</label>
                                        <input class="form-control" v-model="detalleFormPago.numero_minuta">
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.numero_minuta"
                                                    v-text="message"
                                            ></li>
                                        </ul>
                                    </div>
                                </div>

                                <div v-if="detalleFormPago.via_pagox.id_via_pago === 4" class="col-sm-6">
                                    <div class="form-group">
                                        <label for>Número Nota Crédito</label>
                                        <input class="form-control" v-model="detalleFormPago.numero_nota_credito">
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.numero_nota_credito"
                                                    v-text="message"
                                            ></li>
                                        </ul>
                                    </div>
                                </div>

                                <div v-if="detalleFormPago.via_pagox.id_via_pago === 5" class="col-sm-6">
                                    <div class="form-group">
                                        <label for>Número Cheque</label>
                                        <input class="form-control" v-model="detalleFormPago.numero_cheque">
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.numero_cheque"
                                                    v-text="message"
                                            ></li>
                                        </ul>
                                    </div>
                                </div>

                                <div v-if="detalleFormPago.via_pagox.id_via_pago === 6" class="col-sm-6">
                                    <div class="form-group">
                                        <label for>Número Transferencia</label>
                                        <input class="form-control" v-model="detalleFormPago.numero_transferencia">
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.numero_transferencia"
                                                    v-text="message"
                                            ></li>
                                        </ul>
                                    </div>
                                </div>

                                <div v-if="detalleFormPago.via_pagox.id_via_pago === 7" class="col-sm-6">
                                    <div class="form-group">
                                        <label for>Número Recibo Pago Anticipado</label>
                                        <input class="form-control" v-model="detalleFormPago.numero_recibo_pago">
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.numero_recibo_pago"
                                                    v-text="message"
                                            ></li>
                                        </ul>
                                    </div>
                                </div>

                            </template>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for>Monto {{detalleFormPago.moneda_x ? detalleFormPago.moneda_x.codigo : ''}}</label>
                                    <input class="form-control" v-model.number="detalleFormPago.monto">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.monto"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-sm-6 mt-2">

                                    <label for>&nbsp;</label>
                                    <b-button @click="agregarMetodoPago" variant="primary"
                                            ref="agregarpago">Agregar Método
                                    </b-button>

                            </div>


                            <!--  <div class="col-sm-6">
                                <div class="form-group">
                                  <label for>&nbsp;</label>
                                  <button @click="pagoCompleto(0)" class="btn btn-info btn-agregar" ref="pagocord">Efectivo Completo Córdobas</button>
                                </div>
                              </div>

                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for>&nbsp;</label>
                                  <button @click="pagoCompleto(1)" class="btn btn-info btn-agregar" ref="pagodolar">Efectivo Completo Dólares</button>
                                </div>
                              </div>-->


                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Tipo</th>
                                        <th>Moneda</th>
                                        <th>Monto</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(item, index) in form.detallePago">
                                        <tr>
                                            <td style="width: 2%">
                                                <button @click="eliminarLineaPago(item, index)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                            <td style="width: 10%">
                                                <input class="form-control" disabled
                                                       v-model="item.via_pagox.descripcion">
                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detallePagos.${index}.via_pagox.id_via_pago`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </td>

                                            <td style="width: 10%">
                                                <input class="form-control" disabled
                                                       v-model="item.moneda_x.descripcion">
                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detallePagos.${index}.moneda_x.id_moneda`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </td>


                                            <td style="width: 5%" v-if="item.moneda_x.id_moneda === 1">
                                                <input class="form-control" type="number"
                                                       v-model.number="item.monto" step="0.01"
                                                       @change="calcularEquivalente(item)">

                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detalleProductos.${index}.monto`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </td>

                                            <td style="width: 5%" v-if="item.moneda_x.id_moneda === 3">
                                                <input class="form-control" type="number" step="0.01"
                                                       v-model.number="item.monto_me"
                                                       @change="calcularEquivalente(item)">
                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detalleProductos.${index}.monto`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </td>

                                        </tr>
                                        <tr></tr>
                                    </template>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>


                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Deuda</label>

                                    <p><strong>C$ {{645 | formatMoney(2)}}</strong></p>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.mt_deuda"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Diferencia</label>
                                    <p><strong>C$ {{0 | formatMoney(2)}}</strong></p>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.pago_vuelto"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Diferencia Dólares</label>

                                    <p><strong>$ {{form.pago_pendiente | formatMoney(2)}}</strong></p>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.pago_pendiente"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Vuelto Dólares</label>
                                    <p><strong>$ {{form.pago_vuelto | formatMoney(2)}}</strong></p>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.pago_vuelto"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <b-card-footer class="text-right mt-2">

                    <router-link :to="{ name: 'facturas' }">
                        <b-button variant="secondary" type="submit" class="mx-1">Cancelar</b-button>
                    </router-link>

                    <b-button :disabled="btnAction !== 'Facturar'"
                              @click="registrar"
                              variant="primary"
                              type="submit"
                    >{{ btnAction }}
                    </b-button>
                </b-card-footer>


            </b-card>

        </div>


    </div>


</template>

<script>
    import {BCard, BCardText, BLink, BFormGroup, BFormFile, BButton, BAlert, BCardFooter} from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import loader from '../assets/images/loader/block50.gif'
    import {getUserData} from "../auth/utils";
    import ToastificationContent from "../@core/components/toastification/ToastificationContent";

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
            BCardFooter
        },
        data() {
            return {
                loading: false,
                msg: 'Cargando el contenido espere un momento',
                url: loader,
                format: "dd-MM-yyyy",


                proformasBusquedaURL: "/inventario/proformas/buscar",

                clientesBusquedaURL: "/ventas/clientes/buscar",
                vendedoresBusquedaURL: "/ventas/vendedores/buscar",

                sucursalesBusquedaURL: "/sucursales/buscar",
                //bodegasBusquedaURL: "/bodegas/buscar",
                //productosBodegaBusquedaURL: "/productos/buscar-bodega-venta",
                clienteCredito: false,
                plazo_maximo_credito: 0,

                bodegas: [],
                sucursales: [],
                productos: [],
                servicios: [],
                productosORG: [],

                departamentos: [],
                municipios: [],
                zonas: [],
                vendedores: [],
                id_sucursal: 0,
                no_documento: '',

                formCliente: {
                    tipo_persona: 1,
                    nombre_comercial: '',
                    vendedor: '',
                    numero_ruc: '',
                    numero_cedula: '',
                    direccion: '',
                    id_tipo_cliente: 1,
                    telefono: '',
                    correo: '',
                    municipio: '',
                    giro_negocio: '',
                    zona: '',
                    id_impuesto: 1,
                    tipo_contribuyente: 1,
                    retencion_ir: true,
                    retencion_imi: true,
                    clasificacion: 1,
                    permite_credito: false,
                    plazo_credito: 0,
                    limite_credito: 0,
                    porcentaje_descuento: 0,
                    observaciones: '',
                    permite_consignacion: false,
                    tramite_cheque: 15,
                },


                //afectacionesBusquedaURL: "/ventas/afectaciones/buscar",
                afectaciones: [],
                vias_pago: [],
                monedas: [],
                bancos: [],
                detalleForm: {
                    productox: '',
                    afectacionx: [],
                    cantidad: 1,
                    precio_sugerido: 0,
                    precio_sugerido_me: 0,
                    descuento: 0,
                    precio_costo: 0,
                    precio_lista: 0,
                    subtotal: 0,
                    total: 0,
                    total_sin_iva: 0,
                },

                detalleFormPago: {
                    via_pagox: [],
                    monto: 1,
                    moneda_x: [],
                    banco_x: [],
                    numero_minuta: '',
                    numero_nota_credito: '',
                    numero_cheque: '',
                    numero_transferencia: '',
                    numero_recibo_pago: '',
                },
                tipo_cliente: 1,//tipo normal por defecto
                proformaForm: {
                    afectacion_producto: [],
                    bodega_producto: [],
                },
                proformaFormHeader: {es_nuevo: true},


                form: {
                    es_nuevo: true,
                    proforma_cliente: [],
                    proforma_bodega: [],
                    proforma_sucursal: [],
                    proforma_vendedor: [],
                    proformasBusqueda: {},
                    proforma_especifica: false,
                    tipo_venta: 1,
                    no_documento: "",
                    f_factura: "YYYY-MM-DD",
                    f_vencimiento: "YYYY-MM-DD",
                    serie: "",
                    id_tipo: 1,
                    //factura_moneda: {},
                    factura_sucursal: {},
                    factura_bodega: "",
                    tipo_identificacion: 1,
                    identificacion: "",
                    factura_cliente: {},
                    id_tipo_cliente: 1,
                    dias_credito: 0,
                    nombre_razon: "",
                    factura_vendedor: "",
                    t_cambio: 35.60,
                    tasa_paralela: 0,
                    doc_exoneracion: "",
                    doc_exoneracion_ir: "",
                    doc_exoneracion_imi: "",
                    impuesto_exonerado: false,
                    mt_retencion: 0,
                    mt_retencion_imi: 0,
                    mt_impuesto: 0,
                    mt_descuento: 0,
                    mt_ajuste: 0,

                    pago_vuelto: 0,
                    pago_pendiente: 0,

                    pago_vuelto_mn: 0,
                    pago_pendiente_mn: 0,

                    observacion: "",
                    mt_subtotal: 0,
                    mt_subtotal_sin_iva: 0,
                    aplicaIR: false,
                    aplicaIMI: false,
                    aplicaIVA: true,
                    total_final: 0,
                    total_final_cordobas: 0,

                    total_unidades_sin_bonificacion: 0,
                    total_unidades_con_bonificacion: 0,

                    detalleProductos: [],
                    detallePago: [],

                },
                btnAction: "Facturar",
                btnActionCliente: "Registrar Cliente",
                registrandoCliente: false,

                errorMessages: []
            }

        },
        methods: {
            getUserData() {
                axios.get('api/user').then(res => {
                    this.form.userData = res.data
                })
            },
        },
        mounted() {
            getUserData();
        }
    }
</script>

<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>
