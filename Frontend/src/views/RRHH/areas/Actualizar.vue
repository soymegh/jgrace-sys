<template>
	<div class="main">
	
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Modificar Área</div>
						<div class="box-description">Formulario para modificar Área</div>
					</div>
					<form>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label for=""> Código</label>
									<input disabled class="form-control" placeholder="Dígite el codigo de area" v-model="form.codigo">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.codigo" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label for="">Dirección</label>
									<v-select
											:disabled="true"
											:options="direcciones"
											label="descripcion"
											v-model="form.direccion_area"
									></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.direccion" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="form-group">
									<label for=""> Descripción</label>
									<input class="form-control" placeholder="Dígite el nombre del area" v-model="form.descripcion">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
									</ul>
								</div>
							</div>


							<!--<div class="col-sm-4">
								<div class="form-group">
									<label for="">Cuenta Contable</label>
									<v-select
											:options="cuentas_contables"
											label="nombre_cuenta_completo"
											v-model="form.cuenta_contable_area"
									></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.cuenta_contable" v-text="message"></li>
									</ul>
								</div>
							</div>-->

						</div>
					</form>

					<div class="row">
						<div class="col-md-6">
							<div class="content-box-footer text-left">
								<template v-if="form.activo==1">
									<button @click="desactivar(form.id_area)" class="btn btn-danger"><i class="fa fa-trash-o">	Eliminar</i></button>
								</template>
								<template v-else>
									<button @click="activar(form.id_area)" class="btn btn-success"><i class="fa fa-check-square">	Habilitar</i></button>
								</template>
							</div>
						</div>

						<div class="col-md-6">
							<div class="content-box-footer text-right">
								<router-link :to="{ name: 'listado-areas' }" tag="button">
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
	import area from '../../api/areas'
	import direccion from '../../api/direcciones'
	import cuentas_contables from '../../api/cuentas_contables'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				loading:true,
				msg: 'Cargando contenido, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				cuentas_contables:[],
				direcciones:[],
				form: {
					area: '',
					direccion_area:'',
					cuenta_contable_area:''
				},
				btnAction: 'Guardar',
				errorMessages: []
			}
		},
		methods: {
			obtenerTodasCuentasContables() {
				var self = this;
				cuentas_contables.obtenerTodasCuentasContables(
						data => {
							//self.form.cuenta_contable_area = data;
							self.cuentas_contables = data;
						},
						err => {
							console.log(err);
						}
				);

			},

			obtenerTodasDirecciones(){
				var self = this;
				direccion.obtenerTodasDirecciones(
						data => {
							//self.form.direccion_area = data;
							self.direcciones = data;

						},
						err => {
							console.log(err);
						}
				);

			},
			obtenerArea() {
				var self = this
				area.obtenerArea({
					id_area: self.$route.params.id_area
				}, data => {
					self.form = data
					self.loading = false;
				}, err => {
					alertify.error(err.id_area[0],5);
           this.$router.push({
            	name: 'listado-areas'
          });
				})

				
			},
			actualizar() {
				var self = this
				self.loading = true;
				self.btnAction = 'Guardando, espere......'
				area.actualizar(self.form, data => {
					alertify.success("Área actualizada correctamente",5);
					this.$router.push({
						name: 'listado-areas'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Guardar'
				})
			},

		desactivar(areaId) {
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
					area.desactivar({
						id_area: areaId
					}, data => {
						alertify.warning("Área desactivada correctamente", 5);
						this.$router.push({
							name: 'listado-areas'
						})
					}, err => {
						console.log(err)
					})
				} else {
					self.btnAction = "Guardar";
				}

				})
		},
		activar(areaId) {

			var self = this;
			self.$swal.fire({
				title: 'Esta seguro de cambiar el estado?',
				text: "",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Si, confirmar',
				cancelButtonText: 'Cancelar'
			}).then((result) => {
				if (result.value) {
					area.activar({
						id_area: areaId
					}, data => {
						alertify.success("Área activada correctamente", 5);
						this.$router.push({
							name: 'listado-areas'
						})
					}, err => {
						console.log(err)
					})
				} else {
					self.btnAction = "Guardar";
				}
			})
		}
		},
		mounted() {
			this.obtenerTodasDirecciones();
			this.obtenerTodasCuentasContables();
			this.obtenerArea();
		}
    }
</script>