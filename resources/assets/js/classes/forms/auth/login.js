import Errors from "../../errors";

require('../../errors');

export default class Form {
    constructor(data) {
        this.originalData = data;

        for (let field in data) {
            this[field] = data[field];
        }
        this.errors = new Errors();
    }

    reset() {
        for (let field in originalData) {
            this[field] = null;
        }
    }

    data() {
        return {
            email: this.email,
            password: this.password,
        };
    }

    submit(requestType, url) {
        console.info('requestType', requestType);
        axios[requestType](url, this.data())
            .then(this.onSuccess.bind(this))
            .catch(this.onFail.bind(this));
    }

    onSuccess(response) {

    }

    onFail(response) {
        console.info('response.response.data', response.response.data.errors);
        if (response.response.status === 422) {
            this.errors.set(response.response.data.errors);
        }
    }
}