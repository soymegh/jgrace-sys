<template>
	<div class="main">

		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Modificar ingresos y deducciones</div>
				<div class="box-description">Formulario para modificar ingresos y deducciones</div>
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
							<label for="">Cuenta contable</label>
							<v-select
									:options="cuentas_contables"
									label="nombre_cuenta_completo"
									v-model="form.ingreso_deduccion_cuenta_contable"
							></v-select>
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.ingreso_deduccion_cuenta_contable" v-text="message"></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label for="">Cuenta contable administrativa</label>
							<v-select
									:options="cuentas_contables_administrativa"
									label="nombre_cuenta_completo"
									v-model="form.ingreso_deduccion_cuenta_contable_administrativa"
							></v-select>
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.ingreso_deduccion_cuenta_contable_administrativa" v-text="message"></li>
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
							<label class="check-label2"><input type="checkbox" v-model="form.grabable"> Grabable de impuesto</label>
						</div>
					</div>

				</div>
			</form>

			<div class="row">
				<div class="col-md-6">
					<div class="content-box-footer text-left">
						<template v-if="form.estado===1">
							<button @click="desactivar(form.id_cat_ingreso_deduccion)" class="btn btn-danger"><i
									class="fa fa-trash-o"> Deshabilitar</i></button>
						</template>
						<template v-else>
							<button @click="activar(form.id_cat_ingreso_deduccion)" class="btn btn-success"><i
									class="fa fa-check-square"> Habilitar</i></button>
						</template>
					</div>
				</div>

				<div class="col-md-6">
					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-ingresos' }" tag="button">
							<button class="btn btn-default" type="button">Cancelar</button>
						</router-link>
						<button :disabled="btnAction != 'Guardar' ? true : false" @click="actualizar"
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
	import ingreso from '../../api/ingresos_deducciones'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				loading: true,
				msg: 'Cargando contenido, espere un momento',
				url: '/public' + loadingImage,   //It is important to import the loading image then use here
				cuentas_contables:[],
				cuentas_contables_administrativa:[],
				bancos:[],
				monedas:[],
				form: {
					ingreso: '',
					cve_ingreso_deduccion: '',
					moneda_cuenta_bancaria: '',
					cuenta_contable_cuenta_bancaria: '',
					cuenta_contable_administrativa:'',
					registro_manual:'',
				},
				btnAction: 'Guardar',
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
			obtenerIngresoDeduccion() {
				var self = this
				ingreso.obtenerIngresoDeduccion({
					id_cat_ingreso_deduccion: self.$route.params.id_cat_ingreso_deduccion
				}, data => {
					self.form = data.deduccion;
					self.cuentas_contables = data.cuentas_contables;
					self.loading = false;
				}, err => {
					alertify.error(err.id_cat_ingreso_deduccion[0], 5);
					this.$router.push({
						name: 'listado-ingresos'
					});
				})


			},
			actualizar() {
				var self = this
				self.loading = true;
				self.btnAction = 'Guardando, espere......'
				ingreso.actualizar(self.form, data => {
					alertify.success("Registro actualizado correctamente", 5);
					this.$router.push({
						name: 'listado-ingresos'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
					self.btnAction = 'Guardar'
				})
			},

			desactivar(cuentabancariaId) {

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
						ingreso.desactivar({
							id_cat_ingreso_deduccion: cuentabancariaId
						}, data => {
							alertify.warning("Registro desactivado correctamente", 5);
							this.$router.push({
								name: 'listado-ingresos'
							})
						}, err => {
							console.log(err)
						});
					}else{
						self.btnAction = "Guardar";
					}
				})
			},
			activar(cuentabancariaId) {
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
						ingreso.activar({
							id_cat_ingreso_deduccion: cuentabancariaId
						}, data => {
							alertify.success("Registro activado correctamente", 5);
							this.$router.push({
								name: 'listado-ingresos'
							})
						}, err => {
							console.log(err)
						});
					}else{
						self.btnAction = "Guardar";
					}
				})
			}
		},
		mounted() {
			this.obtenerIngresoDeduccion();
			this.nueva();
		}
	}
</script>
<style>
	.check-label2 {
		margin-top: 40px;
	}
</style>