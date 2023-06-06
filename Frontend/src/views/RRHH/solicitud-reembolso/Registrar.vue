<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Crear nueva solicitud de reembolso</div>
						<div class="box-description">Formulario para registrar solicitud de reembolso</div>
					</div>

					<div class="row">
						<div class="col-sm-8">
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
								<label for>Fecha</label>
								<datepicker :format="format" :language="es" :value="new Date()" v-model="fechax2" @selected="onDateSelect"></datepicker>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.fecha_solicitud" v-text="message"></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for>Caja chica</label>
								<v-select label="descripcion" v-model="form.caja" v-on:input="obtenerComp" :options="cajas"></v-select>
								<ul class="error-messages">
									<li :key="message" v-for="message in errorMessages.caja" v-text="message"></li>
								</ul>
							</div>
						</div>
					</div>
				<br>

                    <div class="alert alert-success">
                        <span><strong>Detalle de la solicitud</strong></span>
                    </div>
						<div class="row">

                        <template v-if="loading">
                            <BlockUI :message="msg" :url="url"></BlockUI>
                        </template>
						</div>

					<div class="row">
						<div class="col-sm-12">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.comprobante" v-text="message"></li>
							</ul>

							<table class="table table-bordered">
								<thead>
								<tr>
								<!--	<th></th>-->
									<th >Comprobante</th>
									<th >Beneficiario</th>
									<th >Concepto </th>
									<th >Valor</th>
									<!--<th >SubTotal</th>-->
								</tr>
								</thead>
								<tbody>
								<template  v-for="(item, index) in form.comprobante">
									<tr>
										<!--<td style="width: 2%">
											<button @click="eliminarLinea(item, index)">
												<i class="fa fa-trash"></i>
											</button>
										</td>-->
										<td>
											<input  class="form-control" type="number" :disabled="true"  v-model="item.numero">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`comprobante.${index}.numero`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="text" :disabled="true" v-model.number="item.trabajador_comprobante.nombre_completo">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`comprobante.${index}.trabajador_comprobante.nombre_completo`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="text" :disabled="true" v-model.number="item.concepto">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`comprobante.${index}.concepto`]" v-text="message"></li>
											</ul>
										</td>
										<td>
											<input  class="form-control" type="number" :disabled="true" v-model.number="item.monto_entregado">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages[`comprobante.${index}.monto_entregado`]" v-text="message"></li>
											</ul>
										</td>


									</tr>
									<tr></tr>
								</template>
								</tbody>
								<tfoot>
								<tr>
									<td colspan="2"></td>
									<td><strong>Total</strong></td>
									<td> <strong> {{calcular_total | formatMoney(2)}}</strong></td>
								</tr>
								<tr>

								</tr>
								</tfoot>
							</table>
						</div>
                    </div>

					<br>


					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-comprobante' }" tag="button">
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
	import solicitud from '../../api/solicitud_reembolso'
	import normativa from '../../api/normativas'
	import loadingImage from '../../assets/images/block50.gif'
	import sucursal from "../../api/sucursales";
	import cliente from "../../api/clientes";
	import es from "vuejs-datepicker/dist/locale/translations/es";
	import numberasstring from "../../assets/custom-scripts/NumberAsString";
	import asignacion from "../../api/asignacion-ingreso-deduccion";

	export default {
		data() {
			return {
				loading:false,
				msg: 'Cargando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				detalleForm: {
					cantidad:0,
					fecha_viatico: moment(new Date()).format("YYYY-MM-DD"),
					objetivo: '',
					monto_viatico:0,
				},
				form: {
					trabajador: [],
					fecha_solicitud : moment(new Date()).format("YYYY-MM-DD"),
					total:0,
					detalleSolicitud:[],
					comprobante:[],
					concepto:'',
					monto_letras:'',

				},
				cajas:[],
				fechax: new Date(),
				fechax2: new Date(),
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		computed:{
				calcular_total() {
				this.form.total = this.form.comprobante.reduce((carry, item) => {
							return(carry + Number(item.monto_entregado));
						}
						, 0)
					this.monto_letras();
				return this.form.total;
			},

		},
		methods: {

			obtenerComprobantes(){
				var self = this
				self.loading = true;
				solicitud.obtenerSolicitud({}, data => {
					//self.form.comprobante = data.comprobante;
					self.cajas = data.cajas;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			obtenerComp(){ //Cargar comprobantes de la caja chica seleccionada
				var self = this;
				self.loading = true;
				this.form.comprobante=[];
				solicitud.obtenerComp({id_caja_chica : this.form.caja.id_caja_chica}, data => {
					self.form.comprobante = data.comp;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			registrar() {
				var self = this;
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				solicitud.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos registrados correctamente",5);
					this.$router.push({
						name: 'listado-comprobante'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			},
			monto_letras(){
				if(this.form.total >0){
					this.form.monto_letras = numberasstring.numberasstring(this.form.total,'CORDOBA','CORDOBAS',true);
				}else{
					this.form.monto_letras ='SELECCIONE UN MONTO MAYOR QUE CERO';
				}
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
			this.obtenerComprobantes();
		}
	}
</script>

<style>
	.btn-agregar {
		margin-top:33px;
	}
</style>