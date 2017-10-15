require('es6-promise/auto');

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import loading from '../partials/loadings/loading';
import alert from '../partials/modals/alert';

export default new Vuex.Store({
    modules: {
        loading,
        alert,
        index: {
            state: {},
            mutations: {},
            actions: {},
            getters: {}
        }
    }
});