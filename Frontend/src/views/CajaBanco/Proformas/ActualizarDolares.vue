<template>
    <b-card>
        <b-alert variant="success" show class="mb-2 mt-2">
            <div class="alert-body">
                <span><strong>Datos del Cliente</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-6">
                <div class="form-group">
                    <label> Cliente</label>
                    <div class="form-group">
                        <v-select
                                v-model="proforma.proforma_cliente"
                                style="width: 100%;"
                                :filterable="false"
                                :options="clientes"
                                label="text"
                                @search="onSearch"
                                @input="seleccionarCliente"
                        >
                            <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select-->
                            <template slot="no-options">
                                Escriba para buscar un cliente
                            </template>
                        </v-select>
                    </div>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.factura_cliente" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Tipo Cliente</label>
                    <select v-on:change="cambiarMascara" class="form-control" disabled
                            v-model.number="proforma.tipo_identificacion">
                        <option value="1">Natural</option>
                        <option value="2">Jurídico</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <div class="form-group text-center">
                        <b-button
                                variant="success"
                                @click="abrirModal"
                                v-b-tooltip.hover.top="'Registrar cliente nuevo'"
                                class="btn-agregar"
                                :disabled="true"
                        >
                            <feather-icon icon="PlusCircleIcon"></feather-icon>
                            Nuevo
                        </b-button>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label> Identificación</label>
                    <input class="form-control" v-mask="mascara" placeholder="Número Identificación"
                           v-model="form.identificacion">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.identificacion" v-text="message"></li>
                        </ul>
                    </b-alert>

                </div>
            </div>
        </b-row>

        <b-alert variant="success" show class="mt-2 mb-2">
            <div class="alert-body">
                <span><strong>Datos de Factura</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label> Sucursal</label>
                    <div class="form-group">
                        <v-select
                                label="descripcion"
                                v-model="proforma.proforma_sucursal"
                                :disabled="true"
                        ></v-select>

                    </div>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.factura_sucursal" v-text="message"></li>
                        </ul>
                    </b-alert>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Bodega</label>
                    <v-select
                            label="descripcion"
                            v-model="proforma.proforma_bodega"
                            :options="bodegas"
                            v-on:input="seleccionarBodega()"
                            :disabled="true"
                    >
                        <template slot="no-options">No se encontraron registros</template>
                    </v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.factura_bodega" v-text="message"></li>
                        </ul>
                    </b-alert>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label>Tipo Factura</label>
                    <b-form-select @change="seleccionarTipo"  disabled
                            v-model.number="proforma.id_tipo">
                        <option value="1">Contado</option>
                        <option :disabled="!clienteCredito" value="2">Crédito</option>
                    </b-form-select>
                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label> Plazo Crédito</label>
                    <b-form-select @change="obtenerTCParalela" :disabled="!(proforma.id_tipo===2)"
                             v-model.number="proforma.dias_credito">
                        <option :disabled="(form.id_tipo===2)" value="0">N/A</option>
                        <option :disabled="!(plazo_maximo_credito>=8)" value="8">8 días</option>
                        <option :disabled="!(plazo_maximo_credito>=15)" value="15">15 días</option>
                        <option :disabled="!(plazo_maximo_credito>=30)" value="30">30 días</option>
                        <option :disabled="!(plazo_maximo_credito>=45)" value="45">45 días</option>
                        <option :disabled="!(plazo_maximo_credito>=60)" value="60">60 días</option>
                    </b-form-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.plazo_credito" v-text="message"></li>
                        </ul>
                    </b-alert>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label> Vendedor</label>
                    <!--<div class="form-group">
                       <typeahead :initial="form.factura_vendedor" :trim="80" :url="vendedoresBusquedaURL" @input="seleccionarVendedor" style="width: 100%;"></typeahead>
                     </div>-->
                    <v-select
                            label="nombre_completo"
                            v-model="proforma.proforma_vendedor"
                            :options="vendedores"
                            :disabled="true"
                    ></v-select>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.factura_vendedor" v-text="message"></li>
                        </ul>
                    </b-alert>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Fecha Emitida</label>
                    <b-form-datepicker
                            v-model="form.f_factura"
                            :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                            local="es"
                            :disabled="true"
                            selected-variant="primary"
                            class="mb-0"
                            placeholder="f.emision"
                    />
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.f_factura"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>
                </div>
            </div>


            <div class="col-sm-3">
                <div class="form-group">
                    <label for>Fecha Vencimiento</label>
                    <input disabled type="text" class="form-control" v-model="proforma.f_vencimiento">
                </div>
            </div>


            <div class="col-sm-2">
                <div class="form-group">
                    <label for>T/C</label>
                    <input disabled type="text" class="form-control" v-model="proforma.t_cambio">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for>Observaciones</label>
                    <input type="text" class="form-control" readonly v-model="proforma.observacion">
                    <ul class="error-messages">
                        <li
                                :key="message"
                                v-for="message in errorMessages.observacion"
                                v-text="message"
                        ></li>
                    </ul>
                </div>
            </div>

        </b-row>

        <div v-if="!form.factura_bodega">
            <b-alert variant="success" show class="mt-2 mb-2">
                <div class="alert-body">
                    <span>Se requiere que seleccione una bodega para continuar.</span>
                </div>
            </b-alert>
            <hr>
        </div>

        <b-alert variant="success" show class="mt-2 mb-2">
            <div class="alert-body">
                <span><strong>Detalle de productos</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-6">
                <div class="form-group">
                    <label> Producto</label>
                    <div class="form-group">
                        <v-select v-model="detalleForm.productox" :options="productos"
                                  track-by="id_producto"
                                  label="text"
                                  placeholder="Selecciona un producto"
                                  :searchable="true"
                                  ref="producto"
                                  v-on:input="cargar_detalles_producto()"
                        ></v-select>
                    </div>
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li :key="message" v-for="message in errorMessages.productox" v-text="message"></li>
                        </ul>
                    </b-alert>
                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label for>Cantidad</label>
                    <input @keydown.enter="cambiarFocoCantidad"
                           @change="detalleForm.cantidad = Math.max(Math.min(Math.round(detalleForm.cantidad), (!isNaN(detalleForm.productox.cantidad_disponible))?detalleForm.productox.cantidad_disponible:0 ), 1)"
                           class="form-control" ref="cantidad" type="number" min="0"
                           v-model.number="detalleForm.cantidad">
                    <b-alert  variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.cantidadx"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for>Precio Lista $ </label>
                    <input  class="form-control" v-model.number="detalleForm.precio_sugerido_me">
                    <b-alert variant="danger" show>
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.preciox"
                                    v-text="message"
                            ></li>
                        </ul>
                    </b-alert>
                </div>
            </div>


            <div class="col-sm-2">
                <div class="form-group">
                    <label for>&nbsp;</label>
                    <b-button @click="agregarDetalle" class="btn-agregar"  variant="info" ref="agregar">
                        <feather-icon icon="PlusCircleIcon"></feather-icon> Agregar
                    </b-button>
                </div>
            </div>

        </b-row>

        <b-row>
            <div class="col-sm-12">
                <b-alert variant="danger" show>
                    <ul class="error-messages">
                        <li
                                :key="message"
                                v-for="message in errorMessages.detalleProductos"
                                v-text="message"
                        ></li>
                    </ul>
                </b-alert>

                <div class="table table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <!--<th>Afectación</th>
                            <th>% Ajuste</th>-->
                            <th>Cantidad</th>
                            <!-- <th >U/M</th>-->
