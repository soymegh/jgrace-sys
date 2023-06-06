<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Crear nueva solicitud de viatico</div>
						<div class="box-description">Formulario para registrar solicitud de viatico</div>
					</div>

					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for>Usuario Solicitante</label>
								<v-select label="nombre_completo" v-model="form.trabajador" :options="trabajadores"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.trabajador" v-text="message"></li>
								</ul>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
								<label for>Fecha</label>
								<datepicker :format="format" :language="es" :value="new Date()" v-model="fechax2" @selected="onDateSelect"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.f_necesidad" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for=""> Concepto</label>
								<input class="form-control" placeholder="Dígite una descripción" v-model="form.concepto">
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.concepto" v-text="message"></li>
								</ul>
							</div>
						</div>
					</div>
				<br>

                    <div class="alert alert-success">
                        <span><strong>Detalle de la solicitud</strong></span>
                    </div>
						<div class="row">

							<div class="col-sm-2">
								<div class="form-group">
									<label for>Fecha</label>
									<datepicker :format="format" :language="es" :value="new Date()" v-model="fechax" @selected="onDateSelect2"></datepicker>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.viatico" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label for>Desayuno</label>
									<multiselect :allow-empty="true" :options="viatico_desayuno"
												 :searchable="true"
												 :show-labels="true"
												 deselect-label="Deseleccionar"
												 select-label="Seleccionar"
												 label="viatico_con_monto"
												 placeholder="Seleccionar un viatico"
												 ref="viatico"
												 track-by="id_viatico"
												 v-model="detalleForm.desayuno"
									></multiselect>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.viatico" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label for>Almuerzo</label>
									<multiselect :allow-empty="true" :options="viatico_almuerzo"
												 :searchable="true"
												 :show-labels="true"
												 deselect-label="Deseleccionar"
												 select-label="Seleccionar"
												 label="viatico_con_monto"
												 placeholder="Seleccionar un viatico"
												 ref="viatico"
												 track-by="id_viatico"
												 v-model="detalleForm.almuerzo"
									></multiselect>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.viatico" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label for>Cena</label>
									<multiselect :allow-empty="true" :options="viatico_cena"
												 :searchable="true"
												 :show-labels="true"
												 deselect-label="Deseleccionar"
												 select-label="Seleccionar"
												 label="viatico_con_monto"
												 placeholder="Seleccionar un viatico"
												 ref="viatico"
												 track-by="id_viatico"
												 v-model="detalleForm.cena"
									></multiselect>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.viatico" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label for>Transporte</label>
									<multiselect :allow-empty="true" :options="viatico_transporte"
												 :searchable="true"
												 :show-labels="true"
												 deselect-label="Deseleccionar"
												 select-label="Seleccionar"
												 label="viatico_con_monto"
												 placeholder="Seleccionar un viatico"
												 ref="viatico"
												 track-by="id_viatico"
												 v-model="detalleForm.transporte"
									></multiselect>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.viatico" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group">
									<label for>Hospedaje</label>
									<multiselect :allow-empty="true" :options="viatico_hospedaje"
												 :searchable="true"
												 :show-labels="true"
												 deselect-label="Deseleccionar"
												 select-label="Seleccionar"
												 label="viatico_con_monto"
												 placeholder="Seleccionar un viatico"
												 ref="viatico"
												 track-by="id_viatico"
												 v-model="detalleForm.hospedaje"
									></multiselect>
									<ul class="error-messages">
										<li :key="message" v-for="message in errorMessages.viatico" v-text="message"></li>
									</ul>
								</div>
							</div>

							<div class="col-sm-2">
							<div class="form-group">
								<label for>&nbsp;</label>
								<button @click="agregarDetalle" class="btn btn-info btn-agregar" ref="agregar">Agregar detalle</button>
							</div>
							</div>


                        <template v-if="loading">
                            <BlockUI :message="msg" :url="url"></BlockUI>
                        </template>
						</div>



					<div class="row">
						<div class="col-sm-12">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.distribucionSolicitud" v-text="message"></li>
							</ul>

							<table class="table table-bordered">
								<thead>
								<tr>
									<th></th>
									<th >Fecha</th>
									<th >Desayuno</th>
									<th >Almuerzo </th>
									<th >Cena</th>
									<th >Transporte</th>
									<th >Hospedaje</th>
									<th >SubTotal</th>
								</tr>
								</thead>
								<tbody>
								<template  v-for="(item, index) in form.distribucionSolicitud">
									<tr>
										<td style="width: 2%">
											<button @click="eliminarLinea(item, index)">
												<i class="fa fa-trash"></i>
											</button>
										</td>
										<td>
											<input  class="form-control" type="date" :disabled="true"  v-model="item.fecha_viatico">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`distribucionSolicitud.${index}.fecha_viatico`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="number" :disabled="true" v-model.number="item.monto_desayuno">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`distribucionSolicitud.${index}.desayuno`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="number" :disabled="true" v-model.number="item.monto_almuerzo">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`distribucionSolicitud.${index}.almuerzo`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="number" :disabled="true" min="0" v-model.number="item.monto_cena">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`distribucionSolicitud.${index}.cena`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="number" :disabled="true" min="0" v-model.number="item.monto_transporte">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`distribucionSolicitud.${index}.transporte`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="number" :disabled="true" min="0" v-model.number="item.monto_hospedaje">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`distribucionSolicitud.${index}.hospedaje`]" v-text="message"></li>
											</ul>
										</td>
										<td>
										<strong> {{sub_total(item) | formatMoney(2)}}</strong>
										</td>

									</tr>
									<tr></tr>
								</template>
								</tbody>
								<tfoot>
								<tr>
									<td colspan="1"></td>
									<td><strong>Totales</strong></td>
									<td> <strong> {{calcular_total_desayuno | formatMoney(2)}}</strong></td>
									<td> <strong> {{calcular_total_almuerzo | formatMoney(2)}}</strong></td>
									<td> <strong> {{calcular_total_cena | formatMoney(2)}}</strong></td>
									<td> <strong> {{calcular_total_transporte | formatMoney(2)}}</strong></td>
									<td> <strong> {{calcular_total_hospedaje | formatMoney(2)}}</strong></td>
									<td> <strong> {{calcular_total | formatMoney(2)}}</strong></td>
								</tr>
								<tr>

								</tr>
								</tfoot>
							</table>
						</div>



                    </div>



					<br>

					<div class="alert alert-success">
						<span><strong>Resumen de solicitud</strong></span>
					</div>

					<div class="row">
						<div class="col-sm-4">

							<div class="col-sm-12">
								<table class="table table-bordered">
									<thead>
									<tr>
										<td><strong>Resumen</strong></td>
										<td><strong>Cantidad</strong></td>
										<td><strong>Monto total</strong></td>
									</tr>
									</thead>
									<tbody>

									<tr v-if="form.total_cantidad_desayunos>0">
										<td >
											<label > Desayuno</label>
										</td>
										<td>
											<p><strong>{{this.form.total_cantidad_desayunos}}</strong></p>
										</td>
										<td>
											<p><strong>{{this.form.total_desayuno}}</strong></p>
										</td>
									</tr>



									<tr v-if="form.total_cantidad_almuerzos>0">
										<td >
											<label > Almuerzo</label>
										</td>
										<td>
											<p><strong>{{this.form.total_cantidad_almuerzos}}</strong></p>
										</td>
										<td>
											<p><strong>{{this.form.total_almuerzo}}</strong></p>
										</td>
									</tr>
									<tr v-if="form.total_cantidad_cenas>0">
										<td>
											<label > Cena</label>
										</td>
										<td >
											<p><strong>{{this.form.total_cantidad_cenas}}</strong></p>
										</td>
										<td >
											<p><strong>{{this.form.total_cena}}</strong></p>
										</td>
									</tr>
									<tr v-if="form.total_cantidad_transportes>0">
										<td >
											<label > Transporte</label>
										</td>
										<td >
											<p><strong>{{this.form.total_cantidad_transportes}}</strong></p>
										</td>
										<td >
											<p><strong>{{this.form.total_transporte}}</strong></p>
										</td>
									</tr>
									<tr v-if="form.total_cantidad_hospedajes>0">
										<td >
											<label > Hospedaje</label>
										</td>
										<td >
											<p><strong>{{this.form.total_cantidad_hospedajes}}</strong></p>
										</td>
										<td >
											<p><strong>{{this.form.total_hospedaje}}</strong></p>
										</td>
									</tr>
									</tbody>
									<tfoot>
									<tr>

										<td>Total</td>
										<td>{{cantidad}}</td>
										<td>{{form.total_monto | formatMoney(2)}}</td>
									</tr>
									</tfoot>
								</table>
							</div>

						</div>
					</div>

					<br>


					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-solicitud' }" tag="button">
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
	import solicitud from '../../api/solicitud_viatico'
	import normativa from '../../api/normativas'
	import loadingImage from '../../assets/images/block50.gif'
	import sucursal from "../../api/sucursales";
	import cliente from "../../api/clientes";
	import es from "vuejs-datepicker/dist/locale/translations/es";

	export default {
		data() {
			return {
				loading:false,
				msg: 'Guardando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				detalleForm: {
					cantidad:0,
					fecha_viatico: moment(new Date()).format("YYYY-MM-DD"),
					objetivo: '',
					monto_viatico:0,
					desayuno:null,
					almuerzo:null,
					cena:null,
					transporte:null,
					hospedaje:null
				},
				form: {
					trabajador: [],
					fecha_solicitud : moment(new Date()).format("YYYY-MM-DD"),
					total:0,
					total_desayuno:0,
					total_almuerzo:0,
					total_cena:0,
					total_transporte:0,
					total_hospedaje:0,
					detalleSolicitud:[],
                    distribucionSolicitud:[],
					concepto:'',
					total_cantidad_desayunos:0,
					total_cantidad_almuerzos:0,
					total_cantidad_cenas:0,
					total_cantidad_transportes:0,
					total_cantidad_hospedajes:0,
                    cantidad:0,
				},
				fechax: new Date(),
				fechax2: new Date(),
				trabajadores:[],
				viatico_desayuno:[],
				viatico_almuerzo:[],
				viatico_cena:[],
				viatico_transporte:[],
				viatico_hospedaje:[],
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		computed:{
				calcular_total() {
				this.form.total = this.form.distribucionSolicitud.reduce((carry, item) => {
							return(carry + Number(item.subtotal.toFixed(2)));
						}
						, 0)
				return this.form.total;
			},

			calcular_total_desayuno(){

				if(this.form.distribucionSolicitud){
					this.form.total_desayuno = this.form.distribucionSolicitud.reduce((carry, item) => {
								return(carry + Number(item.monto_desayuno));
							}
							, 0);



				let contador_desayunos = 0;
					this.form.distribucionSolicitud.forEach((fila, indice) => {
						if (fila.id_viatico_desayuno) {
							contador_desayunos ++;
						}
					});

					this.form.total_cantidad_desayunos = contador_desayunos;

					return this.form.total_desayuno;
				}
			},
			calcular_total_almuerzo(){
				if(this.form.distribucionSolicitud){
					this.form.total_almuerzo = this.form.distribucionSolicitud.reduce((carry, item) => {
								return(carry + Number(item.monto_almuerzo));
							}
							, 0)
					let contador_almuerzos = 0;
					this.form.distribucionSolicitud.forEach((fila, indice) => {
						if ((fila.id_viatico_almuerzo)) {
							contador_almuerzos ++;
						}
					});

					this.form.total_cantidad_almuerzos = contador_almuerzos;
					return this.form.total_almuerzo;
				}
			},
			calcular_total_cena(){
				if(this.form.distribucionSolicitud){
					this.form.total_cena = this.form.distribucionSolicitud.reduce((carry, item) => {
								return(carry + Number(item.monto_cena));
							}
							, 0)
					let contador_cenas = 0;
					this.form.distribucionSolicitud.forEach((fila, indice) => {
						if ((fila.id_viatico_cena)) {
							contador_cenas ++;
						}
					});

					this.form.total_cantidad_cenas = contador_cenas;
					return this.form.total_cena;
				}
			},
			calcular_total_transporte(){
				if(this.form.distribucionSolicitud){
					this.form.total_transporte = this.form.distribucionSolicitud.reduce((carry, item) => {
								return(carry + Number(item.monto_transporte));
							}
							, 0)
					let contador_transportes = 0;
					this.form.distribucionSolicitud.forEach((fila, indice) => {
						if ((fila.id_viatico_transporte)) {
							contador_transportes ++;
						}
					});

					this.form.total_cantidad_transportes = contador_transportes;
					return this.form.total_transporte;
				}
			},
			calcular_total_hospedaje(){
				if(this.form.distribucionSolicitud){
					this.form.total_hospedaje = this.form.distribucionSolicitud.reduce((carry, item) => {
								return(carry + Number(item.monto_hospedaje));
							}
							, 0)
					let contador_hospedajes = 0;
					this.form.distribucionSolicitud.forEach((fila, indice) => {
						if ((fila.id_viatico_hospedaje)) {
							contador_hospedajes ++;
						}
					});

					this.form.total_cantidad_hospedajes = contador_hospedajes;
					return this.form.total_hospedaje;
				}
			},
			cantidad() {
				this.form.cantidad= Number(this.form.total_cantidad_desayunos) + Number(this.form.total_cantidad_almuerzos) + Number(this.form.total_cantidad_cenas)
						+ Number(this.form.total_cantidad_transportes) + Number(this.form.total_cantidad_hospedajes);

				this.form.total_monto= Number(this.form.total_desayuno) + Number(this.form.total_almuerzo) + Number(this.form.total_cena)
						+ Number(this.form.total_transporte) + Number(this.form.total_hospedaje);


				if(!isNaN(this.form.cantidad)){
					return this.form.cantidad;
				}
				else return 0;
			},
		},
		methods: {

			nuevo(){
				var self = this
				self.loading = true;
				solicitud.nuevo({}, data => {
					self.trabajadores = data.trabajadores;
					self.viatico_desayuno = data.viatico_desayuno;
					self.viatico_almuerzo = data.viatico_almuerzo;
					self.viatico_cena = data.viatico_cena;
					self.viatico_transporte = data.viatico_transporte;
					self.viatico_hospedaje = data.viatico_hospedaje;
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
				solicitud.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-solicitud'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			},
			onDateSelect(date) {
				//console.log(date); //
				this.form.fecha_solicitud = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelect2(date) {
				//console.log(date); //
				this.detalleForm.fecha_viatico = moment(date).format("YYYY-MM-DD"); //
			},
			agregarDetalle() {
				let self = this;

				if((this.detalleForm.almuerzo===null) &&
				(this.detalleForm.desayuno===null)&&
				(this.detalleForm.cena===null)&&
				(this.detalleForm.transporte===null)&&
				(this.detalleForm.hospedaje===null)){

					alertify.warning("No se puede agregar un detalle que esté vacío",5);

				}else{

				if(this.detalleForm.fecha_viatico){
						let i = 0;
						let keyx = 0;
						if(self.form.distribucionSolicitud){
							self.form.distribucionSolicitud.forEach((fila, key) => {
								if(self.detalleForm.fecha_viatico===fila.fecha_viatico){
									i++;
									keyx = key;
								}
							});
						}
						if(i === 0){
							this.form.distribucionSolicitud.push({
								fecha_viatico: this.detalleForm.fecha_viatico,
								id_viatico_desayuno: (this.detalleForm.desayuno!=="" &&this.detalleForm.desayuno!==null)?this.detalleForm.desayuno.id_viatico:null,
								monto_desayuno: (this.detalleForm.desayuno!==""&&this.detalleForm.desayuno!==null)?this.detalleForm.desayuno.monto:0,
								id_viatico_almuerzo: (this.detalleForm.almuerzo!==""&&this.detalleForm.almuerzo!==null)?this.detalleForm.almuerzo.id_viatico:null,
								monto_almuerzo: (this.detalleForm.almuerzo!==""&&this.detalleForm.almuerzo!==null)?this.detalleForm.almuerzo.monto:0,
								id_viatico_cena: (this.detalleForm.cena!==""&&this.detalleForm.cena!==null)?this.detalleForm.cena.id_viatico:null,
								monto_cena: (this.detalleForm.cena!==""&&this.detalleForm.cena!==null)?this.detalleForm.cena.monto:0,
								id_viatico_transporte: (this.detalleForm.transporte!==""&&this.detalleForm.transporte!==null)?this.detalleForm.transporte.id_viatico:null,
								monto_transporte: (this.detalleForm.transporte!==""&&this.detalleForm.transporte!==null)?this.detalleForm.transporte.monto:0,
								id_viatico_hospedaje: (this.detalleForm.hospedaje!==""&&this.detalleForm.hospedaje!==null)?this.detalleForm.hospedaje.id_viatico:null,
								monto_hospedaje: (this.detalleForm.hospedaje!==""&&this.detalleForm.hospedaje!==null)?this.detalleForm.hospedaje.monto:0,

								subtotal:0
							});
							//this.detalleForm.fecha_viatico='';
							this.detalleForm.almuerzo=null;
							this.detalleForm.desayuno=null;
							this.detalleForm.cena=null;
							this.detalleForm.transporte=null;
							this.detalleForm.hospedaje=null;

						}else{
							alertify.warning("Ya existe un registro con la fecha seleccionada",5);
						}

				}else{
					alertify.warning("Verifique que ningún campo esté vacío",5);
				}
				}
			},
			eliminarLinea(item, index) {
				if (this.form.distribucionSolicitud.length >= 1) {
					this.form.distribucionSolicitud.splice(index, 1);

				}else{
					this.form.distribucionSolicitud=[];
				}
			},
			sub_total(itemX) {
				itemX.subtotal= Number(itemX.monto_desayuno) + Number(itemX.monto_almuerzo) + Number(itemX.monto_cena)
                + Number(itemX.monto_transporte) + Number(itemX.monto_hospedaje);

				if(!isNaN(itemX.subtotal)){
					return itemX.subtotal;
				}
				else return 0;
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