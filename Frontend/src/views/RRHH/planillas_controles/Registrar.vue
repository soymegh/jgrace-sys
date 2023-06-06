<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Control de planillas</div>
						<div class="box-description">Formulario para registrar planilla</div>
					</div>
						<div class="row">

							<!--<div class="col-sm-3">
								<div class="form-group">
									<label for="">Sucursal</label>
									<v-select
											:options="sucursales"
											label="descripcion"
											v-model="form.sucursal"
									></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.sucursal" v-text="message"></li>
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
											v-model="form.anio"
											:options="periodos"
											v-on:input="obtenerMeses"
									></v-select>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label class="label-search">Mes:</label>
									<v-select :style="'margin-left: .5rem!important;'"
											  label="descripcion"
											  v-model="form.mes"
											  :options="meses"
											  v-on:input="cambiarFechas"
									></v-select>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label for>Fecha desde</label>
									<datepicker :disabled-dates="disabledDates" :format="format" :disabled="false" :language="es" :value="new Date()" v-model="f_desdex" @selected="onDateSelectFechaDesde"></datepicker>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.f_desdex" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for>Fecha hasta</label>
									<datepicker :disabled-dates="disabledDates" :format="format" :disabled="false" :language="es" :value="new Date()" v-model="f_hastax" @selected="onDateSelectFechaHasta"></datepicker>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.f_hastax" v-text="message"></li>
									</ul>
								</div>
							</div>
						</div>


					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-planilla-control' }" tag="button">
							<button class="btn btn-default" type="button">Cancelar</button>
						</router-link>
						<button :disabled="btnAction != 'Registrar' ? true : false" @click="registrar" class="btn btn-primary" type="button">{{ btnAction }}</button>
					</div>
					<template v-if="loading">
						<BlockUI :message="msg" :url="url"></BlockUI>
					</template>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/ecmascript-6">
	import planilla from '../../api/planillas_controles.js'
	import trabajador from '../../api/trabajadores'
	import loadingImage from '../../assets/images/block50.gif'
	import es from "vuejs-datepicker/dist/locale/translations/es";

	var fecha_actual = new Date();
	fecha_actual.setHours(23,59,59,999);


	export default {
		data() {
			return {
				disabledDates: {
					to: '',//new Date(2020, 0, 1), // Disable all dates up to specific date 31/12/2019
					from: fecha_actual, // Disable all dates after specific date 01/02/2020
				},
				loading:false,
				msg: 'Cargando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				form: {
					sucursal: [],
					tipo_planilla: 'Q',
					codigo_planilla: '',
					descripcion: '',
					f_desde : moment(new Date()).format("YYYY-MM-DD"),
					f_hasta : moment(new Date()).format("YYYY-MM-DD"),
					anio:0,
					mes:0,
				},
				f_desdex : new Date(),
				f_hastax : new Date(),
				sucursales:[],
				periodos:[],
				meses:[],
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		methods: {
			cambiarFechas(){

				let self = this;


				self.disabledDates.to = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);

				self.disabledDates.from = new Date(self.form.mes.ultimo_dia_mes);
				self.disabledDates.from.setTime( self.disabledDates.from.getTime() + 86400000 );

				self.f_desdex = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.f_hastax = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);

				self.form.f_desde = moment(self.f_desdex).format("YYYY-MM-DD"); //
				self.form.f_hasta = moment(self.f_hastax).format("YYYY-MM-DD"); //


			},
			nuevo(){
				var self = this
				self.loading = true;
				planilla.nueva({}, data => {
					self.sucursales = data.sucursales;
					self.sucursal = data.sucursales[0];
					self.periodos = data.periodos;
					self.form.anio = self.periodos[0];
					self.meses = self.form.anio.meses_periodo;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			obtenerMeses(){
				let self = this;
				self.form.mes = [];
				self.meses = self.form.anio.meses_periodo
				self.form.mes = self.meses[0]
			},
			onDateSelectFechaDesde(date) {
				//console.log(date); //
				this.form.f_desde = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelectFechaHasta(date) {
				//console.log(date); //
				this.form.f_hasta = moment(date).format("YYYY-MM-DD"); //
			},
			registrar() {
				var self = this
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				planilla.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-planilla-control'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			},
		},
		mounted() {
			this.nuevo();
		}
	}
</script>