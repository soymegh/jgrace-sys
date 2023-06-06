<template>
	<div class="main">
		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Administrar Cargos</div>
				<div class="box-description">Listado de Cargos</div>
				<div class="box-description"><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal' }">Módulos</router-link>><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal-nomina' }"> Módulo nomina</router-link>> Cargos</div>
			</div>
			<div class="row">
				<div class="col-md-4 sm-text-center">
					<router-link :to="{ name: 'registrar-cargo' }" class="btn btn-success" tag="button">
						<i class="pe-7s-plus"></i> Registrar Cargo
					</router-link>
				</div>
				<div @keyup.enter="filter.page = 1;obtenerCargos();" class="col-md-8 sm-text-center form-inline justify-content-end">
					<div class="form-group check-label">
						<label class="check-label form-control mb-1 mr-sm-1 mb-sm-0"><input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox"> Mostrar Todos</label>
					</div>
					<select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
						<option value="descripcion">Nombre Cargo</option>
					</select>
					<input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text" v-model="filter.search.value">
					<button @click="filter.page = 1;obtenerCargos();" class="btn btn-info"><i class="pe-7s-search"></i> Buscar</button>
					<a :disabled="descargando" @click.prevent="downloadItem('pdf')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-danger"><i aria-hidden="true" class="fa fa-file-pdf-o"></i></button></a>
					<a :disabled="descargando" @click.prevent="downloadItem('xls')" style="color: #ffffff;padding-left: 2px"><button :disabled="descargando" class="btn btn-success"><i aria-hidden="true" class="fa fa-file-excel-o"></i></button></a>
				</div>
			</div>
			<div class="table-responsive mt-3">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Descripción</th>
							<th class="text-center table-number">Estado</th>
							<th class="text-center action">Opciones</th>
						</tr>
					</thead>
					<tbody>
						<tr :key="cargo.id_cargo" v-for="cargo in cargos">
							<td>{{ cargo.descripcion }}</td>
							<td class="text-center">
							 <div v-if="cargo.activo==1">
                              <span class="badge badge-success">Activo</span>
                             </div>
                                 <div v-else>
                              <span class="badge badge-danger">Desactivado</span>
                                 </div>
							</td>
							<td class="text-center">
								
								<router-link :to="{ name: 'actualizar-cargo', params: { id_cargo: cargo.id_cargo } }" tag="a"><i class="icon-pencil"></i></router-link>
							
								<!-- <template v-if="cargo.activo==1">
								<a @click="desactivar(cargo.id_cargo)" href="javascript:;"><i class="fa fa-trash-o"></i></a>
                                </template>
                                <template v-else>
								<a @click="activar(cargo.id_cargo)" href="javascript:;"><i class="fa fa-check-square"></i></a>
                                </template>-->
							</td>
						</tr>
						<tr v-if="!cargos.length">
							<td class="text-center" colspan="2">Sin datos</td>
						</tr>
					</tbody>
				</table>
			</div>
			<pagination :items="cargos" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page" :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>

			<template v-if="loading">
				<BlockUI :message="msg" :url="url"></BlockUI>
			</template>

		</div>
	</div>
</template>

<script type="text/ecmascript-6">
	import cargo from '../../api/cargos'
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
						field: 'descripcion',
						value: '',
						status:0
					}
				},
				cargos: [],
				total: 0,
				descargando:false,
			}
		},
		methods: {
			obtenerCargos() {
				var self = this
				self.loading = true;
				cargo.obtenerCargos(self.filter, data => {
					self.cargos = data.rows
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
				this.obtenerCargos()
			},
			changePage(page) {
				this.filter.page = page
				this.obtenerCargos()
            },
			downloadItem (ext) {
				var self = this;
				if(!this.descargando){
					this.descargando=true;
					self.loading = true;
					alertify.success("Descargando datos, espere un momento.....",5);
					axios.get('rrhh/cargos/reporte/'+ext, { responseType: 'blob' })
							.then(({ data }) => {
								let blob = new Blob([data], { type: 'application/pdf' })
								ext === 'xls' ? blob = new Blob([data], { type: 'application/octet-stream' }) : blob = new Blob([data], { type: 'application/pdf' });
								let link = document.createElement('a');
								link.href = window.URL.createObjectURL(blob);
								link.download = 'Reporte_Cargos.'+ext;
								link.click();
								this.descargando=false;
								self.loading = false;
							}).catch(function (error) {
						alertify.error("Error Descargando archivo.....", 5);
						self.descargando = false;
						self.loading = false;
					})
				}else{
					alertify.warning("Espere a que se complete la descarga anterior",5);
				}
			},
		},
		/*components: {
			'pagination': Pagination
		},*/
		mounted() {
			this.obtenerCargos()
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