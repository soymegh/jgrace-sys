<template>
    <div class="main">
        <div class="content-box">
            <div class="content-box-header">
                <div class="box-title">Modificar un trabajador</div>
                <div class="box-description">Formulario para modificar datos de un trabajador</div>
            </div>

            <vue-tabs>
                <v-tab title="Datos generales del trabajador">
                    <br>
                <div class="row">

                   <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Código trabajador</label>
                            <input class="form-control" placeholder="Código trabajador" type="text"
                                   v-model="form.codigo" disabled>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.codigo" v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Primer nombre</label>
                            <input class="form-control" placeholder="Primer Nombre" type="text"
                                   v-model="form.primer_nombre">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.primer_nombre" v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Segundo Nombre</label>
                            <input class="form-control" placeholder="Segundo Nombre" type="text"
                                   v-model="form.segundo_nombre">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.segundo_nombre"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Primer apellido</label>
                            <input class="form-control" placeholder="Primer apellido" type="text"
                                   v-model="form.primer_apellido">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.primer_apellido"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Segundo apellido</label>
                            <input class="form-control" placeholder="Segundo apellido" type="text"
                                   v-model="form.segundo_apellido">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.segundo_apellido"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Cédula</label>
                            <input class="form-control" v-mask="'#############A'" type="text" placeholder="Ingrese Identificación" v-model="form.cedula">
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
                                    v-model="form.trabajador_area"
                            ></v-select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.trabajador_area"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Cargo</label>
                            <v-select
                                    :options="cargos"
                                    label="descripcion"
                                    v-model="form.trabajador_cargo"
                            ></v-select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.trabajador_cargo"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Tipo de Contrato</label>
                            <select class="form-control" v-model="form.tipo_contrato">
                                <option value="TD">Tiempo determinado</option>
                                <option value="TI">Tiempo indeterminado</option>
                                <option value="PS">Pasantía</option>
                                <option value="SP">Servicios Profesionales</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.tipo_contrato" v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> No. INSS</label>
                            <input class="form-control" placeholder="Ingrese No. INSS" type="number"
                                   v-model="form.trabajador_detalles.no_inss">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages[`trabajador_detalles.no_inss`]"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>


                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Salario Básico</label>
                            <input class="form-control" placeholder="Salario Básico"
                                   v-model="form.trabajador_detalles.salario_basico">
                            <ul class="error-messages">
                                <li :key="message"
                                    v-for="message in  errorMessages[`trabajador_detalles.salario_basico`]"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Sexo</label>
                            <select class="form-control" v-model="form.trabajador_detalles.sexo">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages[`trabajador_detalles.sexo`]"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Estado Civil</label>
                            <select class="form-control" v-model="form.trabajador_detalles.estado_civil">
                                <option value="S">Soltero/a</option>
                                <option value="C">Casado/a</option>
                                <option value="V">Viudo/a</option>
                                <option value="D">Divorciado/a</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages[`trabajador_detalles.estado_civil`]"
                                    v-text="message"></li>
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
                            <input class="form-control" placeholder="Ingrese Email" type="email"
                                   v-model="form.trabajador_detalles.email">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages[`trabajador_detalles.email`]"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>


                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Teléfono</label>
                            <input class="form-control" placeholder="Ingrese télefono"
                                   v-model="form.trabajador_detalles.telefono">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages[`trabajador_detalles.telefono`]"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>


                </div>

                <div class="row">


                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Fecha ingreso</label>
                            <datepicker :format="format" v-model="form.trabajador_detalles.fecha_ingreso" :language="es" :value="new Date()" @selected="onDateSelect"></datepicker>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_detalles.fecha_ingreso']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>

                    <!--<div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Fecha egreso</label>
                            <datepicker :format="format" :language="es" v-model="form.trabajador_detalles.fecha_egreso" :value="new Date()" @selected="onDateSelect2"></datepicker>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_detalles.fecha_egreso']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>-->

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Emergencias Notificar a:</label>
                            <input class="form-control" placeholder="Ingrese contacto" type="text"
                                   v-model="form.trabajador_detalles.notifica">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages[`trabajador_detalles.notifica`]"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>


                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Télefono Notificar:</label>
                            <input class="form-control" placeholder="Ingrese télefono" type="number"
                                   v-model="form.trabajador_detalles.telefono_notifica">
                            <ul class="error-messages">
                                <li :key="message"
                                    v-for="message in errorMessages[`trabajador_detalles.telefono_notifica`]"
                                    v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Nivel academico</label>
                            <v-select
                                    :options="niveles_academicos"
                                    label="descripcion"
                                    v-model="form.trabajador_nivel_academico"
                            ></v-select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_nivel_academico']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Nivel estudio</label>
                            <v-select
                                    :options="niveles_estudios"
                                    label="descripcion"
                                    v-model="form.trabajador_nivel_estudio"
                            ></v-select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_nivel_estudio']" v-text="message"></li>
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
                            <input class="form-control" placeholder="Ingrese dirección"
                                   v-model="form.trabajador_detalles.direccion">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages[`trabajador_detalles.direccion`]"
                                    v-text="message"></li>
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
                                >Escoger Imagen
                                </button>
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
                    <template v-if="form.trabajador_datos_medicos ? form.trabajador_datos_medicos.peso_libras : 'n/a' ">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Peso en libras:</label>
                            <input class="form-control" placeholder="Ingrese peso en libras" type="number" min="0" step="0.01" v-model="form.trabajador_datos_medicos.peso_libras">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.peso_libras']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    </template>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Altura:</label>
                            <input class="form-control" placeholder="Ingrese altura en metros" type="number" min="0" step="00.01" v-model="form.trabajador_datos_medicos.altura">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.altura']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Seguro INSS:</label>
                            <input class="form-control" placeholder="Ingrese seguro inss" type="number" min="0" v-model="form.trabajador_datos_medicos.seguro_inss">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.seguro_inss']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Seguro medico:</label>
                            <input class="form-control" placeholder="Ingrese seguro medico" type="number" min="0" v-model="form.trabajador_datos_medicos.seguro_medico">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.seguro_medico']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> IPSS:</label>
                            <input class="form-control" placeholder="Ingrese ipss" type="text"  v-model="form.trabajador_datos_medicos.inss_ipss">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.inss_ipss']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> IPSSRP:</label>
                            <input class="form-control" placeholder="Ingrese ipssrp" type="text"  v-model="form.trabajador_datos_medicos.inss_ipssrp">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.inss_ipssrp']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Centro privado:</label>
                            <input class="form-control" placeholder="Ingrese centro medico privado" type="text"  v-model="form.trabajador_datos_medicos.centro_privado">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.centro_privado']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Grupo sanguíneo</label>
                            <select class="form-control" v-model="form.trabajador_datos_medicos.grupo_sanguineo">
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
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.grupo_sanguineo']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Alergia</label>
                            <select class="form-control" v-model="form.trabajador_datos_medicos.alergia">
                                <option value="0" >Ninguna</option>
                                <option value="1" >Alimentos</option>
                                <option value="2" >Medicamentos</option>
                                <option value="3" >Ambos</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.alergia']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for=""> Descripción alergia:</label>
                            <input class="form-control" placeholder="Ingrese una descripción de la alergia" type="text"  v-model="form.trabajador_datos_medicos.alergia_descripcion">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.alergia_descripcion']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Diabetes</label>
                            <select class="form-control" v-model="form.trabajador_datos_medicos.diabetes">
                                <option value="0" >Ninguna</option>
                                <option value="1" >Tipo 1</option>
                                <option value="2" >Tipo 2</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.diabetes']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Hipertensión</label>
                            <select class="form-control" v-model="form.trabajador_datos_medicos.hipertension">
                                <option value="0" >Ninguna</option>
                                <option value="1" > Hipertensión arterial primaria</option>
                                <option value="2" >Hipertensión arterial secundaria</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.hipertension']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Cardiaco</label>
                            <select class="form-control" v-model="form.trabajador_datos_medicos.cardiaca">
                                <option value="0" >Ninguna</option>
                                <option value="1" >Cardiomiopatía  </option>
                                <option value="2" >Miocarditis</option>
                                <option value="3" >Arterias coronarias</option>
                                <option value="4" >Arritmia</option>
                                <option value="5" >Daño en las válvulas del corazón</option>
                                <option value="6" >Problemas con el pericardio</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.cardiaca']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Asma</label>
                            <select class="form-control" v-model="form.trabajador_datos_medicos.asma">
                                <option value="0" >Ninguna</option>
                                <option value="1" >Asma persistente</option>
                                <option value="2" >Asma intermitente</option>
                                <option value="3" >Asma persistente leve</option>
                                <option value="4" >Asma persistente moderado</option>
                                <option value="5" >Asma crónico</option>
                            </select>
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.asma']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Otra enfermedad:</label>
                            <input class="form-control" placeholder="Describa otro tipo de enfermedad" type="text"  v-model="form.trabajador_datos_medicos.otra_enfermedad">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.otra_enfermedad']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Medico:</label>
                            <input class="form-control" placeholder="Ingrese nombre de médico" type="text"  v-model="form.trabajador_datos_medicos.nombre_medico">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.nombre_medico']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Teléfono</label>
                            <input class="form-control" placeholder="Ingrese télefono médico"  v-model="form.trabajador_datos_medicos.telefono_medico">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.telefono_medico']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Contacto emergencia:</label>
                            <input class="form-control" placeholder="Ingrese contacto emergencia"  v-model="form.trabajador_datos_medicos.contacto_emergencia">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.contacto_emergencia']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""> Telefono emergencia:</label>
                            <input class="form-control" placeholder="Ingrese telefono emergencia"  v-model="form.trabajador_datos_medicos.telefono_emergencia">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.telefono_emergencia']" v-text="message"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Observaciones:</label>
                            <input class="form-control" placeholder="Escriba alguna observación" type="text"  v-model="form.trabajador_datos_medicos.observaciones">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages['trabajador_datos_medicos.observaciones']" v-text="message"></li>
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
                                <select class="form-control" v-model="detalleForm.sexo">
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
                                <label for="">Parentesco</label>
                                <v-select
                                        :options="parentescos"
                                        label="descripcion"
                                        v-model="detalleForm.parentesco"
                                ></v-select>
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
                                <template  v-for="(item, index) in form.trabajador_grupo_familiar">
                                    <tr>
                                        <td style="width: 2%">
                                            <button @click="eliminarLinea(item, index)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <input  class="form-control" :disabled="true" type="text"  v-model="item.nombres">
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.nombres`]" v-text="message"></li>
                                            </ul>
                                        </td>
                                        <td>
                                            <input  class="form-control" :disabled="true" type="text"  v-model="item.apellidos">
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.apellidos`]" v-text="message"></li>
                                            </ul>
                                        </td>
                                        <td>
                                            <select class="form-control" :disabled="true" v-model="item.sexo">
                                                <option value="M" >Masculino</option>
                                                <option value="F" >Femenino</option>
                                            </select>
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.sexo`]" v-text="message"></li>
                                            </ul>
                                        </td>
                                        <td>
                                            <v-select :options="parentescos" label="descripcion" v-model="item.parentesco" :disabled="true"></v-select>
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.parentesco`]" v-text="message"></li>
                                            </ul>
                                        </td>
                                        <td>
                                            <input  class="form-control" v-mask="'####-##-##'" :disabled="true" type="date"  v-model="item.fecha_nacimiento">
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.fecha_nacimiento`]" v-text="message"></li>
                                            </ul>
                                        </td>
                                        <td>
                                            <select class="form-control"  :disabled="true" v-model="item.tipo_identificacion">
                                                <option value="1" >Cedula</option>
                                            </select>
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.tipo_identificacion`]" v-text="message"></li>
                                            </ul>
                                        </td>
                                        <td>
                                            <input  class="form-control" :disabled="true" v-mask="'#############A'" type="text" v-model.text="item.no_identificacion">
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.no_identificacion`]" v-text="message"></li>
                                            </ul>
                                        </td>
                                        <td>
                                            <input  class="form-control" :disabled="true" type="number" min="0" v-model.number="item.no_telefonico">
                                            <ul class="error-messages">
                                                <li :key="message" v-for="message in errorMessages[`trabajador_grupo_familiar.${index}.no_telefonico`]" v-text="message"></li>
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
                <template>
                <v-tab title= "Ingresos y deducciones">
                <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="error-messages">
                                <li :key="message" v-for="message in errorMessages.trabajador_ingreso_deduccion" v-text="message"></li>
                            </ul>

                            <table class="table table-bordered table-responsive">
                                <thead>
                                <tr>
                                    <!-- <th></th>
                                     <th style="min-width:300px" >Tipo de solicitud</th>-->
                                    <th style="min-width:200px">Ingreso/Deducción</th>
                                    <th style="min-width:150px">Código</th>
                                    <th style="min-width:150px">Descripción</th>
                                    <th style="min-width:150px">Monto</th>
                                    <th ></th>
                                </tr>
                                </thead>
                                <tbody>
                                <template  v-for="(item, index) in form.trabajador_ingreso_deduccion">
                                    <template v-if="item.estado === 1">
                                        <tr>
                                            <!--<td style="width: 2%">
                                                <button @click="eliminarLinea(item, index)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>-->
                                            <td>
                                                <!--<v-select label="ingreso" :disabled="true" v-model="item.asignacion_ingreso.cve_ingreso_deduccion" :options="ingresos"></v-select>-->
                                                <input class="form-control" :disable="true" v-model="item.asignacion_ingreso.cve_ingreso_deduccion">
                                                <ul class="error-messages">
                                                    <li :key="message" v-for="message in errorMessages[`trabajador_ingreso_deduccion.${index}.ingreso`]" v-text="message"></li>
                                                </ul>
                                            </td>
                                            <td>
                                                <input  class="form-control" :disabled="true" type="number" min="0" v-model="item.asignacion_ingreso.codigo">
                                                <ul class="error-messages">
                                                    <li :key="message" v-for="message in errorMessages[`trabajador_ingreso_deduccion.${index}.codigo`]" v-text="message"></li>
                                                </ul>
                                            </td>
                                            <td>
                                                <input  class="form-control" :disabled="true" type="text"  v-model="item.asignacion_ingreso.descripcion">
                                                <ul class="error-messages">
                                                    <li :key="message" v-for="message in errorMessages[`trabajador_ingreso_deduccion.${index}.descripcion`]" v-text="message"></li>
                                                </ul>
                                            </td>
                                            <td>
                                                <input  class="form-control" :disabled="true" type="number" min="0" v-model="item.monto">
                                                <ul class="error-messages">
                                                    <li :key="message" v-for="message in errorMessages[`trabajador_ingreso_deduccion.${index}.monto`]" v-text="message"></li>
                                                </ul>
                                            </td>
                                            <!--<td>
                                            <strong> {{sub_total(item) | formatMoney(2)}}</strong>
                                            </td>-->

                                        </tr>
                                    </template>
                                    <tr></tr>
                                </template>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--  <td colspan="2"></td>
                                      <td>Total</td>
                                      <td> <strong> {{calcular_total | formatMoney(2)}}</strong></td> -->
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </v-tab>
                </template>

            </vue-tabs>

            <div class="row">

                <div class="col-md-6">
                    <div class="content-box-footer text-left">
                        <template v-if="form.activo==1">
                            <button @click="desactivar(form.id_trabajador)" class="btn btn-danger"><i class="fa fa-trash-o">	Eliminar</i></button>
                        </template>
                        <template v-else>
                            <button @click="activar(form.id_trabajador)" class="btn btn-success"><i class="fa fa-check-square">	Habilitar</i></button>
                        </template>
                    </div>
                </div>

            <div class="col-md-6">
                <div class="content-box-footer text-right">
                 <router-link :to="{ name: 'listado-trabajadores' }" tag="button">
                    <button class="btn btn-default" type="button">Cancelar</button>
                 </router-link>
                 <button :disabled="btnAction != 'Guardar' ? true : false" @click="actualizar" class="btn btn-primary"
                        type="button">{{ btnAction }}
                 </button>
                </div>
            </div>


            <template v-if="loading">
                <BlockUI :message="msg" :url="url"></BlockUI>
            </template>

            </div>
        </div>
    </div>

</template>

<script type="text/ecmascript-6">
    import trabajador from '../../api/trabajadores'
    import area from '../../api/areas'
    import academico from '../../api/niveles_academicos'
    import estudio from '../../api/niveles_estudios'
    import medicos from '../../api/datos_medicos'
    import cargo from '../../api/cargos'
    import departamento from '../../api/departamentos'
	import loadingImage from '../../assets/images/block50.gif'
    import es from 'vuejs-datepicker/dist/locale/translations/es'
    import parentesco from "../../api/parentesco";

    export default {
        data() {
            return {
				loading:true,
				msg: 'Cargando contenido, espere un momento',
				url : '/public'+loadingImage,   //It is important to import the loading image then use here
                es: es,
                format: "dd-MM-yyyy",
                areas: [],
                cargos: [],
                departamentos: [],
                municipios: [],
                niveles_academicos: [],
                niveles_estudios: [],
                parentescos: [],
                detalleForm: {
                    nombres:'',
                    apellidos: '',
                    fecha_nacimiento: moment(new Date()).format("YYYY-MM-DD"),
                    sexo: '',
                    id_parentesco:'',
                    tipo_identificacion:'',
                    no_identificacion:'',
                    no_telefonico:'',
                },
                form: {
                    trabajador_grupo_familiar:[],
                    trabajador_ingreso_deduccion:[],
                    codigo: '',
                    primer_nombre: '',
                    segundo_nombre: '',
                    primer_apellido: '',
                    segundo_apellido: '',
                    cedula: '',
                    fecha_ingreso: moment(new Date()).format("YYYY-MM-DD"),
                    fecha_egreso: moment(new Date()).format("YYYY-MM-DD"),
                    trabajador_area: '',
                    trabajador_cargo: '',
                    tipo_contrato: '',
                    no_inss: '',
                    salario_basico: '',
                    trabajador_nivel_academico:'',
                    trabajador_nivel_estudio:'',
                    trabajador_datos_medicos:[],

                    trabajador_detalles: [],

                    /*sexo: '',
                    estado_civil: '',
                    departamento: '',
                    municipio: '',
                    email: '',
                    telefono: '',

                    direccion: '',
                    notifica: '',
                    telefono_notifica: '',*/

                    imagen: '',
                    id_nomina:'',

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
                    self.departamentos = data;
                    //console.log(self.departamentos);
                }, err => {
                    console.log(err)
                })
            },
            obtenerTodosParentesco() {
                var self = this
                parentesco.obtenerTodas(data => {
                    self.parentescos = data
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
                self.form.municipio = '';
                if (self.form.departamento.municipios_departamento)
                    self.municipios = self.form.departamento.municipios_departamento
            },
            obtenerTodosNivelesAcademicos() {
                var self = this
                academico.obtenerTodas(data => {
                    self.niveles_academicos = data;
                    //console.log(self.departamentos);
                }, err => {
                    console.log(err)
                })
            },
            obtenerTodosNivelesEstudios() {
                var self = this
                estudio.obtenerTodas(data => {
                    self.niveles_estudios = data;
                    //console.log(self.departamentos);
                }, err => {
                    console.log(err)
                })
            },
            obtenerDatosMedicos() {
                var self = this
                medicos.obtenerTodas(data => {
                    self.trabajador_datos_medicos = data;
                    //console.log(self.departamentos);
                }, err => {
                    console.log(err)
                })
            },

            obtenerImg() {
                var imagen = $('input[name="imagen"]').val();
                if (JSON.parse(imagen).length) {
                    return imagen;
                } else {
                    return "";
                }
            },
            obtenerTrabajador() {
                var self = this
                trabajador.obtenerTrabajador({
                    id_trabajador: this.$route.params.id_trabajador
                }, data => {
                    self.form = data
                    if(self.form.imagen!==''){
                        try{
                            self.uploader.set_files(data.form.imagen);
                        }catch(Exception ){
                            console.log('error asignando imagen');
                        }
                    }


                    if(self.form.trabajador_detalles.trabajador_municipio){
                    self.form.departamento = self.form.trabajador_detalles.trabajador_municipio.departamento_municipio;
                    self.municipios = self.form.trabajador_detalles.trabajador_municipio.departamento_municipio.municipios_departamento;
                    self.form.municipio = self.form.trabajador_detalles.trabajador_municipio;
                    }
					self.loading = false;
                }, err => {
                    console.log(err)
                    //alertify.error(err.id[0], 5);
                    this.$router.push({
                        name: 'listado-trabajadores'
                    });
                })
            },

            agregarDetalle() {
                let self = this;
                if(this.detalleForm.no_identificacion){
                    let i = 0;
                    let keyx = 0;
                    if(self.form.trabajador_grupo_familiar){
                        self.form.trabajador_grupo_familiar.forEach((fila, key) => {
                            if(self.detalleForm.no_identificacion===fila.no_identificacion){
                                i++;
                                keyx = key;
                            }
                        });
                    }
                    if(i === 0){
                        this.form.trabajador_grupo_familiar.push({
                            nombres: this.detalleForm.nombres,
                            apellidos: this.detalleForm.apellidos,
                            sexo: this.detalleForm.sexo,
                            id_parentesco: (this.detalleForm.parentesco!==""&&this.detalleForm.parentesco!==null)?this.detalleForm.parentesco.id_parentesco:null,
                            fecha_nacimiento: this.detalleForm.fecha_nacimiento,
                            tipo_identificacion: this.detalleForm.tipo_identificacion,
                            no_identificacion: this.detalleForm.no_identificacion,
                            no_telefonico: this.detalleForm.no_telefonico,
                        });
                        //this.detalleForm.fecha_viatico='';
                        this.detalleForm.nombres='';
                        this.detalleForm.apellidos='';
                        this.detalleForm.sexo='';
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
                if (this.form.trabajador_grupo_familiar.length >= 1) {
                    this.form.trabajador_grupo_familiar.splice(index, 1);

                }else{
                    this.form.trabajador_grupo_familiar=[];
                }
            },
            actualizar() {
                var self = this
				self.loading = true;
                self.btnAction = 'Guardando, espere...'
                self.form.imagen = this.obtenerImg();
                trabajador.actualizar(self.form, data => {
                    alertify.success("Datos del trabajador actualizados correctamente", 5);
                    this.$router.push({
                        name: 'listado-trabajadores'
                    })
                }, err => {
                	self.loading = false;
                    self.errorMessages = err
                    self.btnAction = 'Guardar'
                })
            },
            desactivar(areaId) {
                var self = this;
                self.$swal.fire({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText:'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        trabajador.desactivar({
                            id_trabajador: areaId
                        }, data => {
                            alertify.warning("Trabajador desactivado correctamente", 5);
                            this.$router.push({
                                name: 'listado-trabajadores'
                            })
                        }, err => {
                            console.log(err)
                        })
                    } else {
                        self.btnAction = "Guardar";
                    }

                })
            },
            activar(areaId) {

                var self = this;
                self.$swal.fire({
                    title: 'Esta seguro de cambiar el estado?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        trabajador.activar({
                            id_trabajador: areaId
                        }, data => {
                            alertify.success("Trabajador activado correctamente", 5);
                            this.$router.push({
                                name: 'listado-trabajadores'
                            })
                        }, err => {
                            console.log(err)
                        })
                    } else {
                        self.btnAction = "Guardar";
                    }
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
                    on_before_upload: function () {
                        self.btnAction = "Subiendo imágenes...";
                    },
                    on_success_upload: function () {
                        self.btnAction = "Guardar";
                    },
                    on_error: function (err) {
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
            this.obtenerTrabajador();
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