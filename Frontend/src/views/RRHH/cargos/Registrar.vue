<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Registrar Cargo</div>
						<div class="box-description">Formulario para registrar Cargo</div>
					</div>

						<div class="form-group">
							<label for=""> Nombre cargo</label>
							<input class="form-control" placeholder="Dígite el nombre del cargo" v-model="form.descripcion">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
							</ul>
						</div>

					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-cargos' }" tag="button">
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
	import cargo from '../../api/cargos'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				form: {
					descripcion: '',
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
				cargo.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Cargo registrado exitosamente", 5);
					this.$router.push({
						name: 'listado-cargos'
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
