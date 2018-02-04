var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.apiUrl = null;

        this.category = argvs.category;
        this.retailer = argvs.retailer;

        this.baseUrl = null;
        this.storeId = null;
        this.catalogId = null;

        this.priceApiUrl = this.retailer.domain + '/webapp/wcs/stores/servlet/OWGetPrice';

        this.url = this.category.url;
        this.total = null;


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

        this.offset = 0;

        this.hasNext = false;
    }

    scrape() {
        this.fetchPage(response => {
            if (response.match(/ajaxcategoryresultsviewurl = '(.*?)';/) !== null && response.match(/storeId: '(.*?)',/) !== null && response.match(/catalogId: '(.*?)',/) !== null) {
                this.baseUrl = response.match(/ajaxcategoryresultsviewurl = '(.*?)';/)[1];
                this.storeId = response.match(/storeId: '(.*?)',/)[1];
                this.catalogId = response.match(/catalogId: '(.*?)',/)[1];
                this.setUrl();
                this.fetch();
            }
        });
    }

    fetchPage(callback) {
        request(this.url, (error, response, content) => {
            if (typeof callback === 'function') {
                callback(content);
            }
        });
    }

    fetch() {
        let options = {
            url: this.apiUrl,
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

        if (content.totalPages && content.currentPage && parseInt(content.totalPages) > parseInt(content.currentPage)) {
            this.hasNext = true;
            this.offset += parseInt(content.pageSize);
        } else {
            this.hasNext = false;
        }
        if (content.products && typeof content.products === 'object') {
            content.products.forEach(product => {
                let webProduct = {};
                webProduct.name = cheerio.load(product.productName).text();
                webProduct.url = this.retailer.domain + product.productDisplayUrl;
                webProduct.retailer_product_id = product.uniqueID;
                webProduct.model = product.partNumber;
                this.products.push(webProduct);
            });
        }
        if (this.hasNext === true) {
            this.setUrl();
            this.fetch();
        } else {
            this.fetchPrices(() => {
                this.save();
            });
        }
    }

    fetchPrices(callback) {
        let productIds = this.products.map(product => product.retailer_product_id);
        let productIdString = productIds.join('%2C');
        let url = this.priceApiUrl + '?storeId=' + this.storeId + '&catalogId=' + this.catalogId + '&nc=true&productId=' + productIdString;
        request(url, (error, response, content) => {
            content = JSON.parse(content);
            let prices = content.prices;
            this.products = this.products.map(product => {
                let price = prices.filter(price => price.productId === product.retailer_product_id);
                if (price.length > 0) {
                    price = price[0];
                    product.price = price.priceBigDecimal;
                }
                return product;
            });

            if (typeof callback === 'function') {
                callback();
            }
        });
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

    setUrl() {
        this.apiUrl = this.retailer.domain + this.baseUrl + '&storeId=' + this.storeId + '&catalogId=' + this.catalogId + '&beginIndex=' + this.offset;
    }
}

module.exports = Scraper;