var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.category = argvs.category;
        this.url = this.category.url;
        this.products = [];
        this.file = __dirname + '/../../storage/products/' + this.category.id + '.json';

        this.test = argvs.test;

        if (fs.existsSync(this.file)) {
            fs.unlink(this.file, err => {
                console.log(err);
            });
        }
    }

    scrape() {
        this.fetch(this.url);
    }

    fetch(url) {
        request(url, (error, response, html) => {
            this.parse(html);
        })
    }

    parse(html) {
        let $ = cheerio.load(html);
        let $this = this;
        $('ul.products-grid > li.item').each(function () {
            let product = {};

            let classes = $(this).attr('class');
            let classList = classes.split(' ');
            for (let key in classList) {
                if (classList.hasOwnProperty(key)) {
                    if (classList[key].indexOf('allajax-productdatafetch-') > -1) {
                        product.retailer_product_id = classList[key].replace('allajax-productdatafetch-', '');
                    }
                }
            }
            product.name = $(this).find('h2.product-name > a').text();
            product.url = $(this).find('.product-image-wrapper a.product-image').attr('href');
            let priceText = $(this).find('.regular-price .price, .special-price .price').text();
            priceText = priceText.replace(',', '');
            priceText = priceText.replace('$', '');
            product.price = parseFloat(priceText) > 0 ? parseFloat(priceText) : null;
            if (typeof product.retailer_product_id !== 'undefined') {
                $this.products.push(product);
            }
        });

        if ($('.pager li.next a.next').length > 0) {
            let href = $('.pager li.next a.next').attr('href');
            this.fetch(href);
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
        process.exit();
    }
}

module.exports = Scraper;