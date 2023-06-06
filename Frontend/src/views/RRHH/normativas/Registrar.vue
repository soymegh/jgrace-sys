<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Crear nueva normativa</div>
						<div class="box-description">Formulario para registrar normativa</div>
					</div>
					<form>
						<div class="row">
							<div class="col-sm-4">
						<div class="form-group">
							<label for=""> Concepto</label>
							<input class="form-control" placeholder="Dígite un concepto" v-model="form.concepto">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.concepto" v-text="message"></li>
							</ul>
						</div>
							</div>
							<div class="col-sm-4">
						<div class="form-group">
							<label for=""> Monto mínimo</label>
							<input class="form-control" type="number" placeholder="Dígite un monto minimo" v-model="form.monto_minimo">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.monto_minimo" v-text="message"></li>
							</ul>
						</div>
							</div>
							<div class="col-sm-4">
						<div class="form-group">
							<label for=""> Monto máximo</label>
							<input class="form-control" type="number" placeholder="Dígite un monto maximo" v-model="form.monto_maximo">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.monto_maximo" v-text="message"></li>
							</ul>
						</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
							<div class="form-group">
								<label for=""> Regulación de tiempo</label>
								<input class="form-control" placeholder="Describir regulación de tiempo para la normativa" v-model="form.tiempo">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.tiempo" v-text="message"></li>
								</ul>
							</div>
							</div>
						</div>
					</form>
					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-normativas' }" tag="button">
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
	import normativa from '../../api/normativas'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				loading:false,
				msg: 'Guardando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				form: {
					concepto: '',
					monto_maximo: 0,
					monto_minimo: 0,
					tiempo: '',
				},
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		methods: {
			registrar() {
				var self = this
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				normativa.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-normativas'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			}
		}
    }
</script>