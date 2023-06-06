import Vue from 'vue'

// axios
import axios from 'axios'
const axiosIns = axios.create({
  // You can add your headers here
  // ================================
   //baseURL: 'http://localhost:8001/', // route with port of API Laravel
   //timeout: 1000,
   //withCredentials: true,
  // headers: {'X-Custom-Header': 'foobar'}
});

Vue.prototype.$http = axiosIns

export default axiosIns
