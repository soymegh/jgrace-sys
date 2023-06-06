<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Asignación de ingresos y deducciones</div>
						<div class="box-description">Formulario para registrar solicitud de vacaciones</div>
					</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for>Planilla</label>
									<v-select label="planilla" v-model="form.planilla" :disabled="true" :options="planillas"></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.planilla" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label for>Empleado</label>
									<v-select label="nombre_completo" v-model="form.trabajador" v-on:input="obtenerIngreso" :options="trabajadores"></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.trabajador" v-text="message"></li>
									</ul>
								</div>
							</div>
						</div>

						<div class="row">

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
									<label for>Ingreso/Deducción</label>
									<v-select label="ingreso" v-model="form.ingreso" :options="ingresos"></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.ingreso" v-text="message"></li>
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
										<template v-if="item.estado === 1">
										<tr>
											<td style="width: 2%">
												<button @click="eliminarLinea(item, index)">
													<i class="fa fa-trash"></i>
												</button>
											</td>
											<td>
												<v-select label="ingreso" :disabled="true" v-model="item.asignacion_ingreso.cve_ingreso_deduccion" :options="ingresos"></v-select>
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.ingreso`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.asignacion_ingreso.codigo">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.codigo`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="text"  v-model="item.asignacion_ingreso.descripcion">
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
										</template>
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


					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-asignacion-ingreso' }" tag="button">
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
	import asignacion from '../../api/asignacion-ingreso-deduccion'
	import trabajador from '../../api/trabajadores'
	import loadingImage from '../../assets/images/block50.gif'
	import es from "vuejs-datepicker/dist/locale/translations/es";


	export default {
		data() {
			return {
				loading:false,
				msg: 'Cargando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				form: {
					detalleSolicitud:[],
					trabajador: [],
					planilla: [],
					ingreso: [],
					monto: 0,
				},
				trabajadores:[],
				planillas:[],
				ingresos:[],
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		methods: {
			nuevo(){
				var self = this
				self.loading = true;
				asignacion.nuevo({id_planilla_control: this.$route.params.id_planilla_control}, data => {
					self.trabajadores = data.trabajadores;
					self.planillas = data.planillas;
					self.ingresos = data.ingresos;
					self.form.planilla = data.planilla;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			obtenerIngreso(){
				var self = this
				self.loading = true;
				this.form.detalleSolicitud=[];
				asignacion.obtenerIngreso({id_planilla_control: this.$route.params.id_planilla_control, id_trabajador : this.form.trabajador.id_trabajador}, data => {
					self.form.detalleSolicitud = data.ingresos;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			registrar() {
				var self = this
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				asignacion.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-asignacion-ingreso'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			},
			agregarDetalle() {
				let self = this;
				if(this.form.trabajador.id_trabajador > 0 && this.form.monto > 0){
					if(this.form.planilla.id_planilla_control > 0  &&  this.form.ingreso.id_cat_ingreso_deduccion > 0) {
						let i = 0;
						let keyx = 0;
						/*if (self.form.detalleSolicitud) {
							self.form.detalleSolicitud.forEach((fila, key) => {
								if (self.form.descripcion === fila.descripcion) {
									i++;
									keyx = key;
								}
							});
						}*/
						if (i === 0) {
							this.form.detalleSolicitud.push({
								id_cat_ingreso_deduccion_trabajador:0,
								estado:1,
								id_planilla_control: this.form.planilla.id_planilla_control,
								id_sucursal: this.form.planilla.id_sucursal,
								id_trabajador: this.form.trabajador.id_trabajador,
								asignacion_ingreso: this.form.ingreso,
								codigo: this.form.ingreso.codigo,
								descripcion: this.form.ingreso.descripcion,
								id_cat_ingreso_deduccion: this.form.ingreso.id_cat_ingreso_deduccion,
								monto: this.form.monto,

							});
							this.form.ingreso = '';
							this.form.codigo = '';
							this.form.descripcion = '';
							this.form.monto = '';


						} else {
							alertify.warning("Ya existe un registro con la descripcion seleccionada", 5);
						}
					}else{
						alertify.warning("Verifique que ningún campo está vacío", 5);
					}

				}else{
					alertify.warning("Verifique que ningún campo está vacío",5);
				}
			},
			eliminarLinea(item, index) {
				if (this.form.detalleSolicitud.length >= 1) {
					//this.form.detalleSolicitud.splice(index, 1);
					item.estado = 0;

				}else{
					this.form.detalleSolicitud=[];
				}
			},
			limpiarDetalle(){
				this.form.detalleSolicitud=[];
			},
		},
		mounted() {
			this.nuevo();
		}
	}
</script>