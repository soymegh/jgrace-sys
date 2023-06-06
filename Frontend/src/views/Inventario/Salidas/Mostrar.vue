<template>
    <section class="invoice-preview-wrapper">

        <!-- Alert: No item found -->
        <!--<b-alert
                variant="danger"
                :show="invoiceData === undefined"
        >
            <h4 class="alert-heading">
                Error fetching invoice data
            </h4>
            <div class="alert-body">
                No invoice found with this invoice id. Check
                <b-link
                        class="alert-link"
                        :to="{ name: 'apps-invoice-list'}"
                >
                    Invoice List
                </b-link>
                for other invoices.
            </div>
        </b-alert>-->

        <b-row class="invoice-preview">

            <!-- Col: Left (Invoice Container) -->
            <b-col
                    cols="12"
                    xl="9"
                    md="8"
            >
                <b-card
                        no-body
                        class="invoice-preview-card"
                >
                    <!-- Header -->
                    <b-card-body class="invoice-padding pb-0">

                        <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">

                            <!-- Header: Left Content -->
                            <div>
                                <div class="logo-wrapper" style="margin-bottom: 0.9rem !important;">

                                    <h3 class="text-primary invoice-logo ml-0">
                                        {{nombre_empresa}}
                                    </h3>
                                </div>
                                <p class="card-text mb-25 ml-0">
                                    {{'Dirección: '+direccion_empresa}}
                                </p>
                                <p class="card-text mb-25 ml-0">
                                    {{'Telefono: '+telefono_empresa}}
                                </p>

                            </div>

                            <!-- Header: Right Content -->
                            <div class="mt-md-0 mt-2">
                                <h4 class="invoice-title">
                                    Salida
                                    <span class="invoice-number">#{{ form.codigo_salida}}</span>
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title">
                                        F. entrada:
                                    </p>
                                    <p class="invoice-date">
                                        {{ formatDate(form.fecha_salida)}}
                                    </p>
                                </div>
                                <!--<div class="invoice-date-wrapper">
                                    <p class="invoice-date-title">
                                        Due Date:
                                    </p>
                                    <p class="invoice-date">
                                        {{ 'invoiceData.dueDate' }}
                                    </p>
                                </div>-->
                            </div>
                        </div>
                    </b-card-body>

                    <!-- Spacer -->
                    <hr class="invoice-spacing">

                    <!-- Invoice Client & Payment Details -->
                    <b-card-body

                            class="invoice-padding pt-0"
                    >
                        <b-row class="invoice-spacing">

                            <!-- Col: Invoice To -->
                            <b-col cols="12" xl="4" class="p-0">
                                <h6 class="mb-25">
                                    {{ 'Número documento:' }}
                                </h6>
                                <p class="card-text mb-25">
                                    {{form.numero_documento}}
                                </p>
                                <h6 class="mb-25">
                                    {{ 'Observaciones:' }}
                                </h6>
                                <p class="card-text mb-25">
                                    {{form.descripcion_salida}}
                                </p>
                            </b-col>

                            <!-- Col: Payment Details   xl="6" cols="12" class="p-0 mt-xl-0 mt-2 d-flex justify-content-xl-end"-->
                            <b-col cols="12" xl="4" class="p-0 justify-content-xl-center">
                                <h6 class="mb-25">
                                    {{ 'Bodega saliente:' }}
                                </h6>
                                <router-link
                                             :to="{ name: 'inventario-bodegas-actualizar', params: { id_bodega: form.id_bodega } }"
                                             target="_blank">
                                    <p class="card-text mb-25">
                                        {{ form.salida_bodega.descripcion}}
                                    </p>
                                </router-link>
                                <template v-if="form.id_tipo_salida ===4">
                                    <h6 class="mb-25">
                                        {{ 'Bodega entrante:' }}
                                    </h6>
                                    <router-link
                                                 :to="{ name: 'inventario-bodegas-actualizar', params: { id_bodega: form.id_bodega_entrante } }"
                                                 target="_blank">
                                        <p class="card-text mb-25">
                                            {{ form.salida_bodega_entrante.descripcion}}
                                        </p>
                                    </router-link>
                                </template>
                                <h6 class="mb-25 mt-1">
                                    {{ 'Tipo de salida:' }}
                                </h6>
                                <p class="card-text mb-25">
                                    {{form.salida_tipo.descripcion}}
                                </p>
                                <h6 class="mb-25">
                                    {{ 'Usuario registra:' }}
                                </h6>
                                <p class="card-text mb-25">
                                    {{ form.u_creacion}}
                                </p>

                            </b-col>
                            <b-col cols="12" xl="4" class="p-0 justify-content-xl-end">
                                <h6 class="mb-25">
                                    {{ 'Estado actual:' }}
                                </h6>
                                <p class="card-text mb-25">
                                <div v-if="form.estado===0">
                                    <span class="badge badge-danger" style="font-size: 100%;">Cancelada</span>
                                </div>

                                <div v-if="form.estado===1">
                                    <span class="badge badge-primary" style="font-size: 100%;">Emitida</span>
                                </div>

                                <div v-if="form.estado===2">
                                    <span class="badge badge-success" style="font-size: 100%;">Despachada</span>
                                </div>
                                </p>
                                <h6 class="mb-25">
                                    {{ 'Fecha emisión:' }}
                                </h6>
                                <p class="card-text mb-25">
                                    {{ formatDate(form.fecha_salida)}}
                                </p>
                                <h6 class="mb-25">
                                    {{ 'Fecha despacho:' }}
                                </h6>
                                <p class="card-text mb-25">
                                    {{ form.fecha_despacho ? formatDate(form.fecha_despacho) : 'N/D'}}
                                </p>

                            </b-col>
                            <template v-if="form.salida_cliente">
                                <b-col cols="12" xl="12" class="p-0 justify-content-xl-end">
                                    <h6 class="mb-25">
                                        {{ 'Cliente:' }}
                                    </h6>
                                    <p class="card-text mb-25">
                                        {{form.salida_cliente.tipo_persona === 1? (form.salida_cliente.nombre_completo + ' | '+form.salida_cliente.numero_cedula):(form.salida_cliente.razon_social + ' | ' + form.salida_cliente.numero_ruc)}}
                                    </p>
                                </b-col>
                            </template>
                        </b-row>
                    </b-card-body>

                    <!-- Invoice Description: Table -->
                    <b-table-lite
                            responsive
                            :items="items"
                            :fields="fields"
                            foot-clone

                    >
                        <template #cell(codigo_barra)="data">
                            <b-card-text class="font-weight-bold mb-25">
                                {{data.item.codigo_producto}}
                            </b-card-text>
                        </template>
                        <template #cell(descripcion)="data">
                            <b-card-text class="font-weight-bold mb-25">
                                {{data.item.descripcion_producto}}
                            </b-card-text>
                        </template>
                        <template #cell(unidad_medida)="data">
                            <b-card-text class="font-weight-bold mb-25">
                                {{data.item.unidad_medida}}
                            </b-card-text>
                        </template>
                        <template #cell(cantidad_saliente)="data">
                            <b-card-text class="font-weight-bold mb-25">
                                {{data.item.cantidad_saliente}}
                            </b-card-text>
                        </template>
                        <template #cell(cantidad_despachada)="data">
                            <b-card-text class="font-weight-bold mb-25">
                                {{data.item.cantidad_despachada}}
                            </b-card-text>
                        </template>

                        <!--Custo footer - summary-->
                        <template #foot(codigo_barra)="data">
                            <span class="text-bold">{{ 'Totales:' }}</span>
                        </template>
                        <template #foot(descripcion)="data">
                            <span class="text-bold">{{ '' }}</span>
                        </template>
                        <template #foot(unidad_medida)="data">
                            <span class="text-bold">{{ '' }}</span>
                        </template>
                        <template #foot(cantidad_saliente)="data">
                            <span class="text-bold">{{ total_cantidad_saliente }}</span>
                        </template>
                        <template #foot(cantidad_despachada)="data">
                            <span class="text-bold">{{ total_cantidad_despachada }}</span>
                        </template>
                    </b-table-lite>

                    <!-- Invoice Description: Total -->
                    <b-card-body class="invoice-padding pb-0">
                        <!--<b-row>

                            &lt;!&ndash; Col: Sales Persion &ndash;&gt;
                            <b-col
                                    cols="12"
                                    md="6"
                                    class="mt-md-0 mt-3"
                                    order="2"
                                    order-md="1">
                                <b-card-text class="mb-0">

                                </b-card-text>
                            </b-col>

                            &lt;!&ndash; Col: Total &ndash;&gt;
                            <b-col
                                    cols="12"
                                    md="6"
                                    class="mt-md-6 d-flex justify-content-end"
                                    order="1"
                                    order-md="2">
                                <div class="invoice-total-wrapper">
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">
                                            Total unidades:
                                        </p>
                                        <p class="invoice-total-amount">
                                            {{total_cantidad_prods}}
                                        </p>
                                    </div>

                                </div>
                            </b-col>
                        </b-row>-->
                    </b-card-body>

                    <!-- Spacer -->
                    <hr class="invoice-spacing">

                    <!-- Note -->
                    <b-card-body class="invoice-padding pt-0">
                        <b-row>

                            <!-- Col: Sales Persion -->
                            <b-col
                                    cols="12"
                                    md="6"
                                    class="mt-md-0 mt-3"
                                    order="2"
                                    order-md="1">
                                <b-card-text class="mb-0">

                                </b-card-text>
                            </b-col>

                            <!-- Col: Total -->
                            <b-col
                                    cols="12"
                                    md="6"
                                    class="mt-md-6 d-flex justify-content-end"
                                    order="1"
                                    order-md="2">
                                <!-- Summary-->
                                <!--<div class="invoice-total-wrapper">
                                    <div class="invoice-total-item">
                                        <p class="invoice-total-title">
                                            Total unidades:
                                        </p>
                                        <p class="invoice-total-amount">
                                            {{total_cantidad_prods}}
                                        </p>
                                    </div>

                                </div>-->
                            </b-col>
                        </b-row>
                    </b-card-body>
                </b-card>
            </b-col>

            <!-- Right Col: Card -->
            <b-col
                    cols="12"
                    md="4"
                    xl="3"
                    class="invoice-actions"
            >
                <b-card>

                    <!-- Button: Send Invoice -->
                    <!--                    <b-button-->
                    <!--                            v-ripple.400="'rgba(255, 255, 255, 0.15)'"-->
                    <!--                            v-b-toggle.sidebar-send-invoice-->
                    <!--                            variant="primary"-->
                    <!--                            class="mb-75"-->
                    <!--                            block-->
                    <!--                    >-->
                    <!--                        Send Invoice-->
                    <!--                    </b-button>-->

                    <!-- Button: DOwnload -->
                    <b-button
                            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                            variant="outline-secondary"
                            class="mb-75"
                            block
                            @click.prevent="downloadItem('pdf',form.id_salida)"
                    >
                        Download <feather-icon icon="DownloadIcon" aria-hidden="true" style="color: #0a91ff"></feather-icon>
                    </b-button>

                    <!-- Button: Print -->
                    <b-button
                            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                            variant="outline-secondary"
                            class="mb-75"
                            block
                            @click="printWindow"
                    >
                        Print
                    </b-button>

                    <!-- Button: Edit -->
                    <!--<b-button
                            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                            variant="outline-secondary"
                            class="mb-75"
                            block
                            :to="{ name: 'apps-invoice-edit', params: { id: $route.params.id } }"
                    >
                        Edit
                    </b-button>-->

                    <!-- Button: Add Payment -->
                    <!-- <b-button
                             v-b-toggle.sidebar-invoice-add-payment
                             v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                             variant="success"
                             class="mb-75"
                             block
                     >
                         Add Payment
                     </b-button>-->
                </b-card>
            </b-col>
        </b-row>
        <template v-if="loading">
            <BlockUI :url="url"></BlockUI>
        </template>

    </section>