<!--                            <th>P. Lista C$</th>-->
                            <th>Descuento %</th>
                            <th>Descuento $</th>
                            <!--<th>Ajuste C$</th>-->
                            <th>Precio Unit. $</th>
                            <!--<th >Monto IVA U$</th>-->
                            <th>Valor $</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(item, index) in form.detalleProductos">
                            <tr>
                                <td style="width: 1%">
                                    <b-button variant="danger" @click="eliminarLinea(item, index)">
                                        <feather-icon icon="TrashIcon"></feather-icon>
                                    </b-button>
                                </td>
                                <td style="width: 20%">
                                    <input class="form-control" disabled v-model="item.productox.descripcion">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`detalleProductos.${index}.productox.id_producto`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>
                                </td>
                                <!--<td style="width: 12%">
                                    <v-select
                                            :disabled="true"
                                            v-model="item.afectacionx"
                                            label="descripcion"
                                            :options="afectaciones"
                                            v-on:input="calcularAjuste(item)"
                                    ></v-select>
                                    <ul class="error-messages">
                                        <li
                                                v-for="message in errorMessages[`detalleProductos.${index}.afectacionx.id_afectacion`]"
                                                :key="message"
                                                v-text="message">
                                        </li>
                                    </ul>
                                </td>

                                <td style="width: 5%">
                                    {{item.p_ajuste +'%'}}
                                </td>-->

                                <td style="width: 1%">
                                    <input @change="item.cantidad = Math.max(Math.min(Math.round(item.cantidad), item.productox.cantidad_disponible), 1)"
                                           class="form-control" type="number" min="1"
                                           v-model.number="item.cantidad">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`detalleProductos.${index}.cantidad`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>
                                </td>

                                <!--<td style="width: 3%">
                                     {{item.productox.unidad_medida}}
                                     <ul class="error-messages">
                                       <li
                                               :key="message"
                                               v-for="message in errorMessages[`detalleProductos.${index}.unidad_medida`]"
                                               v-text="message">
                                       </li>
                                     </ul>
                                   </td>-->


                                <!--<td style="width: 10%">
                                    <input class="form-control" disabled v-model.number="item.precio">
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`detalleProductos.${index}.precio`]"
                                                v-text="message">
                                        </li>
                                    </ul>
                                </td>-->

                                <td style="width: 1%">
                                    <input :disabled="item.p_ajuste>0" class="form-control"
                                           v-model.number="item.p_descuento"
                                           @change="item.p_descuento = Math.max(Math.min(Math.round(item.p_descuento), 50), 0)">
                                    <b-alert variant="danger" show>
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages[`detalleProductos.${index}.p_descuento`]"
                                                    v-text="message">
                                            </li>
                                        </ul>
                                    </b-alert>
                                </td>

                                <td style="width: 3%">
                                    <strong> {{calcular_montos(item)| formatMoney(2)}}</strong>
                                </td>

                                <!--<td style="width: 8%"><strong>C$ {{item.mt_ajuste| formatMoney(2)}}</strong>
                                </td>
