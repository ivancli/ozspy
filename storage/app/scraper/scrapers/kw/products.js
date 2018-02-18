var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.apiUrl = 'http://search.unbxdapi.com/';
        this.jsUrl = 'http://www.kitchenwarehouse.com.au/kwh/scripts/kwh-autosuggest.js';

        this.category = argvs.category;
        this.retailer = argvs.retailer;
        this.url = null;
        this.products = [];
        this.file = __dirname + '/../../storage/products/' + this.category.id + '.json';

        if (fs.existsSync(this.file)) {
            fs.unlink(this.file, err => {
                console.log(err);
            });
        }

        this.available = true;

        this.apiKey = null;
        this.siteName = null;

        this.start = 0;
        this.rows = 30;


    }

    scrape() {
        let $this = this;

        this.fetchJS((error, response, content) => {
            if ($this.apiKey !== null && $this.siteName !== null) {
                let apiUrl = $this.setupUrl();
                $this.fetch(apiUrl);
            }
        });
    }

    fetchJS(callback) {
        let $this = this;
        request(this.jsUrl, (error, response, content) => {
            if (typeof callback === 'function') {
                $this.apiKey = content.match(/UnbxdApiKey="(.*?)"/)[1];
                $this.siteName = content.match(/UnbxdSiteName="(.*?)"/)[1];
                callback(error, response, content);
            }
        });
    }

    fetch(url) {
        request(url, (error, response, content) => {
            this.parse(content);
        })
    }

    parse(content) {

        content = JSON.parse(content);

        if (content.response) {


            content.response.products.forEach(product => {
                let webProduct = {};
                webProduct.name = cheerio.load(product.title).text();
                webProduct.url = product.productUrl;
                webProduct.retailer_product_id = product.uniqueId;
                webProduct.price = product.price !== null && parseFloat(product.price) > 0 ? product.price : null;
                webProduct.brand = product.Brand_On_Website !== null ? cheerio.load(product.Brand_On_Website).text() : null;
                this.products.push(webProduct);
            });


            if (!isNaN(content.response.numberOfProducts) && this.products.length < content.response.numberOfProducts) {
                this.start += content.response.products.length;
                let apiUrl = this.setupUrl();
                this.fetch(apiUrl);
            } else {
                this.save();
            }
        }
    }

    save() {
        let object = {
            category_id: this.category.id,
            scraped_at: moment().format(),
            products: this.products
        };

        jsonfile.writeFileSync(this.file, object);

        if (global.gc) {
            global.gc();
        } else {
            console.log('Garbage collection unavailable.  Pass --expose-gc when launching node to enable forced garbage collection.');
        }
        process.exit();
    }

    setupUrl() {

        let categories = this.getParameterByName('stq', this.category.url);
        if (categories === null) {
            categories = this.category.url.replace(this.retailer.domain, '');
        }

        return this.apiUrl + this.apiKey + '/' + this.siteName + '/search?q=' + encodeURI(categories) +
            '&start=' + this.start +
            '&rows=' + this.rows +
            '&format=json' +
            '&stats=price' +
            '&facet.multiselect=true' +
            '&indent=off' +
            '&device-type=Desktop' +
            '&unbxd-url=' + encodeURI(this.category.url) +
            '&unbxd-referrer=' +
            '&user-type=new' +
            '&api-key=' + this.apiKey +
            '&uid=uid-' + (new Date()).getTime() + parseInt(Math.random() * 1000000)
    }

    getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        let regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
}

module.exports = Scraper;