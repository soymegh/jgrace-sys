<template>
	<div class="main">
				<div class="content-box">
					<div class="content-box-header">
						<div class="box-title">Registrar nuevo trabajador</div>
						<div class="box-description">Formulario de nuevo trabajador</div>
					</div>


						<vue-tabs>
							<v-tab title="Datos generales del trabajador">
								<br>
								<div class="row">

									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Primer nombre</label>
											<input class="form-control" placeholder="Primer Nombre" type="text" v-model="form.primer_nombre">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.primer_nombre" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Segundo Nombre</label>
											<input class="form-control" placeholder="Segundo Nombre" type="text" v-model="form.segundo_nombre">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.segundo_nombre" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Primer apellido</label>
											<input class="form-control" placeholder="Primer apellido" type="text" v-model="form.primer_apellido">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.primer_apellido" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Segundo apellido</label>
											<input class="form-control" placeholder="Segundo apellido" type="text" v-model="form.segundo_apellido">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.segundo_apellido" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-4">
										<div class="form-group">
											<label for=""> Cédula</label>
											<input class="form-control" v-mask="'#############A'" type="text" placeholder="Ingrese Identificación"  v-model="form.cedula">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.cedula" v-text="message"></li>
											</ul>
										</div>
									</div>



								</div>


								<div class="row">

									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Area</label>
											<v-select
													:options="areas"
													label="descripcion"
													v-model="form.area"
											></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.area" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Cargo</label>
											<v-select
													:options="cargos"
													label="descripcion"
													v-model="form.cargo"
											></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.cargo" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Tipo de Contrato</label>
											<select class="form-control" v-model="form.tipo_contrato">
												<option value="TD" >Tiempo determinado</option>
												<option value="TI" >Tiempo indeterminado</option>
												<option value="PS" >Pasantía</option>
												<option value="SP" >Servicios Profesionales</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.tipo_contrato" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> No. INSS</label>
											<input class="form-control" placeholder="Ingrese No. INSS" type="number" v-model="form.no_inss">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.no_inss" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-4">
										<div class="form-group">
											<label for=""> Salario Básico</label>
											<input class="form-control" placeholder="Salario Básico" v-model="form.salario_basico">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.salario_basico" v-text="message"></li>
											</ul>
										</div>
									</div>

								</div>

								<div class="row">


									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Sexo</label>
											<select class="form-control" v-model="form.sexo">
												<option value="M" >Masculino</option>
												<option value="F" >Femenino</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.sexo" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Estado Civil</label>
											<select class="form-control" v-model="form.estado_civil">
												<option value="S" >Soltero/a</option>
												<option value="C" >Casado/a</option>
												<option value="V" >Viudo/a</option>
												<option value="D" >Divorciado/a</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.estado_civil" v-text="message"></li>
											</ul>
										</div>
									</div>


									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Departamento</label>
											<v-select
													:options="departamentos"
													label="descripcion"
													v-model="form.departamento"
													v-on:input="obtenerMunicipios()"
											></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.departamento" v-text="message"></li>
											</ul>
										</div>
									</div>


									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Municipio</label>
											<v-select
													:options="municipios"
													label="descripcion"
													v-model="form.municipio"
											></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.municipio" v-text="message"></li>
											</ul>
										</div>
									</div>


									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Email</label>
											<input class="form-control" placeholder="Ingrese Email" type="email" v-model="form.email">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.email" v-text="message"></li>
											</ul>
										</div>
									</div>


									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Teléfono</label>
											<input class="form-control" placeholder="Ingrese télefono"  v-model="form.telefono">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.telefono" v-text="message"></li>
											</ul>
										</div>
									</div>


								</div>

								<div class="row">

									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Fecha ingreso</label>
											<datepicker :format="format" v-model="form.fecha_ingreso" :language="es" :value="new Date()" @selected="onDateSelect"></datepicker>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.fecha_ingreso" v-text="message"></li>
											</ul>
										</div>
									</div>

									<!--<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Fecha egreso</label>
											<datepicker :format="format" :language="es" v-model="form.fecha_egreso" :value="new Date()" @selected="onDateSelect2"></datepicker>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.fecha_egreso" v-text="message"></li>
											</ul>
										</div>
									</div>-->


									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Emergencias Notificar a:</label>
											<input class="form-control" placeholder="Ingrese contacto" type="text" v-model="form.notifica">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.notifica" v-text="message"></li>
											</ul>
										</div>
									</div>


									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Télefono Notificar:</label>
											<input class="form-control" placeholder="Ingrese télefono" type="number" v-model="form.telefono_notifica">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.telefono_notifica" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Nivel Academico </label>
											<v-select
													:options="niveles_academicos"
													label="descripcion"
													v-model="form.nivel"
											></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.nivel" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Nivel estudio</label>
											<v-select
													:options="niveles_estudios"
													label="descripcion"
													v-model="form.nivel_estudio"
											></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.nivel_estudio" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Nómina</label>
											<select class="form-control" v-model="form.id_nomina">
												<option value="1" >Personal lubnica</option>
												<option value="2" >Impulsadoras</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.id_nomina" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-8">
										<div class="form-group">
											<label for=""> Dirección</label>
											<input class="form-control" placeholder="Ingrese dirección"  v-model="form.direccion">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.direccion" v-text="message"></li>
											</ul>
										</div>
									</div>

								</div>

								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label for>Imagen Trabajador</label>
											<div class="uploader">
												<button
														class="btn btn-primary"
														id="uploader-button"
														type="button"
												>Escoger Imagen</button>
											</div>
											<ul class="error-messages">
												<li
														:key="message"
														v-for="message in errorMessages.imagen"
														v-text="message"
												></li>
											</ul>
										</div>
									</div>
								</div>
							</v-tab>

							<v-tab title="Datos medicos">
								<br>
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Peso en libras:</label>
											<input class="form-control" placeholder="Ingrese peso en libras" type="number" min="0" step="00.01" v-model="form.pesoLibras">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.pesoLibras" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Altura:</label>
											<input class="form-control" placeholder="Ingrese altura en metros" type="number" min="0" step="00.01" v-model="form.altura">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.altura" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Seguro INSS:</label>
											<input class="form-control" placeholder="Ingrese seguro inss" type="number" min="0" v-model="form.seguroInss">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.seguroInss" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Seguro medico:</label>
											<input class="form-control" placeholder="Ingrese seguro medico" type="number" min="0" v-model="form.seguroMedico">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.seguroMedico" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> IPSS:</label>
											<input class="form-control" placeholder="Ingrese ipss" type="text"  v-model="form.inssIpss">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.inssIpss" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> IPSSRP:</label>
											<input class="form-control" placeholder="Ingrese ipssrp" type="text"  v-model="form.inssIpssrp">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.inssIpssrp" v-text="message"></li>
											</ul>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Centro privado:</label>
											<input class="form-control" placeholder="Ingrese centro medico privado" type="text"  v-model="form.centroPrivado">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.centroPrivado" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Grupo sanguíneo</label>
											<select class="form-control" v-model="form.grupoSanguineo">
												<option value="1" >O-</option>
												<option value="2" >O+</option>
												<option value="3" >A-</option>
												<option value="4" >A+</option>
												<option value="5" >B-</option>
												<option value="6" >B+</option>
												<option value="7" >AB-</option>
												<option value="8" >AB+</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.grupoSanguineo" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Alergia</label>
											<select class="form-control" v-model="form.alergia">
												<option value="0" >Ninguna</option>
												<option value="1" >Alimentos</option>
												<option value="2" >Medicamentos</option>
												<option value="3" >Ambos</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.alergia" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for=""> Descripción alergia:</label>
											<input class="form-control" placeholder="Ingrese una descripción de la alergia" type="text"  v-model="form.alergiaDescripcion">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.alergiaDescripcion" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Diabetes</label>
											<select class="form-control" v-model="form.diabetes">
												<option value="0" >Ninguna</option>
												<option value="1" >Tipo 1</option>
												<option value="2" >Tipo 2</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.diabetes" v-text="message"></li>
											</ul>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Hipertensión</label>
											<select class="form-control" v-model="form.hipertension">
												<option value="0" >Ninguna</option>
												<option value="1" > Hipertensión arterial primaria</option>
												<option value="2" >Hipertensión arterial secundaria</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.hipertension" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Cardiaco</label>
											<select class="form-control" v-model="form.cardiaca">
												<option value="0" >Ninguna</option>
												<option value="1" >Cardiomiopatía  </option>
												<option value="2" >Miocarditis</option>
												<option value="3" >Arterias coronarias</option>
												<option value="4" >Arritmia</option>
												<option value="5" >Daño en las válvulas del corazón</option>
												<option value="6" >Problemas con el pericardio</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.cardiaca" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Asma</label>
											<select class="form-control" v-model="form.asma">
												<option value="0" >Ninguna</option>
												<option value="1" >Asma persistente</option>
												<option value="2" >Asma intermitente</option>
												<option value="3" >Asma persistente leve</option>
												<option value="4" >Asma persistente moderado</option>
												<option value="5" >Asma crónico</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.asma" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for="">Otra enfermedad:</label>
											<input class="form-control" placeholder="Describa otro tipo de enfermedad" type="text"  v-model="form.otraEnfermedad">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.otraEnfermedad" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Medico:</label>
											<input class="form-control" placeholder="Ingrese nombre de médico" type="text"  v-model="form.nombreMedico">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.nombreMedico" v-text="message"></li>
											</ul>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Teléfono</label>
											<input class="form-control" placeholder="Ingrese télefono médico"  v-model="form.telefonoMedico">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.telefonoMedico" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Contacto emergencia:</label>
											<input class="form-control" placeholder="Ingrese contacto emergencia"  v-model="form.contactoEmergencia">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.contactoEmergencia" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Telefono emergencia:</label>
											<input class="form-control" placeholder="Ingrese telefono emergencia"  v-model="form.telefonoEmergencia">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.telefonoEmergencia" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="">Observaciones:</label>
											<input class="form-control" placeholder="Escriba alguna observación" type="text"  v-model="form.observaciones">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.observaciones" v-text="message"></li>
											</ul>
										</div>
									</div>
								</div>
							</v-tab>

							<v-tab title= "Grupos familiares">
								<br>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<label for=""> Nombres</label>
											<input class="form-control" placeholder="Dígite los nombres" v-model="detalleForm.nombres">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.nombres" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for=""> Apellidos</label>
											<input class="form-control" placeholder="Dígite los apellidos" v-model="detalleForm.apellidos">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.apellidos" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Sexo</label>
											<select class="form-control" v-model="detalleForm.sexoGrupo">
												<option value="M" >Masculino</option>
												<option value="F" >Femenino</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.sexoGrupo" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Parentesco</label>
											<v-select :options="detalleForm.parentescos" label="descripcion" v-model="detalleForm.parentesco"></v-select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.parentesco" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for=""> Fecha nacimiento</label>
											<datepicker :format="format" v-model="fechax" :language="es" :value="new Date()" @selected="onDateSelectNac"></datepicker>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.fecha_nacimiento" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Tipo identificación</label>
											<select class="form-control" v-model="detalleForm.tipo_identificacion">
												<option value="1" >Cedula</option>
											</select>
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.tipo_identificacion" v-text="message"></li>
											</ul>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label for=""> Cédula</label>
											<input class="form-control" v-mask="'#############A'" type="text" placeholder="Ingrese Identificación"  v-model="detalleForm.no_identificacion">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.no_identificacion" v-text="message"></li>
											</ul>
										</div>
									</div>

									<div class="col-sm-3">
										<div class="form-group">
											<label for>Telefono</label>
											<input class="form-control"  type="number" min="0"  v-model.number="detalleForm.no_telefonico">
											<ul class="error-messages">
												<li :key="message" v-for="message in errorMessages.no_telefonico" v-text="message"></li>
											</ul>
										</div>
									</div>


									<div class="col-sm-3">
										<div class="form-group">
											<label for>&nbsp;</label>
											<button @click="agregarDetalle" class="btn btn-info btn-agregar" ref="agregar">Agregar detalle</button>
										</div>
									</div>

									<template v-if="loading">
										<BlockUI :message="msg" :url="url"></BlockUI>
									</template>

								</div>

								<div class="row">
									<div class="col-sm-12">
										<ul class="error-messages">
											<li :key="message" v-for="message in errorMessages.detalleGrupo" v-text="message"></li>
										</ul>

										<table class="table table-bordered table-responsive">
											<thead>
											<tr>
												<th></th>
												<th >Nombres</th>
												<th >Apellidos</th>
												<th >Sexo</th>
												<th >Parentesco</th>
												<th >Fecha Nac.</th>
												<th >Tipo identificación</th>
												<th >No.identificación</th>
												<th >No.telefonico</th>
											</tr>
											</thead>
											<tbody>
											<template  v-for="(item, index) in form.detalleGrupo">
												<tr>
													<td style="width: 2%">
														<button @click="eliminarLinea(item, index)">
															<i class="fa fa-trash"></i>
														</button>
													</td>
													<td>
														<input  class="form-control" :disabled="true" type="text"  v-model="item.nombres">
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.nombres`]" v-text="message"></li>
														</ul>
													</td>
													<td>
														<input  class="form-control" :disabled="true" type="text"  v-model="item.apellidos">
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.apellidos`]" v-text="message"></li>
														</ul>
													</td>
													<td>
														<select class="form-control" :disabled="true" v-model="item.sexoGrupo">
															<option value="M" >Masculino</option>
															<option value="F" >Femenino</option>
														</select>
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.sexoGrupo`]" v-text="message"></li>
														</ul>
													</td>
													<td>
														<v-select :options="detalleForm.parentescos" :disabled="true" label="descripcion" v-model="item.parentesco"></v-select>
														<!--<input  class="form-control" :disabled="true" type="text"  v-model="item.parentescos">-->
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.parentescos.id_parentesco`]" v-text="message"></li>
														</ul>
													</td>
													<td>
														<input  class="form-control" v-mask="'####-##-##'" :disabled="true" type="date"  v-model="item.fecha_nacimiento">
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.fecha_nacimiento`]" v-text="message"></li>
														</ul>
													</td>
													<td>
														<select class="form-control"  :disabled="true" v-model="item.tipo_identificacion">
															<option value="1" >Cedula</option>
														</select>
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.tipo_identificacion`]" v-text="message"></li>
														</ul>
													</td>
													<td>
														<input  class="form-control" :disabled="true" v-mask="'#############A'" type="text" v-model.text="item.no_identificacion">
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.no_identificacion`]" v-text="message"></li>
														</ul>
													</td>
													<td>
														<input  class="form-control" :disabled="true" type="number" min="0" v-model.number="item.no_telefonico">
														<ul class="error-messages">
															<li :key="message" v-for="message in errorMessages[`detalleGrupo.${index}.no_telefonico`]" v-text="message"></li>
														</ul>
													</td>
													<!--<td>
                                                    <strong> {{sub_total(item) | formatMoney(2)}}</strong>
                                                    </td>-->

												</tr>
												<tr></tr>
											</template>
											</tbody>
											<tfoot>
											<!--<tr>
												<td colspan="4"></td>
												<td>Total</td>
												<td> <strong> {{calcular_total | formatMoney(2)}}</strong></td>
											</tr>-->
											</tfoot>
										</table>
									</div>
								</div>
							</v-tab>
						</vue-tabs>

					<div class="content-box-footer text-right">
						<router-link :to="{ name: 'listado-trabajadores' }" tag="button">
							<button class="btn btn-default" type="button">Cancelar</button>
						</router-link>
						<button :disabled="btnAction != 'Guardar' ? true : false" @click="registrar" class="btn btn-primary" type="button">{{ btnAction }}</button>
					</div>
					<template v-if="loading">
						<BlockUI :message="msg" :url="url"></BlockUI>
					</template>
				</div>
			</div>
		
</template>

<script type="text/ecmascript-6">
	import trabajador from '../../api/trabajadores'
	import area from '../../api/areas'
	import cargo from '../../api/cargos'
	import academico from '../../api/niveles_academicos'
	import parentesco from '../../api/parentesco'
	import estudio from '../../api/niveles_estudios'
	import medico from '../../api/datos_medicos'
	import departamento from '../../api/departamentos'
	import loadingImage from '../../assets/images/block50.gif'
	import es from 'vuejs-datepicker/dist/locale/translations/es'

	export default {
		data() {
			return {
				loading:false,
				msg: 'Guardando los datos, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
				es: es,
				format: "dd-MM-yyyy",
				areas:[],
				niveles_academicos:[],
				niveles_estudios:[],
				cargos:[],
				departamentos:[],
				municipios:[],
				detalleForm: {
					parentescos:[],
					nombres:'',
					apellidos: '',
					fecha_nacimiento: moment(new Date()).format("YYYY-MM-DD"),
					sexoGrupo: '',
					parentesco:'',
					tipo_identificacion:'',
					no_identificacion:'',
					no_telefonico:'',
				},
				form: {
					detalleGrupo:[],
				    codigo: '',
					primer_nombre: '',
					segundo_nombre: '',
					primer_apellido: '',
					segundo_apellido: '',
					cedula: '',
					fecha_ingreso: moment(new Date()).format("YYYY-MM-DD"),
					fecha_egreso: moment(new Date()).format("YYYY-MM-DD"),
					area: '',
					nivel: '',
					nivel_estudio: '',
					seguroInss: '',
					seguroMedico: '',
					inssIpss: '',
					inssIpssrp: '',
					centroPrivado: '',
					grupoSanguineo: '',
					alergia: '',
					alergiaDescripcion: '',
					diabetes: '',
					hipertension: '',
					cardiaca: '',
					pesoLibras: '',
					altura: '',
					asma: '',
					otraEnfermedad: '',
					nombreMedico: '',
					telefonoMedico: '',
					contactoEmergencia: '',
					telefonoEmergencia: '',
					observaciones: '',
					cargo: '',
					tipo_contrato: '',
					no_inss: '',
					salario_basico: '',

					sexo: '',
					estado_civil: '',
					departamento: '',
					municipio: '',
					email: '',
					telefono: '',

					direccion: '',
					notifica: '',
					telefono_notifica: '',

					imagen: '',

					id_nomina:1,

				},
				fechax: new Date(),
				btnAction: 'Guardar',
				errorMessages: []
			}
		},
		methods: {
			obtenerTodosCargos() {
				var self = this
				cargo.obtenerTodos(data => {
					self.cargos = data
				}, err => {
					console.log(err)
				})
			},
			obtenerTodosParentesco() {
				var self = this
				parentesco.obtenerTodas(data => {
					self.detalleForm.parentescos = data
				}, err => {
					console.log(err)
				})
			},
			obtenerTodasAreas() {
				var self = this
				area.obtenerTodas(data => {
					self.areas = data
				}, err => {
					console.log(err)
				})
			},
			obtenerTodosDepartamentos() {
				var self = this
				departamento.obtenerTodos(data => {
					self.departamentos = data
				}, err => {
					console.log(err)
				})
			},
			obtenerTodosNivelesAcademicos() {
				var self = this
				academico.obtenerTodas(data => {
					self.niveles_academicos = data
				}, err => {
					console.log(err)
				})
			},
			obtenerTodosNivelesEstudios() {
				var self = this
				estudio.obtenerTodas(data => {
					self.niveles_estudios = data
				}, err => {
					console.log(err)
				})
			},
			onDateSelect(date) {
				//console.log(date); //
				this.form.fecha_ingreso = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelect2(date) {
				//console.log(date); //
				this.form.fecha_egreso = moment(date).format("YYYY-MM-DD"); //
			},
			onDateSelectNac(date) {
				//console.log(date); //
				this.detalleForm.fecha_nacimiento = moment(date).format("YYYY-MM-DD"); //
			},
			obtenerMunicipios() {
				var self = this
				self.form.municipio= null;
					if(self.form.departamento.municipios_departamento)
					self.municipios = self.form.departamento.municipios_departamento
			},

			obtenerImg() {
				var imagen = $('input[name="imagen"]').val();
				if (JSON.parse(imagen).length) {
					return imagen;
				} else {
					return "";
				}
			},
			agregarDetalle() {
				let self = this;
				if(this.detalleForm.no_identificacion){
					let i = 0;
					let keyx = 0;
					if(self.form.detalleGrupo){
						self.form.detalleGrupo.forEach((fila, key) => {
							if(self.detalleForm.no_identificacion===fila.no_identificacion){
								i++;
								keyx = key;
							}
						});
					}
					if(i === 0){
						//this.detalleForm.parentescos.descripcion = this.detalleForm.parentescos.text;
						this.form.detalleGrupo.push({
							nombres: this.detalleForm.nombres,
							apellidos: this.detalleForm.apellidos,
							sexoGrupo: this.detalleForm.sexoGrupo,
							parentesco: this.detalleForm.parentesco,
							fecha_nacimiento: this.detalleForm.fecha_nacimiento,
							tipo_identificacion: this.detalleForm.tipo_identificacion,
							no_identificacion: this.detalleForm.no_identificacion,
							no_telefonico: this.detalleForm.no_telefonico,
						});
						//this.detalleForm.fecha_viatico='';
						this.detalleForm.nombres='';
						this.detalleForm.apellidos='';
						this.detalleForm.sexoGrupo='';
						this.detalleForm.parentesco='';
						this.detalleForm.fecha_nacimiento='';
						this.detalleForm.tipo_identificacion='';
						this.detalleForm.no_identificacion='';
						this.detalleForm.no_telefonico='';

					}else{
						alertify.warning("Ya existe un registro con la fecha seleccionada",5);
					}

				}else{
					alertify.warning("Los campos no pueden estar vacíos",5);
				}
			},
			eliminarLinea(item, index) {
				if (this.form.detalleGrupo.length >= 1) {
					this.form.detalleGrupo.splice(index, 1);

				}else{
					this.form.detalleGrupo=[];
				}
			},
			registrar() {
				var self = this
				self.btnAction = 'Guardando, espere...'
				self.form.imagen = this.obtenerImg();
				self.loading = true;
				trabajador.registrar(self.form, data => {
					self.loading = false;
					alertify.success("Datos del trabajador registrados correctamente",5);
					this.$router.push({
						name: 'listado-trabajadores'
					})
				}, err => {
					self.loading = false;
					self.errorMessages = err
                   	self.btnAction = 'Guardar'
				})
			},
			initUploader() {
				var self = this;
				this.uploader = $("#uploader-button").uploader({
					upload_url: window.location.origin + "/public/media/upload",
					file_picker_url: window.location.origin + "/public/media/get-files",
					input_name: "imagen",
					maximum_total_files: 1,
					maximum_file_size: 5000000,
					file_types_allowed: ["image/jpeg", "image/png"],
					on_before_upload: function() {
						self.btnAction = "Subiendo imágenes...";
					},
					on_success_upload: function() {
						self.btnAction = "Guardar";
					},
					on_error: function(err) {
						console.log(err);
					}
				});
			}
		},
		mounted() {
			this.initUploader();
			this.obtenerTodosCargos();
			this.obtenerTodasAreas();
			this.obtenerTodosDepartamentos();
			this.obtenerTodosNivelesAcademicos();
			this.obtenerTodosNivelesEstudios();
			this.obtenerTodosParentesco();
		}
    }
</script>
<style>
	.btn-agregar {
		margin-top:33px;
	}
</style>
