<template>
  <div class="main">
    <div class="content-box">
      <div class="content-box-header">

      </div>
      <div class="row">

        <div class="col-sm-2 sm-text-center form-inline justify-content-start">
          <router-link class="btn btn-success" tag="button" :to="{ name: 'registrar-factura' }">
            <i class="pe-7s-plus"></i> Facturar
          </router-link>
        </div>

        <div

                class="col-sm-10 sm-text-center form-inline justify-content-end"
        >
          <select

                  class="form-control mb-1 mr-sm-1 mb-sm-0 search-field"
          >
            <option value="no_documento">No. Documento</option>
          </select>
          <input

                  class="form-control mb-1 mr-sm-1 mb-sm-0"
                  placeholder="Buscar"
                  type="text"
          >
          <button class="btn btn-info" >
            <i class="pe-7s-search"></i> Buscar
          </button>
        </div>
      </div>
      <div class="table-responsive mt-3">
        <table class="table table-striped table-bordered">
          <thead>
          <tr>
            <th>No. Factura</th>
            <th>Fecha</th>
            <th>Vendedor</th>
            <th>Bodega</th>
            <th>Cliente</th>
            <th colspan="2" class="text-center action">Opciones</th>
          </tr>
          </thead>
          <tbody>
          <template>

            <tr >
              <td >{{"FACT.1"}}</td>
              <td >{{"25/01/2022"}}</td>
              <td >{{"Isaac Rodriguez"}}</td>
              <td >{{"Bodega Central"}}</td>
              <td >{{"Juan Perez"}}</td>
              <td class="text-center" style="color: blue">{{"Editar"}}</td>
            </tr>
            <tr >
              <td >{{"FACT.2"}}</td>
              <td >{{"24/01/2022"}}</td>
              <td >{{"Isaac Rodriguez"}}</td>
              <td >{{"Bodega Central"}}</td>
              <td >{{"Juan Perez"}}</td>
              <td class="text-center" style="color: blue">{{"Editar"}}</td>

            </tr>
          </template>
         <!-- <tr >
            <td class="text-center" colspan="10">Sin datos</td>
          </tr>-->
          </tbody>
        </table>
      </div>


    </div>
  </div>
</template>

<script>
import { BCard, BCardText } from 'bootstrap-vue'
import {getUserData} from "../auth/utils";

export default {
  components: {
    BCard,
    BCardText,
  },
  data() {
    return {
      form: {
        userData: {},
      }
    }
  },
  methods: {
    getUserData() {
      axios.get('api/user').then(res => {
        this.form.userData = res.data
      })
    },
  },
  mounted() {
    getUserData();
  }
}
</script>

<style>

</style>
