<template>
	<div class="main">
		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Control de planillas</div>
				<div class="box-description">Listado de planillas</div>
				<div class="box-description"><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal' }">Módulos</router-link>><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal-nomina' }"> Módulo nómina</router-link>> Control de planillas</div>
			</div>
			<div class="row">
				<div class="col-md-6 sm-text-center">
					<router-link class="btn btn-success" tag="button" :to="{ name: 'registrar-planilla-control' }">
						<i class="pe-7s-plus"></i> Registrar planilla
					</router-link>
				</div>
				<div @keyup.enter="filter.page = 1;obtenerplanillas();" class="col-md-6 sm-text-center form-inline justify-content-end">
					<div class="form-group check-label">
						<label class="check-label form-control mb-1 mr-sm-1 mb-sm-0"><input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox"> Mostrar Todos</label>
					</div>
					<select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
						<option value="descripcion">Descripcion</option>
					</select>
					<input v-model="filter.search.value" class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text">
					<button class="btn btn-info" @click="filter.page = 1; obtenerplanillas();">
						<i class="pe-7s-search"></i> Buscar
					</button>
				</div>
			</div>
			<div class="table-responsive mt-3">
				<table class="table table-striped table-bordered">
					<thead>
					<tr>
						<!--<th>Sucursal</th>-->
						<th>Código de planilla</th>
						<th>Tipo de planilla</th>
						<th>Descripción</th>
						<th>Desde</th>
						<th>Hasta</th>
						<th class="text-center table-number">Estado</th>
						<th class="text-center action">Acciones</th>
					</tr>
					</thead>
					<tbody>
					<tr :key="planilla.id_planilla_control" v-for="planilla in planillas">

						<!--<td class="text-center">{{ planilla.planilla_control_sucursal.descripcion }}</td>-->
						<td>{{ planilla.codigo_planilla}}</td>
						<td>{{ planilla.tipo_planilla }}</td>
						<td>{{ planilla.descripcion}}</td>
						<td>{{ formatDate(planilla.f_desde) }}</td>
						<td>{{ formatDate(planilla.f_hasta) }}</td>
						<td class="text-center">
							<div v-if="planilla.estado === 1">
								<span class="badge badge-success">Activo</span>
							</div>
							<div v-if="planilla.estado === 2">
								<span class="badge badge-primary">Generada</span>
							</div>
							<div v-if="planilla.estado === 3">
								<span class="badge badge-info">Procesada</span>
							</div>
							<div v-if="planilla.estado === 0">
								<span class="badge badge-danger">Desactivado</span>
							</div>
						</td>
						<td class="text-center" v-if="planilla.estado < 3">
							<router-link :to="{ name: 'actualizar-planilla-control', params: { id_planilla_control: planilla.id_planilla_control } }" tag="a"><i class="icon-pencil"></i></router-link>
							<template v-if="planilla.estado === 2"><router-link  v-tooltip="'Procesar planilla'" tag="a" :to="{ name: 'procesar-planilla', params: { id_planilla_control: planilla.id_planilla_control } }"><i class="icon-arrow-right"></i></router-link></template>
							<!--<a @click.prevent="downloadItem('planillas/reporte/expediente/'+planilla.id_planilla)"><i class="icon-arrow-down-circle"></i></a>-->
						</td>
					</tr>
					<tr v-if="!planillas.length">
						<td class="text-center" colspan="8">Sin registros</td>
					</tr>
					</tbody>
				</table>
			</div>
			<pagination
					@changePage="changePage"
					@changeLimit="changeLimit"
					:items="planillas"
					:total="total"
					:page="filter.page"
					:limitOptions="filter.limitOptions"
					:limit="filter.limit"
			></pagination>

			<template v-if="loading">
				<BlockUI :message="msg" :url="url"></BlockUI>
			</template>

		</div>
	</div>
</template>

<script type="text/ecmascript-6">
	import planilla from "../../api/planillas_controles";
	import loadingImage from '../../assets/images/block50.gif'


	export default {
		data() {
			return {
				descargando:false,
				loading:true,
				msg: 'Cargando el contenido espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				filter: {
					page: 1,
					limit: 5,
					limitOptions: [5, 10, 15, 20],
					search: {
						field: 'descripcion',
						value: "",
						status:0,
					}
				},
				planillas: [],
				total: 0
			};
		},
		methods: {
			formatDate(date) {
				return moment(date).format('YYYY-MM-DD')
			},
			obtenerplanillas() {
				var self = this;
				self.loading = true;
				planilla.obtener(self.filter, data => {
					self.planillas = data.rows
					self.total = data.total
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			changeLimit(limit) {
				this.filter.page = 1;
				this.filter.limit = limit;
				this.obtenerplanillas();
			},
			changePage(page) {
				this.filter.page = page;
				this.obtenerplanillas();
			},

			downloadItem(extension, id_importacionx) {
				var self = this;
				if (!this.descargando) {
					self.loading = true;
					let url = 'inventario/facturas/reporte/' + extension + '/' + id_importacionx;
					this.descargando = true;
					alertify.success("Descargando datos, espere un momento.....", 5);
					axios.get(url, {responseType: 'blob'})
							.then(({data}) => {
								let blob = new Blob([data], {type: 'text/plain'})

								extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

								let link = document.createElement('a');
								link.href = window.URL.createObjectURL(blob)
								link.download = 'FormatoFactura.' + extension;
								link.click()
								//this.descargando = false;
								self.loading = false;
								self.descargando = false;
							}).catch(function (error) {
						alertify.error("Error Descargando archivo.....", 5);
						self.descargando = false;
						self.loading = false;
					})
				} else {
					alertify.warning("Espere a que se complete la descarga anterior", 5);
				}
			},


		},
		/*components: {
          pagination: Pagination
        },*/
		mounted() {
			this.obtenerplanillas();
		}
	};
</script>
<style lang="scss" scoped>
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