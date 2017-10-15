import {
    SET_LOADING_PROMISE, UNSET_LOADING_PROMISE
} from '../../actions/action-types';



export default class Loading {
    constructor($store) {
        this.$store = $store;
    }

    setLoadingPromise(text) {
        if (typeof text !== 'undefined') {
            this.$store.dispatch(SET_LOADING_PROMISE, {
                text
            });
        } else {
            this.$store.dispatch(SET_LOADING_PROMISE)
        }
    }

    unsetLoadingPromise() {
        this.$store.dispatch(UNSET_LOADING_PROMISE);
    }
}