</template>

<script type="text/ecmascript-6">
    import {ref, onUnmounted} from '@vue/composition-api'
    import store from '@/store'
    import router from '@/router'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import {
        BRow, BCol, BCard, BCardBody, BTableLite, BCardText, BButton, BAlert, BLink, VBToggle,
    } from 'bootstrap-vue'
    import Logo from '@core/layouts/components/Logo.vue'
    import Ripple from 'vue-ripple-directive'
    import salida from '../../../api/Inventario/salidas'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
    import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
    import axios from "axios";
    // import invoiceStoreModule from '../invoiceStoreModule'
    // import InvoiceSidebarSendInvoice from '../InvoiceSidebarSendInvoice.vue'
    // import InvoiceSidebarAddPayment from '../InvoiceSidebarAddPayment.vue'

    export default {
        directives: {
            Ripple,
            'b-toggle': VBToggle,
        },
        components: {
            BRow,
            BCol,
            BCard,
            BCardBody,
            BTableLite,
            BCardText,
            BButton,
            BAlert,
            BLink,

            Logo,
            // InvoiceSidebarAddPayment,
            // InvoiceSidebarSendInvoice,
        },
        data() {
            return {
                loading: true,
                msg: 'Cargando el contenido espere un momento',
                url: loadingImage,
                nombre_empresa: '',
                direccion_empresa: '',
                telefono_empresa: '',
                format: "dd-MM-yyyy",
                descargando: false,
                form: {
                    codigo_salida: "",
                    fecha_salida: "",
                    id_tipo_salida: "",
                    id_proveedor: 0,
                    id_bodega: 0,
                    detalleProductos: [],
                    salida_proveedor: [],
                    salida_bodega : [],
                    salida_productos: [],
                    salida_tipo:[],
                    log_registro:[],
                    total: 0,
                    sub_total: 0,
                },
                btnAction: "Registrar",
                errorMessages: [],
                items: [],
                fields: [
                    'codigo_barra',
                    'descripcion',
                    'unidad_medida',
                    'cantidad_saliente',
                    'cantidad_despachada',
                ],
            };
        },
        methods: {
            downloadItem(extension, id_salida) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'salidas/reporte/' + extension + '/' + id_salida;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoControlSalida.' + extension;
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
                            },{
                                position:'bottom-right'
                            });
                        }).catch(function (error) {
                        this.$toast({
                                component: ToastificationContent,
                                props: {
                                    title: 'Notificación',
                                    icon: 'AlertTriangleIcon',
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
                                icon: 'AlertCircleIcon',
                                text: 'Espere a que se complete la descarga anterior',
                                variant: 'warning',
                            }
                        },
                        {
                            position: 'bottom-right'
                        });
                }
            },
            descargarReporteSalida () {
                if(!this.loading){
                    var self=this;
                    self.loading=true;
                    var extensionx = 'pdf';
                    alertify.success("Descargando datos, espere un momento.....",5);
                    axios.post('salidas/reporte',
                        {
                            id_salida : self.form.id_salida,
                            extension : extensionx
                        }, { responseType: 'blob' })
                        .then(({ data }) => {
                            let blob = new Blob([data], { type: 'application/pdf' })
                            extensionx === 'xls' ? blob = new Blob([data], { type: 'application/octet-stream' }) : blob = new Blob([data], { type: 'application/pdf' });
                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'ReporteSalida.'+ extensionx;
                            link.click()
                            this.loading=false;
                        }) .catch(function (error) {
                        alertify.error("Error Descargando archivo.....",5);
                        self.loading=false;
                    })
                }else{
                    alertify.warning("Espere a que se complete la descarga anterior",5);
                }
            },
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
            obtenerSalida() {
                var self = this;
                salida.obtenerSalida(
                    {
                        id_salida: this.$route.params.id_salida
                    },
                    data => {
                        self.form = data.salida;
                        self.items = self.form.salida_productos;
                        self.nombre_empresa = data.nombre_empresa;
                        self.direccion_empresa = data.direccion_empresa;
                        self.telefono_empresa = data.telefono_empresa;
                        self.loading = false;
                    },
                    err => {
                        console.log(err);
                        self.loading = false;
                    }
                );
            },
            printWindow : function () {
                window.print();
            }
        },
        computed: {

            total_cantidad_saliente() {
                return this.form.salida_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_saliente))
                }, 0)
            },

            total_cantidad_despachada() {
                return this.form.salida_productos.reduce((carry, item) => {
                    return (carry + Number(item.cantidad_despachada))
                }, 0)
            },

            total_subt() {
                return this.form.salida_productos.reduce((carry, item) => {
                        return(carry + Number((Number(item.cantidad_despachada * item.precio_unitario).toFixed(2))));
                    }
                    , 0)
            },
            gran_total() {
                return this.form.salida_productos.reduce((carry, item) => {
                        return(carry + Number((Number(item.cantidad_despachada * item.precio_unitario).toFixed(2))));
                    }
                    , 0)
            },
        },
        mounted() {
            this.obtenerSalida();
        },
        setup() {
            const invoiceData = ref(null)
            const paymentDetails = ref({})

            // Invoice Description
            // ? Your real data will contain this information
            const invoiceDescription = [
                {
                    taskTitle: 'Native App Development',
                    taskDescription: 'Developed a full stack native app using React Native, Bootstrap & Python',
                    rate: '$60.00',
                    hours: '30',
                    total: '$1,800.00',
                },
                {
                    taskTitle: 'UI Kit Design',
                    taskDescription: 'Designed a UI kit for native app using Sketch, Figma & Adobe XD',
                    rate: '$60.00',
                    hours: '20',
                    total: '$1200.00',
                },
            ]

            const printInvoice = () => {
                window.print()
            }

            /*            return {
                            invoiceData,
                            paymentDetails,
                            invoiceDescription,
                            printInvoice,
                        }*/
        },
    }
</script>

<style lang="scss" scoped>
    @import "~@core/scss/base/pages/app-invoice.scss";
</style>

<style lang="scss">
    @media print {

        // Global Styles
        body {
            background-color: transparent !important;
        }
        nav.header-navbar {
            display: none;
        }
        .main-menu {
            display: none;
        }
        .header-navbar-shadow {
            display: none !important;
        }
        .content.app-content {
            margin-left: 0;
            padding-top: 2rem !important;
        }
        footer.footer {
            display: none;
        }
        .card {
            background-color: transparent;
            box-shadow: none;
        }
        .customizer-toggle {
            display: none !important;
        }

        // Invoice Specific Styles
        .invoice-preview-wrapper {
            .row.invoice-preview {
                .col-md-8 {
                    max-width: 100%;
                    flex-grow: 1;
                }

                .invoice-preview-card {
                    .card-body:nth-of-type(2) {
                        .row {
                            > .col-12 {
                                max-width: 50% !important;
                            }

                            .col-12:nth-child(2) {
                                display: flex;
                                align-items: flex-start;
                                justify-content: flex-end;
                                margin-top: 0 !important;
                            }
                        }
                    }
                }
            }

            // Action Right Col
            .invoice-actions {
                display: none;
            }
        }
    }
</style>
