<template>
	<div class="main">
	
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Modificar normativa</div>
						<div class="box-description">Formulario para modificar normativa</div>
					</div>
					<form>
						<div class="form-group">
							<label for=""> Concepto</label>
							<input class="form-control" placeholder="Dígite un concepto" v-model="form.concepto">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.concepto" v-text="message"></li>
							</ul>
						</div>

						<div class="form-group">
							<label for=""> Monto mínimo C$</label>
							<input class="form-control" type="number" placeholder="Dígite un monto minimo" v-model="form.monto_minimo">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.monto_minimo" v-text="message"></li>
							</ul>
						</div>

						<div class="form-group">
							<label for=""> Monto máximo C$</label>
							<input class="form-control" type="number" placeholder="Dígite un monto maximo" v-model="form.monto_maximo">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.monto_maximo" v-text="message"></li>
							</ul>
						</div>

						<div class="form-group">
							<label for=""> Regulación de tiempo</label>
							<input class="form-control" placeholder="Dígite un tiempo especifico" v-model="form.tiempo">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.tiempo" v-text="message"></li>
							</ul>
						</div>
					</form>

					<div class="row">


						<div class="col-md-6">
							<div class="content-box-footer text-left">
								<template v-if="form.estado">
									<button @click="desactivar(form.id_normativa)" class="btn btn-danger"><i class="fa fa-trash-o">	Eliminar</i></button>
								</template>
								<template v-else>
									<button @click="activar(form.id_normativa)" class="btn btn-success"><i class="fa fa-check-square">	Habilitar</i></button>
								</template>
							</div>
						</div>

						<div class="col-md-6">
							<div class="content-box-footer text-right">
								<router-link :to="{ name: 'listado-normativas' }" tag="button">
									<button class="btn btn-default" type="button">Cancelar</button>
								</router-link>
								<button :disabled="btnAction != 'Guardar' ? true : false" @click="actualizar" class="btn btn-primary" type="button">{{ btnAction }}</button>
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
	import normativa from '../../api/normativas'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				loading:true,
				msg: 'Cargando contenido, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				form: {
					concepto: '',
					monto_maximo: 0,
					monto_minimo: 0,
					tiempo: '',
				},
				btnAction: 'Guardar',
				errorMessages: []
			}
		},
		methods: {
			obtenerNormativa() {
				var self = this
				//Array(1,2,3).includes(Number(self.$route.params.id_zona)) ? self.SoloLectura = true : self.SoloLectura = false
				normativa.obtenerNormativa({
					id_normativa: this.$route.params.id_normativa
				}, data => {
					self.form = data
					self.loading = false;
				}, err => {
					alertify.error(err.id_normativa[0],5);
					this.$router.push({
						name: 'listado-normativas'
					});
				})
			},
		actualizar() {
				var self = this
			self.loading = true;
				self.btnAction = 'Guardando, espere......'
					normativa.actualizar(self.form, data => {
					alertify.success("Datos actualizados correctamente",5);
					this.$router.push({
						name: 'listado-normativas'
					})
				}, err => {
						self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Guardar'
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
						normativa.desactivar({
							id_normativa: zonaId
						}, data => {
							alertify.warning("Normativa desactivado correctamente", 5);
							this.$router.push({
								name: 'listado-normativas'
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
						normativa.activar({
							id_normativa: zonaId
						}, data => {
							alertify.success("Normativa activado correctamente", 5);
							this.$router.push({
								name: 'listado-normativas'
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
			this.obtenerNormativa()
		}
    }
</script>