<template>
  <div class="main">
    <div class="row">
      <div class="col-md-12">
        <div class="content-box">

          <form>

            <div class="row">
              <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                  <thead>
                  <tr>
                    <th>Unidad de medida</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>UND</td>
                    <td>Unidades</td>
                    <td>Activo</td>
                    <td style="color: blue">Editar</td>
                  </tr>
                  <tr>
                    <td>KG</td>
                    <td>Kilogramo</td>
                    <td>Activo</td>
                    <td style="color: blue">Editar</td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </form>



        </div>
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
