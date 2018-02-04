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
        $('#category-grid .panel_product').each(function () {
            let product = {};
            product.retailer_product_id = $(this).attr("data-pid");
            let nameNode = $(this).find('.info .name');
            product.name = nameNode.text();
            product.url = nameNode.attr('href');
            let priceNode = $(this).find('.price-device .price');
            let priceText = priceNode.text().replace('$', '');
            product.price = parseFloat(priceText) > 0 ? parseFloat(priceText) : null;
            $this.products.push(product);
        });

        if ($('#toolbar-btm .icn-next-page').length > 0) {
            let href = $('#toolbar-btm .icn-next-page').attr('href');
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