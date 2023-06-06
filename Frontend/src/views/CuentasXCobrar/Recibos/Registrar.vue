<template>
    <b-card>
        <b-alert show variant="success">
            <div class="alert-body">
                <span><strong>Datos del Recibo</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="tipo_recibo">Tipo de recibo:</label>
                    <b-form-select class="form-control" id="tipo_recibo" v-model="form.tipo_recibo">
                        <option value="1">Cancelación factura crédito</option>
                        <option value="2">Anticipo de clientes</option>
                    </b-form-select>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.tipo_recibo"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label> Cliente</label>
                    <div class="form-group">
                        <v-select
                                :filterable="false"
                                :options="clientes"
                                @input="seleccionarCliente"
                                @search="onSearch"
                                label="text"
                                style="width: 100%;"
                                v-model="form.recibo_cliente"
                        >
                            <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select-->
                            <template slot="no-options">
                                Escriba para buscar un cliente
                            </template>
                        </v-select>
                    </div>
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.recibo_cliente"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="nombre_pesona"> Recibo de:</label>
                    <input class="form-control" id="nombre_pesona" placeholder="Nombre persona"
                           v-model="form.nombre_persona">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.nombre_persona"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="nombre_pesona"> Concepto:</label>
                    <input class="form-control" id="nombre_pesona" placeholder="Concepto o Descripción del recibo"
                           v-model="form.concepto">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.concepto"
                                v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for>Fecha</label>
                    <b-form-datepicker
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            :disabled="false"
                            class="mb-0"
                            local="es"
                            placeholder="f.emision"
                            selected-variant="primary"
                            v-model="form.fecha_emision"
                    />
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


            <div class="col-sm-6">
                <div class="form-group">
                    <label for="tc">T/C</label>
                    <input class="form-control" disabled id="tc" type="text" v-model="form.t_cambio">
                </div>
            </div>

            <!--<template v-if="form.tipo_recibo === '2'">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label> Cuentas contables</label>
                        <div class="form-group">
                            <v-select :allow-empty="false" :options="cuentas_contables"
                                      :searchable="true"
                                      label="nombre_cuenta_completo"
                                      placeholder="Selecciona una cuenta para pagar"
                                      ref="cuenta"
                                      track-by="id_cuenta_contable"
                                      v-model="form.cuenta_contablex"
                            ></v-select>
                        </div>
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.cuenta_contablex"
                                    v-text="message"></li>
                            </ul>
                        </b-alert>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> Concepto comprobante:</label>
                            <input class="form-control"
                                   placeholder="Digite el concepto para esta cuenta en comprobante contable"
                                   v-model="form.concepto_comprobante">
                            <b-alert show variant="danger">
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.concepto_comprobante"
                                        v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>
            </template>-->


        </b-row>

        <div v-if="!form.recibo_cliente">
            <b-alert show variant="info">
                <div class="alert-body">
                    <span>Se requiere que seleccione un cliente para continuar.</span>
                </div>
            </b-alert>

            <hr>
        </div>

        <br>
        <div class="row">
            <template v-if="form.tipo_recibo === '1'">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label> Listado de Documentos pendientes</label>
                        <div class="form-group">
                            <v-select :allow-empty="false" :options="cuentas"
                                      :searchable="true"
                                      label="text"
                                      placeholder="Selecciona una cuenta para pagar"
                                      ref="cuenta"
                                      track-by="id_cuentaxcobrar"
                                      v-model="detalleForm.cuentax"
                                      v-on:input="cargar_detalles_cuenta()"
                            ></v-select>
                        </div>
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.cuentax" v-text="message"></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for>Saldo Pendiente C$</label>
                        <label class="form-control"> {{detalleForm.cuentax?detalleForm.cuentax.saldo_actual:0 |
                            formatMoney(2)}}</label>

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for>Monto Abono C$</label>
                        <input  @change="detalleForm.monto = detalleForm.cuentax? Math.max(Math.min(Number(!isNaN(detalleForm.monto)?detalleForm.monto.toFixed(2):0), Number(Number(detalleForm.cuentax.saldo_actual).toFixed(2))), 1):0"
                               class="form-control" min="0" ref="abono" type="number"
                               v-model.number="detalleForm.monto">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.monto"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </template>

            <template v-if="form.tipo_recibo === '2'">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for>Monto Abono C$</label>
                        <input
                                class="form-control" min="0" ref="abono" type="number"
                                v-model.number="detalleForm.monto">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.monto"
                                        v-text="message"
                                ></li>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </template>


            <div class="col-sm-2">
                <div class="form-group">
                    <b-form-checkbox class="mx-lg-1 mb-sm-1 mt-sm-1" v-model="detalleForm.aplicaIR">
                        Retención IR
                    </b-form-checkbox>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>No. Documento Exoneración IR</label>
                    <input :disabled="!detalleForm.aplicaIR" class="form-control" type="text"
                           v-model="detalleForm.doc_exoneracion_ir">
                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <b-form-checkbox class="mx-lg-1 mb-sm-1 mt-sm-1" v-model="detalleForm.aplicaIMI">
                        Retención IMI
                    </b-form-checkbox>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>No. Documento Exoneración IMI</label>
                    <input :disabled="!detalleForm.aplicaIMI" class="form-control" type="text"
                           v-model="detalleForm.doc_exoneracion_imi">
                </div>
            </div>


            <template v-if="form.tipo_recibo === '1'">
                <div class="col-sm1">
                    <div class="form-group">
                        <label for>&nbsp;</label>
                        <b-button @click="agregarDetalle" class="btn-agregar" ref="agregar" variant="info">Agregar
                        </b-button>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="col-sm1">
                    <div class="form-group">
                        <label for>&nbsp;</label>
                        <b-button @click="agregarDetalleOtros" class="btn-agregar" ref="agregar" variant="info">Agregar
                        </b-button>
                    </div>
                </div>
            </template>


        </div>

        <div class="row">
            <div class="col-sm-12">
                <b-alert show variant="danger">
                    <ul class="error-messages">
                        <li
                                :key="message"
                                v-for="message in errorMessages.detalleCuentasxCobrar"
                                v-text="message"
                        ></li>
                    </ul>
                </b-alert>

                <table class="table table-bordered table-hover table-responsive">
                    <thead>
                    <tr>
                        <th></th>
                        <th v-if="form.tipo_recibo ==='1'" style="min-width:50px">Documento Origen</th>
                        <th style="min-width:300px">Concepto</th>
                        <th style="min-width:100px">Subtotal C$</th>
                        <th style="min-width:100px">Retención IR C$</th>
                        <th style="min-width:100px">Retención IMI C$</th>
                        <th style="min-width:150px">Monto Abono C$</th>
                        <th v-if="form.tipo_recibo ==='1'" colspan="2" style="min-width:300px">Saldo Pendiente C$</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in form.detalleCuentasxCobrar">
                        <tr>
                            <td>
                                <b-button @click="eliminarLinea(item, index)" v-b-tooltip.hover.top="'Eliminar línea'"
                                          variant="outline-danger">
                                    <feather-icon icon="TrashIcon"></feather-icon>
                                </b-button>
                            </td>
                            <td v-if="form.tipo_recibo ==='1'" style="min-width:150px">
                                <input class="form-control" disabled
                                       v-model="item.cuentax.no_documento_origen">
                            </td>

                            <td>
                                {{item.descripcion_pago}}
                            </td>

                            <td class="text-center" style="width: 12%">
                                <input class="form-control" disabled min="0"
                                       v-model.number="item.monto_subtotal">
                            </td>
                            <td class="text-center" style="width: 12%">
                                <input class="form-control"
                                       disabled
                                       v-model.number="item.monto_retencion_ir"
                                       v-b-tooltip.hover.top="'No. Documento Exoneración:'+item.doc_exoneracion_ir">
                            </td>
                            <td class="text-center" style="width: 12%">
                                <input class="form-control"
                                       disabled
                                       v-model.number="item.monto_retencion_imi"
                                       v-b-tooltip.hover.top="'No. Documento Exoneración:'+item.doc_exoneracion_imi">
                            </td>
                            <td class="text-center">
                                <!-- Para recibos por cobro a clientes-->
                                <input v-if="form.tipo_recibo ==='1'" @change="establecerConcepto(item)"
                                       class="form-control" min="0" v-model.number="item.monto">
                                <!-- Para recibos por anticipos de clientes-->
                                <input v-if="form.tipo_recibo ==='2'" readonly
                                       class="form-control" min="0" v-model.number="item.monto">
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`detalleCuentasxCobrar.${index}.monto`]"
                                                v-text="message">
                                        </li>
                                    </ul>
                                </b-alert>

                            </td>
                            <td v-if="form.tipo_recibo ==='1'" class="text-center" colspan="2">
                                <strong>C$ {{calcularSaldoX(item)| formatMoney(2)}}</strong>
                            </td>

                        </tr>
                        <tr></tr>
                    </template>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td class="text-right"><strong> Total a pagar C$</strong></td>
                        <td  class="text-center">
                            <strong>{{total_a_pagar| formatMoney(2)}}</strong>
                        </td>
                        <td v-if="form.tipo_recibo ==='1'" class="text-right"><strong> Total saldo pendiente C$</strong></td>
                        <td v-if="form.tipo_recibo ==='1'" class="text-center">
                            <strong>{{form.saldo_total| formatMoney(2)}}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td class="text-right"><strong> Total a pagar $</strong></td>
                        <td class="text-center">
                            <strong>{{form.monto_total_me| formatMoney(2)}}</strong>
                        </td>
                        <td v-if="form.tipo_recibo ==='1'" class="text-right"><strong> Total saldo pendiente $</strong></td>
                        <td v-if="form.tipo_recibo ==='1'" class="text-center">
                            <strong>{{form.saldo_total_me| formatMoney(2)}}</strong>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <br>
        <b-alert show variant="success">
            <div class="alert-body">
                <span><strong>Datos de pago y Resumen</strong></span>
            </div>
        </b-alert>

        <hr>
        <b-row>
            <div class="col-sm-12">

                <b-row>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Método Pago</label>
                            <div class="form-group">
                                <v-select :allow-empty="false" :options="vias_pago"
                                          :searchable="true"
                                          label="descripcion"
                                          placeholder="Selecciona un método pago"
                                          ref="via_pago"
                                          track-by="id_via_pago"
                                          v-model="detalleFormPago.via_pagox"
                                ></v-select>
                            </div>
                            <b-alert show variant="danger">
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.via_pagox"
                                        v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Moneda</label>
                            <div class="form-group">
                                <v-select :allow-empty="false" :options="monedas"
                                          :searchable="true"
                                          label="descripcion"
                                          placeholder="Selecciona una moneda"
                                          ref="moneda"
                                          track-by="id_moneda"
                                          v-model="detalleFormPago.moneda_x"
                                ></v-select>
                            </div>
                            <b-alert show variant="danger">
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.moneda_x"
                                        v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <template v-if="detalleFormPago.via_pagox">

                        <div class="col-sm-6"
                             v-if="[1,3,5,6].indexOf(detalleFormPago.via_pagox.id_via_pago) >= 0">
                            <div class="form-group">
                                <label> Banco</label>
                                <div class="form-group">
                                    <v-select :allow-empty="false" :options="bancos"
                                              :searchable="true"
                                              label="descripcion"
                                              placeholder="Selecciona un método pago"
                                              ref="banco"
                                              track-by="id_banco"
                                              v-model="detalleFormPago.banco_x"
                                    ></v-select>
                                </div>
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li :key="message" v-for="message in errorMessages.moneda_x"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div class="col-sm-6"
                             v-if="[1].indexOf(detalleFormPago.via_pagox.id_via_pago) >= 0">
                            <div class="form-group">
                                <label for>Número Minuta</label>
                                <input class="form-control" v-model="detalleFormPago.numero_minuta">
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_minuta"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div class="col-sm-6" v-if="detalleFormPago.via_pagox.id_via_pago === 3">
                            <div class="form-group">
                                <label for="numero_minuta">Número Voucher</label>
                                <input class="form-control" id="numero_minuta" v-model="detalleFormPago.numero_minuta">
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_minuta"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div class="col-sm-6" v-if="detalleFormPago.via_pagox.id_via_pago === 4">
                            <div class="form-group">
                                <label for="nota_credito">Número Nota Crédito</label>
                                <input class="form-control" id="nota_credito"
                                       v-model="detalleFormPago.numero_nota_credito">
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_nota_credito"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div class="col-sm-6" v-if="detalleFormPago.via_pagox.id_via_pago === 5">
                            <div class="form-group">
                                <label for="numero_cheque">Número Cheque</label>
                                <input class="form-control" id="numero_cheque" v-model="detalleFormPago.numero_cheque">
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_cheque"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div class="col-sm-6" v-if="detalleFormPago.via_pagox.id_via_pago === 6">
                            <div class="form-group">
                                <label for="numero_transferencia">Número Transferencia</label>
                                <input class="form-control" id="numero_transferencia"
                                       v-model="detalleFormPago.numero_transferencia">
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_transferencia"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div class="col-sm-6" v-if="detalleFormPago.via_pagox.id_via_pago === 7">
                            <div class="form-group">
                                <label for="numero_recibo_pago">Número Recibo Pago Anticipado</label>
                                <input class="form-control" id="numero_recibo_pago"
                                       v-model="detalleFormPago.numero_recibo_pago">
                                <b-alert show variant="danger">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_recibo_pago"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                    </template>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="monto">Monto {{detalleFormPago.moneda_x ? detalleFormPago.moneda_x.codigo
                                :''}}</label>
                            <input class="form-control" id="monto" min="0" v-model.number="detalleFormPago.monto">
                            <b-alert show variant="danger">
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.monto"
                                            v-text="message"
                                    ></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for>&nbsp;</label>
                            <b-button @click="agregarMetodoPago" class="btn-agregar" ref="agregarpago"
                                      variant="info">Agregar Pago
                            </b-button>
                        </div>
                    </div>


                    <div class="col-sm-12 table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Tipo</th>
                                <th>Moneda</th>
                                <th>Monto</th>
                                <th>No Referencia</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="(item, index) in form.detallePago">
                                <tr>
                                    <td style="width: 2%">
                                        <b-button variant="danger" @click="eliminarLineaPago(item, index)">
                                            <feather-icon icon="TrashIcon"></feather-icon>
                                        </b-button>
                                    </td>
                                    <td style="width: 5%">
                                        <input class="form-control" disabled
                                               v-model="item.via_pagox.descripcion">
                                        <b-alert show variant="danger">
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detallePagos.${index}.via_pagox.id_via_pago`]"
                                                        v-text="message">
                                                </li>
                                            </ul>
                                        </b-alert>

                                    </td>

                                    <td style="width: 5%">
                                        <input class="form-control" disabled
                                               v-model="item.moneda_x.descripcion">
                                        <b-alert show variant="danger">
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detallePagos.${index}.moneda_x.id_moneda`]"
                                                        v-text="message">
                                                </li>
                                            </ul>
                                        </b-alert>

                                    </td>


                                    <td style="width: 5%" v-if="item.moneda_x.id_moneda === 1">
                                        <input @change="calcularEquivalente(item)" class="form-control"
                                               min="0" type="number"
                                               v-model.number="item.monto">

                                        <b-alert show variant="danger">
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detallePagos.${index}.monto`]"
                                                        v-text="message">
                                                </li>
                                            </ul>
                                        </b-alert>

                                    </td>

                                    <td style="width: 5%" v-if="item.moneda_x.id_moneda === 2">
                                        <input @change="calcularEquivalente(item)" class="form-control" min="0"
                                               type="number"
                                               v-model.number="item.monto_me">
                                        <b-alert show variant="danger">
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detallePagos.${index}.monto`]"
                                                        v-text="message">
                                                </li>
                                            </ul>
                                        </b-alert>

                                    </td>

                                    <td style="width: 5%">
                                        <template v-if="[1,3].indexOf(item.via_pagox.id_via_pago) >= 0">
                                            <input class="form-control"
                                                   v-model="item.numero_minuta">
                                            <b-alert show variant="danger">
                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detalleCuentasxCobrar.${index}.numero_minuta`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </b-alert>

                                        </template>

                                        <template v-if="item.via_pagox.id_via_pago === 4">
                                            <input class="form-control"
                                                   v-model="item.numero_nota_credito">
                                            <b-alert show variant="danger">
                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detalleCuentasxCobrar.${index}.numero_nota_credito`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </b-alert>

                                        </template>

                                        <template v-if="item.via_pagox.id_via_pago === 5">
                                            <input class="form-control"
                                                   v-model="item.numero_cheque">
                                            <b-alert show variant="danger">
                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detalleCuentasxCobrar.${index}.numero_cheque`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </b-alert>

                                        </template>

                                        <template v-if="item.via_pagox.id_via_pago === 6">
                                            <input class="form-control"
                                                   v-model="item.numero_transferencia">
                                            <b-alert show variant="danger">
                                            </b-alert>
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detalleCuentasxCobrar.${index}.numero_transferencia`]"
                                                        v-text="message">
                                                </li>
                                            </ul>
                                        </template>

                                        <template v-if="item.via_pagox.id_via_pago === 7">
                                            <input class="form-control"
                                                   v-model="item.numero_recibo_pago">
                                            <b-alert show variant="danger">
                                                <ul class="error-messages">
                                                    <li
                                                            :key="message"
                                                            v-for="message in errorMessages[`detalleCuentasxCobrar.${index}.numero_recibo_pago`]"
                                                            v-text="message">
                                                    </li>
                                                </ul>
                                            </b-alert>

                                        </template>

                                    </td>

                                </tr>
                                <tr></tr>
                            </template>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>


                </b-row>
            </div>

            <br>
            <br>

            <div class="col-sm-12">

                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="2">Resumen</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="width: 50%">
                                <label> Total a Abonar en Córdobas</label>
                            </td>
                            <td style="width: 50%">
                                <p><strong>C$ {{form.monto_total | formatMoney(2)}}</strong></p>
                            </td>
                        </tr>

                        <template v-for="(item, index) in form.detallePago">

                            <tr>
                                <td style="width: 50%">
                                    {{'(-) Pagado con ' +item.via_pagox.descripcion+ ' (Equivalente en Córdobas)'}}
                                </td>
                                <td style="width: 50%">
                                    <p><strong>C$ {{item.monto | formatMoney(2)}}</strong></p>
                                </td>
                            </tr>

                        </template>
                        <tr>
                            <td style="width: 50%">
                                <label> Monto Pendiente Córdobas</label>
                            </td>
                            <td style="width: 50%">
                                <p><strong>C$ {{total_deuda | formatMoney(2)}}</strong></p>
                            </td>
                        </tr>

                        <tr>
                            <td style="width: 50%">
                                <label> Monto Pendiente (Equivalente en Dólares)</label>
                            </td>
                            <td style="width: 50%">
                                <p><strong>$ {{form.pago_pendiente | formatMoney(2)}}</strong></p>
                            </td>
                        </tr>


                        <tr>
                            <td style="width: 50%">
                                <label> Monto Vuelto</label>
                            </td>
                            <td style="width: 50%">
                                <p><strong>C$ {{form.pago_vuelto_mn | formatMoney(2)}}</strong></p>
                            </td>
                        </tr>


                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>

            </div>
        </b-row>
        <b-card-footer class="text-lg-right">
            <router-link :to="{ name: 'recibos' }">
                <b-button class="mx-lg-1 m-0" type="button" variant="secondary">Cancelar</b-button>
            </router-link>
            <b-button
                    :disabled="btnAction !== 'Registrar Recibo'"
                    @click="registrar"
                    class="mx-lg-1 m-0"
                    type="button" variant="primary"
            >{{ btnAction }}
            </b-button>
        </b-card-footer>


        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>


    </b-card>
