<template>
		<b-card>
			<b-row>
				<div @keyup.enter="filter.page = 1;obtener();" class="col-md-12 sm-text-center form-inline justify-content-end">
					<b-form-select v-model="filter.search.field" class="mb-1 mr-sm-1 mb-sm-0 search-field">
						<option value="descripcion_movimiento">Descripción</option>
						<option value="no_documento_origen">No.Documento</option>
					</b-form-select>
					<input v-model="filter.search.value" class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text">
					<b-button @click="filter.page = 1;obtener();" class="btn btn-info"><feather-icon icon="SearchIcon"></feather-icon> Buscar</b-button>
				</div>
			</b-row>
			<div class="table-responsive mt-3">
				<table class="table table-striped ">
					<thead>
						<tr>
							<th></th>
							<th>Tipo Documento</th>
							<th>No. Documento</th>
							<th>Fecha Emisión</th>
							<th>Descripción</th>
							<th>Cliente</th>
							<!--<th class="">Débito</th>-->
							<th class="">Monto</th>
							<th class="">Saldo actual</th>
							<th >Fecha Vencimiento</th>
							<th class="text-center table-number">Estado</th>
							<!--<th class="text-center table-number">Opciones</th>-->
						</tr>
					</thead>
					<tbody>
					<template v-for="(cuentaxcobrar,key) in cuentas_por_cobrar">
						<tr :key="cuentaxcobrar.id_cuentaxcobrar">
							<td  class="detail-link">
								<div v-b-tooltip.hover.top="'Mostrar Detalle'" @click="mostrarProductos(key)" class="link"></div>
							</td>
							<td>{{ cuentaxcobrar.id_tipo_documento === 1? 'Factura Crédito':cuentaxcobrar.id_tipo_documento === 2?'Recibo':cuentaxcobrar.id_tipo_documento === 3?'Nota de Crédito':cuentaxcobrar.id_tipo_documento === 4?'Nota de Débito':'Otro' }}</td>
							<td>{{ cuentaxcobrar.no_documento_origen }}</td>
							<td>{{ formatDate(cuentaxcobrar.fecha_movimiento) }}</td>
							<td>{{ cuentaxcobrar.descripcion_movimiento }}</td>
							<td>{{ cuentaxcobrar.cuenta_cliente? cuentaxcobrar.cuenta_cliente.nombre_comercial:''}}</td>
							<!--<template v-if="cuentaxcobrar.monto_movimiento < 0">
								<td class="">C$ {{ cuentaxcobrar.monto_movimiento < 0?cuentaxcobrar.monto_movimiento*-1:'-' | formatMoney(2) }}</td>
								<td class=""> - </td>
							</template>-->

							<template v-if="currency_id === 1">
								<template v-if="cuentaxcobrar.monto_movimiento > 0">
									<!--<td class=""> - </td>-->
									<td class="">C$ {{ cuentaxcobrar.monto_movimiento > 0?cuentaxcobrar.monto_movimiento:'-' | formatMoney(2) }}</td>

								</template>
								<template v-if="cuentaxcobrar.id_tipo_documento === 1">
									<td class="">C$ {{ cuentaxcobrar.saldo_actual | formatMoney(2) }}</td>
								</template>
								<template v-else>
									<td class=""> - </td>
								</template>
							</template>
							<template v-else>
								<template v-if="cuentaxcobrar.monto_movimiento_me > 0">
									<!--<td class=""> - </td>-->
									<td class="">$ {{ cuentaxcobrar.monto_movimiento_me > 0?cuentaxcobrar.monto_movimiento_me:'-' | formatMoney(2) }}</td>

								</template>
								<template v-if="cuentaxcobrar.id_tipo_documento === 1">
									<td class="">$ {{ cuentaxcobrar.saldo_actual_me | formatMoney(2) }}</td>
								</template>
								<template v-else>
									<td class=""> - </td>
								</template>
							</template>

							<td>{{ cuentaxcobrar.id_tipo_documento === 1? formatDate(cuentaxcobrar.fecha_vencimiento) :'N/A' }}</td>
							<td class="text-center">
								<div v-if="cuentaxcobrar.estado === 1">
									<span class="badge badge-success">Abierto</span>
								</div>
								<div v-else>
									<span class="badge badge-danger">Cerrado</span>
								</div>
							</td>
							<!--<td class="text-center">
								&lt;!&ndash;<template v-if="[1,2,3,4].indexOf(tipo.id_tipo_entrada) < 0">&ndash;&gt;
								&lt;!&ndash;<router-link tag="a" :to="{ name: 'actualizar-tipo-entrada', params: { id_tipo_entrada: tipo.id_tipo_entrada } }"><i class="icon-pencil"></i></router-link>&ndash;&gt;
								&lt;!&ndash;</template>&ndash;&gt;
							</td>-->
						</tr>
						<tr v-if="cuentaxcobrar.mostrar" :key="cuentaxcobrar.no_documento_origen">
							<td></td>
							<td colspan="9">
								<table class="table table-striped">
									<thead>
									<tr>
										<th>Fecha pago</th>
										<th>Descripción</th>
										<th>Monto C$</th>
										<th>Monto $</th>
									</tr>
									</thead>
									<tbody>
									<tr v-for="recibosDetalle in cuentaxcobrar.cuentas_detalles" :key="recibosDetalle.id_recibo_detalle">
										<td>{{ formatDate(recibosDetalle.fecha_pago) }}</td>
										<td>{{ recibosDetalle.descripcion_pago }}</td>
										<td>{{ recibosDetalle.monto | formatMoney(2)}}</td>
										<td>{{ recibosDetalle.monto_me | formatMoney(2)}}</td>
									</tr>
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</td>
							<td></td>
						</tr>
						<tr v-if="!cuentas_por_cobrar.length">
							<td class="text-center" colspan="5">Sin datos</td>
						</tr>
					</template>

					</tbody>
				</table>
			</div>
			<b-card-footer>
				<pagination @changePage="changePage" @changeLimit="changeLimit" :items="cuentas_por_cobrar" :total="total" :page="filter.page" :limitOptions="filter.limitOptions" :limit="filter.limit"></pagination>
			</b-card-footer>

			<template v-if="loading">
				<BlockUI :url="url"></BlockUI>
			</template>
		</b-card>
