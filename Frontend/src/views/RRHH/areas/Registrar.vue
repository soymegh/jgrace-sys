<template>
	<div class="main">
		<div class="row">
			<div class="col-md-12">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Registrar area</div>
						<div class="box-description">Formulario para registrar area</div>
					</div>
					<form>

							<div class="row">
						<!--	<div class="col-sm-3">
						<div class="form-group">
							<label for=""> Código</label>
							<input class="form-control" placeholder="Dígite el codigo de area" v-model="form.codigo">
							<ul class="error-messages">
								<li :key="message" v-for="message in errorMessages.codigo" v-text="message"></li>
							</ul>
						</div
						</div>>-->

								<div class="col-sm-3">
									<div class="form-group">
										<label for=""> Descripción</label>
										<input class="form-control" placeholder="Dígite el nombre del area" v-model="form.descripcion">
										<ul class="error-messages">
											<li :key="message" v-for="message in errorMessages.descripcion" v-text="message"></li>
										</ul>
									</div>
								</div>

							<div class="col-sm-3">
              <div class="form-group">
                <label for="">Dirección</label>
                <v-select
                  :options="direcciones"
                  label="descripcion"
                  v-model="form.direccion"
                ></v-select>
                <ul class="error-messages">
                  <li :key="message" v-for="message in errorMessages.direccion" v-text="message"></li>
                </ul>
              </div>
            </div>


								<!--<div class="col-sm-3">
                      <div class="form-group">
                        <label for="">Cuenta Contable</label>
                        <v-select
                          :options="cuentas_contables"
                          label="nombre_cuenta_completo"
                          v-model="form.cuenta_contable"
                        ></v-select>
                        <ul class="error-messages">
                          <li :key="message" v-for="message in errorMessages.cuenta_contable" v-text="message"></li>
                        </ul>
                      </div>
                    </div>-->

  					</div>

					</form>
					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-areas' }" tag="button">
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
	import direccion from '../../api/direcciones'
	import cuentas_contables from '../../api/cuentas_contables'
	import area from '../../api/areas'
	import loadingImage from '../../assets/images/block50.gif'
	

	export default {
		data() {
			return {
				loading:false,
				msg: 'Guardando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				
				cuentas_contables:[],
				direcciones:[],
				form: {
					codigo: '',
					descripcion: '',
				},
				btnAction: 'Registrar',
				errorMessages: []
			}
		},
		methods: {
 		obtenerTodasCuentasContables() {
				var self = this;
        cuentas_contables.obtenerTodasCuentasContables(
          data => {
            self.cuentas_contables = data;
          },
          err => {
            console.log(err);
          }
        );
      
		},

		obtenerTodasDirecciones(){
		var self = this;
			direccion.obtenerTodasDirecciones(
          data => {
            self.direcciones = data;
          },
          err => {
            console.log(err);
          }
        );

		},

			registrar() {
				var self = this
				self.btnAction = 'Registrando, espere....'
				self.loading = true;
				area.registrar(self.form, data => {
					self.loading = false;
					this.$router.push({
						name: 'listado-areas'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Registrar'
				})
			}
		},
		  mounted() {
    this.obtenerTodasDirecciones();
		this.obtenerTodasCuentasContables();
  }
    }
</script>