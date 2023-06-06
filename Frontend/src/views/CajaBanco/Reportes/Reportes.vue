<template>
    <b-row>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Clientes
                </h3>
                <a :disabled="descargando"  @click.prevent="downloadItemClientes ('pdf')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1 mr-1" variant="danger" v-b-tooltip.hover.top="'PDF'"> Decargar PDF <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
                <a :disabled="descargando"  @click.prevent="downloadItemClientes ('xls')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1" variant="success" v-b-tooltip.hover.top="'XLS'"> Decargar XLS <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Vendedores
                </h3>
                <a :disabled="descargando"  @click.prevent="downloadItemVendedores ('pdf')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1 mr-1" variant="danger" v-b-tooltip.hover.top="'PDF'"> Decargar PDF <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
                <a :disabled="descargando"  @click.prevent="downloadItemVendedores ('xls')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1" variant="success" v-b-tooltip.hover.top="'XLS'"> Decargar XLS <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Cuentas Bancarias
                </h3>
                <a :disabled="descargando"  @click.prevent="downloadItemCuentasBancarias ('pdf')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1 mr-1" variant="danger" v-b-tooltip.hover.top="'PDF'"> Decargar PDF <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
                <a :disabled="descargando"  @click.prevent="downloadItemCuentasBancarias ('xls')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1" variant="success" v-b-tooltip.hover.top="'XLS'"> Decargar XLS <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Tipo Clientes
                </h3>
                <a :disabled="descargando"  @click.prevent="downloadItemTipoClientes ('pdf')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1 mr-1" variant="danger" v-b-tooltip.hover.top="'PDF'"> Decargar PDF <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
                <a :disabled="descargando"  @click.prevent="downloadItemTipoClientes ('xls')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1" variant="success" v-b-tooltip.hover.top="'XLS'"> Decargar XLS <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
            </b-card>
        </div>
        <div class="col-sm-3 col-md-6">
            <b-card>
                <h3>
                    Reporte Bancos
                </h3>
                <a :disabled="descargando"  @click.prevent="downloadItemBancos ('pdf')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1 mr-1" variant="danger" v-b-tooltip.hover.top="'PDF'"> Decargar PDF <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
                <a :disabled="descargando"  @click.prevent="downloadItemBancos ('xls')" style="color: #ffffff;padding-left: 2px"><b-button :disabled="descargando" class="mt-1" variant="success" v-b-tooltip.hover.top="'XLS'"> Decargar XLS <feather-icon icon="DownloadCloudIcon"></feather-icon></b-button></a>
            </b-card>
        </div>
    </b-row>
</template>

<script>
    import axios from "axios";
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
        BFormSelect,
    } from 'bootstrap-vue'
    import loadingImage from '../../../assets/images/loader/block50.gif'
    import vSelect from 'vue-select'
    import bodegas from "../../../api/Inventario/bodegas";
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
                form:{
                    id_bodega: ''
                },
                bodegas: [],
                facturas: [],
                total: 0
            };
        },
        methods:{
            downloadItemClientes(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'cuentas-por-cobrar/clientes/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoClientes.' + extension;
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

            downloadItemVendedores(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'ventas/vendedores/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoVendedores.' + extension;
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
            downloadItemCuentasBancarias(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'contabilidad/cuentas-bancarias/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoCuentasBancarias.' + extension;
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
            downloadItemTipoClientes(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'ventas/tipo-cliente/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoTipoClientes.' + extension;
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
            downloadItemBancos(extension) {
                const self = this;
                if (!this.descargando) {
                    self.loading = true;
                    let url = 'cajabanco/bancos/reporte/' + extension;
                    this.descargando = true;
                    axios.get(url, {responseType: 'blob'})
                        .then(({data}) => {
                            let blob = new Blob([data], {type: 'application/pdf'})

                            extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'FormatoBancos.' + extension;
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
        }
    }
</script>

<style scoped>

</style>