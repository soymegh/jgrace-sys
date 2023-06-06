<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Solicitudes de vacaciones</div>
						<div class="box-description">Formulario para registrar solicitud de vacaciones</div>
					</div>
						<div class="row">
							<div class="col-sm-4">
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

							<div class="col-sm-4">
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
							<div class="col-sm-4">
								<div class="form-group">
									<label for>Fecha solicitud</label>
									<datepicker :disabled-dates="disabledDates" :format="format" :language="es" :value="new Date()" v-model="fechax1" @selected="onDateSelect"></datepicker>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label>Tipo de solicitud</label>
									<select :disabled="false" class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="form.tipo_solicitud">
										<option value="1">Solicitud de vacaciones</option>
										<option value="2">Solicitud de devolucion de vacaciones</option>
										<!--<option value="3">Solicitud de pago</option>-->
									</select>
								</div>
							</div>
							<template v-if="form.tipo_solicitud < 3" :disabled="true">
							<div class="col-sm-4">
								<div class="form-group">
									<label for>Fecha desde</label>
									<datepicker :disabled-dates="disabledDates" :format="format" :disabled="false" :language="es" :value="new Date()" v-model="fechax2" @selected="onDateSelect2"></datepicker>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for>Fecha hasta</label>
									<datepicker :disabled-dates="disabledDates" :format="format" :disabled="false" :language="es" :value="new Date()" v-model="fechax3" @selected="onDateSelect3"></datepicker>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
									</ul>
								</div>
							</div>
							</template>

						</div>
						<div class="alert alert-success">
							<span><strong>Información del empleado solicitante</strong></span>
						</div>
						<div class="row">
							<template v-if="form.tipo_solicitud < 3">
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Empleado</label>
								<v-select label="nombre_completo" v-model="form.trabajador" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.trabajador" v-text="message"></li>
								</ul>
							</div>
						</div>
							</template>
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha ingreso</label>
								<v-select label="fecha_ingreso" :disabled="true" v-model="form.trabajador.trabajador_detalles" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha" v-text="message"></li>
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
						</div>
					<template v-if="form.tipo_solicitud < 3">
						<div class="alert alert-success">
							<span><strong>Solicitud de vacaciones</strong></span>
						</div>

						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for=""> Días solicitados</label>
									<input class="form-control" :disabled="false" type="number" min="0.5" step="0.5" v-model="form.cantidad_dias">
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.total_dias" v-text="message"></li>
									</ul>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for=""> Saldo a la fecha</label>
									<v-select label="saldo_actual" :disabled="true" v-model="form.trabajador.saldo_actual" :options="trabajadores"></v-select>
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
					</template>


					<template v-if="form.tipo_solicitud < 3" :disabled="true">
					<div class="alert alert-success">
						<span><strong>Detalle de solicitud</strong></span>
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
										<th style="min-width:200px">Fecha desde</th>
										<th style="min-width:150px">Fecha hasta</th>
										<th style="min-width:150px">Cantidad de días</th>
										<th style="min-width:150px">Año</th>
										<th style="min-width:150px">Mes</th>
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
												<input type="text" disabled v-model="item.fecha_desdex">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.fecha_desdex`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input type="text" disabled v-model="item.fecha_hastax">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.fecha_hastax`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.cantidad_dias">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.cantidad_dias`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.anio">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.anio`]" v-text="message"></li>
												</ul>
											</td>
											<td>
												<input  class="form-control" :disabled="true" type="number" min="0" v-model="item.mes">
												<ul class="error-messages">
													<li :key="message" v-for="message in errorMessages[`detalleSolicitud.${index}.mes`]" v-text="message"></li>
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
					</template>

					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-vacaciones' }" tag="button">
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
	import solicitud from '../../api/solicitud-vacaciones'
	import saldo from '../../api/saldo-vacaciones'
	import trabajador from '../../api/trabajadores'
	import loadingImage from '../../assets/images/block50.gif'
	import es from "vuejs-datepicker/dist/locale/translations/es";
	import cuentas_contables from "../../api/cuentas_contables";
	import comprobante from "../../api/caja-chica-comprobante";
	import interno from "../../api/contratos_internos";
	import departamento from "../../api/departamentos";
	import aplicaciones from "../../api/baterias_aplicaciones";
	import numberasstring from "../../assets/custom-scripts/NumberAsString";
	var fecha_actual = new Date();
	fecha_actual.setHours(23,59,59,999);
	export default {
		data() {
			return {


				disabledDates: {
					to: '',//new Date(2020, 0, 1), // Disable all dates up to specific date 31/12/2019
					from: fecha_actual, // Disable all dates after specific date 01/02/2020
				},
				loading:false,
				msg: 'Guardando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				form: {
					detalleSolicitud:[],
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
					salario_basico : 0,
					dias_meses : 30,
					mes:0,
					anio:0,
					tipo_solicitud:1,
					tipo_pago:1,
					monto:0,
					monto_letras:'',
					dias_a_pagar:1,
				},
				trabajadores:[],
				periodos:[],
				meses:[],
				saldos:[],
				fechax1: new Date(),
				fechax2: new Date(),
				fechax3: new Date(),
				fechax4: new Date(),
				fechax5: new Date(),

				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		computed:{

			calcular_total() {
				let self = this;
				this.form.total_dias = this.form.detalleSolicitud.reduce((carry, item) => {
							return(carry + Number(item.cantidad_dias));
						}
						, 0)

				if(self.form.trabajador.trabajador_detalles){

					if(self.form.tipo_solicitud === 1  ){
					self.form.saldo_dias = Number((self.form.trabajador.saldo_actual - self.form.total_dias).toFixed(2));
					}else if (self.form.tipo_solicitud === 2){
						self.form.saldo_dias = Number((self.form.trabajador.saldo_actual + self.form.total_dias).toFixed(2));
					}/* else if(self.form.tipo_solicitud == 3)
					{
						self.form.saldo_dias = Number((self.form.trabajador.saldo_actual - self.form.dias_a_pagar).toFixed(2));
					}
						self.form.costo_vacaciones = Number(((self.form.trabajador.trabajador_detalles.salario_basico / self.form.dias_meses) * (self.form.total_dias)).toFixed(2));
						*/

				}else{
					self.form.saldo_dias = 0;
					self.form.costo_vacaciones = 0;

				}


				return this.form.total_dias;
			},
			calcularSaldoDias(){
				let self = this;
				if(self.form.tipo_solicitud == 1){

				self.form.saldo_dias = Number((self.form.trabajador.saldo_actual - self.form.total_dias).toFixed(2));

				}else if(self.form.tipo_solicitud == 2)
					{
						self.form.saldo_dias = Number((self.form.trabajador.saldo_actual + self.form.total_dias).toFixed(2));

					}else if(self.form.tipo_solicitud == 3)
					{
						self.form.saldo_dias = Number((self.form.trabajador.saldo_actual - self.form.dias_a_pagar).toFixed(2));
					}

				if(!isNaN(self.form.saldo_dias)){
					return self.form.saldo_dias;
				}else{
					return 0;
				}
			},
			calcularCostoVacacion(){
				let self = this;
				self.form.costo_vacaciones = Number(((self.form.trabajador.trabajador_detalles.salario_basico / self.form.dias_meses) * (self.form.total_dias)).toFixed(2));
				if(!isNaN(self.form.costo_vacaciones)){
					return self.form.costo_vacaciones;
				}else{
					return 0;
				}
			},
			calcularPagoVacaciones(){
				let self = this;
					self.form.monto = Number( (( (self.form.trabajador.salario_ingreso_deduccion) / (self.form.dias_meses)) * (self.form.dias_a_pagar)).toFixed(2));

					if(!isNaN(self.form.monto)){
						return self.form.monto
					}else{
						return 0;
					}

			},
			/*monto_letras(){
				if(this.form.monto >0){
					this.form.monto_letras = numberasstring.numberasstring(this.form.monto,'CORDOBA','CORDOBAS',true);
				}else{
					this.form.monto_letras ='SELECCIONE UN MONTO MAYOR QUE CERO';
				}
				return this.form.monto_letras;
			},*/
		},
		methods: {
			cambiarFechas(){

				let self = this;

				self.form.detalleSolicitud = [];

				self.disabledDates.to = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.disabledDates.from = new Date(self.form.mes.ultimo_dia_mes);
				self.disabledDates.from.setTime( self.disabledDates.from.getTime() + 86400000 );
				//self.fechax1 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.fechax2 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.fechax3 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.fechax4 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);
				self.fechax5 = new Date(self.form.anio.periodo, self.form.mes.mes-1, 1);

				//self.form.f_solicitud = moment(self.fechax1).format("YYYY-MM-DD"); //
				self.form.fecha_desde = moment(self.fechax2).format("YYYY-MM-DD"); //
				self.form.fecha_hasta = moment(self.fechax3).format("YYYY-MM-DD"); //
				self.form.fecha_desdex = moment(self.fechax4).format("YYYY-MM-DD");//
				self.form.fecha_hastax = moment(self.fechax5).format("YYYY-MM-DD"); //


			},
			obtenerSaldos() {
				var self = this;
				saldo.obtenerTodas(
						data => {
							self.saldos = data;
						},
						err => {
							console.log(err);
						}
				);
			},
			nuevo(){
				var self = this
				self.loading = true;
				solicitud.nuevo({}, data => {
					self.trabajadores = data.trabajadores;
					self.periodos = data.periodos;
					self.form.anio = self.periodos[0];
					self.meses = self.form.anio.meses_periodo
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			obtenerMeses(){
				let self = this;
				self.form.mes = [];
				self.meses = self.form.anio.meses_periodo
				self.form.mes = self.meses[0]
			},
			/*cargar_saldo()
			{
				var self = this;
				//self.limpiar_campos();
				if (self.saldos){
					self.form.saldo_actual = self.saldos.saldo_actual;
				}
			},*/
			monto_letras(){
				if(this.form.monto >0){
					this.form.monto_letras = numberasstring.numberasstring(this.form.monto,'CORDOBA','CORDOBAS',true);
				}else{
					this.form.monto_letras ='SELECCIONE UN MONTO MAYOR QUE CERO';
				}
				return this.form.monto_letras;
			},
			registrar() {
				var self = this
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				solicitud.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-vacaciones'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
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
				if(this.form.cantidad_dias){//validar que periodo y mes seleccionado
					let i = 0;
					let keyx = 0;
					if(self.form.detalleSolicitud){
						self.form.detalleSolicitud.forEach((fila, key) => {
							if(self.form.cantidad_dias===fila.cantidad_dias){
								i++;
								keyx = key;
							}
						});
					}
					if(i === 0) {
						this.form.detalleSolicitud.push({
							fecha_desdex: this.form.fecha_desdex,
							fecha_hastax: this.form.fecha_hastax,
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
						alertify.warning("Ya existe un registro con la fecha seleccionada",5);
					}

				}else{
					alertify.warning("Los campos no pueden estar vacíos",5);
				}
			},
			eliminarLinea(item, index) {
				if (this.form.detalleSolicitud.length >= 1) {
					this.form.detalleSolicitud.splice(index, 1);

				}else{
					this.form.detalleSolicitud=[];
				}
			},
		},
		mounted() {
			this.nuevo();
			this.obtenerSaldos();
			//this.cargar_saldo();
		}
	}
</script>