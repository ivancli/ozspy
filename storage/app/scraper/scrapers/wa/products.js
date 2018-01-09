var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.apiUrl = 'https://www.winningappliances.com.au/api/category/';

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

        this.department = null;
        this.filterFieldName = null;
        this.filterFieldValue = null;
        this.available = true;
    }

    scrape() {
        this.setUrl();
        this.fetch(this.url);
    }

    fetch(url) {
        request(url, (error, response, content) => {
            this.parse(content);
        })
    }

    parse(content) {
        content = JSON.parse(content);
        if (content.products) {
            content.products.forEach(product => {
                let webProduct = {};
                webProduct.name = cheerio.load(product.title).text();

                let paths = product.uri.split('/');
                let slug = null;
                if (paths.length > 0) {
                    slug = paths[paths.length - 1];
                }
                webProduct.slug = slug;
                webProduct.url = this.retailer.domain + product.uri;
                webProduct.price = product.price !== null && parseFloat(product.price) > 0 ? parseFloat(product.price) : null;
                webProduct.brand = product.brand;
                webProduct.sku = product.sku;
                this.products.push(webProduct);
            });
            console.log(this.products);
            this.save();
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
    }

    setUrl() {
        let url = this.category.url;
        url = url.substr(url.indexOf('/c/') + 3).replace((new RegExp(/\//, 'g')), '%2F');
        this.url = this.apiUrl + url;
    }
}

module.exports = Scraper;