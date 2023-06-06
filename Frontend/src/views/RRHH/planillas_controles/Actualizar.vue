<template>
	<div class="main">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Control de planillas</div>
						<div class="box-description">Formulario para registrar planilla</div>
					</div>
					<div class="row">

					<!--	<div class="col-sm-3">
							<div class="form-group">
								<label for="">Sucursal</label>
								<v-select
										:options="sucursales"
										label="descripcion"
										v-model="form.planilla_control_sucursal"
								></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.planilla_control_sucursal" v-text="message"></li>
								</ul>
							</div>
						</div>-->

						<div class="col-sm-3">
							<div class="form-group">
								<label for=""> Tipo de planilla</label>
								<select :disabled="false" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="form.tipo_planilla">
									<option value="Q">Quincenal</option>
									<option value="M">Mensual</option>
								</select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.monto" v-text="message"></li>
								</ul>
							</div>
						</div>


						<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Descripción</label>
								<input class="form-control" :disabled="false" type="text" min="0" v-model="form.descripcion">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="label-search">Año:</label>
								<v-select
										label="periodo"
										v-model="form.anio_proceso"
										:options="periodos"
										v-on:input="obtenerMeses"
								></v-select>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<label class="label-search">Mes:</label>
								<v-select :style="'margin-left: .5rem!important;'"
										  label="mes_letras"
										  v-model="form.mes_proceso"
										  :options="meses"
										  v-on:input="cambiarFechas"
								></v-select>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha desde</label>
								<datepicker :disabled-dates="disabledDates" :format="format" :language="es" v-model="f_desdex" @selected="onDateSelectFechaDesde"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.f_desdex" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha hasta</label>
								<datepicker :disabled-dates="disabledDates" :format="format" :language="es" v-model="f_hastax" @selected="onDateSelectFechaHasta"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.f_hastax" v-text="message"></li>
								</ul>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="content-box-footer text-right">
								<router-link :to="{ name: 'listado-planilla-control' }" tag="button">
									<button class="btn btn-default" type="button">Cancelar</button>
								</router-link>
								<template v-if="form.estado ===1">
								<button :disabled="btnAction != 'Guardar' ? true : false" @click="actualizar(1)" class="btn btn-primary" type="button">{{ btnAction }}</button>
								<!--<button :disabled="btnActionConf !== 'Confirmar' " @click="actualizar(2)" class="btn btn-success" type="button">{{ btnActionConf }}</button>-->
								</template>
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
	import solicitud from '../../api/asignacion-ingreso-deduccion'
	import loadingImage from '../../assets/images/block50.gif'
	import es from "vuejs-datepicker/dist/locale/translations/es";
	import planilla from "../../api/planillas_controles";

	var fecha_actual = new Date();
	fecha_actual.setHours(23,59,59,999);

	export default {
		data() {
			return {
				disabledDates: {
					to: '',//new Date(2020, 0, 1), // Disable all dates up to specific date 31/12/2019
					from: fecha_actual, // Disable all dates after specific date 01/02/2020
				},
				loading: true,
				msg: 'Cargando contenido, espere un momento',
				url: '/public' + loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				form: {
					planilla:[],
					sucursal: [],
					tipo_planilla: 'Q',
					codigo_planilla: '',
					descripcion: '',
					f_desde : '',
					f_hasta : '',
					anio:0,
					mes:0,
				},
				f_desdex : new Date(),
				f_hastax : new Date(),
				sucursales:[],
				periodos:[],
				meses:[],
				btnAction: 'Guardar',
				btnActionConf: 'Confirmar',
				errorMessages: []
			}
		},
		methods: {
			cambiarFechas(){

				let self = this;


				self.disabledDates.to = new Date(self.form.anio_proceso.periodo, self.form.mes_proceso.mes-1, 1);

				self.disabledDates.from = new Date(self.form.mes_proceso.ultimo_dia_mes);
				self.disabledDates.from.setTime( self.disabledDates.from.getTime() + 86400000 );

				self.f_desdex = new Date(self.form.anio_proceso.periodo, self.form.mes_proceso.mes-1, 1);
				self.f_hastax = new Date(self.form.anio_proceso.periodo, self.form.mes_proceso.mes-1, 1);

				self.form.f_desde = moment(self.f_desdex).format("YYYY-MM-DD"); //
				self.form.f_hasta = moment(self.f_hastax).format("YYYY-MM-DD"); //


			},
			nuevo(){
				var self = this
				self.loading = true;
				planilla.nueva({}, data => {
					self.sucursales = data.sucursales;
					self.sucursal = data.sucursales[1];
					self.periodos = data.periodos;
					//self.form.anio_proceso = self.periodos[0];
					self.meses = self.form.anio_proceso.meses_periodo;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			obtenerMeses(){
				let self = this;
				self.form.mes_proceso = [];
				self.meses = self.form.anio_proceso.meses_periodo
				self.form.mes_proceso = self.meses[0]
			},
			onDateSelectFechaDesde(date) {
				//console.log(date); //
				this.form.f_desde = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelectFechaHasta(date) {
				//console.log(date); //
				this.form.f_hasta = moment(date).format("YYYY-MM-DD"); //
			},
			obtenerPlanilla() {
				var self = this
				//Array(1,2,3).includes(Number(self.$route.params.id_zona)) ? self.SoloLectura = true : self.SoloLectura = false
				planilla.obtenerPlanillaControl({
					id_planilla_control: this.$route.params.id_planilla_control
				}, data => {
					self.form = data.planilla;
					self.form.anio_proceso = data.planilla.planilla_periodo_proceso;
					self.form.mes_proceso = data.planilla.planilla_mes_proceso;
					self.sucursales = data.sucursales;
					self.f_desdex = data.planilla.f_desde;
					self.f_hastax = data.planilla.f_hasta;
					self.loading = false;
				}, err => {
					alertify.error(err.id_planilla_control[0],5);
					this.$router.push({
						name: 'listado-planilla-control'
					});
				})
			},
		actualizar(estado) {
				var self = this
			self.loading = true;
				self.btnAction = 'Guardando, espere......'
				self.btnActionConf = 'Guardando, espere......'
				self.form.estado = estado;
					planilla.actualizar(self.form, data => {
					alertify.success("Datos actualizados correctamente",5);
					this.$router.push({
						name: 'listado-planilla-control'
					})
				}, err => {
						self.loading = false;
						self.errorMessages = err
                   		self.btnAction = 'Guardar'
						self.btnActionConf = 'Confirmar'
				})
			},
			desactivar(zonaId) {
				var self = this;
				self.$swal.fire({
					title: 'Esta seguro de cambiar el estado?',
					text: "",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Si, confirmar',
					cancelButtonText:'Cancelar'
				}).then((result) => {
					if (result.value) {
						//var self = this
						planilla.desactivar({
							id_feriado: zonaId
						}, data => {
							alertify.warning("Día feriado desactivado correctamente", 5);
							this.$router.push({
								name: 'listado-planilla-control'
							})
						}, err => {
							console.log(err)
						})
					}else{
						self.btnAction = "Guardar";
					}
				})
			},
			activar(zonaId) {
				var self = this;
				self.$swal.fire({
					title: 'Esta seguro de cambiar el estado?',
					text: "",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Si, confirmar',
					cancelButtonText:'Cancelar'
				}).then((result) => {
					if (result.value) {
						var self = this
						planilla.activar({
							id_feriado: zonaId
						}, data => {
							alertify.success("Nivel estudio activado correctamente", 5);
							this.$router.push({
								name: 'listado-planilla-control'
							})
						}, err => {
							console.log(err)
						})
					}else{
						self.btnAction = "Guardar";
					}
				})
			}
		},
		mounted() {
			this.obtenerPlanilla();
			this.nuevo();
		}
    }
</script>