</template>


<script type="text/ecmascript-6">
    import recibos from "../../../api/CuentasXCobrar/recibos_oficiales";
    import cuenta from "../../../api/CuentasXCobrar/cuentas_por_cobrar";
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import numberasstring from "../../../assets/custom-scripts/NumberAsString"
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
    import roundNumber from '../../../assets/custom-scripts/Round'
    import axios from "axios";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";

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
                loading: true,
                url: loadingImage,

                es: es,
                format: "dd-MM-yyyy",

                clientesBusquedaURL: "/ventas/clientes/buscar",

                cuentas: [],
                cuentas_contables: [],
                centros_costo: [],
                centros_ingreso: [],
                auxiliares: [],
                vias_pago: [],
                monedas: [],
                bancos: [],
                detalleForm: {
                    cuentax: '',
                    fecha_pago: '',
                    descripcion_pago: '',
                    monto: 0,
                    monto_me: 0,
                    moneda_x: [],
                    doc_exoneracion_ir: '',
                    doc_exoneracion_imi: '',
                    aplicaIR: false,
                    aplicaIMI: false,
                },

                detalleFormPago: {
                    via_pagox: null,
                    monto: 0,
                    monto_me: 0,
                    moneda_x: null,
                    banco_x: null,
                    numero_minuta: '',
                    numero_nota_credito: '',
                    numero_cheque: '',
                    numero_transferencia: '',
                    numero_recibo_pago: ''
                },

                form: {
                    notRequired: 'No requiere un código auxiliar',
                    notAvailable: 'Codigo no disponible',
                    concepto_comprobante: '',
                    cuenta_contablex: '',
                    no_documento: "",
                    fecha_emision: moment(new Date()).format("YYYY-MM-DD"),
                    recibo_cliente: {},
                    nombre_persona: "",
                    concepto: "",
                    centro_costo: [],
                    auxiliar: [],

                    t_cambio: 0,
                    monto_total_me: 0,
                    monto_total: 0,
                    // monto_total_letras: '',
                    // monto_total_letras_me: '',
                    saldo_total_me: 0,
                    saldo_total: 0,

                    detalleCuentasxCobrar: [],
                    detallePago: [],

                    pago_vuelto: 0,
                    pago_pendiente: 0,

                    pago_vuelto_mn: 0,
                    pago_pendiente_mn: 0,

                    tipo_recibo: '1',
                    otros_conceptos: false,
                    es_deudor: false,

                },
                btnAction: "Registrar Recibo",
                errorMessages: [],
                clientes: [],
            }

        },
        computed: {


            total_a_pagar() {
                let self = this;

                self.form.monto_total = Number(this.form.detalleCuentasxCobrar.reduce((carry, item) => {
                    return (carry + Number(item.monto))
                }, 0).toFixed(2));

                self.form.saldo_total = Number(this.form.detalleCuentasxCobrar.reduce((carry, item) => {
                    return (carry + Number(Number(item.cuentax.saldo_actual) - item.monto))
                }, 0).toFixed(2));

                if (!isNaN(this.form.monto_total)) {
                    // this.form.monto_total_me = roundNumber.decimalAdjust('ceil', Number((this.form.monto_total / this.form.t_cambio).toFixed(2)));
                    this.form.monto_total_me = Number(this.form.monto_total / this.form.t_cambio).toFixed(2);
                    // console.log("monto total en dolares  "+this.form.monto_total_me);
                    // this.form.saldo_total_me = roundNumber.decimalAdjust('ceil', Number((this.form.saldo_total / this.form.t_cambio).toFixed(2)));
                    this.form.saldo_total_me = Number(this.form.saldo_total / this.form.t_cambio).toFixed(2);

                    return this.form.monto_total;
                } else return 0;
            },


            total_deuda() {

                let total_pagos = this.form.detallePago.reduce((carry, item) => {
                    return (carry + Number(item.moneda_x.id_moneda === 1 ? Number(item.monto) : Math.round(((item.monto_me * this.form.t_cambio) * 100)/100).toFixed(2)) )
                }, 0);


                /*let total_pagos_cuentas = this.form.detalleCuentasxCobrar.reduce((carry, item) => {
                    return (carry + Number(item.monto));
                }, 0);*/
                let total_pagos_cuentas = this.form.monto_total;
                // console.log('total recibido' + total_pagos);
                // console.log('total a pagar' + total_pagos_cuentas);
                if ((total_pagos_cuentas - total_pagos).toFixed(2) < 0) {
                    this.form.pago_pendiente = 0;
                    this.form.pago_pendiente_mn = 0;

                    // this.form.pago_vuelto_mn = roundNumber.round(Number((Number((total_pagos).toFixed(2)) - Number((this.form.monto_total)).toFixed(2))), 2);
                     this.form.pago_vuelto_mn = Number(total_pagos_cuentas - total_pagos).toFixed(2);
                    // this.form.pago_vuelto = roundNumber.round(Number((this.form.pago_vuelto_mn / this.form.t_cambio).toFixed(2)), 2);
                    this.form.pago_vuelto = Number(this.form.pago_vuelto_mn / this.form.t_cambio).toFixed(2);

                } else {
                    this.form.pago_pendiente_mn = Number(total_pagos_cuentas - total_pagos).toFixed(2);
                    /*console.log(" total pagos cuentas "+total_pagos_cuentas);
                    console.log("total pagos  "+ total_pagos);
                    console.log(" pago pendiente mn "+this.form.pago_pendiente_mn);*/
                    // this.form.pago_pendiente = roundNumber.decimalAdjust('ceil', Number((this.form.pago_pendiente_mn / this.form.t_cambio).toFixed(4)), -2);
                    this.form.pago_pendiente = Number(this.form.pago_pendiente_mn / this.form.t_cambio).toFixed(2);

                    this.form.pago_vuelto = 0;
                    this.form.pago_vuelto_mn = 0;
                }

                if (!isNaN(this.form.pago_pendiente_mn)) {
                    //this.form.pago_pendiente = roundNumber.round(Number((this.form.pago_pendiente_mn / this.form.t_cambio)),2);
                    return this.form.pago_pendiente_mn;
                } else return 0;
            },
            monto_letras(){
                if(this.form.monto_total > 0){
                    this.form.monto_total_letras = numberasstring.numberasstring(this.form.monto_total,'CORDOBA','CORDOBAS',true)
                } else {
                    this.form.monto_total_letras = '';
                }
                return this.form.monto_total_letras;
            },
            monto_letras_me(){
                if(this.form.monto_total_me > 0){
                    this.form.monto_total_letras_me = numberasstring.numberasstring(this.form.monto_total_me,'DOLAR','DOLARES',true)
                } else {
                    this.form.monto_total_letras_me = '';
                }
                return this.form.monto_total_letras_me;
            },

        },
        methods: {
            /*cambiarTipo() {
                let self = this;
                if (self.form.tipo_recibo === 1) {
                    self.form.otros_conceptos = false;
                } else if (self.form.tipo_recibo === 3){
                    self.form.otros_conceptos = true;
                }
            },*/

            /*
                    Author: omorales (c)
                    hot search
                    21/03/22    */
            onSearch(search, loading) {
                if (search.length) {
                    loading(true);
                    this.search(loading, search, this)
                }
            },
            select(e) {
                this.$emit('input', {
                    target: {
                        value: result,
                    },
                });
                this.onEsc()
            },
            search: _.debounce((loading, search, vm) => { // /ventas/clientes/buscar
                const self = this;
                axios.get(`/cuentas-por-cobrar/clientes/buscar?q=${escape(search)}&es_deudor=${vm.form.es_deudor}`).then(res => {
                    vm.options = res.data.results;
                    vm.clientes = res.data.results;
                    loading(false)
                })
            }, 100),

            calcularSaldoX(item) {

                return Number((roundNumber.round(Number(item.cuentax.saldo_actual), 2)) - Number(item.monto).toFixed(2));

            },

            cargar_detalles_cuenta() {
                var self = this;
                if (self.detalleForm.cuentax)
                    //self.detalleForm.moneda_x = self.monedas[1];
                    self.detalleForm.monto_me = 0;
                self.detalleForm.monto = Number(Number(self.detalleForm.cuentax.saldo_actual).toFixed(2));
            },

            establecerConcepto(itemX) {
                itemX.monto = Math.max(Math.min(Number(!isNaN(itemX.monto) ? itemX.monto.toFixed(2) : 0), Number(Number(itemX.cuentax.saldo_actual).toFixed(2))), 1);
                if (itemX.monto === Number(Number(itemX.cuentax.saldo_actual).toFixed(2))) {
                    itemX.descripcion_pago = 'Cancela factura No.' + itemX.cuentax.no_documento_origen + '.';
                } else {
                    itemX.descripcion_pago = 'Abona factura No.' + itemX.cuentax.no_documento_origen + '. Saldo C$' + (Number(itemX.cuentax.saldo_actual) - itemX.monto).toFixed(2) + '.';
                }

                let monto_retencion_ir = 0, monto_retencion_imi = 0, iva = 1.15;

                if (itemX.cuentax.cuenta_factura.impuesto_exonerado) {
                    iva = 1;
                }

                if (itemX.aplicaIR) {
                    monto_retencion_ir = roundNumber.round(Number(itemX.monto / iva) * 0.02, 2);
                } else {
                    monto_retencion_ir = 0;
                }

                if (itemX.aplicaIMI) {
                    monto_retencion_imi = roundNumber.round(Number(itemX.monto / iva) * 0.01, 2);
                } else {
                    monto_retencion_imi = 0;
                }

                itemX.monto_retencion_ir = monto_retencion_ir;
                itemX.monto_retencion_imi = monto_retencion_imi;

            },

            calcularEquivalente(itemX) {
                if (itemX.moneda_x.id_moneda === 1 && itemX.monto > 0) {
                    itemX.monto_me = Number((itemX.monto / this.form.t_cambio).toFixed(2));
                }

                if (itemX.moneda_x.id_moneda === 2 && itemX.monto_me > 0) {
                    itemX.monto = roundNumber.round(Number((itemX.monto_me * this.form.t_cambio)), 2);
                }
            },

            seleccionarCliente(e) {
                // const clienteP = e.target.value;
                let self = this;
                // self.form.recibo_cliente = clienteP;
                self.form.nombre_persona = self.form.recibo_cliente.nombre_comercial;

                self.loading = true;
                self.form.detalleCuentasxCobrar = [];
                self.form.detallePago = [];
                self.detalleForm.cuentax = '';

                cuenta.obtenerCuentasCliente({
                    id_cliente: self.form.recibo_cliente.id_cliente
                }, data => {
                    if (data !== null) {
                        self.cuentas = data;
                        self.detalleForm.cuentax = '';
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'No se encuentran cuentas pendientes para este cliente.',
                                    variant: 'warning',
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.detalleForm.cuentax = '';
                    }
                    self.loading = false;
                }, err => {
                    /*if(err.codigo_bateria){
                      self.detalleForm.bateria_busqueda = '';
                      self.$refs.bateria.focus();
                      alertify.warning("No se encuentra esta batería.",5);
                      self.detalleForm.cuentax = '';
                    }*/
                    self.loading = false;
                })

            },


            nuevo() {
                var self = this;
                recibos.nuevo({}, data => {
                        self.vias_pago = data.vias_pago;
                        self.monedas = data.monedas;
                        self.bancos = data.bancos;
                        self.cuentas = [];
                        self.cuentas_contables = data.cuentas_contables;
                        self.centros_costo = data.centro_costo;
                        self.centros_ingreso = data.centro_ingreso;
                        self.auxiliares = data.auxiliares;
                        self.form.t_cambio = Number(data.t_cambio.tasa_paralela);
                        self.loading = false;

                    },
                    err => {
                        console.log(err);
                    })

            },

            agregarDetalle() {
                var self = this;
                if (self.detalleForm.cuentax) {
                    if (self.detalleForm.monto > 0) {
                        let validacion = 0;
                        if (self.detalleForm.aplicaIR && self.detalleForm.doc_exoneracion_ir === '') {
                            validacion++;
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'CheckIcon',
                                        text: 'Si el pago retiene IR debe digitar el No. documento de retención.',
                                        variant: 'warning',
                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                        }
                        if (self.detalleForm.aplicaIMI && self.detalleForm.doc_exoneracion_imi === '') {
                            validacion++;
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'CheckIcon',
                                        text: 'Si el pago retiene IMI, digitar el No. documento IMI',
                                        variant: 'warning',
                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                        }
                        if (validacion === 0) {
                            let i = 0;
                            let keyx = 0;

                            if (self.form.detalleCuentasxCobrar) {
                                self.form.detalleCuentasxCobrar.forEach((cuentax, key) => {
                                    if (self.detalleForm.cuentax.id_cuentaxcobrar === cuentax.cuentax.id_cuentaxcobrar) {
                                        i++;
                                        keyx = key;
                                    }
                                });
                            }
                            if (i === 0) {

                                if (Number(Number(self.detalleForm.monto).toFixed(2)) === Number(Number(self.detalleForm.cuentax.saldo_actual).toFixed(2))) {
                                    self.detalleForm.descripcion_pago = 'Cancela factura No.' + self.detalleForm.cuentax.no_documento_origen + '.';
                                } else {
                                    self.detalleForm.descripcion_pago = 'Abona factura No.' + self.detalleForm.cuentax.no_documento_origen + '. Saldo C$' + (Number(self.detalleForm.cuentax.saldo_actual) - self.detalleForm.monto).toFixed(2) + '.';
                                }

                                let monto_retencion_ir = 0, monto_retencion_imi = 0, iva = 1.15;
                                if (!self.detalleForm.cuentax.es_registro_importado) {
                                    if (self.detalleForm.cuentax.cuenta_factura.impuesto_exonerado) {
                                        iva = 1;
                                    }
                                }

                                if (self.detalleForm.aplicaIR) {
                                    monto_retencion_ir = roundNumber.round(Number(self.detalleForm.monto / iva) * 0.02, 2);
                                } else {
                                    monto_retencion_ir = 0;
                                }

                                if (self.detalleForm.aplicaIMI) {
                                    monto_retencion_imi = roundNumber.round(Number(self.detalleForm.monto / iva) * 0.01, 2);
                                } else {
                                    monto_retencion_imi = 0;
                                }

                                self.form.detalleCuentasxCobrar.push({
                                    cuentax: self.detalleForm.cuentax,
                                    monto: Number(self.detalleForm.monto),
                                    monto_me: Number(self.detalleForm.monto / self.form.t_cambio).toFixed(2),
                                    monto_subtotal: Number(self.detalleForm.monto - monto_retencion_imi - monto_retencion_ir).toFixed(2),
                                    monto_retencion_ir: monto_retencion_ir,
                                    monto_retencion_imi: monto_retencion_imi,
                                    aplicaIR: self.detalleForm.aplicaIR,
                                    aplicaIMI: self.detalleForm.aplicaIMI,
                                    doc_exoneracion_ir: self.detalleForm.doc_exoneracion_ir,
                                    doc_exoneracion_imi: self.detalleForm.doc_exoneracion_imi,
                                    descripcion_pago: self.detalleForm.descripcion_pago,

                                });
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'CheckIcon',
                                            text: 'Agregado correctamente.',
                                            variant: 'success',
                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                            } else {
                                let nuevo_total = self.form.detalleCuentasxCobrar[keyx].monto + self.detalleForm.monto;
                                if (nuevo_total <= Number(self.form.detalleCuentasxCobrar[keyx].cuentax.saldo_actual)) {

                                    //self.form.detalleCuentasxCobrar[keyx].monto_subtotal = nuevo_total;
                                    self.form.detalleCuentasxCobrar[keyx].monto = nuevo_total;
                                    self.form.detalleCuentasxCobrar[keyx].monto_me = Number(self.detalleForm.monto / self.form.t_cambio).toFixed(2);
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'CheckIcon',
                                                text: 'Cuenta agregada correctamente',
                                                variant: 'success',
                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });

                                    if (nuevo_total === Number(self.form.detalleCuentasxCobrar[keyx].cuentax.saldo_actual)) {
                                        self.form.detalleCuentasxCobrar[keyx].descripcion_pago = 'Cancela factura No.' + self.form.detalleCuentasxCobrar[keyx].cuentax.no_documento_origen + '.';
                                    } else {
                                        self.form.detalleCuentasxCobrar[keyx].descripcion_pago = 'Abona factura No.' + self.detalleCuentasxCobrar[keyx].cuentax.no_documento_origen + '. Saldo C$' + (Number(self.form.detalleCuentasxCobrar[keyx].cuentax.saldo_actual) - nuevo_total).toFixed(2) + '.';
                                    }

                                } else {
                                    self.form.detalleCuentasxCobrar[keyx].monto = Number(self.form.detalleCuentasxCobrar[keyx].cuentax.saldo_actual);
                                    self.form.detalleCuentasxCobrar[keyx].monto_me = Number((self.form.detalleCuentasxCobrar[keyx].cuentax.saldo_actual / self.form.t_cambio).toFixed(2));
                                    self.form.detalleCuentasxCobrar[keyx].descripcion_pago = 'Cancela factura No.' + self.form.detalleCuentasxCobrar[keyx].cuentax.no_documento_origen + '.';
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'CheckIcon',
                                                text: 'Se ha agregado el monto máximo para saldar esta cuenta.',
                                                variant: 'warning',
                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                }
                            }

                            self.detalleForm.cuentax = null;
                            self.detalleForm.monto_me = 0;
                            self.detalleForm.monto = 0;
                            self.detalleForm.descripcion_pago = '';
                            this.$refs.cuenta.$el.focus()
                            // this.monto_letras();
                            // this.monto_letras_me();

                        }
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'El monto del abono debe ser mayor a cero.',
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
                                icon: 'CheckIcon',
                                text: 'Debe seleccionar una cuenta a saldar para agregar un abono.',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },
            agregarDetalleOtros() {
                const self = this;
                if (self.detalleForm.monto > 0) {
                    let validacion = 0;
                    if (self.detalleForm.aplicaIR && self.detalleForm.doc_exoneracion_ir === '') {
                        validacion++;
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'Si el pago retiene IR debe digitar el No. documento de retención.',
                                    variant: 'warning',
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                    }
                    if (self.detalleForm.aplicaIMI && self.detalleForm.doc_exoneracion_imi === '') {
                        validacion++;
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'Si el pago retiene IMI, digitar el No. documento IMI',
                                    variant: 'warning',
                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                    }
                    if (validacion === 0) {
                        let i = 0;
                        let keyx = 0;
                        if (i === 0) {
                            self.detalleForm.descripcion_pago = 'Abono anticipo de cliente';
                            let monto_retencion_ir = 0, monto_retencion_imi = 0, iva = 1.15;
                            if (self.detalleForm.aplicaIR) {
                                monto_retencion_ir = roundNumber.round(Number(self.detalleForm.monto / iva) * 0.02, 2);
                            } else {
                                monto_retencion_ir = 0;
                            }

                            if (self.detalleForm.aplicaIMI) {
                                monto_retencion_imi = roundNumber.round(Number(self.detalleForm.monto / iva) * 0.01, 2);
                            } else {
                                monto_retencion_imi = 0;
                            }

                            self.form.detalleCuentasxCobrar.push({
                                cuentax: '',
                                monto: Number(self.detalleForm.monto),
                                monto_me: Number(self.detalleForm.monto / self.form.t_cambio).toFixed(2),
                                monto_subtotal: Number(self.detalleForm.monto - monto_retencion_imi - monto_retencion_ir).toFixed(2),
                                monto_retencion_ir: monto_retencion_ir,
                                monto_retencion_imi: monto_retencion_imi,
                                aplicaIR: self.detalleForm.aplicaIR,
                                aplicaIMI: self.detalleForm.aplicaIMI,
                                doc_exoneracion_ir: self.detalleForm.doc_exoneracion_ir,
                                doc_exoneracion_imi: self.detalleForm.doc_exoneracion_imi,
                                descripcion_pago: self.detalleForm.descripcion_pago,

                            });
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'CheckIcon',
                                        text: 'Agregado correctamente.',
                                        variant: 'success',
                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                        } else {
                            let nuevo_total = self.form.detalleCuentasxCobrar[keyx].monto + self.detalleForm.monto;
                            //self.form.detalleCuentasxCobrar[keyx].monto_subtotal = nuevo_total;
                            self.form.detalleCuentasxCobrar[keyx].monto = nuevo_total;
                            self.form.detalleCuentasxCobrar[keyx].monto_me = Number(self.detalleForm.monto / self.form.t_cambio).toFixed(2);

                            // self.detalleForm.cuentax = null;
                            // self.detalleForm.monto_me = 0;
                            // self.detalleForm.monto = 0;
                            // self.detalleForm.descripcion_pago = '';
                            // this.$refs.cuenta.$el.focus()
                            // this.monto_letras();
                        }
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'El monto del abono debe ser mayor a cero.',
                                    variant: 'warning',
                                }
                            },
                            {
                                position: 'bottom-right'
                            });

                    }
                }
            },
            eliminarLinea(item, index) {
                if (this.form.detalleCuentasxCobrar.length >= 1) {
                    this.form.detalleCuentasxCobrar.splice(index, 1);

                } else {
                    this.form.detalleCuentasxCobrar = [];
                }
            },


            agregarMetodoPago() {
                const self = this;
                if (self.detalleFormPago.via_pagox && self.detalleFormPago.moneda_x) {
                    if (self.detalleFormPago.monto > 0) {
                        let i = 0;
                        let keyx = 0;
                        if (self.form.detallePago) {
                            self.form.detallePago.forEach((via_pago_x, key) => {
                                if ((self.detalleFormPago.via_pagox.id_via_pago === via_pago_x.via_pagox.id_via_pago) &&
                                    self.detalleFormPago.moneda_x.id_moneda === via_pago_x.moneda_x.id_moneda) {
                                    i++;
                                    keyx = key;
                                }
                            });
                        }
                        let monto_me = 0, monto_mn = 0;

                        if (self.detalleFormPago.moneda_x.id_moneda === 1) {
                            monto_mn = Number(self.detalleFormPago.monto);
                            monto_me = Number(self.detalleFormPago.monto / self.form.t_cambio).toFixed(2);

                        } else if (self.detalleFormPago.moneda_x.id_moneda === 2) {
                            monto_me = Number(self.detalleFormPago.monto);
                            //monto_mn = Number((self.detalleFormPago.monto * self.form.t_cambio));
                            monto_mn = Math.round(((self.detalleFormPago.monto * this.form.t_cambio)*100)/100).toFixed(2); // correct way to rounding, search for issues in other functions
                            // console.log('monto mn: ' + monto_mn);
                        }

                        if (i === 0) {
                            self.form.detallePago.unshift({
                                via_pagox: self.detalleFormPago.via_pagox,
                                moneda_x: self.detalleFormPago.moneda_x,
                                monto: Number(monto_mn),
                                monto_me: Number(monto_me),
                                banco_x: self.detalleFormPago.banco_x,
                                numero_minuta: self.detalleFormPago.numero_minuta,
                                numero_nota_credito: self.detalleFormPago.numero_nota_credito,
                                numero_cheque: self.detalleFormPago.numero_cheque,
                                numero_transferencia: self.detalleFormPago.numero_transferencia,
                                numero_recibo_pago: self.detalleFormPago.numero_recibo_pago,

                            });
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'CheckIcon',
                                        text: 'Método de pago agregado correctamente.',
                                        variant: 'success',
                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                        } else {
                            //let nuevo_monto_total = self.form.detallePago[keyx].monto + self.detalleFormPago.monto;
                            self.form.detallePago[keyx].monto_me = Number(self.form.detallePago[keyx].monto_me + self.detalleFormPago.monto);
                            self.form.detallePago[keyx].monto = Number((self.form.detallePago[keyx].monto_me * self.form.t_cambio).toFixed(2));
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'CheckIcon',
                                        text: 'Pago agregado correctamente.',
                                        variant: 'success',
                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                        }

                        self.detalleFormPago.via_pagox = null;
                        self.detalleFormPago.monto = 0;
                        self.detalleFormPago.monto_me = 0;
                        self.detalleFormPago.moneda_x = null;
                        self.detalleFormPago.banco_x = null;
                        self.detalleFormPago.numero_minuta = '';
                        self.detalleFormPago.numero_nota_credito = '';
                        self.detalleFormPago.numero_cheque = '';
                        self.detalleFormPago.numero_transferencia = '';
                        self.detalleFormPago.numero_recibo_pago = '';


                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'CheckIcon',
                                    text: 'El monto debe ser mayor a cero.',
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
                                icon: 'CheckIcon',
                                text: 'Debe seleccionar un método de pago y una moneda.',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },

            eliminarLineaPago(item, index) {
                if (this.form.detallePago.length >= 1) {
                    this.form.detallePago.splice(index, 1);

                } else {
                    this.form.detallePago = [];
                }
            },


            registrar() {
                var self = this;
                self.btnAction = "Registrando, espere....";
                if (self.form.monto_total > 0 && self.form.pago_pendiente > 0) {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'CheckIcon',
                                text: 'El monto del recibo debe ser pagado en su totalidad.',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    //self.errorMessages.serie = Array('Error serie');
                    self.btnAction = "Registrar Recibo";
                } else if (self.form.monto_total > 0 && self.form.pago_pendiente > 0) {

                } else {
                    self.$swal.fire({
                        title: 'Esta seguro de procesar el recibo?',
                        text: "Detalles del recibo: ",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, registrar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.value) {
                            recibos.registrar(
                                self.form,
                                data => {
                                    this.$swal.fire(
                                        'Recibo Registrado!',
                                        'El recibo fue registrado correctamente',
                                        'success'
                                    );
                                    this.$router.push({
                                        name: "recibos"
                                    });
                                },
                                err => {
                                    self.errorMessages = err;
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'CheckIcon',
                                                text: 'Revise los datos faltantes.',
                                                variant: 'warning',
                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                    self.btnAction = "Registrar Recibo";
                                }
                            );
                        } else {
                            self.btnAction = "Registrar Recibo";
                        }
                    })
                }
            },

            registrarCliente() {
                var self = this;
                cliente.registrarBasico(self.formCliente, data => {
                    alertify.success("Cliente registrado exitosamente", 5);
                    //console.log(data);
                    self.form.recibo_cliente = data;
                    self.form.tipo_identificacion = self.form.recibo_cliente.tipo_persona;
                    if (self.form.recibo_cliente.tipo_persona === 1) {
                        self.form.identificacion = self.form.recibo_cliente.numero_cedula;
                    } else {
                        self.form.identificacion = self.form.recibo_cliente.numero_ruc;
                    }

                    self.$refs.modal.close();

                }, err => {
                    self.errorMessages = err
                })
            },

            seleccionarTipo() {
                var self = this;
                if (self.form.id_tipo === 1) {
                    self.form.dias_credito = 0;
                } else {
                    self.form.dias_credito = 0;
                    self.form.dias_credito = self.plazo_maximo_credito;
                }
                /*calcular fecha*/
            }

        },
        mounted() {
            // this.obtenerAfectacionesTodas();
            // this.obtenerTodasBodegasProductos();
            this.nuevo();
        }
    };
</script>
<style lang="scss">

    @import 'src/@core/scss/vue/libs/vue-select.scss';

    .btn-agregar {
        margin-top: 1.6rem;
    }

    .check-label2 {
        margin-top: 40px;
    }
</style>




