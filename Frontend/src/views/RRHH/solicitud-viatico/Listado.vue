<template>
	<div class="main">
		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Administrar solicitudes de viatico</div>
				<div class="box-description">Listado de solicitudes</div>
				<div class="box-description"><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal' }">Módulos</router-link>><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal-tesoreria' }"> Módulo tesorería</router-link>> Solicitud de viaticos</div>
			</div>
			<div class="row">
				<div class="col-md-3 sm-text-center">
					<router-link :to="{ name: 'registrar-solicitud' }" class="btn btn-success" tag="button">
						<i class="pe-7s-plus"></i> Registrar solicitud
					</router-link>
				</div>
				<div @keyup.enter="filter.page = 1;obtener();" class="col-md-9 sm-text-center form-inline justify-content-end">
					<div class="form-group check-label">
						<label class="check-label form-control mb-1 mr-sm-1 mb-sm-0"><input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox"> Mostrar Todos</label>
					</div>
					<select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
						<option value="concepto">Concepto</option>
					</select>
					<input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text" v-model="filter.search.value">
					<button @click="filter.page = 1;obtener();" class="btn btn-info" style="margin-left: 5px" ><i class="pe-7s-search"></i> Buscar</button>
					<!--<a :disabled="descargando" @click.prevent="downloadItem('pdf')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-danger"><i aria-hidden="true" class="fa fa-file-pdf-o"></i></button></a>
					<a :disabled="descargando" @click.prevent="downloadItem('xls')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-success"><i aria-hidden="true" class="fa fa-file-excel-o"></i></button></a>-->
				</div>
			</div>
			<div class="table-responsive mt-3">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Concepto</th>
							<th>Nombre solicitante</th>
							<th>Fecha solicitud</th>
							<th class="text-center">Monto total C$</th>
							<th class="text-center">Estado</th>
							<th class="text-center action">Opciones</th>
						</tr>
					</thead>
					<tbody>
						<tr :key="solicitud.id_solicitud_viatico" v-for="solicitud in solicitudes">
							<td>{{solicitud.concepto}}</td>
							<td>{{ solicitud.solicitud_trabajador?solicitud.solicitud_trabajador.nombre_completo : 'N/D' }}</td>
							<td>{{ formatDate(solicitud.fecha_solicitud) }}</td>
							<td class="text-center">{{ solicitud.monto_total }}</td>
							<td class="text-center">
							 <div v-if="solicitud.estado === 1">
                              <span class="badge badge-success">Registrado</span>
                             </div>
								<div v-if="solicitud.estado === 2">
									<span class="badge badge-info">Revisado</span>
								</div>
								<div v-if="solicitud.estado === 3">
									<span class="badge badge-primary">Comprobante</span>
								</div>
								<div v-if="solicitud.estado === 4">
									<span class="badge badge-info">Reembolsado</span>
								</div>
                                 <div v-if="solicitud.estado === 0">
                              <span class="badge badge-danger">Anulado</span>
                                 </div>
							</td>
							<td class="text-center">
								<router-link :to="{ name: 'actualizar-solicitud', params: { id_solicitud_viatico: solicitud.id_solicitud_viatico } }" tag="a"><i class="icon-pencil"></i></router-link>
								<template v-if="[2].indexOf(Number(solicitud.estado)) >= 0 ">
									<router-link  v-tooltip="'Crear comprobante'" :to="{ name: 'registrar-comprobante-solicitud', params: { id_solicitud_viatico: solicitud.id_solicitud_viatico } }" tag="a"><i class="fa fa-bandcamp"></i></router-link>
								</template>
								<a :disabled="descargando" @click.prevent="downloadItem('pdf',solicitud.id_solicitud_viatico)"><i aria-hidden="true" class="fa fa-eye"></i></a>
							</td>
						</tr>
						<tr v-if="!solicitudes.length">
							<td class="text-center" colspan="5">Sin datos</td>
						</tr>
					</tbody>
				</table>
			</div>
			<pagination :items="solicitudes" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page" :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>

			<template v-if="loading">
				<BlockUI :message="msg" :url="url"></BlockUI>
			</template>
		</div>
	</div>
</template>

<script type="text/ecmascript-6">
	import solicitud from '../../api/solicitud_viatico';
	import loadingImage from '../../assets/images/block50.gif'
	import cuenta from "../../api/cuentas_por_cobrar";
	//import Pagination from '../layout/Pagination'

	export default {
		data() {
			return {
				loading:true,
				msg: 'Cargando el contenido espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				filter: {
					page: 1,
					limit: 5,
					limitOptions: [5, 10, 15, 20],
					search: {
						field: 'concepto',
						value: '',
						status:0
					}
				},
				solicitudes: [],
				total: 0,
				descargando : false,
				trabajadoresBusquedaURL: "/trabajadores/buscar_trabajador",
				trabajador:{}
			}
		},
		methods: {
			obtener() {
				var self = this
				self.loading = true;
				solicitud.obtener(self.filter, data => {
					self.solicitudes = data.rows
					self.total = data.total
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
			downloadItem(extension, id_solicitud_viatico) {
				var self = this;
				if (!this.descargando) {
					self.loading = true;
					let url = 'solicitud-viatico/reporte/' + extension + '/' + id_solicitud_viatico;
					this.descargando = true;
					alertify.success("Descargando datos, espere un momento.....", 5);
					axios.get(url, {responseType: 'blob'})
							.then(({data}) => {
								let blob = new Blob([data], {type: 'application/pdf'})

								extension === 'xls' ? blob = new Blob([data], {type: 'application/excel'}) : blob = new Blob([data], {type: 'application/pdf'});

								let link = document.createElement('a');
								link.href = window.URL.createObjectURL(blob)
								link.download = 'FormatoSolicitudViatico.' + extension;
								link.click()
								this.descargando = false;
								self.loading = false;
							}).catch(function (error) {
						alertify.error("Error Descargando archivo.....", 5);
						self.descargando = false;
						self.loading = false;
					})
				} else {
					alertify.warning("Espere a que se complete la descarga anterior", 5);
				}
			},
			seleccionarTrabajador(e) {
				const trabajadorP = e.target.value;
				var self = this;
				self.trabajador = trabajadorP;

				self.filter.search.value = self.trabajador.id_trabajador;
			},
			deseleccionar()
			{
				this.trabajador = {};
				this.filter.search.value=''
			},
			formatDate(date) {
				return moment(date).format('YYYY-MM-DD')
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
    	    width: 100px;
        }
    }
</style>