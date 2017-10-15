/**
 * Created by Ivan on 2/03/2017.
 */
import {
    SET_LOADING_PROMISE, UNSET_LOADING_PROMISE
} from '../../../actions/mutation-types';


export default {
    namespaced: true,
    state: {
        promise: 0,
        text: null,
    },
    mutations: {
        [SET_LOADING_PROMISE](state, params) {
            if (state.promise >= 0) {
                state.promise++;
            } else {
                state.promise = 1;
            }
            if (params && params.hasOwnProperty('text')) {
                state.text = params.text;
            }
        },
        [UNSET_LOADING_PROMISE](state, params) {
            if (state.promise > 0) {
                state.promise--;
            } else {
                state.promise = 0;
            }
            state.text = null;
        },
    },
    actions: {
        setLoadingPromise({commit, state}, params) {
            commit(SET_LOADING_PROMISE, params);
        },
        unsetLoadingPromise({commit, state}, params) {
            commit(UNSET_LOADING_PROMISE, params);
        },
    },
    getters: {
        promise: state => state.promise,
        text: state => state.text,
    }
}