-->

                                <td style="width: 1%">
                                    <strong> {{item.p_unitario| formatMoney(2)}}</strong>
                                </td>

                                <td style="width: 5%">
                                    <strong> {{item.total_sin_iva| formatMoney(2)}}</strong>
                                </td>


                            </tr>
                            <tr></tr>
                        </template>
                        </tbody>
                        <tfoot>

                        <tr>
                            <td colspan="5"></td>
                            <td>Subtotal</td>
                            <td><strong>$ {{total_subt_sin_iva | formatMoney(2)}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td>No Doc. Exoneración</td>
                            <td><input :disabled="form.aplicaIVA" class="form-control"
                                       v-model="form.doc_exoneracion"></td>
                            <td><label class="check-label"><input v-model="form.aplicaIVA"
                                                                  type="checkbox"> I.V.A.</label>
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.doc_exoneracion"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>
                            </td>
                            <td><strong>$ {{total_impuesto | formatMoney(2)}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td>No. Documento:</td>
                            <td><input :disabled="!form.aplicaIR" class="form-control"
                                       v-model="form.doc_exoneracion_ir"></td>
                            <td><label class="check-label"><input :disabled="!(form.id_tipo===1)"
                                                                  v-model="form.aplicaIR"
                                                                  type="checkbox"> Retención</label>
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.doc_exoneracion_ir"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>
                            </td>
                            <td><strong>$ {{total_retencion | formatMoney(2)}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td>No. Documento:</td>
                            <td><input :disabled="!form.aplicaIMI" class="form-control"
                                       v-model="form.doc_exoneracion_imi"></td>
                            <td><label class="check-label"><input :disabled="!(form.id_tipo===1)"
                                                                  v-model="form.aplicaIMI"
                                                                  type="checkbox"> Retención IMI</label>
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.doc_exoneracion_imi"
                                                v-text="message"
                                        ></li>
                                    </ul>
                                </b-alert>
                            </td>
                            <td><strong>$ {{total_retencion_imi | formatMoney(2)}}</strong></td>
                        </tr>

                        <tr class="table-active table-light">
                            <td colspan="0"></td>
                            <td class="item-footer"> Totales</td>
                            <td class="item-footer">
                                <strong>{{total_cantidad}}</strong>
                            </td>
                            <td colspan="0"><!--Total Descuento | Ajuste--></td>
                            <td><strong>$ {{total_descuento | formatMoney(2)}}</strong></td>
                            <td><strong><!--C$ {{total_ajuste | formatMoney(2)}}--></strong></td>
                           <!-- <td>Total</td>-->
                            <td><strong>$ {{gran_total_cordobas | formatMoney(2)}}</strong></td>
                        </tr>

                        <tr>
                            <td colspan="5"></td>
                            <td>Equivalente Córdobas</td>
                            <td><strong>C$ {{gran_total | formatMoney(2)}}</strong></td>
                        </tr>

                        </tfoot>
                    </table>
                </div>

            </div>
        </b-row>
        <br>
        <b-card-footer class="text-lg-right text-center">
            <router-link  :to="{ name: 'cajabanco-proformas' }">
                <b-button type="button" variant="secondary" class="mx-lg-1">Cancelar</b-button>
            </router-link>
            <b-button :disabled="btnAction !== 'Actualizar Cotización'" @click="actualizar" variant="primary" class="mx-lg-1"
                    type="button">{{ btnAction }}
            </b-button>
            <b-button :disabled="btnActionAnu !== 'Anular Cotización'" @click="anular(6)" variant="danger" class="mx-lg-1"
                    type="button">{{ btnActionAnu }}
            </b-button>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>

        <!-- Modal - registro basico cliente -->

        <div>
            <b-modal id="modal-select2" title="Registrar cliente" ref="modal" hide-footer size="lg">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Tipo Persona</label>
                            <b-form-select  v-model.number="formCliente.tipo_persona">
                                <option value="1">Natural</option>
                                <option value="2">Jurídico</option>
                            </b-form-select>
                        </div>
                    </div>

                    <template v-if="formCliente.tipo_persona === 1">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Número Cédula</label>
                                <input class="form-control" v-mask="'#############A'" placeholder="Número Cédula"
                                       v-model="formCliente.numero_cedula">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message" v-for="message in errorMessages.numero_cedula"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Número RUC</label>
                                <input class="form-control" v-mask="'A#############'" placeholder="Número RUC"
                                       v-model="formCliente.numero_ruc">
                                <b-alert variant="danger" show>
                                    <ul class="error-messages">
                                        <li :key="message" v-for="message in errorMessages.numero_ruc"
                                            v-text="message"></li>
                                    </ul>
                                </b-alert>

                            </div>
                        </div>
                    </template>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Nombre Completo</label>
                            <input class="form-control" placeholder="Nombre Completo"
                                   v-model="formCliente.nombre_comercial">
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.nombre_comercial"
                                        v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Contacto</label>
                            <input class="form-control" placeholder="Contacto" v-model="formCliente.contacto">
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.contacto" v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> Dirección</label>
                            <input class="form-control" placeholder="Dirección" v-model="formCliente.direccion">
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.direccion" v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Teléfono</label>
                            <input class="form-control" placeholder="Teléfono" v-model="formCliente.telefono">
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.telefono" v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Departamento</label>
                            <v-select
                                    :options="departamentos"
                                    label="descripcion"
                                    v-model="formCliente.departamento"
                                    v-on:input="obtenerMunicipios()"
                                    style="background: white"
                                    placeholder="Seleccione un departamento"
                            >
                                <template slot="no-options">No se encontraron registros</template>
                            </v-select>
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.departamento" v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Municipio</label>
                            <v-select
                                    :options="municipios"
                                    label="descripcion"
                                    v-model="formCliente.municipio"
                                    style="background: white"
                                    placeholder="Seleccione un municipio"
                            >
                                <template slot="no-options">No se encontraron registros</template>
                            </v-select>
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.municipio" v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Vendedor</label>
                            <v-select
                                    :options="vendedores"
                                    label="nombre_completo"
                                    v-model="formCliente.vendedor"
                                    style="background: white"
                                    placeholder="Seleccione un vendedor"
                            >
                                <template slot="no-options">No se encontraron registros</template>
                            </v-select>
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.vendedor" v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> Observaciones</label>
                            <input class="form-control" placeholder="Observaciones" v-model="formCliente.observaciones">
                            <b-alert variant="danger" show>
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.observaciones" v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>
                </div>

                <div class="content-box-footer text-right">
                    <b-button variant="secondary" @click="cerrarModal" class="mx-lg-1 mx-0">Cancelar</b-button>
                    <b-button :disabled="btnActionCliente !== 'Registrar Cliente'"
                              @click="registrarCliente" class="mx-lg-1 mx-0"
                              variant="primary"
                              type="button">{{ btnActionCliente }}
                    </b-button>
                </div>
            </b-modal>
        </div>


        <!-- Fin Modal - registro basico cliente -->

    </b-card>
</template>


<script type="text/ecmascript-6">

    import proforma from "../../../api/CajaBanco/proformas";
    import bodega from "../../../api/Inventario/bodegas";
    import cliente from '../../../api/Ventas/clientes'
    import tc from '../../../api/contabilidad/tasas-cambio'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import {
        BPaginationNav,
        BFormCheckbox,
        BFormGroup,
        BCard,
        BCardFooter,
        VBTooltip,
        BRow,
        BButton,
        BFormCheckboxGroup,
        BFormDatepicker,
        BAlert,
        BFormSelect, BModal, VBModal, BListGroup, BListGroupItem, BFormInput, BForm
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import Ripple from 'vue-ripple-directive'
    import roundNumber from '../../../assets/custom-scripts/Round'
    import moment from '../../../../../Backend/resources/app/assets/plugins/moment/moment'
    import ToastificationContent from '../../../@core/components/toastification/ToastificationContent'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import axios from "axios";
    import Round from '../../../assets/custom-scripts/Round'

    export default {
        components: {
            BCard,
            BCardFooter,
            BPaginationNav,
            BFormCheckbox,
            BFormGroup,
            BFormInput,
            vSelect,
            BRow,
            BButton,
            BFormCheckboxGroup,
            BFormDatepicker,
            BAlert,
            BFormSelect,
            BModal,
            BListGroup,
            BListGroupItem,
            BForm,
        },
        directives: {
            'b-tooltip': VBTooltip,
            'b-modal': VBModal,
            Ripple,
        },
        data() {
            return {
                loading: true,
                msg: 'Cargando el contenido espere un momento',
                url:  loadingImage,

                es: es,
                format: "dd-MM-yyyy",
                mascara: '#############A',

                clientesBusquedaURL: "/ventas/clientes/buscar",
                vendedoresBusquedaURL: "/ventas/vendedores/buscar",

                sucursalesBusquedaURL: "/sucursales/buscar",
                //bodegasBusquedaURL: "/bodegas/buscar",
                //productosBodegaBusquedaURL: "/productos/buscar-bodega-venta",
                clienteCredito: false,
                plazo_maximo_credito: 0,

                bodegas: [],
                productos: [],
                servicios: [],
                productosORG: [],

                departamentos: [],
                municipios: [],
                zonas: [],
                vendedores: [],

                formCliente: {
                    tipo_persona: 1,
                    nombre_comercial: '',
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
                proforma: {},

                form: {
                    no_documento: "",
                    id_proforma: "",
                    f_factura: moment(new Date()).format("YYYY-MM-DD"),
                    f_vencimiento: moment(new Date()).format("YYYY-MM-DD"),
                    serie: "",
                    id_tipo: 1,
                    es_nuevo: false,
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
                    t_cambio: 0,
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


                    detalleProductos: [],
                    detallePago: [],


                },
                btnAction: "Actualizar Cotización",
                btnActionAnu: "Anular Cotización",

                errorMessages: []
            }

        },
        computed: {
            total_subt_sin_iva() {
                this.form.mt_subtotal_sin_iva = Number((this.form.detalleProductos.reduce((carry, item) => {
                        return (carry + Number(item.total_sin_iva));
                    }
                    , 0)).toFixed(2));
                this.form.mt_subtotal = Number((this.form.detalleProductos.reduce((carry, item) => {
                        return (carry + Number(item.subtotal));
                    }
                    , 0)).toFixed(2));
                return this.form.mt_subtotal_sin_iva.toFixed(2);
            },
            total_impuesto() {
                if (this.form.aplicaIVA) {
                    this.form.mt_impuesto = Number((this.form.detalleProductos.reduce((carry, item) => {
                        return (carry + Number(item.mt_impuesto.toFixed(2)))
                    }, 0)).toFixed(2));
                } else {
                    this.form.mt_impuesto = 0;
                }

                return this.form.mt_impuesto;
            },

            total_retencion() {
                if (this.form.aplicaIR && (Number(this.form.mt_subtotal_sin_iva * Number(this.form.t_cambio)) >= 1000)) {
                    this.form.mt_retencion = Number((this.form.mt_subtotal_sin_iva * 0.02).toFixed(2));
                } else {
                    this.form.mt_retencion = 0;
                }
                return this.form.mt_retencion;
            },

            total_retencion_imi() {
                if (this.form.aplicaIMI) {
                    this.form.mt_retencion_imi = Number((this.form.mt_subtotal_sin_iva * 0.01).toFixed(2));
                } else {
                    this.form.mt_retencion_imi = 0;
                }

                return this.form.mt_retencion_imi;
            },

            total_ajuste() {
                this.form.mt_ajuste = this.form.detalleProductos.reduce((carry, item) => {
                    return (carry + Number(item.mt_ajuste))
                }, 0)
                return this.form.mt_ajuste;
            },

            total_descuento() {
                this.form.mt_descuento = Number((this.form.detalleProductos.reduce((carry, item) => {
                    return (carry + Number(item.mt_descuento.toFixed(2)))
                }, 0)).toFixed(2));
                return this.form.mt_descuento;
            },


            total_cantidad() {
                return this.form.detalleProductos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad))
                }, 0)
            },


            gran_total_cordobas() {
                this.form.total_final_cordobas = roundNumber.round(Number(((this.form.mt_subtotal_sin_iva - this.form.mt_retencion - this.form.mt_retencion_imi + this.form.mt_impuesto)).toFixed(2)), 2);
                //roundNumber.round(Number((Number(this.form.total_final)*this.form.t_cambio).toFixed(2)),2);

                if (!isNaN(this.form.total_final_cordobas)) {
                    return this.form.total_final_cordobas;


                } else return 0;
            },


            gran_total() {
                this.form.total_final = roundNumber.decimalAdjust('ceil', Number(this.form.total_final_cordobas * this.form.t_cambio), -1);

                if (!isNaN(this.form.total_final)) {
                    return this.form.total_final;
                } else return 0;
            },


            total_deuda() {

                /*let total_pagos = this.form.detallePago.reduce((carry, item) => {
                    return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto_me : Number((item.monto/this.form.t_cambio).toFixed(2))));
                }, 0);*/

                let total_pagos_cordobas = 0;

                if (this.form.detallePago.length) {
                    total_pagos_cordobas = this.form.detallePago.reduce((carry, item) => {
                        return (carry + Number(item.moneda_x.id_moneda === 1 ? item.monto : Number((item.monto_me * this.form.t_cambio).toFixed(2))));
                    }, 0);
                }

                /*console.log('Total pago C$: '+total_pagos_cordobas);
                console.log('Total factura C$: '+ this.form.total_final_cordobas);
                console.log('Dif C$: '+ this.form.total_final_cordobas- total_pagos_cordobas);*/

                if (((Number((this.form.total_final_cordobas).toFixed(2)) - Number((total_pagos_cordobas).toFixed(2))).toFixed(2)) < 0.005) {
                    this.form.pago_pendiente = 0;
                    this.form.pago_pendiente_mn = 0;
                } else {
                    this.form.pago_pendiente_mn = Number((Number((this.form.total_final_cordobas).toFixed(2)) - Number((total_pagos_cordobas).toFixed(2))).toFixed(2));
                    this.form.pago_pendiente = roundNumber.decimalAdjust('ceil', Number((this.form.pago_pendiente_mn / this.form.t_cambio).toFixed(4)), -2);
                }


                /*let total_pagos_cordobas = this.form.detallePago.reduce((carry, item) => {
                  return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto : Number((item.monto_me*this.form.t_cambio).toFixed(2))));
                }, 0);*/

                if (!isNaN(this.form.pago_pendiente_mn)) {
                    //Number((Number((this.form.total_final*this.form.t_cambio).toFixed(2)) - total_pagos_cordobas).toFixed(2));
                    return this.form.pago_pendiente_mn;
                } else return 0;
            },

            total_vuelto() {

                /* let total_pagos = this.form.detallePago.reduce((carry, item) => {
                     return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto_me : Number(item.monto/this.form.t_cambio.toFixed(2))));
                 }, 0);*/

                let total_pagos = 0;

                if (this.form.detallePago.length) {
                    total_pagos = this.form.detallePago.reduce((carry, item) => {
                        return (carry + Number(item.moneda_x.id_moneda === 1 ? item.monto : Number((item.monto_me * this.form.t_cambio).toFixed(2))));
                    }, 0);
                }


                if (((Number((this.form.total_final_cordobas).toFixed(2)) - Number((total_pagos).toFixed(2))).toFixed(2)) > 0) {///revision
                    this.form.pago_vuelto = 0;
                    this.form.pago_vuelto_mn = 0;
                } else {
                    this.form.pago_vuelto_mn = roundNumber.round(Number((Number((total_pagos).toFixed(2)) - Number((this.form.total_final_cordobas)).toFixed(2))), 2);
                    this.form.pago_vuelto = roundNumber.round(Number((this.form.pago_vuelto_mn / this.form.t_cambio).toFixed(2)), 2);
                }

                //console.log('Master C$: '+((Number((this.form.total_final).toFixed(2)) - Number((total_pagos).toFixed(2))).toFixed(2)));


                /*let total_pagos_cordobas = this.form.detallePago.reduce((carry, item) => {
                  return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto : Number((item.monto_me*this.form.t_cambio).toFixed(2))));
                }, 0);*/

                if (!isNaN(this.form.pago_vuelto_mn)) {

                    return this.form.pago_vuelto_mn;
                } else return 0;


            },


        },
        methods: {
            /*
                     Author: omorales (c)
                     hot search
                     14/03/22 */
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
                const self = this
                axios.get(`/cuentas-por-cobrar/clientes/buscar?q=${escape(search)}&es_deudor=${vm.form.es_deudor}`).then(res => {
                    vm.options = res.data.results
                    vm.clientes = res.data.results
                    loading(false)
                })
            }, 100),
            actualizar() {
                var self = this

                self.btnAction = 'Guardando, espere......'
                self.$swal.fire({
                    title: 'Esta seguro de modificar esta proforma?',
                    text: "Se registrarán los cambios",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        self.loading = true;
                        proforma.actualizar(self.form, data => {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Proforma actualizada correctamente. ' ,
                                        variant: 'success',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            this.$router.push({
                                name: 'cajabanco-proformas'
                            })
                        }, err => {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Ha ocurrido un error, revise los datos faltantes. ',
                                        variant: 'warning',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            self.loading = false;
                            self.errorMessages = err
                            self.btnAction = 'Actualizar Cotización'
                        })
                    } else {
                        self.loading = false;
                        self.btnAction = "Actualizar Cotización";
                    }
                })


            },

            obtenerProforma() {
                var self = this;
                proforma.obtenerProforma(
                    {
                        id_proforma: this.$route.params.id_proforma,
                        cargar_dependencias: true
                    },
                    data => {
                        self.loading = false;
                        self.proforma = data.factura;
                        self.form.id_proforma = data.factura.id_proforma;
                        self.ajustes = data.ajustes_basicos;


                        self.proforma.proforma_productos.forEach((detalleProducto, key) => {
                            self.form.detalleProductos.push({
                                productox: detalleProducto.bodega_producto,
                                afectacionx: detalleProducto.afectacion_producto,
                                cantidad: Number(detalleProducto.cantidad),
                                precio_costo: Number(detalleProducto.bodega_producto.costo_promedio),
                                precio_lista: Number(detalleProducto.precio_lista),
                                precio: Number(detalleProducto.precio),
                                p_descuento: Number(detalleProducto.p_descuento),
                                mt_descuento: Number(detalleProducto.mt_descuento),
                                p_impuesto: Number(detalleProducto.p_impuesto),
                                mt_impuesto: Number(detalleProducto.mt_impuesto),
                                subtotal: 0,
                                total: 0,
                                total_sin_iva: 0,
                                mt_ajuste: Number(detalleProducto.mt_ajuste),
                                p_ajuste: Number(detalleProducto.p_ajuste),
                            });


                        });

                        self.seleccionarBodega();

                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error al cargar los datos. ' . err ,
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.loading = false;
                        console.log(err);
                    }
                );
            },
            cambiarMascara() {
                let self = this;
                self.form.identificacion = '';
                if (self.form.tipo_identificacion === 1) {
                    self.mascara = '#############A'
                } else {
                    self.mascara = 'A#############'
                }
            },

            calcularEquivalente(itemX) {
                if (itemX.moneda_x.id_moneda === 1 && itemX.monto > 0) {
                    itemX.monto_me = Number((itemX.monto / this.form.t_cambio).toFixed(2));
                }

                if (itemX.moneda_x.id_moneda === 2 && itemX.monto_me > 0) {
                    itemX.monto = Number((itemX.monto_me * this.form.t_cambio).toFixed(2));
                }
            },

            abrirModal() {
                this.$refs['modal'].show()
            },
            cerrarModal() {
                this.$refs['modal'].hide()
            },
            cambiarFocoCantidad() {
                const self = this;
                self.$refs.agregar.focus()
            },

            seleccionarCliente(e) {
                const trabajadorP = e.target.value;
                var self = this;
                self.form.factura_cliente = trabajadorP;
                self.form.tipo_identificacion = self.form.factura_cliente.tipo_persona;
                self.form.id_tipo = 1;
                self.form.dias_credito = 0;
                self.plazo_maximo_credito = self.form.factura_cliente.plazo_credito;
                self.form.factura_vendedor = trabajadorP.vendedor_cliente;
                (self.form.factura_cliente.permite_credito && self.form.factura_cliente.plazo_credito > 0) ? self.clienteCredito = true : self.clienteCredito = false;

                if (self.form.factura_cliente.tipo_persona === 1) {
                    self.form.identificacion = self.form.factura_cliente.numero_cedula;
                } else {
                    self.form.identificacion = self.form.factura_cliente.numero_ruc;
                }
            },
            seleccionarVendedor(e) {
                const proveedorP = e.target.value;
                var self = this;
                self.form.proforma_vendedor = proveedorP;
            },
            seleccionarSucursal(e) {
                const sucursalP = e.target.value;
                var self = this;
                self.bodegas = [];
                self.form.factura_bodega = [];
                self.proforma.proforma_sucursal = sucursalP;
                self.form.serie = self.form.proforma_sucursal.serie;
                if (self.form.proforma_sucursal.sucursal_bodegas.length) {
                    self.bodegas = self.form.proforma_sucursal.sucursal_bodegas;
                    self.form.proformma_bodega = self.bodegas[0];

                    self.loading = true;
                    self.form.detalleProductos = [];
                    self.form.detallePago = [];
                    self.detalleForm.productox = '';


                    bodega.buscarProductosBodega({
                        id_bodega: self.proforma.id_bodega
                    }, data => {
                        if (data !== null) {

                            self.productos = [];
                            self.productosORG = [];
                            self.servicios = [];

                            self.productosORG = data.productos;
                            self.servicios = data.servicios;

                            self.productosORG.forEach((producto, key) => {
                                self.productos.push(producto)
                            });
                            self.servicios.forEach((servicio, key) => {
                                self.productos.push(servicio)
                            });

                            self.detalleForm.productox = '';
                        } else {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'No se encuentran productos para esta bodega. ',
                                        variant: 'warning',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            self.detalleForm.productox = '';
                        }
                        self.loading = false;
                    }, err => {
                        /*if(err.codigo_bateria){
                          self.detalleForm.bateria_busqueda = '';
                          self.$refs.bateria.focus();
                          alertify.warning("No se encuentra esta batería.",5);
                          self.detalleForm.productox = '';
                        }*/
                        self.loading = false;
                    })

                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'La sucursal seleccionada no tiene bodegas disponibles. ',
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },
            seleccionarArea(e) {
                const areaP = e.target.value;
                var self = this;
                self.form.factura_area = areaP;
            },
            seleccionarBodega() {
                var self = this;
                self.loading = true;
                /*                self.form.detalleProductos = [];
                                self.form.detallePago = [];
                                self.detalleForm.productox = '';*/

                bodega.buscarProductosBodega({
                    id_bodega: self.proforma.id_bodega
                }, data => {
                    if (data !== null) {

                        self.productos = [];
                        self.productosORG = [];
                        self.servicios = [];

                        self.productosORG = data.productos;
                        self.servicios = data.servicios;

                        self.productosORG.forEach((producto, key) => {
                            self.productos.push(producto)
                        });
                        self.servicios.forEach((servicio, key) => {
                            self.productos.push(servicio)
                        });

                        self.detalleForm.productox = '';
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'No se encuentran productos para esta bodega. ',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.detalleForm.productox = '';
                    }
                    self.loading = false;
                }, err => {
                    /*if(err.codigo_bateria){
                      self.detalleForm.bateria_busqueda = '';
                      self.$refs.bateria.focus();
                      alertify.warning("No se encuentra esta batería.",5);
                      self.detalleForm.productox = '';
                    }*/
                    self.loading = false;
                })
            },
            cargar_detalles_producto() {
                //const productoP = e.target.value;
                var self = this;
                //self.detalleForm.productox = productoP;
                if (self.detalleForm.productox)
                    self.detalleForm.cantidad = 1;
                if (self.tipo_cliente === 1) {
                    self.detalleForm.precio_sugerido = Round.round(Number((self.detalleForm.productox.precio_sugerido * self.form.tasa_paralela)), 2);
                    self.detalleForm.precio_sugerido_me = Number(self.detalleForm.productox.precio_sugerido)
                } else {
                    self.detalleForm.precio_sugerido = Round.round(Number((self.detalleForm.productox.precio_distribuidor * self.form.tasa_paralela)), 2);
                    self.detalleForm.precio_sugerido_me = Number(self.detalleForm.productox.precio_distribuidor)
                }
                self.$refs.cantidad.focus();
                //self.detalleForm.precio_unitario = self.detalleForm.productox.costo_promedio;

            },
            calcularAjuste(itemX) {
                itemX.p_ajuste = Number(itemX.afectacionx.valor);
                if (itemX.p_ajuste > 0) {
                    itemX.p_descuento = 0;
                }
            },

            calcular_montos(itemX) {

                itemX.subtotal = Number(((Number(itemX.precio) * Number(itemX.cantidad))).toFixed(2));

                itemX.mt_descuento = Number((Number(itemX.p_descuento / 100) * Number(((Number(itemX.precio) * Number(itemX.cantidad))).toFixed(2))).toFixed(2));

                itemX.p_ajuste = Number(itemX.afectacionx.valor);

                itemX.mt_ajuste = Number((Number(itemX.p_ajuste / 100) * Number(((Number(itemX.precio) * Number(itemX.cantidad))).toFixed(2))).toFixed(2));

                itemX.p_unitario = Number(((Number(itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste) / Number(itemX.cantidad))).toFixed(2));

                /*console.log(itemX.p_impuesto);
                console.log(Number(itemX.p_impuesto/100));
                console.log(itemX.subtotal-itemX.mt_descuento-itemX.mt_ajuste);
                console.log(Number((Number(itemX.p_impuesto/100)*Number((itemX.subtotal-itemX.mt_descuento-itemX.mt_ajuste)))));
                */
                /*let xy = Number((Number(itemX.p_impuesto/100)*Number((itemX.subtotal-itemX.mt_descuento-itemX.mt_ajuste))));
                console.log(roundNumber.round(xy,2));*/

                itemX.mt_impuesto = roundNumber.round(Number((Number(itemX.p_impuesto / 100) * Number((itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste))).toFixed(2)), 2);

                itemX.total_sin_iva = roundNumber.round(Number((itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste)), 2);

                itemX.total = Number((itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste + itemX.mt_impuesto).toFixed(2));

                if (!isNaN(itemX.mt_descuento)) {
                    return itemX.mt_descuento;
                } else return 0;
            },

            obtenerTCParalela() {
                var self = this;
                self.loading = true;
                tc.obtenerTCParalela({
                    fecha: self.form.f_factura,
                    dias: self.form.dias_credito
                }, data => {
                    if (data.tasa_paralela !== null) {
                        self.form.t_cambio = Number(data.tasa_paralela);
                        //self.form.tasa_oficial = Number(data.tasa);
                        self.form.f_vencimiento = data.fecha;

                        self.form.detalleProductos.forEach((productox, key) => {
                            productox.precio_lista = Number((productox.precio_lista_me * self.form.tasa_paralela).toFixed(2));
                            productox.precio = Number((productox.precio_lista_me * self.form.tasa_paralela).toFixed(2));
                            // console.log(productox.precio_lista);T/C
                        });


                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'No se encuentran tasas de cambio para esta fecha. ',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.form.t_cambio = 0;
                        self.form.f_vencimiento = self.form.f_factura;
                        self.form.detalleProductos = [];
                    }
                    self.loading = false;
                }, err => {
                    if (err.fecha[0]) {
                        self.form.t_cambio = 0;
                        self.form.f_vencimiento = self.form.f_factura;
                        alertify.warning(err.fecha[0], 5);
                        self.loading = false;
                    }
                })
            },

            /*obtenerTC(){
              var self = this;
              tc.obtenerTC({
                fecha: self.form.f_factura
              }, data => {
                if(data.tasa_paralela !== null){
                  self.form.t_cambio = Number(data.tasa_paralela);
                }else{
                  alertify.warning("No se encuentra tasa de cambio para esta fecha.",5);
                  self.form.t_cambio = 0;
                }
                self.loading = false;
              }, err => {
                if(err.fecha[0]){
                  self.form.t_cambio = 0;
                  alertify.warning(err.fecha[0],5);
                  self.loading = false;
                }
              })
            },*/


            /*obtenerAfectacionesTodas() {
               var self = this;
               afectacion.obtenerTodos(
                       data => {
                         self.afectaciones = data;
                       },
                       err => {
                         console.log(err);
                       }
               );
             },*/

            obtenerMunicipios() {
                var self = this
                self.form.municipio = null;
                if (self.formCliente.departamento.municipios_departamento)
                    self.municipios = self.formCliente.departamento.municipios_departamento
            },

            nueva() {
                var self = this;
                proforma.nueva({}, data => {
                        self.vias_pago = data.vias_pago;
                        self.afectaciones = data.afectaciones;
                        self.detalleForm.afectacionx = self.afectaciones[0];
                        self.monedas = data.monedas;
                        self.bancos = data.bancos;
                        //self.form.factura_bodega=self.bodegas[0];
                        self.productos = [];
                        self.form.t_cambio = Number(data.t_cambio.tasa);
                        self.form.tasa_paralela = Number(data.t_cambio.tasa_paralela);
                        self.zonas = data.zonas;
                        self.vendedores = data.vendedores;
                        self.formCliente.zona = data.zonas[0];
                        self.departamentos = data.departamentos
                        self.formCliente.departamento = self.departamentos[9]
                        self.municipios = self.formCliente.departamento.municipios_departamento
                        self.formCliente.municipio = self.municipios[5];
                        self.loading = false;
                        /*self.form.factura_bodega.productos_bodega.forEach((bodega_producto, key) => {
                          self.productos.push({
                            codigo_sistema: bodega_producto.producto_venta.codigo_sistema,
                            costo_promedio:  Number(bodega_producto.producto_venta.costo_promedio),
                            precio:Number(bodega_producto.producto_venta.precio_sugerido),
                            descripcion:  bodega_producto.producto_venta.descripcion,
                            id_producto:  bodega_producto.producto_venta.id_producto,
                            tasa_impuesto: Number(bodega_producto.producto_venta.tasa_impuesto),
                            id_bodega_producto:  self.form.factura_bodega.productos_bodega[key].id_bodega_producto,
                            nombre_comercial:  bodega_producto.producto_venta.nombre_comercial,
                            cantidad_disponible: Number(self.form.factura_bodega.productos_bodega[key].cantidad),
                            unidad_medida: bodega_producto.producto_venta.unidad_medida,
                            nombre_full: bodega_producto.producto_venta.descripcion+' - '+bodega_producto.producto_venta.codigo_barra,
                          });
                        });*/


                    },
                    err => {
                        console.log(err);
                        self.loading = false;
                    })

            },

            agregarDetalle() {
                var self = this;
                if (self.detalleForm.productox && self.detalleForm.afectacionx) {
                    if (self.detalleForm.cantidad > 0) {
                        let i = 0;
                        let keyx = 0;
                        if (self.form.detalleProductos) {
                            self.form.detalleProductos.forEach((productox, key) => {
                                if ((self.detalleForm.productox.id_producto === productox.productox.id_producto)
                                    && (self.detalleForm.afectacionx.id_afectacion === productox.afectacionx.id_afectacion)) {
                                    i++;
                                    keyx = key;
                                }
                            });
                        }
                        if (i === 0) {
                            self.form.detalleProductos.push({
                                productox: self.detalleForm.productox,
                                afectacionx: self.detalleForm.afectacionx,
                                cantidad: Number(self.detalleForm.cantidad),
                                precio_costo: Number(self.detalleForm.productox.costo_promedio),
                                //precio_lista:  Number((self.detalleForm.productox.precio_sugerido*self.form.t_cambio).toFixed(2)),
                                //precio:  Number((self.detalleForm.productox.precio_sugerido*self.form.t_cambio).toFixed(2)),
                                precio_lista: Number(self.detalleForm.precio_sugerido),
                                precio_lista_me: Number(self.detalleForm.precio_sugerido_me),
                                precio: Number(self.detalleForm.precio_sugerido),
                                p_descuento: 0,
                                mt_descuento: 0,
                                p_impuesto: Number(self.detalleForm.productox.tasa_impuesto),
                                mt_impuesto: 0,
                                subtotal: 0,
                                total: 0,
                                total_sin_iva: 0,
                                mt_ajuste: 0,
                                p_ajuste: 0,
                            });
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Artículo agregado. ',
                                        variant: 'success',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                        } else {
                            let nuevo_total = self.form.detalleProductos[keyx].cantidad + self.detalleForm.cantidad;
                            if (nuevo_total <= self.form.detalleProductos[keyx].productox.cantidad_disponible) {
                                self.form.detalleProductos[keyx].cantidad = nuevo_total;
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'Artículo agregado. ',
                                            variant: 'success',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                            } else {
                                self.form.detalleProductos[keyx].cantidad = Number(self.form.detalleProductos[keyx].productox.cantidad_disponible);
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'Se ha agregado la cantidad máxima disponible para este producto. ',
                                            variant: 'warning',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                            }
                        }

                        self.detalleForm.productox = null;
                        self.detalleForm.afectacionx = self.afectaciones[0];
                        self.detalleForm.cantidad = 0;
                        self.detalleForm.precio_sugerido = 0;
                        self.detalleForm.subtotal = 0;
                        self.detalleForm.total = 0;
                        self.detalleForm.total_sin_iva = 0;
                        this.$refs.producto.$refs.search.focus();


                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Los valores para cantidad y precio unitario deben ser mayor a cero. ',
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
                                text: 'Debe seleccionar un producto. ',
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },
            eliminarLinea(item, index) {
                if (this.form.detalleProductos.length >= 1) {
                    this.form.detalleProductos.splice(index, 1);

                } else {
                    this.form.detalleProductos = [];
                }
            },

            pagoCompleto(IdMoneda) {
                var self = this;

                if (Number(self.form.total_final_cordobas.toFixed(2)) > 0) {
                    self.form.detallePago = [];
                    /*
                          let monto =0,monto_me=0;
                          if(IdMoneda === 1){
                            monto=Number(self.form.total_final_cordobas.toFixed(2));
                            monto_me=Number((self.form.total_final_cordobas/self.form.t_cambio).toFixed(2));
                          }else{
                            monto=Number(self.form.total_final_cordobas.toFixed(2));
                            monto_me=Number((self.form.total_final_cordobas/self.form.t_cambio).toFixed(2));
                          }*/

                    self.form.detallePago.push({
                        via_pagox: self.vias_pago[1],
                        moneda_x: self.monedas[Number(IdMoneda)],
                        monto: Number(self.form.total_final_cordobas.toFixed(2)),
                        monto_me: Number((self.form.total_final_cordobas / self.form.t_cambio).toFixed(2)),
                        banco_x: null,
                        numero_minuta: '',
                        numero_nota_credito: '',
                        numero_cheque: '',
                        numero_transferencia: '',
                        numero_recibo_pago: '',

                    });

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
                }

            },


            agregarMetodoPago() {
                var self = this;
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
                            monto_me = Number((self.detalleFormPago.monto / self.form.t_cambio).toFixed(2));

                        } else if (self.detalleFormPago.moneda_x.id_moneda === 2) {
                            monto_me = Number(self.detalleFormPago.monto);
                            monto_mn = Number((self.detalleFormPago.monto * self.form.t_cambio).toFixed(2));
                        }

                        if (i === 0) {
                            self.form.detallePago.push({
                                via_pagox: self.detalleFormPago.via_pagox,
                                moneda_x: self.detalleFormPago.moneda_x,
                                monto: Number(monto_mn.toFixed(2)),
                                monto_me: Number(monto_me.toFixed(2)),
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
                                        icon: 'InfoIcon',
                                        text: 'Metodo de pago agregado. ',
                                        variant: 'success',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                        } else {

                            if (self.detalleFormPago.moneda_x.id_moneda === 1) {
                                monto_mn = Number(self.detalleFormPago.monto);
                                monto_me = roundNumber.round(Number((self.detalleFormPago.monto / self.form.t_cambio)), 2);

                                self.form.detallePago[keyx].monto = Number((self.form.detallePago[keyx].monto + self.detalleFormPago.monto).toFixed(2));
                                self.form.detallePago[keyx].monto_me = Number((self.form.detallePago[keyx].monto / self.form.t_cambio).toFixed(2));

                            } else if (self.detalleFormPago.moneda_x.id_moneda === 2) {
                                self.form.detallePago[keyx].monto_me = Number((self.form.detallePago[keyx].monto_me + self.detalleFormPago.monto).toFixed(2));
                                self.form.detallePago[keyx].monto = Number((self.form.detallePago[keyx].monto_me * self.form.t_cambio).toFixed(2));
                            }

                            //let nuevo_monto_total = self.form.detallePago[keyx].monto + self.detalleFormPago.monto;

                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Pago agregado. ',
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
                                    icon: 'InfoIcon',
                                    text: 'El monto debe ser mayor a cero. ',
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
                                text: 'Debe seleccionar un metodo y un monto. ',
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

            procesar_factura() {
                var self = this;
                self.$swal.fire({
                    title: 'Esta seguro de completar la cotización?',
                    text: "Detalles de la cotización: ",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        proforma.registrar(
                            self.form,
                            data => {
                                this.$swal.fire(
                                    'Proforma Registrada!',
                                    'La cotización fue registrada correctamente',
                                    'success'
                                ).then((result2) => {
                                    if (result2.value) {
                                        this.$router.push({
                                            name: "cajabnanco-proformas"
                                        });
                                    }
                                })
                            },
                            err => {
                                self.errorMessages = err;
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'Revise los datos faltantes. ',
                                            variant: 'warning',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                                self.btnAction = "Actualizar Cotización";

                            }
                        );
                    } else {
                        self.btnAction = "Actualizar Cotización";

                    }
                })
            },

            registrar() {
                var self = this;
                self.btnAction = "Registrando, espere....";

                /*facturas de contado*/
                if (self.form.id_tipo === 1) {

                    if (self.form.pago_vuelto_mn >= 0 /*&& self.form.total_final_cordobas > 0*/ && self.form.pago_pendiente_mn === 0) {
                        this.procesar_factura();
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Las facturas de contado deben ser pagadas en su totalidad. ',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        //self.errorMessages.serie = Array('Error serie');
                        self.btnAction = "Actualizar Cotización";

                    }

                }


                /*facturas de credito*/
                if (self.form.id_tipo === 2) {

                    if (self.form.pago_vuelto_mn === 0 && self.form.total_final_cordobas > 0 && self.form.pago_pendiente_mn > 1) {
                        self.procesar_factura();
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Las facturas de crédito deben de tener saldo a pagar. ',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        //self.errorMessages.serie = Array('Error serie');
                        self.btnAction = "Actualizar Cotización";

                    }

                }


            },
            /*
			* Auth: omorales
			* Method: anular cotización - Cambiar a estado 6 solicitando justificación
			* date: 08/0/2021
			* */
            anular(estado) {
                var txtAprobar = 'Justificación:';

                var self = this;

                self.$swal.fire({
                    title: '¿Está seguro de anular esa cotización?',
                    text: txtAprobar,
                    type: 'warning',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    self.loading = true;
                    if (result.value) {
                        //var self = this
                        proforma.anular({
                            id_proforma: self.form.id_proforma,
                            estado: estado,
                            observacion: result.value
                        }, data => {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Se ha anulado la proforma correctamente. ',
                                        variant: 'warning',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            this.$router.push({
                                name: 'cajabanco-proformas'
                            })
                        }, err => {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Ha ocurrido un error, revise los datos faltantes. ' + err,
                                        variant: 'warning',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            self.loading = false;
                            console.log(err)
                        })
                    } else {
                        self.loading = false;
                    }
                })
            },

            registrarCliente() {
                var self = this
                cliente.registrarBasico(self.formCliente, data => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Cliente registrado correctamente. ',
                                variant: 'success',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    //console.log(data);
                    self.form.factura_cliente = data;
                    self.form.tipo_identificacion = self.form.factura_cliente.tipo_persona;
                    if (self.form.factura_cliente.tipo_persona === 1) {
                        self.form.identificacion = self.form.factura_cliente.numero_cedula;
                    } else {
                        self.form.identificacion = self.form.factura_cliente.numero_ruc;
                    }

                    self.$refs['modal'].hide();

                }, err => {
                    self.errorMessages = err
                })
            },

            seleccionarTipo() {
                var self = this;
                if (self.form.id_tipo === 1) {
                    self.form.dias_credito = 0;
                } else {
                    self.form.aplicaIR = false;
                    self.form.aplicaIMI = false;

                    self.form.mt_retencion = 0;
                    self.form.mt_retencion_imi = 0;

                    self.form.doc_exoneracion_ir = '';
                    self.form.doc_exoneracion_imi = '';

                    self.form.dias_credito = 0;
                    self.form.dias_credito = self.plazo_maximo_credito;
                }
                self.obtenerTCParalela();
                /*calcular fecha*/
            }

        },
        mounted() {
            // this.obtenerAfectacionesTodas();
            // this.obtenerTodasBodegasProductos();
            this.nueva();
            this.obtenerProforma();
        }
    };
</script>
<style lang="scss">
    @import "src/@core/scss/vue/libs/vue-select";
    .btn-agregar {
        margin-top: 1.6rem;
    }

    .check-label2 {
        margin-top: 30px;
    }

</style>



