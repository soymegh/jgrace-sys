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
								<label>Tipo de solicitud</label>
								<select :disabled="true" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field"  v-model="form.id_tipo">
									<option value="1">Solicitud de vacaciones</option>
									<option value="2">Solicitud de devolucion de vacaciones</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha solicitud</label>
								<datepicker :format="format" :language="es" :disabled="true" :value="new Date()" v-model="form.f_solicitud" @selected="onDateSelect"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha desde</label>
								<datepicker :format="format" :disabled="true" :language="es" :value="new Date()" v-model="form.fecha_desde" @selected="onDateSelect2"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha hasta</label>
								<datepicker :format="format" :disabled="true" :language="es" :value="new Date()" v-model="form.fecha_hasta" @selected="onDateSelect3"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
								</ul>
							</div>
						</div>

					</div>
					<div class="alert alert-success">
						<span><strong>Información del empleado solicitante</strong></span>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Empleado</label>
								<v-select label="nombre_completo" :disabled="true" v-model="form.solicitud_trabajador" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.trabajador" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha ingreso</label>
								<v-select label="fecha_ingreso" :disabled="true" v-model="form.trabajador_detalles" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
								</ul>
							</div>
						</div>
						<!--<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Area</label>
								<v-select label="descripcion" :disabled="true"  v-model="form.trabajador_area" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.area" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Cargo</label>
								<v-select label="descripcion" :disabled="true" v-model="form.trabajador_cargo" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.cargo" v-text="message"></li>
								</ul>
							</div>
						</div>-->
					</div>
					<div class="alert alert-success">
						<span><strong>Solicitud de vacaciones</strong></span>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="label-search">Año:</label>
								<v-select
										label="periodo"
										v-model="form.anio"
										:options="periodos"
										v-on:input="obtenerMeses"
								></v-select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="label-search">Mes:</label>
								<v-select :style="'margin-left: .5rem!important;'"
										  label="descripcion"
										  v-model="form.mes"
										  :options="meses"
										  v-on:input="cambiarFechas"
								></v-select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Días solicitados</label>
								<input class="form-control" :disabled="false"  type="number" min="0" v-model="form.cantidad_dias">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.total_dias" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for=""> Saldo a la fecha</label>
								<input class="form-control" :disabled="true"  type="number" v-if="form.solicitud_trabajador" v-model="form.solicitud_trabajador.saldo_actual">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.saldo_actual" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha desde</label>
								<datepicker :disabled-dates="disabledDates" :format="format" :disabled="false" :language="es" :value="new Date()" v-model="fechax4" @selected="onDateSelect4"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha_desdex" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha hasta</label>
								<datepicker :disabled-dates="disabledDates" :format="format" :disabled="false" :language="es" :value="new Date()" v-model="fechax5" @selected="onDateSelect5"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha_hastax" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for=""> Justificación</label>
								<input class="form-control" :disabled="false"  v-model="form.justificacion">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.justificacion" v-text="message"></li>
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
						<span><strong>Detalle de solicitud</strong></span>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.solicitud_detalle" v-text="message"></li>
							</ul>

							<table class="table table-bordered table-responsive">
								<thead>
								<tr>
									<th></th>
									<!--<th style="min-width:300px" >Tipo de solicitud</th>-->
									<th style="min-width:200px">Fecha desde</th>
									<th style="min-width:150px">Fecha hasta</th>
									<th style="min-width:150px">Cantidad de días</th>
									<th style="min-width:150px">Año</th>
									<th style="min-width:150px">Mes</th>
									<th ></th>
								</tr>
								</thead>
								<tbody>
								<template  v-for="(item, index) in form.solicitud_detalle">
									<tr>
										<td style="width: 2%">
											<button @click="eliminarLinea(item, index)">
												<i class="fa fa-trash"></i>
											</button>
										</td>
										<!--<td>
                                            <select :disabled="true" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="item.tipo">
                                                <option value="1">Solicitud de vacaciones</option>
                                                <option value="2">Solicitud de devolucion de vacaciones</option>
                                            </select>
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.tipo`]" v-text="message"></li>
                                            </ul>
                                        </td>-->
										<td>
											{{formatDate(item.fecha_desde)}}
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`solicitud_detalle.${index}.fecha_desdex`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											{{formatDate(item.fecha_hasta)}}
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`solicitud_detalle.${index}.fecha_hastax`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.cantidad_dias">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`solicitud_detalle.${index}.cantidad_dias`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.anio">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`solicitud_detalle.${index}.anio`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.mes">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`solicitud_detalle.${index}.mes`]" v-text="message"></li>
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
									<td colspan="2"></td>
									<td>Total</td>
									<td> <strong> {{calcular_total | formatMoney(2)}}</strong></td>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for=""> Observación</label>
								<input class="form-control" :disabled="false"  v-model="form.observacion">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.observacion" v-text="message"></li>
								</ul>
							</div>
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
								<router-link :to="{ name: 'listado-vacaciones' }" tag="button">
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
	import solicitud from '../../api/solicitud-vacaciones'
	import loadingImage from '../../assets/images/block50.gif'
	import saldo from '../../api/saldo-vacaciones'
	import es from "vuejs-datepicker/dist/locale/translations/es";
	var fecha_actual = new Date();
	fecha_actual.setHours(23,59,59,999);

	export default {
		data() {
			return {
				disabledDates: {
					to: '',//new Date(2020, 0, 1), // Disable all dates up to specific date 31/12/2019
					from: fecha_actual, // Disable all dates after specific date 01/02/2020
				},
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
					f_solicitud : moment(new Date()).format("YYYY-MM-DD"),
					fecha_desde : moment(new Date()).format("YYYY-MM-DD"),
					fecha_hasta : moment(new Date()).format("YYYY-MM-DD"),
					fecha_desdex : moment(new Date()).format("YYYY-MM-DD"),
					fecha_hastax : moment(new Date()).format("YYYY-MM-DD"),
					costo_vacaciones : '',
					total_dias : '',
					saldo_dias : '',
					dias_meses : 30,
					salario_basico : 0,
					mes:0,
					anio:0
				},
				trabajadores:[],
				periodos:[],
				meses:[],
				fechax1: new Date(),
				fechax2: new Date(),
				fechax3: new Date(),
				fechax4: new Date(),
				fechax5: new Date(),
				btnAction: 'Guardar',
				btnActionConf: 'Confirmar',
				errorMessages: []
			}
		},
			computed:{

				calcular_total() {
					if(this.form.solicitud_detalle){
					this.form.total_dias = this.form.solicitud_detalle.reduce((carry, item) => {
								return(carry + Number(item.cantidad_dias));
							}
							, 0)
					return this.form.total_dias;
					}
				},
				calcularSaldoDias(){
					let self = this;
					self.form.saldo_dias = Number((self.form.trabajador_saldo_vacacion.saldo_actual - self.form.total_dias).toFixed(2));
					if(!isNaN(self.form.saldo_dias)){
						return self.form.saldo_dias;
					}else{
						return 0;
					}
				},
				calcularCostoVacacion(){
					let self = this;
					self.form.costo_vacaciones = Number( ( (self.form.salario_basico / 30) * (self.form.total_dias)).toFixed(2));
					if(!isNaN(self.form.costo_vacaciones)){
						return self.form.costo_vacaciones;
					}else{
						return 0;
					}
				},
			},
		methods: {
			cambiarFechas(){

				let self = this;

				//self.form.detalleSolicitud = [];

				self.disabledDates.to = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.disabledDates.from = new Date(self.form.mes.ultimo_dia_mes);
				self.disabledDates.from.setTime( self.disabledDates.from.getTime() + 86400000 );
				//self.fechax1 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				//self.fechax2 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				//self.fechax3 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.fechax4 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.fechax5 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);

				//self.form.f_solicitud = moment(self.fechax1).format("YYYY-MM-DD"); //
				//self.form.fecha_desde = moment(self.fechax2).format("YYYY-MM-DD"); //
				//self.form.fecha_hasta = moment(self.fechax3).format("YYYY-MM-DD"); //
				self.form.fecha_desdex = moment(self.fechax4).format("YYYY-MM-DD");//
				self.form.fecha_hastax = moment(self.fechax5).format("YYYY-MM-DD"); //


			},
			formatDate(date) {
				return moment(date).format('YYYY-MM-DD')
			},
			obtenerMeses(){
				let self = this;
				self.form.mes = [];
				self.meses = self.form.anio.meses_periodo
				self.form.mes = self.meses[0]
			},
			obtenerSolicitud() {
				var self = this
				//Array(1,2,3).includes(Number(self.$route.params.id_zona)) ? self.SoloLectura = true : self.SoloLectura = false
				solicitud.obtenerSolicitud({
					id_vacacion_solicitud: this.$route.params.id_vacacion_solicitud
				}, data => {
					self.form = data.solicitud;
					self.periodos = data.periodos;
					self.form.anio = self.periodos[0];
					self.meses = self.form.anio.meses_periodo
					//self.form.fechax1 = new Date(data.solicitud.f_solicitud);
					//self.form.fechax2 = new Date(data.solicitud.fecha_desde);
					//self.form.fechax3 = new Date(data.solicitud.fecha_hasta);
					//self.form.fechax4 = new Date(data.solicitud.solicitud_detalle.fecha_desde);
					//self.form.fechax5 = new Date(data.solicitud.solicitud_detalle.fecha_hasta);
					self.form.salario_basico = data.solicitud.trabajador_detalles.salario_basico;
					self.loading = false;
				}, err => {
					//alertify.error(err.id_vacacion_solicitud[0],5);
					console.log(err);
					this.$router.push({
						name: 'listado-vacaciones'
					});
				})
			},
			nuevo(){
				var self = this
				self.loading = true;
				solicitud.nuevo({}, data => {
					self.trabajadores = data.trabajadores;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
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
			onDateSelect(date) {
				//console.log(date); //
				this.form.f_solicitud = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelect2(date) {
				//console.log(date); //
				this.form.fecha_desde = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelect3(date) {
				//console.log(date); //
				this.form.fecha_hasta = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelect4(date) {
				//console.log(date); //
				this.form.fecha_desdex = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelect5(date) {
				//console.log(date); //
				this.form.fecha_hastax = moment(date).format("YYYY-MM-DD"); //
			},
			agregarDetalle() {
				let self = this;
				if(this.form.cantidad_dias){
					let i = 0;
					let keyx = 0;
					if(self.form.solicitud_detalle){
						self.form.solicitud_detalle.forEach((fila, key) => {
							if(self.form.anio.periodo!==fila.anio || self.form.mes.mes !== fila.mes){
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
							anio: this.form.anio.periodo,
							mes: this.form.mes.mes,
						});
						/*	this.form.fecha_desdex='';
                            this.form.fecha_hastax='';*/
						this.form.cantidad_dias='';
						//this.form.anio='';
						//this.form.mes='';

					}else{
						alertify.warning("El periodo y mes seleccionado no coincide con los registros anteriores",5);
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
			this.nuevo()
		}
    }
</script>