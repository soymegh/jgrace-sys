<template>
	<div class="main">
		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Administrar solicitudes de reembolso</div>
				<div class="box-description">Listado de solicitudes de reembolso</div>
				<div class="box-description"><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal' }">Módulos</router-link>><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal-tesoreria' }"> Módulo tesorería</router-link>> Comprobantes</div>
			</div>
			<div class="row">
				<div class="col-sm-5 sm-text-center">
					<router-link :to="{ name: 'registrar-reembolso' }" class="btn btn-primary" tag="button">
						<i class="pe-7s-plus"></i> Registrar solicitud de reembolso
					</router-link>
				</div>
				<div @keyup.enter="filter.page = 1;obtener();" class="col-md-7 sm-text-center form-inline justify-content-end">
					<div class="form-group check-label">
						<label class="check-label form-control mb-1 mr-sm-1 mb-sm-0"><input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox"> Mostrar Todos</label>
					</div>
					<select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
						<option value="descripcion">Descripción</option>
					</select>
					<input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text" v-model="filter.search.value">
					<button @click="filter.page = 1;obtener();" class="btn btn-info"><i class="pe-7s-search"></i> Buscar</button>
					<!--<a :disabled="descargando" @click.prevent="downloadItem('pdf')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-danger"><i aria-hidden="true" class="fa fa-file-pdf-o"></i></button></a>-->

				</div>
			</div>
			<div class="table-responsive mt-3">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Descripción</th>
							<th>Caja chica</th>
							<th >Fecha solicitud</th>
							<th class="text-center">Estado</th>
							<th class="text-center action">Opciones</th>
						</tr>
					</thead>
					<tbody>
						<tr :key="comprobante.id_solicitud_reembolso" v-for="comprobante in comprobantes">
							<td>{{ comprobante.descripcion }}</td>
							<td>{{ comprobante.reembolso_caja.descripcion }}</td>
							<td>{{ formatDate(comprobante.fecha ) }}</td>
							<td class="text-center">
							 <div v-if="comprobante.estado === 1">
                              <span class="badge badge-success">Registrado</span>
                             </div>
								<div v-if="comprobante.estado === 2">
									<span class="badge badge-info">Revisado</span>
								</div>
								<div v-if="comprobante.estado === 3">
									<span class="badge badge-primary">En reembolso</span>
								</div>
								<div v-if="comprobante.estado === 4">
									<span class="badge badge-primary">Reembolsado</span>
								</div>
                                 <div v-if="comprobante.estado === 0">
                              <span class="badge badge-danger">Anulado</span>
                                 </div>
							</td>
							<td class="text-center">
								<!--<router-link :to="{ name: 'actualizar-comprobante', params: { id_solicitud_reembolso: comprobante.id_solicitud_reembolso } }" tag="a"><i class="icon-pencil"></i></router-link>-->
								<a :disabled="descargando" @click.prevent="downloadItem('pdf',comprobante.id_solicitud_reembolso)"><i aria-hidden="true" class="fa fa-eye"></i></a>
							</td>
						</tr>
						<tr v-if="!comprobantes.length">
							<td class="text-center" colspan="5">Sin datos</td>
						</tr>
					</tbody>
				</table>
			</div>
			<pagination :items="comprobantes" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page" :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>

			<template v-if="loading">
				<BlockUI :message="msg" :url="url"></BlockUI>
			</template>
		</div>
	</div>
</template>

<script type="text/ecmascript-6">
	import comprobante from '../../api/solicitud_reembolso';
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
						field: 'descripcion',
						value: '',
						status:0,
						tipo:1,
					}
				},
				comprobantes: [],
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
				comprobante.obtener(self.filter, data => {
					self.comprobantes = data.rows
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
			downloadItem(extension, id_solicitud_reembolso) {
				var self = this;
				if (!this.descargando) {
					self.loading = true;
					let url = 'modulo-tesoreria/reembolsos/reporte/' + id_solicitud_reembolso;
					this.descargando = true;
					alertify.success("Descargando datos, espere un momento.....", 5);
					axios.get(url, {responseType: 'blob'})
							.then(({data}) => {
								let blob = new Blob([data], {type: 'application/pdf'})

								extension === 'xls' ? blob = new Blob([data], {type: 'application/octet-stream'}) : blob = new Blob([data], {type: 'application/pdf'});

								let link = document.createElement('a');
								link.href = window.URL.createObjectURL(blob)
								link.download = 'ReporteReembolso.pdf' //+ extension;
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