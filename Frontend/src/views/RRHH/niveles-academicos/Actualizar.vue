<template>
	<div class="main">
	
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Modificar nivel academico</div>
						<div class="box-description">Formulario para modificar nivel academico</div>
					</div>
					<form>
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
									<label for=""> Abreviatura</label>
									<input class="form-control" placeholder="Dígite una abreviatura" v-model="form.abreviatura">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.abreviatura" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for=""> Acreditación</label>
									<input class="form-control" placeholder="Dígite una acreditación" v-model="form.acreditacion">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.acreditacion" v-text="message"></li>
									</ul>
								</div>
							</div>
						</div>
					</form>

					<div class="row">


						<div class="col-md-6">
							<div class="content-box-footer text-left">
								<template v-if="form.estado">
									<button @click="desactivar(form.id_nivel_academico)" class="btn btn-danger"><i class="fa fa-trash-o">	Eliminar</i></button>
								</template>
								<template v-else>
									<button @click="activar(form.id_nivel_academico)" class="btn btn-success"><i class="fa fa-check-square">	Habilitar</i></button>
								</template>
							</div>
						</div>

						<div class="col-md-6">
							<div class="content-box-footer text-right">
								<router-link :to="{ name: 'listado-niveles-academicos' }" tag="button">
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
	import niveles from '../../api/niveles_academicos'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				loading:true,
				msg: 'Cargando contenido, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				form: {
					descripcion: '',
				},
				btnAction: 'Guardar',
				errorMessages: []
			}
		},
		methods: {
			obtenerZona() {
				var self = this
				//Array(1,2,3).includes(Number(self.$route.params.id_zona)) ? self.SoloLectura = true : self.SoloLectura = false
				niveles.obtenerNivelAcademico({
					id_nivel_academico: this.$route.params.id_nivel_academico
				}, data => {
					self.form = data
					self.loading = false;
				}, err => {
					alertify.error(err.id_nivel_academico[0],5);
					this.$router.push({
						name: 'listado-niveles-academicos'
					});
				})
			},
		actualizar() {
				var self = this
			self.loading = true;
				self.btnAction = 'Guardando, espere......'
					niveles.actualizar(self.form, data => {
					alertify.success("Datos actualizados correctamente",5);
					this.$router.push({
						name: 'listado-niveles-academicos'
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
						niveles.desactivar({
							id_nivel_academico: zonaId
						}, data => {
							alertify.warning("Nivel academico desactivado correctamente", 5);
							this.$router.push({
								name: 'listado-niveles-academicos'
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
						niveles.activar({
							id_nivel_academico: zonaId
						}, data => {
							alertify.success("Nivel academico activado correctamente", 5);
							this.$router.push({
								name: 'listado-niveles-academicos'
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
			this.obtenerZona()
		}
    }
</script>