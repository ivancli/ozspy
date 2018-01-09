var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.apiUrl = 'https://www.appliancesonline.com.au/angular-api/product-filter.ashx';

        this.productApiUrl = 'https://www.appliancesonline.com.au/api/product/id/';

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

        this.categoryId = null;

        this.productIds = [];

        this.pageNumber = 0;

        this.available = true;

        this.cookies = [];
    }

    scrape() {
        this.fetchPage(response => {
            this.extractCategoryId(response);
            this.fetchProductIds();
        });
    }

    extractCategoryId(content) {
        let $ = cheerio.load(content);
        this.categoryId = $('[categoryid]').attr('categoryid');
    }

    fetchPage(callback) {
        let options = {
            url: this.category.url,
            headers: {
                'user-agent': 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
            },
        };
        request(options, (error, response, content) => {
            if (typeof response.headers['set-cookie'] === 'object') {
                response.headers['set-cookie'].forEach(cookie => {
                    this.cookies.push(request.cookie(cookie));
                });
            }

            if (typeof callback === 'function') {
                callback(content);
            }
        });
    }

    setUrl() {
        this.url = this.apiUrl + '?'
            + 'CategoryID=' + this.categoryId + '&'
            + 'view=show_all&'
            + 'pagenum=' + this.pageNumber;
    }

    fetchProductIds() {
        this.pageNumber++;
        this.setUrl();
        console.log(this.url);

        let options = {
            url: this.url,
            headers: {
                'user-agent': 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15'
            }
        };

        request(options, (error, response, content) => {
            this.parseProductIds(content);
        })
    }

    parseProductIds(content) {
        content = JSON.parse(content);
        if (content.gridProductIds) {
            content.gridProductIds.forEach(productId => {
                if (this.productIds.indexOf(productId) === -1) {
                    this.productIds.push(productId);
                }
            });
        }
        if (content.pageCount && content.pageCount > this.pageNumber) {
            this.fetchProductIds();
        } else {
            this.fetch();
        }
    }


    fetch() {
        let productId = this.productIds.shift();
        this.url = this.productApiUrl + productId;

        let options = {
            url: this.url,
            headers: {
                'user-agent': 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15'
            }
        };

        request(options, (error, response, content) => {
            this.parse(content);
        })
    }

    parse(content) {
        content = JSON.parse(content);
        if (content.product) {
            let product = {};
            product.name = content.product.name;
            product.brand = content.product.manufacturer && content.product.manufacturer.name ? content.product.manufacturer.name : null;
            product.sku = content.product.sku;
            product.retailer_product_id = content.product.productId;
            product.price = parseFloat(content.product.price) > 0 ? parseFloat(content.product.price) : null;
            product.url = this.retailer.domain + content.product.url;
            this.products.push(product);
        }
        if (this.productIds.length > 0) {
            this.fetch();
        } else {
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
}

module.exports = Scraper;