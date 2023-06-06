<template>
	<div class="main">
		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Administrar solicitudes de vacaciones</div>
				<div class="box-description">Listado de solicitudes de vacaciones</div>
				<div class="box-description"><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal' }">Módulos</router-link>><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal-nomina' }"> Módulo nomina</router-link>> Solicitud de vacaciones</div>
			</div>
			<div class="row">
				<div class="col-md-6 sm-text-center">
					<router-link :to="{ name: 'registrar-vacaciones' }" class="btn btn-success" tag="button">
						<i class="pe-7s-plus"></i> Registrar solicitud
					</router-link>
				</div>
				<div @keyup.enter="filter.page = 1;obtener();" class="col-md-6 sm-text-center form-inline justify-content-end">
					<div class="form-group check-label">
						<label class="check-label form-control mb-1 mr-sm-1 mb-sm-0"><input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox"> Mostrar Todos</label>
					</div>
					<select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
						<option value="num_solicitud">No. solicitud</option>
					</select>
					<input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text" v-model="filter.search.value">
					<button @click="filter.page = 1;obtener();" class="btn btn-info"><i class="pe-7s-search"></i> Buscar</button>
					<!--<a :disabled="descargando" @click.prevent="downloadItem('pdf')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-danger"><i aria-hidden="true" class="fa fa-file-pdf-o"></i></button></a>
					<a :disabled="descargando" @click.prevent="downloadItem('xls')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-success"><i aria-hidden="true" class="fa fa-file-excel-o"></i></button></a>-->
				</div>
			</div>
			<div class="table-responsive mt-3">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>No. Solicitud</th>
							<th>Tipo de solicitud</th>
							<th>Trabajador</th>
							<th>Fecha solicitud</th>
							<th class="text-center table-number">Estado</th>
							<th colspan="3" class="text-center action">Opciones</th>
						</tr>
					</thead>
					<tbody>
						<tr :key="solicitud.id_vacacion_solicitud" v-for="solicitud in solicitudes">
							<td>{{ solicitud.num_solicitud }}</td>
							<td>{{ solicitud.id_tipo ===1 ? 'Solicitud de vacaciones' : solicitud.id_tipo===2 ? 'Solicitud de devolución de vacaciones' : solicitud.id_tipo === 3 ? 'Solicitud de pago de vacaciones' : 'N/A'}}</td>
							<td>{{ solicitud.solicitud_trabajador?solicitud.solicitud_trabajador.nombre_completo:'N/A'}}</td>
							<td>{{ formatDate( solicitud.f_solicitud) }}</td>
							<td class="text-center">
								<div v-if="solicitud.estado === 1">
									<span class="badge badge-success">Registrado</span>
								</div>
								<div v-if="solicitud.estado === 2">
									<span class="badge badge-info">Autorizado</span>
								</div>
								<div v-if="solicitud.estado === 0">
									<span class="badge badge-danger">Anulado</span>
								</div>
							</td>
							<td class="text-center">
								<template v-if="solicitud.estado===1">
									<button @click="cambiarEstadoSolicitud(0,solicitud.id_vacacion_solicitud)" class="btn btn-danger"><i
											class="fa fa-trash-o"> Rechazar Solicitud</i></button>
								</template>
								<!--<template v-if="solicitud.estado === 2">
									<button @click="cambiarEstadoSolicitud(3,solicitud.id_vacacion_solicitud)" class="btn btn-danger"><i
											class="fa fa-trash-o"> Revocar aprobación</i></button>
								</template>-->
							</td>
							<td class="text-center">
								<template v-if="solicitud.estado===1">
									<button @click="cambiarEstadoSolicitud(2,solicitud.id_vacacion_solicitud)" class="btn btn-success"><i
											class="fa fa-trash-o"> Aprobar Solicitud</i></button>
								</template>
								<template v-else>
								</template>

							</td>
							<td class="text-center">
								<router-link :to="{ name: 'actualizar-vacaciones', params: { id_vacacion_solicitud: solicitud.id_vacacion_solicitud } }" tag="a"><i class="icon-pencil"></i></router-link>
								<a :disabled="descargando" @click.prevent="downloadItem('pdf',solicitud.solicitud_trabajador.id_trabajador)"><i aria-hidden="true" class="fa fa-eye"></i></a>
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
	import solicitud from '../../api/solicitud-vacaciones';
	import loadingImage from '../../assets/images/block50.gif'
	import cliente from "../../api/clientes";
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
						field: 'num_solicitud',
						value: '',
						status:0
					}
				},
				solicitudes: [],
				total: 0,
				descargando : false,
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
			formatDate(date) {
				return moment(date).format('YYYY-MM-DD')
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
			cambiarEstadoSolicitud(estado,id_cliente){

				var txtAprobar = '¿Cual es la justificación de aprobación de esta solicitud?';
				var txtRechazar ='¿Está seguro de rechazar la solicitud para este empleado?';
				var txtAnular ='¿Cual es la justificación de anulación de esta solicitud?';

				var self = this;

				self.$swal.fire({
					title: 'Confirmación de cambio de estado de solicitud de vacaciones',
					text: estado===2?txtAprobar:estado===3?txtAnular:txtRechazar,
					type: 'warning',
					input: 'text',
					inputAttributes: {
						autocapitalize: 'off'
					},
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Si, confirmar',
					cancelButtonText:'Cancelar'
				}).then((result) => {
					self.loading=true;
					if (result.value) {
						//var self = this
						solicitud.cambiarEstadoSolicitud({
							id_vacacion_solicitud: id_cliente,
							estado: estado,
							observacion: result.value
						}, data => {
							alertify.warning("El estado de solicitud ha sido cambiado correctamente", 5);
							self.obtener();
						}, err => {
							self.loading=false;
							console.log(err)
						})
					}else{
						self.loading=false;
					}
				})

			},
			downloadItem(extension, id_trabajador) {
				var self = this;
				if (!this.descargando) {
					self.loading = true;
					let url = 'rrhh/solicitud-vacaciones/reporte/' + id_trabajador;
					this.descargando = true;
					alertify.success("Descargando datos, espere un momento.....", 5);
					axios.get(url, {responseType: 'blob'})
							.then(({data}) => {
								let blob = new Blob([data], {type: 'application/pdf'})

								extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

								let link = document.createElement('a');
								link.href = window.URL.createObjectURL(blob)
								link.download = 'FormatoSolicitudVacaciones.pdf' //+ extension;
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