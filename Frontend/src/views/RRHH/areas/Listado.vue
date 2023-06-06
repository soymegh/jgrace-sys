<template>
	<div class="main">
		<div class="content-box">
			<div class="content-box-header">
				<div class="box-title">Administrar Areas</div>
				<div class="box-description">Listado de Areas</div>
				<div class="box-description"><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal' }">Módulos</router-link>><router-link :style="'margin-right: 10px;color: blue;'" :to="{ name: 'pagina-principal-nomina' }"> Módulo nomina</router-link>> Areas</div>
			</div>
			<div class="row">
				<div class="col-md-6 sm-text-center">
					<router-link :to="{ name: 'registrar-area' }" class="btn btn-success" tag="button">
						<i class="pe-7s-plus"></i> Registrar Area
					</router-link>
				</div>
				<div @keyup.enter="filter.page = 1;obtenerAreas();" class="col-md-6 sm-text-center form-inline justify-content-end">
					<div class="form-group check-label">
						<label class="check-label form-control mb-1 mr-sm-1 mb-sm-0"><input class="form-control mb-1 mr-sm-1 mb-sm-0" v-model="filter.search.status" type="checkbox"> Mostrar Todos</label>
					</div>
					<select class="form-control mb-1 mr-sm-1 mb-sm-0 search-field" v-model="filter.search.field">
						<option value="codigo">Código</option>
						<option value="descripcion">Descripción</option>
					</select>
					<input class="form-control mb-1 mr-sm-1 mb-sm-0" placeholder="Buscar" type="text" v-model="filter.search.value">
					<p>
					<button @click="filter.page = 1;obtenerAreas();" class="btn btn-info"><i class="pe-7s-search"></i> Buscar</button>
					<button class="btn btn-success"><i aria-hidden="true" class="fa fa-file-pdf-o"></i><a href="areas/reporte/agrupado" style="color: #ffffff;">  Agrupado</a></button>
					<button class="btn btn-dark"><i aria-hidden="true" class="fa fa-file-pdf-o"></i><a href="areas/reporte/detallado" style="color: #ffffff;">  Detallado</a></button>
					</p>
				</div>
			</div>
			<div class="table-responsive mt-3">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Código</th>
							<th>Descripción</th>
							<th>Sucursal</th>
							<th>Dirección</th>
							<th class="text-center table-number">Estado</th>
							<th class="text-center action">Opciones</th>
						</tr>
					</thead>
					<tbody>
						<tr :key="area.id_area" v-for="area in areas">
							<td>{{ area.codigo }}</td>
							<td>{{ area.descripcion }}</td>
							<td>{{ area.direccion_area.direccion_sucursal.descripcion}}</td>
							<td>{{ area.direccion_area.descripcion + '('+area.direccion_area.codigo + ')'}}</td>
							<!--<td>{{ area.cuenta_contable_area.nombre_cuenta_completo }}</td>-->
							<td class="text-center">
							 <div v-if="area.activo">
                              <span class="badge badge-success">Activo</span>
                             </div>
                                 <div v-else>
                              <span class="badge badge-danger">Desactivado</span>
                                 </div>
							</td>
							<td class="text-center">
								
								<router-link :to="{ name: 'actualizar-area', params: { id_area: area.id_area } }" tag="a"><i class="icon-pencil"></i></router-link>
							
								<!--<template v-if="area.estado==1">
								<a @click="desactivar(area.id_area)" href="javascript:;"><i class="fa fa-trash-o"></i></a>
                                </template>
                                <template v-else>
								<a @click="activar(area.id_area)" href="javascript:;"><i class="fa fa-check-square"></i></a>
                                </template>-->
							</td>
						</tr>
						<tr v-if="!areas.length">
							<td class="text-center" colspan="6">Sin datos</td>
						</tr>
					</tbody>
				</table>
			</div>
			<pagination :items="areas" :limit="filter.limit" :limitOptions="filter.limitOptions" :page="filter.page" :total="total" @changeLimit="changeLimit" @changePage="changePage"></pagination>

			<template v-if="loading">
				<BlockUI :message="msg" :url="url"></BlockUI>
			</template>

		</div>
	</div>
</template>

<script type="text/ecmascript-6">
	import area from '../../api/areas';
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
					limit: 20,
					limitOptions: [5, 10, 15, 20],
					search: {
						field: 'codigo',
						value: '',
						status:0
					}
				},
				areas: [],
				total: 0
			}
		},
		methods: {
			obtenerAreas() {
				var self = this;
				self.loading = true;
				area.obtener(self.filter, data => {
					self.areas = data.rows;
					self.total = data.total;
					self.loading = false;
				}, err => {
					self.loading = false;
					console.log(err)
				})
			},
			changeLimit(limit) {
				this.filter.page = 1
				this.filter.limit = limit
				this.obtenerAreas()
			},
			changePage(page) {
				this.filter.page = page
				this.obtenerAreas()
            },

		},
		/*components: {
			'pagination': Pagination
		},*/
		mounted() {
			this.obtenerAreas()
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