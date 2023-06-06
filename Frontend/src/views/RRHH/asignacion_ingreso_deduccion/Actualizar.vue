<template>
	<div class="main">
	
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Modificar solicitud de vacaciones</div>
						<div class="box-description">Formulario para modificar solicitud de vacaciones </div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Planilla</label>
								<v-select label="planilla" v-model="form.planilla" :options="planillas"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.planilla" v-text="message"></li>
								</ul>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<label for>Ingreso/Deducción</label>
								<v-select label="ingreso" v-model="form.ingreso" :options="ingresos"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.ingreso" v-text="message"></li>
								</ul>
							</div>
						</div>

					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Empleado</label>
								<v-select label="nombre_completo" v-model="form.trabajador" v-on:input="limpiarDetalle()" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.trabajador" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Area</label>
								<v-select label="descripcion" :disabled="true"  v-model="form.trabajador.trabajador_area" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.area" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Cargo</label>
								<v-select label="descripcion" :disabled="true" v-model="form.trabajador.trabajador_cargo" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.cargo" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Monto</label>
								<input class="form-control" :disabled="false" type="number" min="0" v-model="form.monto">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.monto" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for>&nbsp;</label>
								<button @click="agregarDetalle" class="btn btn-info btn-agregar" ref="agregar">Agregar detalle</button>
							</div>
						</div>
					</div>

					<div class="alert alert-success">
						<span><strong>Detalle de asignación</strong></span>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.detalleSolicitud" v-text="message"></li>
							</ul>

							<table class="table table-bordered table-responsive">
								<thead>
								<tr>
									<th></th>
									<!--<th style="min-width:300px" >Tipo de solicitud</th>-->
									<th style="min-width:200px">Ingreso/Deducción</th>
									<th style="min-width:150px">Código</th>
									<th style="min-width:150px">Descripción</th>
									<th style="min-width:150px">Monto</th>
									<th ></th>
								</tr>
								</thead>
								<tbody>
								<template  v-for="(item, index) in form.detalleSolicitud">
									<tr>
										<td style="width: 2%">
											<button @click="eliminarLinea(item, index)">
												<i class="fa fa-trash"></i>
											</button>
										</td>
										<td>
											<v-select label="ingreso" :disabled="true" v-model="item.ingreso" :options="ingresos"></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.ingreso`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.codigo">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.codigo`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" :disabled="true" type="text"  v-model="item.descripcion">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.descripcion`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.monto">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.monto`]" v-text="message"></li>
											</ul>
										</td>
										<!--<td>
                                        <strong> {{sub_total(item) | formatMoney(2)}}</strong>
                                        </td>-->

									</tr>
									<tr></tr>
								</template>
								</tbody>
								<tfoot>
								<tr>
									<!--  <td colspan="2"></td>
                                      <td>Total</td>
                                      <td> <strong> {{calcular_total | formatMoney(2)}}</strong></td> -->
								</tr>
								</tfoot>
							</table>
						</div>
					</div>


					<div class="row">


					<!--	<div class="col-md-6">
							<div class="content-box-footer text-left">
								<template v-if="form.estado">
									<button @click="desactivar(form.id_vacacion_solicitud)" class="btn btn-danger"><i class="fa fa-trash-o">	Eliminar</i></button>
								</template>
								<template v-else>
									<button @click="activar(form.id_vacacion_solicitud)" class="btn btn-success"><i class="fa fa-check-square">	Habilitar</i></button>
								</template>
							</div>
						</div>-->

						<div class="col-md-12">
							<div class="content-box-footer text-right">
								<router-link :to="{ name: 'listado-asignacion-ingreso' }" tag="button">
									<button class="btn btn-default" type="button">Cancelar</button>
								</router-link>
								<template v-if="form.estado ===1">
								<button :disabled="btnAction != 'Guardar' ? true : false" @click="actualizar(1)" class="btn btn-primary" type="button">{{ btnAction }}</button>
								<!--<button :disabled="btnActionConf !== 'Confirmar' " @click="actualizar(2)" class="btn btn-success" type="button">{{ btnActionConf }}</button>-->
								</template>
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
	import solicitud from '../../api/asignacion-ingreso-deduccion'
	import loadingImage from '../../assets/images/block50.gif'
	import es from "vuejs-datepicker/dist/locale/translations/es";

	export default {
		data() {
			return {
				loading: true,
				msg: 'Cargando contenido, espere un momento',
				url: '/public' + loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				form: {
					solicitud_detalle:[],
					num_solicitud: '',
					trabajador: [],
					cantidad_dias: '',
					justificacion: '',
					costo_vacaciones : '',
					total_dias : '',
					saldo_dias : '',
					dias_meses : 30,
					salario_basico : 0,
				},
				trabajadores:[],
				ingresos:[],
				planillas:[],
				btnAction: 'Guardar',
				btnActionConf: 'Confirmar',
				errorMessages: []
			}
		},
		methods: {
			obtenerSolicitud() {
				var self = this
				//Array(1,2,3).includes(Number(self.$route.params.id_zona)) ? self.SoloLectura = true : self.SoloLectura = false
				solicitud.obtenerAsignacion({
					id_cat_ingreso_deduccion_trabajador: this.$route.params.id_cat_ingreso_deduccion_trabajador
				}, data => {
					self.form = data;
					self.trabajadores = data.trabajadores;
					self.ingresos = data.ingresos;
					self.planillas = data.planillas;
					self.loading = false;
				}, err => {
					alertify.error(err.id_cat_ingreso_deduccion_trabajador[0],5);
					this.$router.push({
						name: 'listado-asignacion-ingreso'
					});
				})
			},
		actualizar(estado) {
				var self = this
			self.loading = true;
				self.btnAction = 'Guardando, espere......'
				self.btnActionConf = 'Guardando, espere......'
				self.form.estado = estado;
					solicitud.actualizar(self.form, data => {
					alertify.success("Datos actualizados correctamente",5);
					this.$router.push({
						name: 'listado-vacaciones'
					})
				}, err => {
						self.loading = false;
						self.errorMessages = err
                   		self.btnAction = 'Guardar'
						self.btnActionConf = 'Confirmar'
				})
			},
			agregarDetalle() {
				let self = this;
				if(this.form.cantidad_dias){
					let i = 0;
					let keyx = 0;
					if(self.form.solicitud_detalle){
						self.form.solicitud_detalle.forEach((fila, key) => {
							if(self.form.cantidad_dias===fila.cantidad_dias){
								i++;
								keyx = key;
							}
						});
					}
					if(i === 0) {
						this.form.solicitud_detalle.push({
							fecha_desde: this.form.fecha_desdex,
							fecha_hasta: this.form.fecha_hastax,
							cantidad_dias: this.form.cantidad_dias,
							anio: this.form.anio,
							mes: this.form.mes,
						});
						/*	this.form.fecha_desdex='';
                            this.form.fecha_hastax='';*/
						this.form.cantidad_dias='';
						this.form.anio='';
						this.form.mes='';

					}else{
						alertify.warning("Ya existe un registro con la fecha seleccionada",5);
					}

				}else{
					alertify.warning("Los campos no pueden estar vacíos",5);
				}
			},
			eliminarLinea(item, index) {
				if (this.form.solicitud_detalle.length >= 1) {
					this.form.solicitud_detalle.splice(index, 1);

				}else{
					this.form.solicitud_detalle=[];
				}
			},
			limpiarDetalle(){
				this.form.detalleSolicitud=[];
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
						feriado.desactivar({
							id_feriado: zonaId
						}, data => {
							alertify.warning("Día feriado desactivado correctamente", 5);
							this.$router.push({
								name: 'listado-feriado'
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
						feriado.activar({
							id_feriado: zonaId
						}, data => {
							alertify.success("Nivel estudio activado correctamente", 5);
							this.$router.push({
								name: 'listado-feriado'
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
			this.obtenerSolicitud()
		}
    }
</script>