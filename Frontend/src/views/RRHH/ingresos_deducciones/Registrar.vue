<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Ingresos y deducciones</div>
						<div class="box-description">Formulario para administrar ingresos y deducciones</div>
					</div>
					<form>
						<div class="row">
							<div class="col-sm-2">
								<div class="form-group">
									<label for="">Tipo</label>
									<select class="form-control" v-model="form.cve_ingreso_deduccion">
										<option value="I" >Ingreso</option>
										<option value="D" >Deducción</option>
									</select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.cve_ingreso_deduccion" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for=""> Descripción</label>
									<input class="form-control" placeholder="Dígite una descripción" v-model="form.descripcion">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for=""> Orden</label>
									<input class="form-control" placeholder="Dígite una orden"  type="number" min="0" v-model="form.orden">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.orden" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for=""> Abreviación</label>
									<input class="form-control" placeholder="Dígite una abreviación" v-mask="'AAAAAA'" v-model="form.abreviacion">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.abreviacion" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="">Cuenta contable venta</label>
									<v-select
											:options="cuentas_contables"
											label="nombre_cuenta_completo"
											v-model="form.cuenta_contable"
									></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.cuenta_contable" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label for="">Cuenta contable administrativa</label>
									<v-select
											:options="cuentas_contables_administrativa"
											label="nombre_cuenta_completo"
											v-model="form.cuenta_contable_administrativa"
									></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.cuenta_contable_administrativa" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="check-label2"><input type="checkbox"
																	   v-model="form.registro_manual"> Permite registro manual</label>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="check-label2"><input type="checkbox"
																	   v-model="form.grabable"> Grabable de impuesto</label>
								</div>
							</div>

						</div>
					</form>
					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-ingresos' }" tag="button">
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
	import cuenta from '../../api/cuentas_contables'
	import loadingImage from '../../assets/images/block50.gif'
	import ingreso from '../../api/ingresos_deducciones'
	import es from "vuejs-datepicker/dist/locale/translations/es";
	import cuenta_bancaria from "../../api/cuentas_bancarias";

	export default {
		data() {
			return {
				loading:false,
				msg: 'Guardando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				cuentas_contables:[],
				cuentas_contables_administrativa:[],
				form: {
					cve_ingreso_deduccion: '',
					descripcion: '',
					orden: '',
					abreviacion: '',
					id_cuenta_contable: '',
                    grabable: '',
					cuenta_contable: '',
					cuenta_contable_administrativa:'',
					registro_manual:'',
				},
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		methods: {

			nueva() {
				var self = this;
				ingreso.nueva({
						}, data => {
							self.cuentas_contables = data.cuentas_contables;
							self.cuentas_contables_administrativa = data.cuentas_contables;
							self.loading = false;
						},
						err => {
							console.log(err);
						})

			},

			onDateSelect(date) {
				//console.log(date); //
				this.form.fecha_inicio = moment(date).format("YYYY-MM-DD"); //
			},
			registrar() {
				var self = this
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				ingreso.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-ingresos'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			}
		},
		mounted() {
			this.nueva();
		}
    }
</script>
<style>
	.check-label2 {
		margin-top: 40px;
	}
</style>