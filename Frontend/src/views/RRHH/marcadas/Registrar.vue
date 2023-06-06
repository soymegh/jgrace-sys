<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Entradas y salidas de personal</div>
						<div class="box-description">Formulario para registrar entradas y salidas del empleado.</div>
					</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for>Empleado</label>
									<v-select label="nombre_completo" v-model="form.trabajador" :disabled="true" v-on:input="obtenerIngreso" ></v-select>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.trabajador" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for>Fecha</label>
									<datepicker :format="format" :language="es" :value="new Date()" v-model="fechax" @selected="onDateSelect"></datepicker>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.f_necesidad" v-text="message"></li>
									</ul>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for> Hora entrada</label>
									<input type="time" class="form-control" v-model="form.hora_entrada" placeholder="Select time">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for> Hora salida</label>
									<input type="time" class="form-control" v-model="form.hora_salida" placeholder="Select time">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for> Hora entrada justificada</label>
									<input type="time" class="form-control" v-model="form.hora_entrada_justificada" placeholder="Select time">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for> Hora salida justificada</label>
									<input type="time" class="form-control" v-model="form.hora_salida_justificada" placeholder="Select time">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label>Tipo</label>
									<select :disabled="false" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="form.tipo">
										<option value="ORG">Original o Presencial</option>
										<option value="JUS">Justificada</option>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for>Justificación</label>
									<multiselect :allow-empty="true" :options="justificaciones"
												 :searchable="true"
												 :show-labels="true"
												 deselect-label="Deseleccionar opción"
												 label="descripcion"
												 placeholder="Seleccionar una justificacion"
												 ref="justificacion"
												 track-by="id_marcada_tipo_justificacion"
												 v-model="form.justificacion"
									></multiselect>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.trabajador" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for> Observación</label>
									<input type="text" class="form-control" v-model="form.observacion" placeholder="Digite una observación">
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
										<th style="min-width:200px">Fecha</th>
										<th style="min-width:150px">Hora entrada</th>
										<th style="min-width:150px">Hora salida</th>
										<th style="min-width:150px">Hora entrada justificada</th>
										<th style="min-width:150px">Hora salida justificada</th>
										<th style="min-width:150px">Tipo</th>
										<th style="min-width:150px">Justificación</th>
										<th style="min-width:150px">Observación</th>
										<th ></th>
									</tr>
									</thead>
									<tbody>
									<template  v-for="(item, index) in form.detalleSolicitud">
										<tr>
											<td style="width: 2%">
												<button v-if="!item.id_marcada" @click="eliminarLinea(item, index)">
													<i class="fa fa-trash"></i>
												</button>
											</td>
											<td>
                                                {{formatDate(item.f_marcada)}}
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.f_marcada`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="time"  v-model="item.hora_entrada">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.hora_entrada`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="time" v-model="item.hora_salida">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.hora_salida`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="time"  v-model="item.hora_entrada_justificada">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.hora_entrada_justificada`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="time" v-model="item.hora_salida_justificada">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.hora_salida_justificada`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="text" v-model="item.tipo">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.tipo`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="text" v-model="item.justificacion">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.justificacion`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="text" v-model="item.observacion">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.observacion`]" v-text="message"></li>
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


					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-marcadas' }" tag="button">
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
	import asignacion from '../../api/marcadas'
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
					tipo: '',
					f_marcada:moment(new Date()).format("YYYY-MM-DD"),
					hora_entrada:'',
					hora_salida:'',
					hora_entrada_justificada:'',
					hora_salida_justificada:'',
					observacion:'',
					justificacion:'',
				},
				justificaciones:[],
				planillas:[],
				fechax:new Date(),
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		methods: {
			nuevo(){
				var self = this
				self.loading = true;
				asignacion.nuevo({id_trabajador: this.$route.params.id_trabajador}, data => {
					self.justificaciones = data.justificaciones;
					self.form.trabajador = data.trabajador;
					self.form.detalleSolicitud = data.marcadas;
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
				if(this.form.trabajador){
						let i = 0;
						let keyx = 0;
						if (self.form.detalleSolicitud) {
							self.form.detalleSolicitud.forEach((fila, key) => {
								if (self.form.f_marcada === fila.f_marcada) {
									i++;
									keyx = key;
								}
							});
						}
						if (i === 0) {
							this.form.detalleSolicitud.push({
								f_marcada:this.form.f_marcada,
								hora_entrada: this.form.hora_entrada,
								hora_salida: this.form.hora_salida,
								hora_entrada_justificada: this.form.hora_entrada_justificada,
								hora_salida_justificada: this.form.hora_salida_justificada,
								id_tipo_marcada_justificacion: (this.form.justificacion!==""&&this.form.justificacion!==null)?this.form.justificacion.id_tipo_marcada_justificacion:null,
								justificacion: this.form.justificacion.descripcion,
								tipo: this.form.tipo,
								observacion: this.form.observacion,
                                id_trabajador: this.form.trabajador.id_trabajador

							});
							this.form.hora_entrada = '';
							this.form.hora_salida = '';
							this.form.hora_entrada_justificada = '';
							this.form.hora_salida_justificada = '';
							this.form.justificacion = '';
							this.form.tipo = '';
							this.form.observacion = '';


						} else {
							alertify.warning("Ya existe un registro con la fecha seleccionada", 5);
						}
				}else{
					alertify.warning("Verifique que ningún campo está vacío",5);
				}
			},
			eliminarLinea(item, index) {
				if (this.form.detalleSolicitud.length >= 1) {
					this.form.detalleSolicitud.splice(index, 1);
					//item.estado = 0;

				}else{
					this.form.detalleSolicitud=[];
				}
			},
			limpiarDetalle(){
				this.form.detalleSolicitud=[];
			},
			onDateSelect(date) {
				//console.log(date); //
				this.form.f_marcada = moment(date).format("YYYY-MM-DD"); //
			},
            formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            },
		},
		mounted() {
			this.nuevo();
		}
	}
</script>
<style>
	.btn-agregar {
		margin-top:33px;
	}
</style>