<template>
	<div class="main">
		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Control de trabajadores</div>
				<div class="box-description">Listado de trabajadores</div>
				<div class="box-description"><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal' }">Módulos</router-link>><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal-nomina' }"> Módulo nomina</router-link>> Trabajadores</div>
			</div>
			<div class="row">
				<div class="col-md-4 sm-text-center">
					<router-link :to="{ name: 'registrar-trabajador' }" class="btn btn-primary" tag="button">
						<i class="pe-7s-plus"></i> Registrar Nuevo Trabajador
					</router-link>
				</div>
				<div @keyup.enter="filter.page = 1;obtenerTrabajadores();" class="col-md-8 sm-text-center form-inline justify-content-end">
					<div class="form-group check-label">
						<label class="check-label form-control mb-1 mr-sm-1 mb-sm-0"><input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox"> Mostrar Todos</label>
					</div>
					<select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
						<option value="primer_nombre">Nombres</option>
						<option value="primer_apellido">Primer Apellido</option>
						<option value="segundo_apellido">Segundo Apellido</option>
					</select>
					<input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text" v-model="filter.search.value">
					<button @click="filter.page = 1;obtenerTrabajadores();" class="btn btn-primary"><i class="pe-7s-search"></i> Buscar</button>
					<!--<a :disabled="descargando" @click.prevent="downloadItem('pdf')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-danger"><i aria-hidden="true" class="fa fa-file-pdf-o"></i></button></a>
					<a :disabled="descargando" @click.prevent="downloadItem('xls')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-success"><i aria-hidden="true" class="fa fa-file-excel-o"></i></button></a>-->
				</div>
			</div>
			<div class="table-responsive mt-3">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th class="text-center table-number">No</th>
							<th>Nombre completo</th>
							<th>Cedula</th>
							<th>Area</th>
							<th>Cargo</th>
							<th class="text-center table-number">Estado</th>
							<th class="text-center action">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<tr :key="trabajador.id_trabajador" v-for="trabajador in trabajadores">

							<td class="text-center">{{ trabajador.codigo }}</td>
							<td>{{ trabajador.primer_nombre + ' ' +(trabajador.segundo_nombre?trabajador.segundo_nombre:'')+ ' ' + trabajador.primer_apellido + ' ' + (trabajador.segundo_apellido?trabajador.segundo_apellido:'')}}</td>
							<td>{{ trabajador.cedula }}</td>
							<td>{{ trabajador.trabajador_area.descripcion }}</td>
							<td>{{ trabajador.trabajador_cargo.descripcion }}</td>
							<td class="text-center">
							 <div v-if="trabajador.activo">
                              <span class="badge badge-success">Activo</span>
                             </div>
                                 <div v-else>
                              <span class="badge badge-danger">Desactivado</span>
                                 </div>
							</td>
							<td class="text-center">
								<router-link :to="{ name: 'actualizar-trabajador', params: { id_trabajador: trabajador.id_trabajador } }" tag="a"><i class="icon-pencil"></i></router-link>
								<a @click.prevent="downloadItem('trabajadores/reporte/expediente/'+trabajador.id_trabajador)"><i class="icon-arrow-down-circle"></i></a>
							</td>
						</tr>
						<tr v-if="!trabajadores.length">
							<td class="text-center" colspan="8">Sin registros</td>
						</tr>
					</tbody>
				</table>
			</div>
			<pagination :items="trabajadores" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page" :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>

			<template v-if="loading">
				<BlockUI :message="msg" :url="url"></BlockUI>
			</template>

		</div>
	</div>
</template>

<script type="text/ecmascript-6">
	import trabajador from '../../api/trabajadores'
	import loadingImage from '../../assets/images/block50.gif'
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
						field: 'primer_nombre',
						value: '',
						status:0
					}
				},
				trabajadores: [],
				total: 0,
				descargando:false,
			}
		},
		methods: {
			obtenerTrabajadores() {
				var self = this;
				self.loading = true;
				trabajador.obtener(self.filter, data => {
					self.trabajadores = data.rows
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
				this.obtenerTrabajadores()
			},
			changePage(page) {
				this.filter.page = page
				this.obtenerTrabajadores()
            },
			downloadItem (url) {
				var self = this;
				self.loading = true;
				//this.descargando=true;
				alertify.success("Descargando datos, espere un momento.....",5);
				axios.get(url, { responseType: 'blob' })
						.then(({ data }) => {
							let blob = new Blob([data], { type: 'application/pdf' })
							let link = document.createElement('a');
							link.href = window.URL.createObjectURL(blob)
							link.download = 'ExpedientePersonal.pdf';
							link.click()
							self.loading = false;
						}) .catch(function (error) {
					alertify.error("Error Descargando archivo.....",5);
					self.loading = false;
				})
				/*}else{
					alertify.warning("Espere a que se complete la descarga anterior",5);
				}*/
			},
		},
		/*components: {
			'pagination': Pagination
		},*/
		mounted() {
			this.obtenerTrabajadores()
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