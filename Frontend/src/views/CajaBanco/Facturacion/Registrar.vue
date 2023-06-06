<template>
    <b-card>
        <b-alert
                show
                variant="success"
        >
            <div class="alert-body">
                <span><strong>Datos del Cliente</strong></span>
            </div>
        </b-alert>

        <template>
            <b-row>

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
                                    v-model="form.factura_cliente"
                            >
                                <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select-->
                                <template slot="no-options">
                                    Escriba para buscar un cliente
                                </template>
                            </v-select>
                        </div>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_cliente"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Tipo Cliente</label>
                        <b-form-select v-model.number="form.tipo_identificacion">
                            <option value="1">
                                Natural
                            </option>
                            <option value="2">
                                Jurídico
                            </option>
                        </b-form-select>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <div class="form-group text-center">
                            <b-button
                                    @click="abrirModal"
                                    class="btn-agregar"
                                    v-b-tooltip.hover.top="'Registrar cliente nuevo'"
                                    variant="success"
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
                        <input class="form-control" placeholder="Número Identificación" v-model="form.identificacion">
                        <b-alert show variant="danger">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.identificacion" v-text="message"/>
                            </ul>
                        </b-alert>

                    </div>
                </div>
            </b-row>
        </template>

        <b-alert
                class="mb-2 mt-2"
                show
                variant="success"
        >
            <div class="alert-body">
                <span><strong>Datos de la Factura</strong></span>
            </div>
        </b-alert>

        <template v-if="proformaFormHeader.es_nuevo === true">
            <b-row>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label> Sucursal</label>
                        <div class="form-group">
                            <!-- <typeahead :initial="form.factura_sucursal" :trim="80" :url="sucursalesBusquedaURL" @input="seleccionarSucursal" style="width: 100%;"></typeahead>-->
                            <b-form-select
                                    @change="seleccionarSucursal"
                                    v-model="form.factura_sucursal"
                            >
                                <option
                                        :key="sucursal.id_sucursal"
                                        :value="sucursal"
                                        v-for="sucursal in sucursales"
                                >{{ sucursal.descripcion }}
                                </option>
                            </b-form-select>

                        </div>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_sucursal"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Serie</label>
                        <input
                                class="form-control"
                                disabled
                                placeholder="Serie"
                                v-model="form.serie"
                        >
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.serie"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Consecutivo</label>
                        <input
                                class="form-control"
                                disabled
                                placeholder="Consecutivo"
                                v-model="no_documento"
                        >
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.no_documento"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Bodega</label>
                        <v-select
                                :options="bodegas"
                                @input="seleccionarBodega()"
                                label="descripcion"
                                v-model="form.factura_bodega"
                        >
                            <template slot="no-options">
                                No se encontraron registros
                            </template>
                        </v-select>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_bodega"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Tipo Factura</label>
                        <b-form-select
                                @change="seleccionarTipo" v-model.number="form.id_tipo"
                        >
                            <option value="1">
                                Contado
                            </option>
                            <option
                                    :disabled="!clienteCredito"
                                    value="2"
                            >
                                Crédito
                            </option>
                        </b-form-select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label> Plazo Crédito</label>
                        <b-form-select
                                :disabled="!(form.id_tipo===2)"
                                @change="obtenerTCParalela"
                                v-model.number="form.dias_credito"
                        >
                            <option
                                    :disabled="(form.id_tipo===2)"
                                    value="0"
                            >
                                N/A
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=8)"
                                    value="8"
                            >
                                8 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=15)"
                                    value="15"
                            >
                                15 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=30)"
                                    value="30"
                            >
                                30 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=45)"
                                    value="45"
                            >
                                45 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=60)"
                                    value="60"
                            >
                                60 días
                            </option>
                        </b-form-select>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.plazo_credito"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label> Vendedor</label>
                        <!-- <div class="form-group">
                                       <typeahead :initial="form.factura_vendedor" :trim="80" :url="vendedoresBusquedaURL" @input="seleccionarVendedor" style="width: 100%;"></typeahead>

                                     </div>-->
                        <v-select
                                :disabled="true"
                                :options="vendedores"
                                label="nombre_completo"
                                v-model="form.factura_vendedor"
                        >
                            <template slot="no-options"/>
                        </v-select>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_vendedor"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Fecha Emitida</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                :disabled="false"
                                class="mb-0"
                                local="es"
                                placeholder="f.emision"
                                selected-variant="primary"
                                v-model="form.f_factura"
                        />
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.f_factura"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>Fecha Vencimiento</label>
                        <input
                                class="form-control"
                                disabled
                                type="text"
                                v-model="form.f_vencimiento"
                        >
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for>T/C</label>
                        <input
                                class="form-control"
                                disabled
                                type="text"
                                v-model="form.t_cambio"
                        >
                    </div>
                </div>


                <div class="col-sm-4 ">
                    <label for=""></label>
                    <b-form-checkbox
                            @input="deseleccionar"
                            class="mx-lg-1 mb-sm-1 mt-sm-1"
                            v-model="form.proforma_especifica"
                    >
                        Tiene proforma
                    </b-form-checkbox>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label> Proforma</label>
                        <div class="form-group">
                            <v-select
                                    :filterable="false"
                                    :options="proformas"
                                    @input="seleccionarProforma"
                                    @search="onSearchP"
                                    label="text"
                                    style="width: 100%;"
                                    v-if="form.proforma_especifica"
                                    v-model="form.proformasBusqueda"
                            >
                                <!--v-on:input="$emit('input', $event.target.value)" Emitir evento a v-model de vue-select-->
                                <template slot="no-options">
                                    Escriba para buscar una proforma
                                </template>
                            </v-select>

                            <input
                                    class="form-control"
                                    disabled
                                    v-if="!form.proforma_especifica"
                            >
                        </div>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.proformasBusqueda"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <!--<div class="col-sm-3"> Cargar productos desde una proforma, manualmente
                    <label for=""></label>
                    <div class="form-group">
                        <div class="form-group">
                            <b-button
                                    variant="success"
                                    @click="cargarProductosProforma"
                            >
                                Cargar Productos
                            </b-button>
                        </div>
                    </div>
                </div>-->

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for>Observaciones</label>
                        <input
                                class="form-control"
                                type="text"
                                v-model="form.observacion"
                        >
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.observacion"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

            </b-row>
        </template>
        <template v-else>
            <b-row>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label> Sucursal</label>
                        <div class="form-group">
                            <!-- <typeahead :initial="form.factura_sucursal" :trim="80" :url="sucursalesBusquedaURL" @input="seleccionarSucursal" style="width: 100%;"></typeahead>-->
                            <v-select
                                    :disabled="true"
                                    :options="sucursales"
                                    label="descripcion"
                                    v-model="form.proforma_sucursal"
                            >
                                <template slot="no-options">
                                    No se encontraron registros
                                </template>
                            </v-select>

                        </div>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_sucursal"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Serie</label>
                        <input
                                class="form-control"
                                disabled
                                placeholder="Serie"
                                v-model="form.proforma_sucursal.serie"
                        >
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.serie"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Consecutivo</label>
                        <input
                                class="form-control"
                                disabled
                                placeholder="Consecutivo"
                                v-model="no_documento"
                        >
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.no_documento"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Bodega</label>
                        <v-select
                                :disabled="true"
                                :options="bodegas"
                                @input="seleccionarBodega()"
                                label="descripcion"
                                v-model="form.proforma_bodega"
                        >
                            <template slot="no-options">
                                No se encontraron registros
                            </template>
                        </v-select>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_bodega"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Tipo Factura</label>
                        <b-form-select
                                @change="seleccionarTipo"
                                v-model.number="proformaFormHeader.id_tipo"
                        >
                            <option value="1">
                                Contado
                            </option>
                            <option
                                    :disabled="!clienteCredito"
                                    value="2"
                            >
                                Crédito
                            </option>
                        </b-form-select>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label> Plazo Crédito</label>
                        <b-form-select
                                :disabled="!(form.id_tipo===2)"
                                @change="obtenerTCParalela"
                                v-model.number="form.dias_credito"
                        >
                            <option
                                    :disabled="(form.id_tipo===2)"
                                    value="0"
                            >
                                N/A
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=8)"
                                    value="8"
                            >
                                8 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=15)"
                                    value="15"
                            >
                                15 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=30)"
                                    value="30"
                            >
                                30 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=45)"
                                    value="45"
                            >
                                45 días
                            </option>
                            <option
                                    :disabled="!(plazo_maximo_credito>=60)"
                                    value="60"
                            >
                                60 días
                            </option>
                        </b-form-select>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.plazo_credito"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label> Vendedor</label>
                        <!-- <div class="form-group">
                                       <typeahead :initial="form.factura_vendedor" :trim="80" :url="vendedoresBusquedaURL" @input="seleccionarVendedor" style="width: 100%;"></typeahead>

                                     </div>-->
                        <v-select
                                :disabled="true"
                                :options="vendedores"
                                label="nombre_completo"
                                v-model="form.proforma_vendedor"
                        >
                            <template slot="no-options">
                                No se encontraron registros
                            </template>
                        </v-select>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.factura_vendedor"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Fecha Emitida</label>
                        <b-form-datepicker
                                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                                :disabled="true"
                                class="mb-0"
                                local="es"
                                placeholder="f. emitida"
                                selected-variant="primary"
                                v-model="form.f_factura"
                        />
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.f_factura"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for>Fecha Vencimiento</label>
                        <input
                                class="form-control"
                                disabled
                                type="text"
                                v-model="form.f_vencimiento"
                        >
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for>T/C</label>
                        <input
                                class="form-control"
                                disabled
                                type="text"
                                v-model="form.t_cambio"
                        >
                    </div>
                </div>

                <div class="col-sm-10">
                    <div class="form-group">
                        <label for>Observaciones</label>
                        <input
                                class="form-control"
                                type="text"
                                v-model="proformaFormHeader.observacion"
                        >
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.observacion"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <div class="col-sm-2">
                    <b-form-checkbox
                            @input="deseleccionar"
                            class="mx-lg-1 mb-sm-1 mt-sm-1"
                            v-model="form.proforma_especifica"
                    >
                        Tiene proforma
                    </b-form-checkbox>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label> Proforma</label>
                        <div class="form-group">
                            <v-select
                                    :trim="80"
                                    :url="proformasBusquedaURL"
                                    @input="seleccionarProforma"
                                    label="no_documento"
                                    style="width: 100%;"
                                    v-if="form.proforma_especifica"
                                    v-model="form.proformasBusqueda"
                            />
                            <input
                                    class="form-control"
                                    disabled
                                    v-if="!form.proforma_especifica"
                            >
                        </div>
                        <b-alert
                                show
                                variant="danger"
                        >
                            <ul class="error-messages">
                                <li
                                        :key="message"
                                        v-for="message in errorMessages.proformasBusqueda"
                                        v-text="message"
                                />
                            </ul>
                        </b-alert>

                    </div>
                </div>
                <!--<div class="col-sm-1"> cargar productos desde proforma manualmente
                    <div class="form-group">
                        <div class="form-group">
                            <b-button
                                    variant="success"
                                    @click="cargarProductosProforma"
                            >Cargar
                                Productos
                            </b-button>
                        </div>
                    </div>
                </div>-->

            </b-row>
        </template>

        <div v-if="!form.factura_bodega">
            <b-alert
                    class="mb-2 mt-2"
                    show
                    variant="success"
            >
                <div class="alert-body">
                    <span>Se requiere que seleccione una bodega para continuar.</span>
                </div>
            </b-alert>
        </div>

        <b-alert
                class="mb-2 mt-2"
                show
                variant="success"
        >
            <div class="alert-body">
                <span><strong>Detalle de factura</strong></span>
            </div>
        </b-alert>

        <b-row>
            <div class="col-sm-4">
                <div class="form-group">
                    <label> Producto</label>
                    <div class="form-group">
                        <v-select
                                :options="productos"
                                :searchable="true"
                                label="text"
                                placeholder="Selecciona un producto"
                                ref="producto"
                                track-by="id_producto"
                                v-model="detalleForm.productox"
                                v-on:input="cargar_detalles_producto()"
                        >
                            <template slot="no-options">
                                No se encontraron registros
                            </template>
                        </v-select>
                    </div>
                    <b-alert
                            show
                            variant="danger"
                    >
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.productox"
                                    v-text="message"
                            />
                        </ul>
                    </b-alert>

                </div>
            </div>

            <div class="col-sm-2">
                <div class="form-group">
                    <label for>Cantidad</label>
                    <input
                            @change="detalleForm.cantidad = Math.max(Math.min(Math.round(detalleForm.cantidad), (!isNaN(detalleForm.productox.cantidad_disponible))?detalleForm.productox.cantidad_disponible:0 ), 1)"
                            @keydown.enter="cambiarFocoCantidad"
                            class="form-control"
                            min="0"
                            ref="cantidad"
                            type="number"
                            v-model.number="detalleForm.cantidad"
                    >
                    <b-alert
                            show
                            variant="danger"
                    >
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.cantidadx"
                                    v-text="message"
                            />
                        </ul>
                    </b-alert>

                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for>Precio Lista C$ </label>
                    <input class="form-control" readonly v-model.number="detalleForm.precio_sugerido">
                    <b-alert show variant="danger">
                        <ul class="error-messages">
                            <li
                                    :key="message"
                                    v-for="message in errorMessages.preciox"
                                    v-text="message"
                            />
                        </ul>
                    </b-alert>

                </div>
            </div>

            <!--<div class="col-sm-2">
                <div class="form-group">
                    <label for>Tipo de Afectación</label>
                    <v-select
                            v-model="detalleForm.afectacionx"
                            :disabled="(!detalleForm.productox)?true:detalleForm.productox.tipo_producto===2"
                            label="descripcion"
                            :options="afectaciones"
                    >
                        <template slot="no-options">
                            No se encontraron registros
                        </template>
                    </v-select>
                </div>
            </div>-->

            <div class="col-sm-2">
                <div class="form-group">
                    <label for>&nbsp;</label>
                    <b-button
                            @click="agregarDetalle"
                            class="btn-agregar"
                            ref="agregar"
                            v-b-tooltip.hover.top="'Agregar producto al detalle'"
                            variant="info"
                    >
                        <feather-icon icon="PlusCircleIcon"></feather-icon>
                        Agregar
                    </b-button>
                </div>
            </div>

        </b-row>

        <b-row>
            <div class="col-sm-12">
                <b-alert show variant="danger">
                    <ul class="error-messages">
                        <li
                                :key="message"
                                v-for="message in errorMessages.detalleProductos"
                                v-text="message"
                        />
                    </ul>
                </b-alert>

                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th class="table-number"></th>
                        <th>Producto</th>
                        <!--                        <th>Afectación</th>-->
                        <!--                        <th>% Ajuste</th>-->
                        <th class="table-number">Cantidad</th>
                        <!-- <th >U/M</th>-->
                        <!--                        <th>P. Lista C$</th>-->
                        <th class="table-number">Descuento %</th>
                        <th>Descuento C$</th>
                        <!--                        <th>Ajuste C$</th>-->
                        <th>Precio Unit. C$</th>
                        <!--<th >Monto IVA U$</th>-->
                        <th>Valor C$</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="(item, index) in form.detalleProductos">
                        <tr>
                            <td style="width:1%">
                                <b-button
                                        @click="eliminarLinea(item, index)"
                                        variant="danger"
                                >
                                    <feather-icon icon="TrashIcon"/>
                                </b-button>
                            </td>
                            <td>
                                <input
                                        class="form-control"
                                        disabled
                                        v-model="item.productox.descripcion"
                                >
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`detalleProductos.${index}.productox.id_producto`]"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </td>
                            <!--<td style="width: 12%">
                                <input
                                        v-model="item.afectacionx.descripcion"
                                        class="form-control"
                                        disabled
                                >

                                &lt;!&ndash;<v-select
                                                      :disabled="true"
                                                      v-model="item.afectacionx"
                                                      label="descripcion"
                                                      :options="afectaciones"
                                                      v-on:input="calcularAjuste(item)"
                                              ></v-select>&ndash;&gt;
                                <b-alert
                                        variant="danger"
                                        show
                                >
                                    <ul class="error-messages">
                                        <li
                                                v-for="message in errorMessages[`detalleProductos.${index}.afectacionx.id_afectacion`]"
                                                :key="message"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </td>-->

                            <!--<td style="width: 5%">
                                {{ item.p_ajuste +'%' }}
                            </td>-->

                            <td style="width:1%">
                                <input @change="item.cantidad = Math.max(Math.min(Math.round(item.cantidad), item.productox.cantidad_disponible), 1)"
                                       class="form-control" min="1" type="number"
                                       v-model.number="item.cantidad">
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`detalleProductos.${index}.cantidad`]"
                                                v-text="message"
                                        />
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
                                <input
                                        v-model.number="item.precio"
                                        class="form-control"
                                        disabled
                                >
                                <b-alert
                                        variant="danger"
                                        show
                                >
                                    <ul class="error-messages">
                                        <li
                                                v-for="message in errorMessages[`detalleProductos.${index}.precio`]"
                                                :key="message"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </td>-->

                            <td style="width:1%">
                                <input
                                        :disabled="item.p_ajuste>0"
                                        @change="item.p_descuento = Math.max(Math.min(item.p_descuento, 50), 0)"
                                        class="form-control"
                                        v-model.number="item.p_descuento"
                                >
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages[`detalleProductos.${index}.p_descuento`]"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </td>

                            <td style="width:12%">
                                <strong>{{ calcular_montos(item)| formatMoney(2) }}</strong>
                            </td>

                            <!--<td style="width: 8%"><strong>C$ {{ item.mt_ajuste| formatMoney(2) }}</strong>
                            </td>-->

                            <td style="width:1%">
                                <strong>{{ item.p_unitario| formatMoney(2) }}</strong>
                            </td>

                            <td style="width:12%">
                                <strong>{{ item.total_sin_iva| formatMoney(2) }}</strong>
                            </td>

                        </tr>
                        <tr/>
                    </template>
                    </tbody>
                    <tfoot>

                    <tr>
                        <td colspan="5">
                            <b-alert
                                    show
                                    variant="danger"
                            >
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.error_general"
                                            v-text="message"
                                    />
                                </ul>
                            </b-alert>

                        </td>
                        <td>Subtotal</td>
                        <td><strong>{{ total_subt_sin_iva | formatMoney(2) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"/>
                        <td>No Doc. Exoneración</td>
                        <td><input
                                :disabled="form.aplicaIVA"
                                class="form-control"
                                v-model="form.doc_exoneracion"
                        ></td>
                        <td>
                            <b-form-checkbox
                                    class="mx-lg-1 mb-sm-1 mt-sm-1"
                                    v-model="form.aplicaIVA"
                            >
                                IVA
                            </b-form-checkbox>
                            <b-alert
                                    show
                                    variant="danger"
                            >
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.doc_exoneracion"
                                            v-text="message"
                                    />
                                </ul>
                            </b-alert>

                        </td>
                        <td><strong>{{ total_impuesto | formatMoney(2) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"/>
                        <td>No. Documento:</td>
                        <td>
                            <input
                                    :disabled="!form.aplicaIR"
                                    class="form-control"
                                    v-model="form.doc_exoneracion_ir"
                            >
                        </td>
                        <td>
                            <b-form-checkbox
                                    :disabled="!(form.id_tipo===1)"
                                    class="mx-lg-1 mb-sm-1 mt-sm-1"
                                    v-model="form.aplicaIR"
                            >
                                Retención
                            </b-form-checkbox>
                            <b-alert
                                    show
                                    variant="danger"
                            >
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.doc_exoneracion_ir"
                                            v-text="message"
                                    />
                                </ul>
                            </b-alert>

                        </td>
                        <td><strong>C$ {{ total_retencion | formatMoney(2) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"/>
                        <td>No. Documento:</td>
                        <td>
                            <input
                                    :disabled="!form.aplicaIMI"
                                    class="form-control"
                                    v-model="form.doc_exoneracion_imi"
                            >
                        </td>
                        <td>
                            <b-form-checkbox
                                    :disabled="!(form.id_tipo===1)"
                                    class="mx-lg-1 mb-sm-1 mt-sm-1"
                                    v-model="form.aplicaIMI"
                            >
                                Retención IMI
                            </b-form-checkbox>
                            <b-alert
                                    show
                                    variant="danger"
                            >
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.doc_exoneracion_imi"
                                            v-text="message"
                                    />
                                </ul>
                            </b-alert>

                        </td>
                        <td><strong>C$ {{ total_retencion_imi | formatMoney(2) }}</strong></td>
                    </tr>

                    <tr class="table-active table-light">
                        <td colspan="0"/>
                        <td class="item-footer">
                            <strong>Totales</strong>
                        </td>
                        <td class="item-footer">
                            <strong>{{ total_cantidad }}</strong>
                        </td>
                        <td colspan="0">
                            <!--                            Total Descuento | Ajuste-->
                        </td>
                        <td><strong>{{ total_descuento | formatMoney(2) }}</strong></td>
                        <td><strong><!--C$ {{ total_ajuste | formatMoney(2) }}--></strong></td>
                        <!--<td>Total</td>-->
                        <td><strong>{{ gran_total_cordobas | formatMoney(2) }}</strong></td>
                    </tr>

                    <tr>
                        <td colspan="5"/>
                        <td>
                            Equivalente Dólares
                        </td>
                        <td><strong>$ {{ gran_total | formatMoney(2) }}</strong></td>
                    </tr>

                    </tfoot>
                </table>
            </div>
        </b-row>

        <br>
        <b-row>
            <div class="col-sm-12">
                <b-row>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Método Pago</label>
                            <div class="form-group">
                                <v-select
                                        :options="vias_pago"
                                        :searchable="true"
                                        label="descripcion"
                                        placeholder="Selecciona un método pago"
                                        ref="via_pago"
                                        track-by="id_via_pago"
                                        v-model="detalleFormPago.via_pagox"
                                >
                                    <template slot="no-options">
                                        No se encontraron registros
                                    </template>
                                </v-select>
                            </div>
                            <b-alert
                                    show
                                    variant="danger"
                            >
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.via_pagox"
                                            v-text="message"
                                    />
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Moneda</label>
                            <div class="form-group">
                                <v-select
                                        :options="monedas"
                                        :searchable="true"
                                        label="descripcion"
                                        placeholder="Selecciona un método pago"
                                        ref="moneda"
                                        track-by="id_moneda"
                                        v-model="detalleFormPago.moneda_x"
                                >
                                    <template slot="no-options">
                                        No se encontraron registros.
                                    </template>
                                </v-select>
                            </div>
                            <b-alert
                                    show
                                    variant="danger"
                            >
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.moneda_x"
                                            v-text="message"
                                    />
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <template v-if="detalleFormPago.via_pagox">

                        <div
                                class="col-sm-6"
                                v-if="[1,3,5,6].indexOf(detalleFormPago.via_pagox.id_via_pago) >= 0"
                        >
                            <div class="form-group">
                                <label> Banco</label>
                                <div class="form-group">
                                    <v-select
                                            :options="bancos"
                                            :searchable="true"
                                            label="descripcion"
                                            placeholder="Selecciona un método pago"
                                            ref="banco"
                                            track-by="id_banco"
                                            v-model="detalleFormPago.banco_x"
                                    >
                                        <template slot="no-options">
                                            No se encontraron registros
                                        </template>
                                    </v-select>
                                </div>
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.moneda_x"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div
                                class="col-sm-6"
                                v-if="[1].indexOf(detalleFormPago.via_pagox.id_via_pago) >= 0"
                        >
                            <div class="form-group">
                                <label for>Número Minuta</label>
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_minuta"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div
                                class="col-sm-6"
                                v-if="detalleFormPago.via_pagox.id_via_pago === 3"
                        >
                            <div class="form-group">
                                <label for>Número Voucher</label>
                                <input
                                        class="form-control"
                                        v-model="detalleFormPago.numero_minuta"
                                >
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_minuta"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div
                                class="col-sm-6"
                                v-if="detalleFormPago.via_pagox.id_via_pago === 4"
                        >
                            <div class="form-group">
                                <label for>Número Nota Crédito</label>
                                <input
                                        class="form-control"
                                        v-model="detalleFormPago.numero_nota_credito"
                                >
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_nota_credito"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div
                                class="col-sm-6"
                                v-if="detalleFormPago.via_pagox.id_via_pago === 5"
                        >
                            <div class="form-group">
                                <label for>Número Cheque</label>
                                <input
                                        class="form-control"
                                        v-model="detalleFormPago.numero_cheque"
                                >
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_cheque"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div
                                class="col-sm-6"
                                v-if="detalleFormPago.via_pagox.id_via_pago === 6"
                        >
                            <div class="form-group">
                                <label for>Número Transferencia</label>
                                <input
                                        class="form-control"
                                        v-model="detalleFormPago.numero_transferencia"
                                >
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_transferencia"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                        <div
                                class="col-sm-6"
                                v-if="detalleFormPago.via_pagox.id_via_pago === 7"
                        >
                            <div class="form-group">
                                <label for>Número Recibo Pago Anticipado</label>
                                <input
                                        class="form-control"
                                        v-model="detalleFormPago.numero_recibo_pago"
                                >
                                <b-alert
                                        show
                                        variant="danger"
                                >
                                    <ul class="error-messages">
                                        <li
                                                :key="message"
                                                v-for="message in errorMessages.numero_recibo_pago"
                                                v-text="message"
                                        />
                                    </ul>
                                </b-alert>

                            </div>
                        </div>

                    </template>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for>Monto {{ detalleFormPago.moneda_x ? detalleFormPago.moneda_x.codigo : ''
                                }}</label>
                            <input
                                    class="form-control"
                                    v-model.number="detalleFormPago.monto"
                            >
                            <b-alert
                                    show
                                    variant="danger"
                            >
                                <ul class="error-messages">
                                    <li
                                            :key="message"
                                            v-for="message in errorMessages.monto"
                                            v-text="message"
                                    />
                                </ul>
                            </b-alert>

                        </div>
                    </div>

                    <div class="col-sm-3">
                        <label for=""></label>
                        <div class="form-group">
                            <label for>&nbsp;</label>
                            <b-button
                                    @click="agregarMetodoPago"
                                    ref="agregarpago"
                                    variant="info"
                            >Agregar Método
                            </b-button>
                        </div>
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

                    <div class="col-sm-8 mt-2 table-responsive">
                        <table class="table ">
                            <thead>
                            <tr>
                                <th/>
                                <th>Tipo</th>
                                <th>Moneda</th>
                                <th>Monto</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="(item, index) in form.detallePago">
                                <tr>
                                    <td style="width: 25%">
                                        <b-button
                                                @click="eliminarLineaPago(item, index)"
                                                variant="danger"
                                        >
                                            <feather-icon icon="TrashIcon"/>
                                        </b-button>
                                    </td>
                                    <td style="width: 25%">
                                        <input
                                                class="form-control"
                                                disabled
                                                v-model="item.via_pagox.descripcion"
                                        >
                                        <b-alert
                                                show
                                                variant="danger"
                                        >
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detallePagos.${index}.via_pagox.id_via_pago`]"
                                                        v-text="message"
                                                />
                                            </ul>
                                        </b-alert>

                                    </td>

                                    <td style="width: 25%">
                                        <input
                                                class="form-control"
                                                disabled
                                                v-model="item.moneda_x.descripcion"
                                        >
                                        <b-alert
                                                show
                                                variant="danger"
                                        >
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detallePagos.${index}.moneda_x.id_moneda`]"
                                                        v-text="message"
                                                />
                                            </ul>
                                        </b-alert>

                                    </td>

                                    <td
                                            style="width: 25%"
                                            v-if="item.moneda_x.id_moneda === 1"
                                    >
                                        <input
                                                @change="calcularEquivalente(item)"
                                                class="form-control"
                                                step="0.01"
                                                type="number"
                                                v-model.number="item.monto"
                                        >
                                        <b-alert
                                                show
                                                variant="danger"
                                        >
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detalleProductos.${index}.monto`]"
                                                        v-text="message"
                                                />
                                            </ul>
                                        </b-alert>

                                    </td>

                                    <td
                                            style="width: 25%"
                                            v-if="item.moneda_x.id_moneda === 2"
                                    >
                                        <input
                                                @change="calcularEquivalente(item)"
                                                class="form-control"
                                                step="0.01"
                                                type="number"
                                                v-model.number="item.monto_me"
                                        >
                                        <b-alert
                                                show
                                                variant="danger"
                                        >
                                            <ul class="error-messages">
                                                <li
                                                        :key="message"
                                                        v-for="message in errorMessages[`detalleProductos.${index}.monto`]"
                                                        v-text="message"
                                                />
                                            </ul>
                                        </b-alert>

                                    </td>

                                </tr>
                                <tr/>
                            </template>
                            </tbody>
                            <tfoot/>
                        </table>
                    </div>

                    <div class="col-sm-4">
                        <b-row>
                            <div class="col-sm-12">
                                <b-alert class="mb-2 mt-2" show variant="success">
                                    <div class="alert-body">
                                        <span><strong>Resumen</strong></span>
                                    </div>
                                </b-alert>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Deuda</label>

                                    <p><strong>C$ {{ total_deuda | formatMoney(2) }}</strong></p>
                                    <b-alert
                                            show
                                            variant="danger"
                                    >
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.mt_deuda"
                                                    v-text="message"
                                            />
                                        </ul>
                                    </b-alert>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Diferencia</label>
                                    <p><strong>C$ {{ total_vuelto | formatMoney(2) }}</strong></p>
                                    <b-alert
                                            show
                                            variant="danger"
                                    >
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.pago_vuelto"
                                                    v-text="message"
                                            />
                                        </ul>
                                    </b-alert>

                                </div>
                            </div>
                        </b-row>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Diferencia Dólares</label>

                                    <p><strong>$ {{ form.pago_pendiente | formatMoney(2) }}</strong></p>
                                    <b-alert
                                            show
                                            variant="danger"
                                    >
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.pago_pendiente"
                                                    v-text="message"
                                            />
                                        </ul>
                                    </b-alert>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Vuelto Dólares</label>
                                    <p><strong>$ {{ form.pago_vuelto | formatMoney(2) }}</strong></p>
                                    <b-alert
                                            show
                                            variant="danger"
                                    >
                                        <ul class="error-messages">
                                            <li
                                                    :key="message"
                                                    v-for="message in errorMessages.pago_vuelto"
                                                    v-text="message"
                                            />
                                        </ul>
                                    </b-alert>

                                </div>
                            </div>
                        </div>

                    </div>

                </b-row>
            </div>


        </b-row>

        <b-card-footer class="text-lg-right text-center">
            <router-link :to="{ name: 'cajabanco-facturas' }">
                <b-button
                        class="mx-lg-1 mx-0"
                        type="button"
                        variant="secondary"
                >
                    Cancelar
                </b-button>
            </router-link>
            <b-button
                    :disabled="btnAction !== 'Facturar'"
                    @click="registrar"
                    class="mx-lg-1 mx-0"
                    variant="success"
            >{{ btnAction }}
            </b-button>
        </b-card-footer>

        <template v-if="loading">
            <BlockUI :url="url"/>
        </template>


        <!-- Modal - registro basico cliente -->

        <div>
            <b-modal hide-footer id="modal-select2" ref="modal" size="lg" title="Registrar cliente">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Tipo Persona</label>
                            <b-form-select v-model.number="formCliente.tipo_persona">
                                <option value="1">Natural</option>
                                <option value="2">Jurídico</option>
                            </b-form-select>
                        </div>
                    </div>

                    <template v-if="formCliente.tipo_persona === 1">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Número Cédula</label>
                                <input class="form-control" placeholder="Número Cédula" v-mask="'#############A'"
                                       v-model="formCliente.numero_cedula">
                                <b-alert show variant="danger">
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
                                <input class="form-control" placeholder="Número RUC" v-mask="'A#############'"
                                       v-model="formCliente.numero_ruc">
                                <b-alert show variant="danger">
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
                            <b-alert show variant="danger">
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
                            <b-alert show variant="danger">
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
                            <b-alert show variant="danger">
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
                            <b-alert show variant="danger">
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
                                    placeholder="Seleccione un departamento"
                                    style="background: white"
                                    v-model="formCliente.departamento"
                                    v-on:input="obtenerMunicipios()"
                            >
                                <template slot="no-options">No se encontraron registros</template>
                            </v-select>
                            <b-alert show variant="danger">
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.departamento"
                                        v-text="message"></li>
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
                                    placeholder="Seleccione un municipio"
                                    style="background: white"
                                    v-model="formCliente.municipio"
                            >
                                <template slot="no-options">No se encontraron registros</template>
                            </v-select>
                            <b-alert show variant="danger">
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
                                    placeholder="Seleccione un vendedor"
                                    style="background: white"
                                    v-model="formCliente.vendedor"
                            >
                                <template slot="no-options">No se encontraron registros</template>
                            </v-select>
                            <b-alert show variant="danger">
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
                            <b-alert show variant="danger">
                                <ul class="error-messages">
                                    <li :key="message" v-for="message in errorMessages.observaciones"
                                        v-text="message"></li>
                                </ul>
                            </b-alert>

                        </div>
                    </div>
                </div>

                <div class="content-box-footer text-right">
                    <b-button @click="cerrarModal" class="mx-lg-1 mx-0" variant="secondary">Cancelar</b-button>
                    <b-button :disabled="btnActionCliente !== 'Registrar Cliente'"
                              @click="registrarCliente" class="mx-lg-1 mx-0"
                              type="button"
                              variant="primary">{{ btnActionCliente }}
                    </b-button>
                </div>
            </b-modal>
        </div>


        <!-- Fin Modal - registro basico cliente -->


    </b-card>


    <!--DIALOG -->
    <!--    <b-modal ref="modal" modal-theme="dark" overlay-theme="dark" title="Registrar Cliente">
              <div class="row">

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label>Tipo Persona</label>
                          <select class="form-control" v-model.number="formCliente.tipo_persona">
                              <option value="1">Natural</option>
                              <option value="2">Jurídico</option>
                          </select>
                      </div>
                  </div>

                  <template v-if="formCliente.tipo_persona === 1">
                      <div class="col-sm-4">
                          <div class="form-group">
                              <label> Número Cédula</label>
                              <input class="form-control" v-mask="'#############A'" placeholder="Número Cédula"
                                     v-model="formCliente.numero_cedula">
                              <ul class="error-messages">
                                  <li :key="message" v-for="message in errorMessages.numero_cedula" v-text="message"></li>
                              </ul>
                          </div>
                      </div>
                  </template>
                  <template v-else>
                      <div class="col-sm-4">
                          <div class="form-group">
                              <label> Número RUC</label>
                              <input class="form-control" v-mask="'A#############'" placeholder="Número RUC"
                                     v-model="formCliente.numero_ruc">
                              <ul class="error-messages">
                                  <li :key="message" v-for="message in errorMessages.numero_ruc" v-text="message"></li>
                              </ul>
                          </div>
                      </div>
                  </template>

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label> Nombre Completo</label>
                          <input class="form-control" placeholder="Nombre Completo"
                                 v-model="formCliente.nombre_comercial">
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.nombre_comercial" v-text="message"></li>
                          </ul>
                      </div>
                  </div>

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label> Contacto</label>
                          <input class="form-control" placeholder="Contacto" v-model="formCliente.contacto">
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.contacto" v-text="message"></li>
                          </ul>
                      </div>
                  </div>

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label> Dirección</label>
                          <input class="form-control" placeholder="Dirección" v-model="formCliente.direccion">
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.direccion" v-text="message"></li>
                          </ul>
                      </div>
                  </div>

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label> Teléfono</label>
                          <input class="form-control" placeholder="Teléfono" v-model="formCliente.telefono">
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.telefono" v-text="message"></li>
                          </ul>
                      </div>
                  </div>

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label>Departamento</label>
                          <v-select
                                  :options="departamentos"
                                  label="descripcion"
                                  v-model="formCliente.departamento"
                                  v-on:input="obtenerMunicipios()"
                                  style="background: white"
                          ></v-select>
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.departamento" v-text="message"></li>
                          </ul>
                      </div>
                  </div>

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label>Municipio</label>
                          <v-select
                                  :options="municipios"
                                  label="descripcion"
                                  v-model="formCliente.municipio"
                                  style="background: white"
                          ></v-select>
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.municipio" v-text="message"></li>
                          </ul>
                      </div>
                  </div>

      &lt;!&ndash;            <div class="col-sm-4">&ndash;&gt;
      &lt;!&ndash;                <div class="form-group">&ndash;&gt;
      &lt;!&ndash;                    <label>Zona</label>&ndash;&gt;
      &lt;!&ndash;                    <v-select&ndash;&gt;
      &lt;!&ndash;                            :options="zonas"&ndash;&gt;
      &lt;!&ndash;                            label="descripcion"&ndash;&gt;
      &lt;!&ndash;                            v-model="formCliente.zona"&ndash;&gt;
      &lt;!&ndash;                            style="background: white"&ndash;&gt;
      &lt;!&ndash;                    ></v-select>&ndash;&gt;
      &lt;!&ndash;                    <ul class="error-messages">&ndash;&gt;
      &lt;!&ndash;                        <li :key="message" v-for="message in errorMessages.zona" v-text="message"></li>&ndash;&gt;
      &lt;!&ndash;                    </ul>&ndash;&gt;
      &lt;!&ndash;                </div>&ndash;&gt;
      &lt;!&ndash;            </div>&ndash;&gt;

                  <div class="col-sm-4">
                      <div class="form-group">
                          <label>Vendedor</label>
                          <v-select
                                  :options="vendedores"
                                  label="nombre_completo"
                                  v-model="formCliente.vendedor"
                                  style="background: white"
                          ></v-select>
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.vendedor" v-text="message"></li>
                          </ul>
                      </div>
                  </div>

                  <div class="col-sm-10">
                      <div class="form-group">
                          <label> Observaciones</label>
                          <input class="form-control" placeholder="Observaciones" v-model="formCliente.observaciones">
                          <ul class="error-messages">
                              <li :key="message" v-for="message in errorMessages.observaciones" v-text="message"></li>
                          </ul>
                      </div>
                  </div>
              </div>

              <div class="content-box-footer text-right">
                  <button class="btn btn-default" @click="cerrarModal">Cancelar</button>
                  <button :disabled="btnActionCliente !== 'Registrar Cliente'"
                          @click="registrarCliente"
                          class="btn btn-primary"
                          type="button">{{ btnActionCliente }}
                  </button>
              </div>

          </b-modal>-->
</template>

<script type="text/ecmascript-6">
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import {
        BAlert,
        BButton,
        BCard,
        BCardFooter,
        BForm,
        BFormCheckbox,
        BFormCheckboxGroup,
        BFormDatepicker,
        BFormGroup,
        BFormInput,
        BFormSelect,
        BListGroup,
        BListGroupItem,
        BModal,
        BPaginationNav,
        BRow,
        VBModal,
        VBTooltip
    } from 'bootstrap-vue'
    import vSelect from 'vue-select'
    import Ripple from 'vue-ripple-directive'
    import axios from 'axios'
    import factura from '../../../api/CajaBanco/facturas'
    import bodega from '../../../api/Inventario/bodegas'
    import proforma from '../../../api/CajaBanco/proformas'
    import cliente from '../../../api/Ventas/clientes'
    import tc from '../../../api/contabilidad/tasas-cambio'
    import roundNumber from '../../../assets/custom-scripts/Round'
    import moment from '../../../../../Backend/resources/app/assets/plugins/moment/moment'
    import ToastificationContent from '../../../@core/components/toastification/ToastificationContent'
    import loadingImage from '../../../assets/images/loader/block50.gif'

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
                url: loadingImage,

                es,
                format: 'dd-MM-yyyy',

                proformasBusquedaURL: '/cajabanco/proformas/buscar',

                clientesBusquedaURL: '/ventas/clientes/buscar',
                vendedoresBusquedaURL: '/ventas/vendedores/buscar',

                sucursalesBusquedaURL: '/sucursales/buscar',
                // bodegasBusquedaURL: "/bodegas/buscar",
                // productosBodegaBusquedaURL: "/productos/buscar-bodega-venta",
                clienteCredito: false,
                plazo_maximo_credito: 0,

                bodegas: [],
                sucursales: [],
                productos: [],
                servicios: [],
                productosORG: [],
                clientes: [],
                proformas: [],

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

                // afectacionesBusquedaURL: "/ventas/afectaciones/buscar",
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
                tipo_cliente: 1, // tipo normal por defecto
                proformaForm: {
                    afectacion_producto: [],
                    bodega_producto: [],
                },
                proformaFormHeader: {es_nuevo: true},

                form: {
                    es_deudor: false,
                    es_nuevo: true,
                    proforma_cliente: [],
                    proforma_bodega: [],
                    proforma_sucursal: [],
                    proforma_vendedor: [],
                    proformasBusqueda: {},
                    proforma_especifica: false,
                    tipo_venta: 1,
                    no_documento: '',
                    f_factura: moment(new Date()).format('YYYY-MM-DD'),
                    f_vencimiento: moment(new Date()).format('YYYY-MM-DD'),
                    serie: '',
                    id_tipo: 1,
                    // factura_moneda: {},
                    factura_sucursal: {},
                    factura_bodega: '',
                    tipo_identificacion: 1,
                    identificacion: '',
                    factura_cliente: {},
                    id_tipo_cliente: 1,
                    dias_credito: 0,
                    nombre_razon: '',
                    factura_vendedor: '',
                    t_cambio: 0,
                    tasa_paralela: 0,
                    doc_exoneracion: '',
                    doc_exoneracion_ir: '',
                    doc_exoneracion_imi: '',
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

                    observacion: '',
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
                btnAction: 'Facturar',
                btnActionCliente: 'Registrar Cliente',
                registrandoCliente: false,

                errorMessages: [],
            }
        },
        computed: {
            total_subt_sin_iva() {
                this.form.mt_subtotal_sin_iva = Number((this.form.detalleProductos.reduce((carry, item) => (carry + Number(item.total_sin_iva)),
                    0)).toFixed(2))
                this.form.mt_subtotal = Number((this.form.detalleProductos.reduce((carry, item) => (carry + Number(item.subtotal)),
                    0)).toFixed(2))
                return this.form.mt_subtotal_sin_iva.toFixed(2)
            },
            total_impuesto() {
                if (this.form.aplicaIVA) {
                    this.form.mt_impuesto = Number((this.form.detalleProductos.reduce((carry, item) => (carry + Number(item.mt_impuesto.toFixed(2))), 0)).toFixed(2))
                } else {
                    this.form.mt_impuesto = 0
                }

                return this.form.mt_impuesto
            },

            total_retencion() {
                if (this.form.aplicaIR && (Number(this.form.mt_subtotal_sin_iva) >= 1000)) {
                    this.form.mt_retencion = Number((this.form.mt_subtotal_sin_iva * 0.03).toFixed(2))
                } else {
                    this.form.mt_retencion = 0
                }
                return this.form.mt_retencion
            },

            total_retencion_imi() {
                if (this.form.aplicaIMI) {
                    this.form.mt_retencion_imi = Number((this.form.mt_subtotal_sin_iva * 0.01).toFixed(2))
                } else {
                    this.form.mt_retencion_imi = 0
                }

                return this.form.mt_retencion_imi
            },

            total_ajuste() {
                this.form.mt_ajuste = this.form.detalleProductos.reduce((carry, item) => (carry + Number(item.mt_ajuste)), 0)
                return this.form.mt_ajuste
            },

            total_descuento() {
                this.form.mt_descuento = Number((this.form.detalleProductos.reduce((carry, item) => (carry + Number(item.mt_descuento.toFixed(2))), 0)).toFixed(2))
                return this.form.mt_descuento
            },

            total_cantidad() {
                this.form.total_unidades_sin_bonificacion = Number(this.form.detalleProductos.reduce((carry, item) => (carry + ((item.afectacionx.id_afectacion !== 2) ? (Number(item.cantidad)) : 0)), 0))

                this.form.total_unidades_con_bonificacion = Number(this.form.detalleProductos.reduce((carry, item) => (carry + ((item.afectacionx.id_afectacion === 2) ? (Number(item.cantidad)) : 0)), 0))

                return this.form.detalleProductos.reduce((carry, item) => (carry + Number(item.cantidad)), 0)
            },

            gran_total_cordobas() {
                this.form.total_final_cordobas = Number((this.form.mt_subtotal_sin_iva - this.form.mt_retencion - this.form.mt_retencion_imi) + this.form.mt_impuesto).toFixed(2);
                // roundNumber.round(Number((Number(this.form.total_final)*this.form.t_cambio).toFixed(2)),2);

                if (!isNaN(this.form.total_final_cordobas)) {
                    return Number(this.form.total_final_cordobas);
                }
                return 0
            },

            gran_total() {
                /* this.form.total_final = roundNumber.decimalAdjust('ceil', Number(this.form.total_final_cordobas / this.form.t_cambio), -2); */
                this.form.total_final = Number(this.form.total_final_cordobas / this.form.tasa_paralela).toFixed(2);

                if (!isNaN(this.form.total_final)) {
                    return this.form.total_final
                }
                return 0
            },

            total_deuda() {
                /* let total_pagos = this.form.detallePago.reduce((carry, item) => {
                              return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto_me : Number((item.monto/this.form.t_cambio).toFixed(2))));
                          }, 0); */


                // total_pagos_cordobas = this.form.detallePago.reduce((carry, item) => (carry + Number(item.moneda_x.id_moneda === 1 ? item.monto : Number(item.monto_me * this.form.t_cambio).toFixed(2))), 0)
                let total_pagos_cordobas = this.form.detallePago.reduce((carry, item) => {
                    return (carry + Number(item.moneda_x.id_moneda === 1 ? Number(item.monto) : Number((item.monto_me) * (this.form.tasa_paralela)).toFixed(2)) )
                }, 0);


                /*console.log('Total pago C$: ' + total_pagos_cordobas);
                console.log('Total factura C$: ' + this.form.total_final_cordobas);
                console.log('Dif C$: ' + this.form.total_final_cordobas - total_pagos_cordobas);*/

                let total_final_cordobas = this.form.total_final_cordobas;
                if ((total_final_cordobas - total_pagos_cordobas).toFixed(2) < 0) {
                    this.form.pago_pendiente = 0;
                    this.form.pago_pendiente_mn = 0;
                    /*                    let result = Number(total_final_cordobas - total_pagos_cordobas).toFixed(2);
                                        console.log("total pagos cordobas" + " "+ total_pagos_cordobas);
                                        console.log("total final cordobas" + " " + total_final_cordobas);
                                        console.log("total final cordobas - total_pagos_cordobas  " + result);*/
                } else {
                    // this.form.pago_pendiente_mn = Number((Number((this.form.total_final_cordobas)).toFixed(2) - Number((total_pagos_cordobas)).toFixed(2)).toFixed(2));
                    this.form.pago_pendiente_mn = Number(total_final_cordobas - total_pagos_cordobas).toFixed(2);

/*                    console.log("total pagos cordobas" + " "+ total_pagos_cordobas);
                    console.log("total final cordobas" + " " + total_final_cordobas);

                    console.log('pago pendiente mn  ' + this.form.pago_pendiente_mn);*/
                    /* this.form.pago_pendiente = roundNumber.decimalAdjust('ceil', Number(this.form.pago_pendiente_mn / this.form.t_cambio), -2); */

                    /*console.log('total final cordobas ' + this.form.total_final_cordobas);
                    console.log('total pagos cordobas ' + total_pagos_cordobas);
                    console.log('Diferencia total - pago ' + this.form.total_final_cordobas - total_pagos_cordobas);
                    console.log('Pago pendiente mn ' + this.form.pago_pendiente_mn);*/
                    this.form.pago_pendiente = Number(this.form.pago_pendiente_mn / this.form.tasa_paralela).toFixed(2)
                }

                /* let total_pagos_cordobas = this.form.detallePago.reduce((carry, item) => {
                            return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto : Number((item.monto_me*this.form.t_cambio).toFixed(2))));
                          }, 0); */

                if (!isNaN(this.form.pago_pendiente_mn)) {
                    // Number((Number((this.form.total_final*this.form.t_cambio).toFixed(2)) - total_pagos_cordobas).toFixed(2));
                    return this.form.pago_pendiente_mn
                }
                return 0
            },

            total_vuelto() {
                /* let total_pagos = this.form.detallePago.reduce((carry, item) => {
                               return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto_me : Number(item.monto/this.form.t_cambio.toFixed(2))));
                           }, 0); */

                let total_pagos = 0;


                const monto_dolares_decimales = (Number(this.form.total_final_cordobas) / Number(this.form.tasa_paralela)).toFixed(2);
                total_pagos = this.form.detallePago.reduce((carry, item) => {
                    // return (carry + Number(item.moneda_x.id_moneda === 1 ? item.monto : Number((item.monto_me * this.form.t_cambio).toFixed(2))));
                    return carry + Number(item.moneda_x.id_moneda === 1 ? item.monto : Number((item.monto_me) * (this.form.tasa_paralela)).toFixed(2))
                }, 0);


                if (((Number((this.form.total_final_cordobas)).toFixed(2) - Number((total_pagos)).toFixed(2))) > 0) { /// revision
                    this.form.pago_vuelto = 0;
                    this.form.pago_vuelto_mn = 0;
                } else {
                    this.form.pago_vuelto_mn = Number((Number((total_pagos)).toFixed(2) - Number((this.form.total_final_cordobas)).toFixed(2)));
                    // console.log(total_pagos);
                    this.form.pago_vuelto = Number((this.form.pago_vuelto_mn / this.form.tasa_paralela)).toFixed(2);
                }

                // console.log('Master C$: '+((Number((this.form.total_final).toFixed(2)) - Number((total_pagos).toFixed(2))).toFixed(2)));

                /* let total_pagos_cordobas = this.form.detallePago.reduce((carry, item) => {
                            return (carry + Number(item.moneda_x.id_moneda === 3 ? item.monto : Number((item.monto_me*this.form.t_cambio).toFixed(2))));
                          }, 0); */

                if (!isNaN(this.form.pago_vuelto_mn)) {
                    return this.form.pago_vuelto_mn
                }
                return 0
            },

        },
        methods: {
            /*
                    Author: omorales (c)
                    hot search
                    14/03/22    */
            onSearch(search, loading) {
                if (search.length) {
                    loading(true)
                    this.search(loading, search, this)
                }
            },
            onSearchP(searchP, loading) {
                if (searchP.length) {
                    loading(true)
                    this.searchP(loading, searchP, this)
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
            searchP: _.debounce((loading, searchP, vm) => { // busqueda proformas
                const self = this
                axios.get(`/cajabanco/proformas/buscar?q=${escape(searchP)}`).then(res => {
                    vm.options = res.data.results
                    vm.proformas = res.data.results
                    loading(false)
                })
            }, 100),
            /*
                   Author: omorales (c)
                   Limpiar formulario al deseleccionar la opción de proforma
                   03/09/21    */
            deseleccionar() {
                this.form.proformasBusqueda = {}
                this.form.detalleProductos = []
                this.form.detallePago = []
                this.detalleForm.productox = ''
                this.proforma_cliente = []
                this.proforma_vendedor = []
                this.proforma_bodega = []
                this.proforma_sucursal = []
                this.proformaFormHeader.es_nuevo = true

                // this.filter.search.value=''
            },
            /*
                    Author: omorales (c)
                    Se agregó metodo para cargar detalle de proforma a la función de selección
                    03/09/21    */
            seleccionarProforma(e) {
                // const oc = e.target.value
                const self = this;
                /* self.form.proformasBusqueda = form.proformasBusqueda*/
                self.cargarDetalleProforma()
            },
            /*
                    Author: omorales (c)
                    Se creo función de carga de detalle de proforma mediante id
                    03/09/21    */
            cargarDetalleProforma() {
                const self = this
                self.loading = true
                proforma.obtenerDetalleProforma({
                        id_proforma: self.form.proformasBusqueda.id_proforma,
                        id_cliente: self.form.proformasBusqueda.id_cliente,
                        id_bodega: self.form.proformasBusqueda.id_bodega,
                    },
                    data => {
                        self.proformaForm = data.detalleProforma;
                        self.proformaFormHeader = data.proforma;
                        self.form.es_nuevo = self.proformaFormHeader.es_nuevo;
                        self.form.id_tipo = self.proformaFormHeader.id_tipo;
                        self.form.serie = self.proformaFormHeader.serie;
                        self.form.tipo_identificacion = self.proformaFormHeader.tipo_identificacion;
                        self.form.identificacion = self.proformaFormHeader.identificacion;
                        self.form.observacion = self.proformaFormHeader.observacion;
                        self.form.t_cambio = self.proformaFormHeader.t_cambio;
                        self.form.dias_credito = self.proformaFormHeader.dias_credito;
                        self.form.proforma_cliente = self.proformaFormHeader.proforma_cliente;
                        self.form.proforma_vendedor = self.proformaFormHeader.proforma_vendedor;
                        self.form.proforma_bodega = self.proformaFormHeader.proforma_bodega;
                        self.form.proforma_sucursal = self.proformaFormHeader.proforma_sucursal;

                        self.form.factura_cliente = self.form.proforma_cliente;
                        self.form.factura_vendedor = self.form.proforma_vendedor;
                        self.form.factura_bodega = self.form.proforma_bodega;
                        self.form.factura_sucursal = self.form.proforma_sucursal;
                        /* self.afectacion_producto = data.detalleProforma.afectacion_producto;
                                        self.bodega_producto = data.detalleProforma.bodega_producto; */
                        this.cargarProductosProforma();
                        self.loading = false
                    }, err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'No se encontró un detalle de la cotización seleccionada',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.loading = false
                    })
            },
            /*
                    Author: omorales (c)
                    Función para agregar datos de la proforma al detalle de productos de la factura
                    03/09/21    */
            cargarProductosProforma() {
                const self = this
                if (self.form.proformasBusqueda) {
                    self.proformaForm.forEach((productox, key) => {
                        if (productox) {
                            let cantidad = 0
                            if (self.form.detalleProductos) {
                                self.form.detalleProductos.forEach((productodx, key) => {
                                    if (productox.bodega_producto.id_producto === productodx.productox.id_producto) {
                                        cantidad = cantidad + productodx.cantidad + productox.cantidad
                                    }
                                })
                            }

                            const i = 0
                            const keyx = 0
                            // if (self.form.detalleProductos) {
                            //     self.form.detalleProductos.forEach((productodx, key) => {
                            //         if ((self.detalleForm.productox.id_producto === productodx.productox.id_producto)
                            //             && (self.detalleForm.afectacionx.id_afectacion === productodx.afectacionx.id_afectacion)) {
                            //             i++;
                            //             keyx = key;
                            //         }
                            //     });
                            // }

                            if (i === 0) {
                                if (cantidad <= Number(productox.bodega_producto.cantidad_disponible)) { /* validar existencias de proforma respecto al inventario */
                                    self.form.detalleProductos.push({
                                        productox: productox.bodega_producto,
                                        afectacionx: productox.afectacion_producto,
                                        cantidad: Number(productox.cantidad),
                                        precio_costo: Number(productox.bodega_producto.costo_promedio),
                                        precio_lista: Number(productox.precio_lista),
                                        precio: Number(productox.precio),
                                        p_descuento: Number(productox.p_descuento),
                                        mt_descuento: Number(productox.mt_descuento),
                                        p_impuesto: Number(productox.p_impuesto),
                                        mt_impuesto: Number(productox.mt_impuesto),
                                        subtotal: 0,
                                        total: 0,
                                        total_sin_iva: 0,
                                        mt_ajuste: Number(productox.mt_ajuste),
                                        p_ajuste: Number(productox.p_ajuste),
                                    })
                                    /* console.log(productox.bodega_producto); */
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'InfoIcon',
                                                text: 'Artículos agregados',
                                                variant: 'success',

                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });

                                    self.detalleForm.productox = null
                                    self.detalleForm.afectacionx = self.afectaciones[0]
                                    self.detalleForm.cantidad = 0
                                    self.detalleForm.precio_sugerido = 0
                                    self.detalleForm.subtotal = 0
                                    self.detalleForm.total = 0
                                    self.detalleForm.total_sin_iva = 0
                                    this.$refs.producto.$el.focus()
                                } else {
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'InfoIcon',
                                                text: 'No se cuenta con existencias suficiente para poder facturar',
                                                variant: 'warning',

                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                }
                            } else {
                                const nuevo_total = self.form.detalleProductos[keyx].cantidad + self.detalleForm.cantidad
                                if (nuevo_total <= self.form.detalleProductos[keyx].productox.cantidad_disponible) {
                                    self.form.detalleProductos[keyx].cantidad = nuevo_total
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'InfoIcon',
                                                text: 'Artículo agregado!',
                                                variant: 'success',

                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                } else {
                                    self.form.detalleProductos[keyx].cantidad = Number(self.form.detalleProductos[keyx].productox.cantidad_disponible)
                                    this.$toast({
                                            component: ToastificationContent,
                                            props: {
                                                title: 'Notificación',
                                                icon: 'InfoIcon',
                                                text: 'Se ha agregado la cantidad máxima de este producto',
                                                variant: 'warning',

                                            }
                                        },
                                        {
                                            position: 'bottom-right'
                                        });
                                }
                            }
                        }
                    })
                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Seleccione una proforma para continuar',
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },

            calcularEquivalente(itemX) {
                if (itemX.moneda_x.id_moneda === 1 && itemX.monto > 0) { // equivalente para moneda cordobas
                    itemX.monto_me = Number(itemX.monto / this.form.t_cambio).toFixed(2)
                }

                if (itemX.moneda_x.id_moneda === 2 && itemX.monto_me > 0) { // equivalente para moneda dolares
                    itemX.monto = Number(itemX.monto_me * this.form.t_cambio).toFixed(2)
                }
            },

            abrirModal() {
                this.$refs['modal'].show()
            },
            cerrarModal() {
                this.$refs['modal'].hide()
            },
            cambiarFocoCantidad() {
                const self = this
                self.$refs.agregar.focus()
            },

            seleccionarCliente(e) {
                // const trabajadorP = e.target.value;
                const self = this;

                // self.form.factura_cliente = trabajadorP;
                self.form.tipo_identificacion = self.form.factura_cliente.tipo_persona;
                self.form.id_tipo = 1;

                self.tipo_cliente = self.form.factura_cliente.id_tipo_cliente;

                self.form.dias_credito = 0;
                self.plazo_maximo_credito = self.form.factura_cliente.plazo_credito;
                self.form.factura_vendedor = self.form.factura_cliente.vendedor_cliente;
                (self.form.factura_cliente.permite_credito && self.form.factura_cliente.plazo_credito > 0) ? self.clienteCredito = true : self.clienteCredito = false

                if (self.form.factura_cliente.tipo_persona === 1) {
                    self.form.identificacion = self.form.factura_cliente.numero_cedula
                } else {
                    self.form.identificacion = self.form.factura_cliente.numero_ruc
                }
            },
            seleccionarVendedor(e) {
                const proveedorP = e.target.value
                const self = this
                self.form.factura_vendedor = proveedorP
            },
            seleccionarSucursal() {
                let self = this;
                self.bodegas = [];
                self.form.factura_bodega = [];
                // self.form.factura_sucursal = sucursalx;
                // console.log(sucursalx);
                // console.log(self.form.factura_sucursal);
                self.form.serie = self.form.factura_sucursal.serie
                if (self.form.factura_sucursal.sucursal_bodega_ventas.length) {
                    self.bodegas = self.form.factura_sucursal.sucursal_bodega_ventas;
                    self.form.factura_bodega = self.bodegas[0];
                    self.id_sucursal = self.form.factura_sucursal.id_sucursal;

                    self.loading = true;
                    self.form.detalleProductos = [];
                    self.form.detallePago = [];
                    self.detalleForm.productox = '';
                    self.form.proforma_especifica = false;
                    self.form.proformasBusqueda = [];
                    self.proformaForm = [];

                    bodega.buscarProductosBodega({
                        id_bodega: self.form.factura_bodega.id_bodega,
                    }, data => {
                        if (data !== null) {
                            self.productos = []
                            self.productosORG = []
                            self.servicios = []

                            self.productosORG = data.productos
                            self.servicios = data.servicios

                            self.productosORG.forEach((producto, key) => {
                                self.productos.push(producto)
                            })
                            self.servicios.forEach((servicio, key) => {
                                self.productos.push(servicio)
                            })

                            self.detalleForm.productox = ''
                        } else {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'No se encuentran productos para esta bodega',
                                        variant: 'warning',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            self.detalleForm.productox = ''
                        }
                        self.loading = false
                    }, err => {
                        /* if(err.codigo_bateria){
                                        self.detalleForm.bateria_busqueda = '';
                                        self.$refs.bateria.focus();
                                        alertify.warning("No se encuentra esta batería.",5);
                                        self.detalleForm.productox = '';
                                      } */
                        self.loading = false
                    })
                } else {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'La sucursal seleccionada no tiene bodegas disponibles.',
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }

                self = this;
                factura.obtenerConsecutivo({
                    id_sucursal: self.id_sucursal,
                    serie: self.form.serie,
                }, data => {
                    if (data !== null) {
                        self.no_documento = data.no_documento_siguiente
                    } else {
                        self.no_documento = ''
                    }
                    self.loading = false
                }, err => {
                    self.loading = false
                })
            },
            obtenerConsecutivo() {
                const self = this
                factura.obtenerConsecutivo({factura_sucursal: self.form.factura_sucursal, serie: self.form.serie})
                {
                    self.no_documento = data.no_documento_siguiente
                }
            },
            seleccionarArea(e) {
                const areaP = e.target.value
                const self = this
                self.form.factura_area = areaP
            },
            seleccionarBodega() {
                const self = this;
                self.loading = true;
                self.form.detalleProductos = [];
                self.form.detallePago = [];
                self.detalleForm.productox = '';

                bodega.buscarProductosBodega({
                    id_bodega: self.form.factura_bodega.id_bodega,
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

                        self.detalleForm.productox = ''
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'No se encuentran productos para esta bodega',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.detalleForm.productox = ''
                    }
                    self.loading = false
                }, err => {
                    /* if(err.codigo_bateria){
                                  self.detalleForm.bateria_busqueda = '';
                                  self.$refs.bateria.focus();
                                  alertify.warning("No se encuentra esta batería.",5);
                                  self.detalleForm.productox = '';
                                } */
                    self.loading = false
                })
            },
            cargar_detalles_producto() {
                // const productoP = e.target.value;
                const self = this;
                // self.detalleForm.productox = productoP;
                if (self.detalleForm.productox) {
                    self.detalleForm.cantidad = 1;

                    if (self.tipo_cliente === 1) {
                        self.detalleForm.precio_sugerido = roundNumber.round(Number((self.detalleForm.productox.precio_sugerido * self.form.tasa_paralela)), 2);
                        self.detalleForm.precio_sugerido_me = Number(self.detalleForm.productox.precio_sugerido)
                    } else {
                        self.detalleForm.precio_sugerido = roundNumber.round(Number((self.detalleForm.productox.precio_distribuidor * self.form.tasa_paralela)), 2);
                        self.detalleForm.precio_sugerido_me = Number(self.detalleForm.productox.precio_distribuidor)
                    }

                    if (self.detalleForm.productox.id_tipo_producto === 2) {
                        self.detalleForm.afectacionx = self.afectaciones[0]
                    }

                    self.$refs.cantidad.focus()
                    // self.detalleForm.precio_unitario = self.detalleForm.productox.costo_promedio;
                }
            },
            calcularAjuste(itemX) {
                itemX.p_ajuste = Number(itemX.afectacionx.valor);
                if (itemX.p_ajuste > 0) {
                    itemX.p_descuento = 0
                }
            },

            calcular_montos(itemX) {
                itemX.subtotal = Number(((Number(itemX.precio) * Number(itemX.cantidad))).toFixed(2));

                // itemX.mt_descuento = Number((Number(itemX.p_descuento / 100) *  ( Number(((Number(itemX.precio) * Number(itemX.cantidad)))  ).toFixed(2))).toFixed(2));
                itemX.mt_descuento = Number((Number(itemX.precio) * Number(itemX.cantidad)) * Number(itemX.p_descuento / 100));

                itemX.p_ajuste = Number(itemX.afectacionx.valor);

                itemX.mt_ajuste = Number((Number(itemX.p_ajuste / 100) * Number(((Number(itemX.precio) * Number(itemX.cantidad))).toFixed(2))).toFixed(2));

                itemX.p_unitario = Number(((Number(itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste) / Number(itemX.cantidad))).toFixed(2));

                /* console.log(itemX.p_impuesto);
                          console.log(Number(itemX.p_impuesto/100));
                          console.log(itemX.subtotal-itemX.mt_descuento-itemX.mt_ajuste);
                          console.log(Number((Number(itemX.p_impuesto/100)*Number((itemX.subtotal-itemX.mt_descuento-itemX.mt_ajuste)))));
                          */
                /* let xy = Number((Number(itemX.p_impuesto/100)*Number((itemX.subtotal-itemX.mt_descuento-itemX.mt_ajuste))));
                          console.log(roundNumber.round(xy,2)); */

                itemX.mt_impuesto = roundNumber.round(Number((Number(itemX.p_impuesto / 100) * Number((itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste))).toFixed(2)), 2);

                itemX.total_sin_iva = roundNumber.round(Number((itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste)), 2);

                itemX.total = Number((itemX.subtotal - itemX.mt_descuento - itemX.mt_ajuste + itemX.mt_impuesto).toFixed(2));

                if (!isNaN(itemX.mt_descuento)) {
                    return itemX.mt_descuento
                }
                return 0
            },

            obtenerTCParalela() {
                const self = this
                self.loading = true
                tc.obtenerTCParalela({
                    fecha: self.form.f_factura,
                    dias: self.form.dias_credito,
                }, data => {
                    if (data.tasa_paralela !== null) {
                        self.form.tasa_paralela = Number(data.tasa_paralela)
                        // self.form.tasa_oficial = Number(data.tasa);
                        self.form.f_vencimiento = data.fecha
                        self.form.detalleProductos.forEach((productox, key) => {
                            productox.precio_lista = Number((productox.precio_lista_me * self.form.tasa_paralela).toFixed(2))
                            productox.precio = Number((productox.precio_lista_me * self.form.tasa_paralela).toFixed(2))
                            // console.log(productox.precio_lista);
                        })
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
                        self.form.t_cambio = 0
                        self.form.f_vencimiento = self.form.f_factura
                        self.form.detalleProductos = []
                    }
                    self.loading = false
                }, err => {
                    if (err.fecha[0]) {
                        self.form.t_cambio = 0
                        self.form.f_vencimiento = self.form.f_factura
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error: ' + err.fecha[0],
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        self.loading = false
                    }
                })
            },

            /* obtenerTC(){
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
                    }, */

            /* obtenerAfectacionesTodas() {
                       var self = this;
                       afectacion.obtenerTodos(
                               data => {
                                 self.afectaciones = data;
                               },
                               err => {
                                 console.log(err);
                               }
                       );
                     }, */

            obtenerMunicipios() {
                const self = this
                self.form.municipio = null
                if (self.formCliente.departamento.municipios_departamento) self.municipios = self.formCliente.departamento.municipios_departamento
            },

            nueva() {
                const self = this
                factura.nueva({
                        id_sucursal: self.id_sucursal,
                    }, data => {
                        self.sucursales = data.sucursales;
                        self.vias_pago = data.vias_pago;
                        self.afectaciones = data.afectaciones;
                        self.detalleForm.afectacionx = self.afectaciones[0];
                        self.monedas = data.monedas;
                        self.bancos = data.bancos;
                        // self.form.factura_bodega=self.bodegas[0];
                        self.productos = [];
                        self.form.t_cambio = Number(data.t_cambio.tasa)
                        self.form.tasa_paralela = Number(data.t_cambio.tasa_paralela);
                        // self.zonas = data.zonas;
                        self.vendedores = data.vendedores;
                        // self.formCliente.zona = data.zonas[0];
                        self.departamentos = data.departamentos;
                        self.formCliente.departamento = self.departamentos[9];
                        self.municipios = self.formCliente.departamento.municipios_departamento;
                        self.formCliente.municipio = self.municipios[5];
                        self.formCliente.vendedor = data.vendedor_actual;
                        self.form.factura_sucursal = self.sucursales[0];
                        self.seleccionarSucursal();
                        self.loading = false
                        /* self.form.factura_bodega.productos_bodega.forEach((bodega_producto, key) => {
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
                                        }); */
                    },
                    err => {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Ha ocurrido un error al cargar los datos. ' + err,
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        console.log(err);
                        self.loading = false
                    })
            },

            agregarDetalle() {
                const self = this
                if (self.detalleForm.productox && self.detalleForm.afectacionx) {
                    if (self.detalleForm.cantidad > 0) {
                        let cantidad = 0;
                        if (self.form.detalleProductos) {
                            self.form.detalleProductos.forEach((productox, key) => {
                                if (self.detalleForm.productox.id_producto === productox.productox.id_producto) {
                                    cantidad = cantidad + productox.cantidad + self.detalleForm.cantidad
                                }
                            })
                        }

                        let i = 0;
                        let keyx = 0;
                        if (self.form.detalleProductos) {
                            self.form.detalleProductos.forEach((productox, key) => {
                                if ((self.detalleForm.productox.id_producto === productox.productox.id_producto)
                                    && (self.detalleForm.afectacionx.id_afectacion === productox.afectacionx.id_afectacion)) {
                                    i++;
                                    keyx = key
                                }
                            })
                        }
                        if (i === 0) {
                            if (cantidad <= Number(self.detalleForm.productox.cantidad_disponible)) {
                                self.form.detalleProductos.push({
                                    productox: self.detalleForm.productox,
                                    afectacionx: self.detalleForm.afectacionx,
                                    cantidad: Number(self.detalleForm.cantidad),
                                    precio_costo: Number(self.detalleForm.productox.costo_promedio),
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
                                            text: 'Producto agregado correctamente.',
                                            variant: 'success',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                            } else {
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'Se ha agregado la cantidad máximad disponible de este producto.',
                                            variant: 'warning',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                            }
                        } else {
                            const nuevo_total = self.form.detalleProductos[keyx].cantidad + self.detalleForm.cantidad;
                            if (nuevo_total <= self.form.detalleProductos[keyx].productox.cantidad_disponible) {
                                self.form.detalleProductos[keyx].cantidad = nuevo_total;
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'Producto agregado.!',
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
                                            text: 'Se ha agregado la cantidad máximad disponible de este producto.',
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
                        // this.$refs.producto.$el.focus();
                        this.$refs.producto.$refs.search.focus();
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Los valores para cantidad y precio deben ser mayor a cero..',
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
                                text: 'Debe seleccionar un producto.',
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
                    this.form.detalleProductos.splice(index, 1)
                } else {
                    this.form.detalleProductos = []
                }
            },

            pagoCompleto(IdMoneda) {
                const self = this

                if (Number(self.form.total_final_cordobas).toFixed(2) > 0) {
                    self.form.detallePago = []
                    /*
                                      let monto =0,monto_me=0;
                                      if(IdMoneda === 1){
                                        monto=Number(self.form.total_final_cordobas.toFixed(2));
                                        monto_me=Number((self.form.total_final_cordobas/self.form.t_cambio).toFixed(2));
                                      }else{
                                        monto=Number(self.form.total_final_cordobas.toFixed(2));
                                        monto_me=Number((self.form.total_final_cordobas/self.form.t_cambio).toFixed(2));
                                      } */

                    self.form.detallePago.push({
                        via_pagox: self.vias_pago[1],
                        moneda_x: self.monedas[Number(IdMoneda)],
                        monto: Number(self.form.total_final_cordobas).toFixed(2),
                        monto_me: Number((self.form.total_final_cordobas / self.form.t_cambio)).toFixed(2),
                        banco_x: null,
                        numero_minuta: '',
                        numero_nota_credito: '',
                        numero_cheque: '',
                        numero_transferencia: '',
                        numero_recibo_pago: '',

                    })

                    self.detalleFormPago.via_pagox = null
                    self.detalleFormPago.monto = 0
                    self.detalleFormPago.monto_me = 0
                    self.detalleFormPago.moneda_x = null
                    self.detalleFormPago.banco_x = null
                    self.detalleFormPago.numero_minuta = ''
                    self.detalleFormPago.numero_nota_credito = ''
                    self.detalleFormPago.numero_cheque = ''
                    self.detalleFormPago.numero_transferencia = ''
                    self.detalleFormPago.numero_recibo_pago = ''
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
                                if (self.detalleFormPago.via_pagox.id_via_pago === via_pago_x.via_pagox.id_via_pago && self.detalleFormPago.moneda_x.id_moneda === via_pago_x.moneda_x.id_moneda) {
                                    i++;
                                    keyx = key
                                }
                            })
                        }
                        let monto_me = 0;
                        let
                            monto_mn = 0;

                        if (self.detalleFormPago.moneda_x.id_moneda === 1) { // metodos de pago con moneda cordobas

                            monto_mn = Number(self.detalleFormPago.monto);
                            monto_me = Number((self.detalleFormPago.monto / self.form.t_cambio)).toFixed(2)

                        } else if (self.detalleFormPago.moneda_x.id_moneda === 2) { // metodos de pago con moneda dolares
                            monto_me = Number(self.detalleFormPago.monto);
                            // const monto_mn_2 = (Number(self.form.total_final_cordobas) / Number(self.form.t_cambio)).toFixed(2);
                            // console.log(`calculo 4 decimales${monto_mn_2}`)
                            monto_mn = Number((self.detalleFormPago.monto * self.form.t_cambio)).toFixed(2)
                            // console.log('monto me: ' + monto_me);
                            console.log('monto mn: ' + monto_mn);
                            // monto_mn = Number((monto_mn_2 * self.form.t_cambio).toFixed(2))
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
                                        icon: 'InfoIcon',
                                        text: 'Método de pago agregado.',
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
                                self.form.detallePago[keyx].monto_me = Number((self.form.detallePago[keyx].monto / self.form.t_cambio).toFixed(2))

                            } else if (self.detalleFormPago.moneda_x.id_moneda === 2) {
                                self.form.detallePago[keyx].monto_me = Number(self.form.detallePago[keyx].monto_me + self.detalleFormPago.monto).toFixed(2);
                                self.form.detallePago[keyx].monto = Number(self.form.detallePago[keyx].monto_me * self.form.t_cambio).toFixed(2);
                            }

                            // let nuevo_monto_total = self.form.detallePago[keyx].monto + self.detalleFormPago.monto;

                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Pago agregado.',
                                        variant: 'warning',

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
                                icon: 'InfoIcon',
                                text: 'Debe seleccionar un método y una moneda.',
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
                    this.form.detallePago.splice(index, 1)
                } else {
                    this.form.detallePago = []
                }
            },

            procesar_factura() {
                const self = this
                self.$swal.fire({
                    title: 'Esta seguro de completar la factura?',
                    text: 'Detalles de la factura: ',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar',
                }).then(result => {
                    if (result.value) {
                        factura.registrar(
                            self.form,
                            // eslint-disable-next-line no-unused-vars
                            data => {
                                this.$swal.fire(
                                    'Factura Registrada!',
                                    'La Factura fue registrada correctamente',
                                    'success',
                                ).then(result2 => {
                                    if (result2.value) {
                                        this.$router.push({
                                            name: 'cajabanco-facturas',
                                        })
                                    }
                                })
                            },
                            err => {
                                self.errorMessages = err
                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'Revise los datos faltantes.',
                                            variant: 'warning',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                                ;
                                self.btnAction = 'Facturar'
                            },
                        )
                    } else {
                        self.btnAction = 'Facturar'
                    }
                })
            },

            registrar() {
                const self = this
                self.btnAction = 'Registrando, espere....'

                /* facturas de contado */
                if (self.form.id_tipo === 1) {
                    if (self.form.pago_vuelto_mn >= 0 /* && self.form.total_final_cordobas > 0 */ && self.form.pago_pendiente_mn === 0) {
                        this.procesar_factura()
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Las facturas de contado deben ser pagadas en su totalidad.',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        ;
                        // self.errorMessages.serie = Array('Error serie');
                        self.btnAction = 'Facturar'
                    }
                }

                /* facturas de credito */
                if (self.form.id_tipo === 2) {
                    if (self.form.pago_vuelto_mn === 0 && self.form.total_final_cordobas > 0 && self.form.pago_pendiente_mn > 1) {
                        if (self.form.factura_cliente) {
                            if (self.form.pago_pendiente_mn <= Number(self.form.factura_cliente.monto_credito_disponible)) {
                                self.procesar_factura()
                            } else {
                                /* alertify.warning(`El monto máximo actual de crédito de este cliente es C$ ${Number(self.form.factura_cliente.monto_credito_disponible).toFixed(2)
                                 } , el monto de ésta factura supera ese monto`, 5);*/

                                this.$toast({
                                        component: ToastificationContent,
                                        props: {
                                            title: 'Notificación',
                                            icon: 'InfoIcon',
                                            text: 'El monto máximo actual de crédito de este cliente es C$ ' + Number(this.form.factura_cliente.monto_credito_disponible).toFixed(2) + ' El monto de la factura supera este limite.',
                                            variant: 'warning',

                                        }
                                    },
                                    {
                                        position: 'bottom-right'
                                    });
                                self.btnAction = 'Facturar'
                            }
                        } else {
                            this.$toast({
                                    component: ToastificationContent,
                                    props: {
                                        title: 'Notificación',
                                        icon: 'InfoIcon',
                                        text: 'Debe seleccionar un cliente.',
                                        variant: 'warning',

                                    }
                                },
                                {
                                    position: 'bottom-right'
                                });
                            self.btnAction = 'Facturar'
                        }
                    } else {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'InfoIcon',
                                    text: 'Las facturas de crédito deben tener saldo a pagar.',
                                    variant: 'warning',

                                }
                            },
                            {
                                position: 'bottom-right'
                            });
                        // self.errorMessages.serie = Array('Error serie');
                        self.btnAction = 'Facturar'
                    }
                }
            },

            registrarCliente() {
                const self = this
                self.btnActionCliente = 'Registrando, espere....'

                if (!self.registrandoCliente) self.registrandoCliente = true
                cliente.registrarBasico(self.formCliente, data => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Cliente registrado exitosamente.',
                                variant: 'success',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    // console.log(data);
                    self.form.factura_cliente = data;
                    self.form.factura_vendedor = self.formCliente.vendedor;
                    self.form.tipo_identificacion = self.form.factura_cliente.tipo_persona;
                    if (self.form.factura_cliente.tipo_persona === 1) {
                        self.form.identificacion = self.form.factura_cliente.numero_cedula
                    } else {
                        self.form.identificacion = self.form.factura_cliente.numero_ruc
                    }
                    self.btnActionCliente = 'Registrar Cliente'
                    // self.form.factura_vendedor
                    self.registrandoCliente = false

                    self.formCliente.tipo_persona = 1;
                    self.formCliente.nombre_comercial = '';
                    self.formCliente.vendedor = '';
                    self.formCliente.numero_ruc = '';
                    self.formCliente.numero_cedula = '';
                    self.formCliente.direccion = '';
                    self.formCliente.id_tipo_cliente = 1;
                    self.formCliente.telefono = '';
                    self.formCliente.correo = '';
                    self.formCliente.municipio = '';
                    self.formCliente.giro_negocio = '';
                    self.formCliente.zona = '';
                    self.formCliente.id_impuesto = 1;
                    self.formCliente.tipo_contribuyente = 1;
                    self.formCliente.retencion_ir = true;
                    self.formCliente.retencion_imi = true;
                    self.formCliente.clasificacion = 1;
                    self.formCliente.permite_credito = false;
                    self.formCliente.plazo_credito = 0;
                    self.formCliente.limite_credito = 0;
                    self.formCliente.porcentaje_descuento = 0;
                    self.formCliente.observaciones = '';
                    self.formCliente.permite_consignacion = false;
                    self.formCliente.tramite_cheque = 15;

                    self.$refs['modal'].hide()
                }, err => {
                    this.$toast({
                            component: ToastificationContent,
                            props: {
                                title: 'Notificación',
                                icon: 'InfoIcon',
                                text: 'Ha ocurrido un error. ' + err,
                                variant: 'warning',

                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                    self.errorMessages = err;
                    self.registrandoCliente = false;
                    self.btnActionCliente = 'Registrar Cliente'
                })
            },

            seleccionarTipo() {
                const self = this
                if (self.form.id_tipo === 1) {
                    self.form.dias_credito = 0
                } else {
                    self.form.aplicaIR = false;
                    self.form.aplicaIMI = false;

                    self.form.mt_retencion = 0;
                    self.form.mt_retencion_imi = 0;

                    self.form.doc_exoneracion_ir = '';
                    self.form.doc_exoneracion_imi = '';

                    self.form.dias_credito = 0;
                    self.form.dias_credito = self.plazo_maximo_credito
                }
                self.obtenerTCParalela()
                /* calcular fecha */
            },

        },
        mounted() {
            // this.obtenerAfectacionesTodas();
            // this.obtenerTodasBodegasProductos();
            this.nueva()
        },
    }
</script>
<style lang="scss">

    @import 'src/@core/scss/vue/libs/vue-select';

    .btn-agregar {
        margin-top: 1.6rem;
    }

    .check-label2 {
        margin-top: 30px;
    }
</style>
