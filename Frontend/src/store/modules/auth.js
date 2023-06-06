import Vue from 'vue';
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex);


const state = {
    user: {},
    status: '',
    auth: false
};

const getters = {
    authenticated(state) {
        return state.auth
    },
    user(state) {
        return state.user
    },
    status(state) {
        return state.status
    }
};
const mutations = {
    SET_AUTHENTICATED(state, value) {
        state.auth = value
    },
    SET_USER(state, value) {
        state.user = value
    },
    SET_STATUS(state, value) {
        state.status = value;
    }
};
const actions = {
    async login({commit}, credentials) {
        await axios.get('/sanctum/csrf-cookie');
        await axios.post('/login', credentials).then(({data}) => {
             console.log(data.status);
            commit("SET_USER", data);
            commit('SET_AUTHENTICATED', true);
            commit('SET_STATUS', data.status);
            // return dispatch("getUser");
        }).catch(({response: {data}}) => {
            console.log(data.status);
            commit('SET_USER', {});
            commit('SET_AUTHENTICATED', false);
            commit('SET_STATUS', data.status);
        })

    },
    async logout({commit}) {
        await axios.post("/logout").then(({data}) => {
            console.log(data);
            commit('SET_USER', {});
            commit('SET_AUTHENTICATED', false)
        }).catch(({response: {data}}) => {
            commit('SET_USER', {});
            commit('SET_AUTHENTICATED', false);
            commit('SET_STATUS', data.status);
        });

        // return dispatch("getUser");
    },
    getUser({commit}) {
        axios.get('/me').then(response => {
            commit("SET_USER", response.data)
        }).catch(() => {
            commit("SET_USER", null)
        })
    },
};

export default {
    state,
    mutations,
    actions,
    getters
}

