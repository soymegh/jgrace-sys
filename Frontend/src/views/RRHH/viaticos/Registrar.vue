<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Crear nuevo viatico</div>
						<div class="box-description">Formulario para registrar viatico</div>
					</div>
					<div class="row">

					<div class="col-sm-4">
						<div class="form-group">
							<label for=""> Descripción</label>
							<input class="form-control" placeholder="Dígite una descripción" v-model="form.descripcion">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
							</ul>
						</div>
					</div>

						<div class="col-sm-4">
							<div class="form-group">
								<label for="">Normativa</label>
								<v-select :options="normativas" label="concepto" v-model="form.normativa"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.normativa" v-text="message"></li>
								</ul>
							</div>
						</div>

					<div class="col-sm-4">
						<div class="form-group">
							<label for=""> Monto</label>
							<input @change="form.monto = Math.max(Math.min(Math.round(form.monto), Number(form.normativa.monto_maximo)), Number(form.normativa.monto_minimo))" class="form-control" type="number" placeholder="Dígite un monto" v-model="form.monto">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.monto" v-text="message"></li>
							</ul>
						</div>
					</div>
					</div>

					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-viaticos' }" tag="button">
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
	import viatico from '../../api/viaticos'
	import normativa from '../../api/normativas'
	import loadingImage from '../../assets/images/block50.gif'
	import sucursal from "../../api/sucursales";
	import cliente from "../../api/clientes";

	export default {
		data() {
			return {
				loading:false,
				msg: 'Guardando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				form: {
					descripcion: '',
					normativa: [],
					monto: 0,
				},
				normativas:[],
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		methods: {

			nuevo(){
				var self = this
				viatico.nuevo({}, data => {
					self.normativas = data.normativas;
				}, err => {
					console.log(err)
				})
			},
			registrar() {
				var self = this
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				viatico.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-viaticos'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			}
		},
		mounted() {
			this.nuevo();
		}
	}
</script>