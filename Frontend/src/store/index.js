import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from "vuex-persistedstate";

// Modules
import app from './app'
import appConfig from './app-config'
import verticalMenu from './vertical-menu'
import auth from './modules/auth'

Vue.use(Vuex);

export default new Vuex.Store({
  plugins:[
      createPersistedState()
  ],
  modules: {
    app,
    appConfig,
    verticalMenu,
    auth
  },
  strict: process.env.DEV,
})
