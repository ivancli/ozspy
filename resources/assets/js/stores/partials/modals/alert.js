/**
 * Created by Ivan on 2/03/2017.
 */
import {
    SET_ALERT_MODAL, CLEAR_ALERT_MODAL
} from '../../../actions/mutation-types';


export default {
    namespaced: true,
    state: {
        title: null,
        content: null,
        effect: 'fade',
        small: true,
    },
    mutations: {
        [SET_ALERT_MODAL](state, params) {
            for (let property in params) {
                if (params.hasOwnProperty(property) && state.hasOwnProperty(property)) {
                    state[property] = params[property];
                }
            }
        },
        [CLEAR_ALERT_MODAL](state, params) {
            this.title = null;
            this.content = null;
            this.effect = 'fade';
            this.small = true;
        },
    },
    actions: {
        setAlertModal({commit, state}, params) {
            commit(SET_ALERT_MODAL, params);
        },
        clearAlertModal({commit, state}, params) {
            commit(CLEAR_ALERT_MODAL, params);
        },
    },
    getters: {
        title: state => state.title,
        content: state => state.content,
        effect: state => state.effect,
        small: state => state.small,
    }
}