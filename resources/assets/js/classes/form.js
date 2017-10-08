import Errors from './errors';

export default class Form {

    /**
     * Initialise data as object constructed
     * @param data
     */
    constructor(data) {
        this.originalData = data;

        for (let field in data) {
            if (data.hasOwnProperty(field)) {
                this[field] = data[field];
            }
        }
        this.errors = new Errors();
    }

    /**
     * Reset data as per originalData
     */
    reset() {
        for (let field in this.originalData) {
            if (this.originalData.hasOwnProperty(field)) {
                this[field] = this.originalData[field];
            }
        }
    }

    /**
     * Collect data from originalData
     * @returns {{}}
     */
    data() {
        let data = {};
        for (let property in this.originalData) {
            if (this.hasOwnProperty(property)) {
                data[property] = this[property];
            }
        }

        return data;
    }

    /**
     * Send request with form data
     * @param requestType
     * @param url
     * @returns {Promise}
     */
    submit(requestType, url) {
        this.errors.clear();

        return new Promise((resolve, reject) => {
            console.info('requestType', requestType);
            axios[requestType](url, this.data())
                .then(response => {
                    this.onSuccess(response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    this.onFail(error.response.data);
                    reject(error.response.data)
                });
        });
    }

    /**
     * Send get request
     * @param url
     * @returns {*}
     */
    get(url) {
        return this.submit('get', url);
    }

    /**
     * Send post request
     * @param url
     * @returns {*}
     */
    post(url) {
        return this.submit('post', url);
    }

    /**
     * Send patch request
     * @param url
     * @returns {*}
     */
    patch(url) {
        return this.submit('patch', url);
    }

    /**
     * Send delete request
     * @param url
     * @returns {*}
     */
    del(url) {
        return this.submit('delete', url);
    }

    /**
     * On success callback
     * @param response
     */
    onSuccess(response) {
        this.reset();
    }

    /**
     * On failed callback
     * @param response
     */
    onFail(response) {
        this.errors.setError(response.errors);
        this.errors.setMessage(response.message);
    }
}