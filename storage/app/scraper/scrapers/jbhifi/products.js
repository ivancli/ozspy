var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.apiUrl = 'https://products.jbhifi.com.au/product/get/id';

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

        this.productIds = [];

        this.productLinks = {};
    }


    scrape() {
        this.setUrl();
        this.fetchIds(this.category.url);
    }

    fetchIds(url) {
        request(url, (error, response, content) => {
            this.parse(content);
        })
    }

    parse(content) {
        let $this = this;
        let $ = cheerio.load(content);
        $('.content[data-productid!="{{ hit.Id }}"]').each(function () {
            let productId = $(this).attr('data-productid');
            if (productId) {
                $this.productIds.push(productId);
                let url = $this.retailer.domain + $(this).find('a.link').attr('href');
                if (!$this.productLinks.hasOwnProperty(productId)) {
                    $this.productLinks[productId] = url;
                }
            }
        });

        let $currentPage = $(".currentPage").next();
        let $nextFive = $(".nextFive");
        if ($currentPage.length > 0) {
            this.fetchIds($currentPage.attr('href'));
        } else if ($nextFive.length > 0) {
            this.fetchIds($nextFive.attr('href'));
        } else {
            this.fetchProductInfo();
        }
    }

    fetchProductInfo() {
        let options = {
            url: this.apiUrl,
            method: 'POST',
            'user-agent': 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
            'content-type': 'application/json; charset=utf-8',
            form: {
                Ids: this.productIds
            },
            json: true,
        };
        request(options, (error, response, content) => {
            if (content) {
                let products = JSON.parse(content.substr(1));
                if (products.Result && products.Result.Products) {
                    products.Result.Products.forEach(product => {
                        if (this.productLinks.hasOwnProperty(product.ProductID)) {
                            let newProduct = {};
                            newProduct.retailer_product_id = product.ProductID;
                            newProduct.sku = product.SKU;
                            newProduct.name = product.DisplayName;
                            newProduct.brand = product.Brand;
                            newProduct.price = product.PlacedPrice;
                            newProduct.url = this.productLinks[product.ProductID];
                            this.products.push(newProduct);
                        }
                    });

                    this.save();
                }
            }
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

    setUrl() {
        let url = this.category.url;
        url = url.substr(url.indexOf('/c/') + 3).replace((new RegExp(/\//, 'g')), '%2F');
        this.url = this.apiUrl + url;
    }
}

module.exports = Scraper;