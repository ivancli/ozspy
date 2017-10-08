export default class Errors {
    constructor() {
        this.errors = {};
        this.message = null;
    }

    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    any() {
        return Object.keys(this.errors).length > 0;
    }

    getError(field) {
        if (this.errors[field]) {
            return this.errors[field][0];
        }
    }

    setError(errors) {
        this.errors = errors;
    }

    setMessage(message) {
        this.message = message;
    }

    getMessage() {
        if (this.message) {
            return this.message;
        }
    }

    clear(field) {
        if (field) {
            delete this.errors[field];
            return;
        }

        this.errors = {};
    }
}