<template>
    <div class="main">

        <div class="content-box">
            <div class="content-box-header">
                <div class="box-title">Procesar planilla</div>
                <div class="box-description">Formulario para procesar planilla</div>
            </div>


                <div class="alert alert-success">
                    <span><strong>Datos de la planilla</strong></span>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="label-search">Código:</label>
                            <v-select label="codigo_planilla" v-model="control_planilla"  :disabled="true"></v-select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                        <label class="label-search">Planilla:</label>
                        <v-select label="descripcion" v-model="control_planilla"  :disabled="true"></v-select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="label-search">Año:</label>
                            <v-select label="anio_proceso" v-model="control_planilla"  :disabled="true"></v-select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="label-search">Mes:</label>
                            <v-select label="mes_proceso" v-model="control_planilla"  :disabled="true"></v-select>
                        </div>
                    </div>

                </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="content-box-footer text-right">
                        <router-link :to="{ name: 'pagina-principal-nomina' }" tag="button">
                            <button class="btn btn-default" type="button">Cancelar</button>
                        </router-link>
                        <button :disabled="btnAction !== 'Procesar'" @click="registrar"
                                class="btn btn-primary" type="button">{{ btnAction }}
                        </button>
                    </div>
                </div>
                <template v-if="loading">
                    <BlockUI :message="msg" :url="url"></BlockUI>
                </template>
            </div>
        </div>
    </div>


</template>

<script type="text/ecmascript-6">
    import planilla_control from '../../api/generar_planilla.js'
    import loadingImage from '../../assets/images/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import { SweetModal } from 'sweet-modal-vue';
    import activo_fijo from "../../api/activos_fijos";
    export default {
        components: {
            SweetModal
        },
        data() {
            return {
                loading:true,
                msg: 'Cargando los datos, espere un momento',
                url : '/public'+loadingImage,   //It is important to import the loading image then use here

                planillas_controles:[],
                control_planilla:[],
                planilla:[],

                es: es,
                format: "dd-MM-yyyy",

                btnAction: 'Procesar',
                errorMessages: []
            }
        },
        methods: {
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
            obtenerplanilla() {
                var self = this
                planilla_control.obtenerPlanillaProcesar({
                    id_planilla_control: self.$route.params.id_planilla_control
                }, data => {
                    self.planilla = data.planilla;
                    self.control_planilla = data.control_planilla;
                    self.loading = false;
                }, err => {
                    alertify.error(err.id_planilla_control[0], 5);
                    this.$router.push({
                        name: 'pagina-principal-nomina'
                    });
                })


            },

            registrar() {
                var self = this
                self.btnAction = 'Guardando, espere......'
                if(self.planilla.length){

                if(self.control_planilla.id_planilla_control){
                    self.$swal.fire({
                        title: '¿Está seguro de procesar esta planilla?',
                        text: "Se registrarán los cambios",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, confirmar',
                        cancelButtonText:'Cancelar'
                    }).then((result) => {
                        if (result.value) {
                            self.loading = true;
                            planilla_control.procesar(
                                {
                                    planilla: self.planilla,
                                    id_planilla_control:self.control_planilla.id_planilla_control,
                                },
                                data => {
                                    alertify.success("Planilla procesada correctamente", 5);
                                    this.$router.push({
                                        name: 'pagina-principal-nomina'
                                    })
                                }, err => {
                                    self.loading = false;
                                    self.errorMessages = err
                                    self.btnAction = 'Procesar'
                                })
                        }else{
                            self.loading = false;
                            self.btnAction = "Procesar";
                        }
                    })
                }else{
                    alertify.warning("Por favor revise si ha seleccionado una planilla valida", 5);
                    self.loading = false;
                    self.btnAction = 'Procesar'
                }

                }else{
                    alertify.warning("El detalle de activos debe contener al menos un elemento", 5);
                    self.loading = false;
                    self.btnAction = 'Procesar'
                }
            },
            nuevo() {
                var self = this;
                planilla_control.nuevo({}, data => {
                        self.planillas_controles = data.planillas_controles;
                        self.loading = false;
                    },
                    err => {
                        console.log(err);
                    })

            },
        },
        mounted() {
            this.nuevo();
            this.obtenerplanilla();
        }
    }
</script>