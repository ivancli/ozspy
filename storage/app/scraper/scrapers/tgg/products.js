var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');
var FileCookieStore = require('tough-cookie-filestore');


class Scraper {
    constructor(argvs) {
        this.apiUrl = null;

        this.category = argvs.category;
        this.retailer = argvs.retailer;

        this.baseUrl = null;
        this.storeId = null;
        this.catalogId = null;

        this.url = this.category.url;
        this.total = null;


        this.products = [];
        this.file = __dirname + '/../../storage/products/' + this.category.id + '.json';

        if (fs.existsSync(this.file)) {
            fs.unlink(this.file, err => {
                console.log(err);
            });
        }

        this.hasNext = false;

        this.offset = 0;

        this.nextUrl = null;

        this.cookies = [];
    }

    scrape() {
        this.fetchPage(response => {
            this.extractProducts(response);
        });
    }

    extractProducts(content) {
        this.extractNextPageUrl(content);
        let $this = this;
        let $ = cheerio.load(content);
        $("#product_listing_tab > ul > li").each(function () {
            let $script = $(this).find(".product-tile-inner script")
            let scriptText = $script.get()[0].children[0].data;
            let idMatches = scriptText.match(/'id': '(.*?)',/);
            let nameMatches = scriptText.match(/'name': '(.*?)',/);
            let priceMatches = scriptText.match(/'price': '(.*?)',/);
            let brandMatches = scriptText.match(/'brand': '(.*?)',/);
            if (idMatches !== null && nameMatches !== null) {
                let product = {};
                product.retailer_product_id = idMatches[idMatches.length - 1];
                product.name = cheerio.load(nameMatches[nameMatches.length - 1]).text();
                product.price = parseFloat(priceMatches[priceMatches.length - 1]);
                product.brand = brandMatches[brandMatches.length - 1];
                let $model = $(this).find(".product-tile-model")
                if ($model.length > 0) {
                    product.model = $model.text();
                }
                let $url = $(this).find("a.disp-block")
                product.url = $url.attr('href');
                $this.products.push(product);
                $this.offset++;
            }
        });

        if ($("#WC_SearchBasedNavigationResults_pagination_link_right_categoryResults").length > 0 && $this.nextUrl !== null) {
            $this.fetchProductList();
        } else {
            this.save();
        }
    }

    fetchPage(callback) {
        let options = {
            url: this.url,
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

    fetchProductList() {


        let cookieJar = request.jar();
        if (this.cookies.length > 0) {
            cookieJar.setCookie(this.cookies.join(','), this.nextUrl);
        }

        let sessionId = this.getParameterByName('ddkey', this.nextUrl);
        sessionId = sessionId.replace('ProductListingView', '');
        let options = {
            method: 'POST',
            url: this.nextUrl,
            headers: {
                'user-agent': 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
            },
            jar: cookieJar,
            form: {
                "contentBeginIndex": 0,
                "productBeginIndex": this.offset,
                "beginIndex": this.offset,
                "orderBy": "",
                "facetId": "",
                "pageView": "grid",
                "resultType": "products",
                "orderByContent": "",
                "searchTerm": "",
                "facet": "",
                "facetLimit": "",
                "minPrice": "",
                "maxPrice": "",
                "pageSize": "",
                "storeId": this.getParameterByName('storeId', this.nextUrl),
                "catalogId": this.getParameterByName('catalogId', this.nextUrl),
                "langId": "-1",
                "objectId": sessionId,
                "requesttype": "ajax",
            }
        };
        request(options, (error, response, content) => {
            if (typeof response.headers['set-cookie'] === 'object') {
                response.headers['set-cookie'].forEach(cookie => {
                    this.cookies.push(request.cookie(cookie));
                });
            }
            if (content) {
                this.extractProducts(content);
            }
        })
    }

    extractNextPageUrl(content) {
        let matchResults = content.match(/SearchBasedNavigationDisplayJS.init\('(?:.*?)','(.*?)'\)/);
        if (matchResults !== null && matchResults.length > 1) {
            this.nextUrl = matchResults[matchResults.length - 1];
            this.nextUrl = this.nextUrl.replace(/searchType=(.*?)&/, '');
            this.nextUrl = this.nextUrl.replace(/metaData=(.*?)/, '');
            return;
        }
        this.nextUrl = null;
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