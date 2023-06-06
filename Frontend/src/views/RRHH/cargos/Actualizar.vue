<template>
	<div class="main">
	
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Modificar Nombre cargo</div>
						<div class="box-description">Formulario para modificar Nombre cargo</div>
					</div>
					<form>
						<div class="form-group">
							<label for=""> Nombre cargo</label>
							<input class="form-control" placeholder="Dígite el Nombre del cargo" v-model="form.descripcion">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
							</ul>
						</div>
					</form>
					<div class="row">


						<div class="col-md-6">
							<div class="content-box-footer text-left">
								<template v-if="form.activo==1">
									<button @click="desactivar(form.id_cargo)" class="btn btn-danger"><i class="fa fa-trash-o">	Eliminar</i></button>
								</template>
								<template v-else>
									<button @click="activar(form.id_cargo)" class="btn btn-success"><i class="fa fa-check-square">	Habilitar</i></button>
								</template>
							</div>
						</div>

						<div class="col-md-6">
							<div class="content-box-footer text-right">
								<router-link :to="{ name: 'listado-cargos' }" tag="button">
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
	import cargo from '../../api/cargos'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				loading:true,
				msg: 'Cargando contenido, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				form: {
					descripcion: ''
				},
				btnAction: 'Guardar',
				errorMessages: []
			}
		},
		methods: {
			obtenerCargo() {
				var self = this
				cargo.obtenerCargo({
					id_cargo: self.$route.params.id_cargo
				}, data => {
					self.form = data
					self.loading = false;
				}, err => {
					 alertify.error(err.id_cargo[0],5);
           this.$router.push({
            	name: 'listado-cargos'
          });
				})

				
			},
			actualizar() {
				var self = this;
				self.loading = true;
				self.btnAction = 'Guardando, espere......';
				cargo.actualizar(self.form, data => {
					alertify.success("Cargo actualizado correctamente",5);
					this.$router.push({
						name: 'listado-cargos'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Guardar'
				})
			},
			desactivar(cargoId) {
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
						cargo.desactivar({
							id_cargo: cargoId
						}, data => {
							alertify.warning("Cargo desactivado correctamente", 5);
							this.$router.push({
								name: 'listado-cargos'
							})
						}, err => {
							console.log(err)
						})
					}else{
						self.btnAction = "Guardar";
					}
				})

			},
			activar(cargoId) {

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
						cargo.activar({
							id_cargo: cargoId
						}, data => {
							alertify.success("Cargo activado correctamente", 5);
							this.$router.push({
								name: 'listado-cargos'
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
			this.obtenerCargo()
		}
    }
</script>