require('es6-promise/auto');

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import loading from '../partials/loadings/loading';

export default new Vuex.Store({
    modules: {
        loading,
        index: {
            state: {},
            mutations: {},
            actions: {},
            getters: {}
        }
    }
});