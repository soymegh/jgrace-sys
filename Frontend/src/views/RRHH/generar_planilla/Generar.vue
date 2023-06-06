<template>
    <div class="main">

        <div class="content-box">
            <div class="content-box-header">
                <div class="box-title">Generar planilla</div>
                <div class="box-description">Formulario para generar planilla</div>
            </div>


                <div class="alert alert-success">
                    <span><strong>Datos de la planilla</strong></span>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                        <label class="label-search">Planilla:</label>
                        <v-select label="descripcion" v-model="planilla_control" :options="planillas_controles" v-on:change="obtenerplanilla"></v-select>
                        </div>
                    </div>

                    <!--<div class="col-sm-4">
                        <div class="form-group">
                            <label for=""> Tipo:</label>
                            <select class="form-control" v-model.number="id_nomina">
                                <option value="1">Personal</option>
                                <option value="2">Impulsadoras</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.id_nomina" v-text="message"></li>
                            </ul>
                        </div>
                    </div>-->

                </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="content-box-footer text-right">
                        <router-link :to="{ name: 'pagina-principal-nomina' }" tag="button">
                            <button class="btn btn-default" type="button">Cancelar</button>
                        </router-link>
                        <button :disabled="btnAction !== 'Generar'" @click="registrar"
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
                id_nomina:1,
                planilla_control:[],
                planilla:[],
                estadox:0,

                es: es,
                format: "dd-MM-yyyy",

                btnAction: 'Generar',
                errorMessages: []
            }
        },
        methods: {
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
            obtenerplanilla() {
                var self = this
                self.loading=true;
                self.planilla=[];
                if(self.planillas_controles){ /* && self.id_nomina parametro para obtener planilla según el tipo*/
                planilla_control.obtenerPlanilla({
                    id_planilla_control: self.planilla_control.id_planilla_control,
                    //id_nomina:self.id_nomina,
                }, data => {
                    self.planilla = data.planilla;
					self.loading = false;
                }, err => {
                    alertify.error(err.id_planilla_control[0], 5);
                    this.$router.push({
                        name: 'pagina-principal-nomina'
                    });
                })
                }else{
                    self.loading=false;
                }
            },

            registrar() {
                var self = this;
                var txtGenerar = '¿Está seguro de generar esta planilla?';
                var txtReGenerar = '¿Está seguro de sobreescribir esta planilla?';
                var txtAnular = '¿Está seguro cancelar la operación?';
                self.btnAction = 'Guardando, espere......'
                if(self.planilla.length){

                if(self.planilla_control.id_planilla_control){
                    self.$swal.fire({
                        title: 'Confirmación de generación de planilla',
                        text: self.planilla_control.estado === 1 ? txtGenerar : self.planilla_control.estado === 2 ? txtReGenerar : txtAnular,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, confirmar',
                        cancelButtonText:'Cancelar'
                    }).then((result) => {
                        if (result.value) {
                            self.loading = true;
                            planilla_control.registrar(
                                {
                                    planilla: self.planilla,
                                    id_planilla_control:self.planilla_control.id_planilla_control,
                                },
                                data => {
                                    alertify.success("Planilla generada correctamente", 5);
                                    this.$router.push({
                                        name: 'pagina-principal-nomina'
                                    })
                                }, err => {
                                    self.loading = false;
                                    self.errorMessages = err
                                    self.btnAction = 'Generar'
                                })
                        }else{
                            self.loading = false;
                            self.btnAction = "Generar";
                        }
                    })
                }else{
                    alertify.warning("Por favor revise si ha seleccionado una planilla valida", 5);
                    self.loading = false;
                    self.btnAction = 'Generar'
                }

                }else{
                    alertify.warning("El detalle de activos debe contener al menos un elemento", 5);
                    self.loading = false;
                    self.btnAction = 'Generar'
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
            this.nuevo()
        }
    }
</script>