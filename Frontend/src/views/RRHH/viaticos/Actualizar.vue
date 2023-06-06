<template>
	<div class="main">
	
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Modificar viatico</div>
						<div class="box-description">Formulario para modificar viaticos</div>
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
								<v-select :options="normativas" label="concepto" v-model="form.normativa_viatico"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.normativa_viatico" v-text="message"></li>
								</ul>
							</div>
						</div>

						<div class="col-sm-4">
							<div class="form-group">
								<label for=""> Monto</label>
								<input @change="form.monto = Math.max(Math.min(Math.round(form.monto), Number(form.normativa_viatico.monto_maximo)), Number(form.normativa_viatico.monto_minimo))" class="form-control" type="number" placeholder="Dígite un monto" v-model="form.monto">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.monto" v-text="message"></li>
								</ul>
							</div>
						</div>
				</div>

					<div class="row">


						<div class="col-md-6">
							<div class="content-box-footer text-left">
								<template v-if="form.estado">
									<button @click="desactivar(form.id_viatico)" class="btn btn-danger"><i class="fa fa-trash-o">	Inhabilitar</i></button>
								</template>
								<template v-else>
									<button @click="activar(form.id_viatico)" class="btn btn-success"><i class="fa fa-check-square">	Habilitar</i></button>
								</template>
							</div>
						</div>

						<div class="col-md-6">
							<div class="content-box-footer text-right">
								<router-link :to="{ name: 'listado-viaticos' }" tag="button">
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
	import viatico from '../../api/viaticos.js'
	import loadingImage from '../../assets/images/block50.gif'

	export default {
		data() {
			return {
				loading:true,
				msg: 'Cargando contenido, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				form: {
					descripcion: '',
					normativa_viatico: [],
					monto: 0,
				},
				normativas:[],
				btnAction: 'Guardar',
				errorMessages: []
			}
		},
		methods: {
			obtenerViatico() {
				var self = this
				//Array(1,2,3).includes(Number(self.$route.params.id_zona)) ? self.SoloLectura = true : self.SoloLectura = false
				viatico.obtenerViatico({
					id_viatico: this.$route.params.id_viatico
				}, data => {
					self.normativas = data.normativas;
					self.form = data.viatico
					self.loading = false;
				}, err => {
					alertify.error(err.id_viatico[0],5);
					this.$router.push({
						name: 'listado-viaticos'
					});
				})
			},
		actualizar() {
				var self = this
			self.loading = true;
				self.btnAction = 'Guardando, espere......'
					viatico.actualizar(self.form, data => {
					alertify.success("Datos actualizados correctamente",5);
					this.$router.push({
						name: 'listado-viaticos'
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
						viatico.desactivar({
							id_viatico: zonaId
						}, data => {
							alertify.warning("Viatico desactivado correctamente", 5);
							this.$router.push({
								name: 'listado-viaticos'
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
						viatico.activar({
							id_viatico: zonaId
						}, data => {
							alertify.success("Viatico activado correctamente", 5);
							this.$router.push({
								name: 'listado-viaticos'
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
			this.obtenerViatico();
		}
    }
</script>