</template>

<script type="text/ecmascript-6">
	import cuentas_cobrar from '../../../api/CuentasXCobrar/cuentas_por_cobrar'
	import es from 'vuejs-datepicker/dist/locale/translations/es'
	import moment from "../../../../../Backend/resources/app/assets/plugins/moment/moment";
	import ToastificationContent from "../../../@core/components/toastification/ToastificationContent";
	import {
		BPaginationNav,
		BFormCheckbox,
		BFormGroup,
		BCard,
		BCardFooter,
		VBTooltip,
		BRow,
		BButton,
		BFormCheckboxGroup,
		BFormDatepicker,
		BAlert,
		BFormSelect,
	} from 'bootstrap-vue'
	import loadingImage from '../../../assets/images/loader/block50.gif'
	import vSelect from 'vue-select'

	export default {
		components: {
			BCard,
			BCardFooter,
			BPaginationNav,
			BFormCheckbox,
			BFormGroup,
			vSelect,
			BRow,
			BButton,
			BFormCheckboxGroup,
			BFormDatepicker,
			BAlert,
			BFormSelect,
		},
		directives: {
			'b-tooltip': VBTooltip,
		},
		data() {
			return {
				loading:true,
				msg: 'Cargando el contenido espere un momento',
				url : loadingImage,   //It is important to import the loading image then use here
				filter: {
					page: 1,
					limit: 5,
					type:'cliente',
					limitOptions: [5, 10, 15, 20],
					search: {
						field: 'descripcion_movimiento',
						value: ''
					}
				},
				cuentas_por_cobrar: [],
				total: 0,
				currency_id:''
			}
		},
		methods: {
			formatDate(date) {
				return moment(date).format('YYYY-MM-DD')
			},
			mostrarProductos(key) {
				if (this.cuentas_por_cobrar[key].mostrar) {
					this.cuentas_por_cobrar[key].mostrar = 0;
				} else {
					this.cuentas_por_cobrar[key].mostrar = 1;
				}
			},
			obtener() {
				var self = this;
				self.loading = true;
				cuentas_cobrar.obtener(self.filter, data => {
					data.rows.forEach((cuentas_por_cobrar, key) => {
						data.rows[key].mostrar = 0;
					});
					self.cuentas_por_cobrar = data.rows;
					self.total = data.total;
					self.currency_id = Number(data.currency_id);
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			changeLimit(limit) {
				this.filter.page = 1
				this.filter.limit = limit
				this.obtener()
			},
			changePage(page) {
				this.filter.page = page
				this.obtener()
            },
		},
		/*components: {
			'pagination': Pagination
		},*/
		mounted() {
			this.obtener()
		}
    }
</script>

<style lang="scss" >
	@import 'src/@core/scss/vue/libs/vue-select';
	.search-field {
		min-width: 120px;
	}

	.table {
		a {
			color: #2675dc;
		}

		.table-number {
			width: 60px;
		}

		.action {
			width: 180px;
		}

		.detail-link {
			width: 60px;
			position: relative;

			.link {
				width: 10px;
				height: 4px;
				margin-left: auto;
				margin-right: auto;
				cursor: pointer;
				margin-top: 8px;
				background-color: #595959;
				border: 1px solid #595959;

				&:before {
					content: "";
					width: 4px;
					height: 10px;
					left: 0px;
					right: 0px;
					cursor: pointer;
					margin: 0px auto;
					margin-top: -4px;
					position: absolute;
					background-color: #595959;
					border: 1px solid #595959;
				}
			}
		}
	}
</style>
