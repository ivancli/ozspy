var request = require('request');
var cherrio = require('cheerio');
var jsonfile = require('jsonfile');


class Scraper {
    constructor(id, url) {
        this.id = id;
        this.url = url;
        this.products = [];
        this.file = __dirname + '/../../storage/products/' + this.id + '.json';
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
        let $ = cherrio.load(html);
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
        this.save();

        if ($('#toolbar-btm .icn-next-page').length > 0) {
            let href = $('#toolbar-btm .icn-next-page').attr('href');
            this.fetch(href);
        }
    }

    save() {
        jsonfile.readFile(this.file, (err, existingObject) => {
            let products = this.products;
            let retailer_product_ids = products.map(product => product.retailer_product_id);

            if (typeof existingObject !== 'undefined') {
                let outstandingProducts = existingObject.products.filter(function (product) {
                    return retailer_product_ids.indexOf(product.retailer_product_id) === -1;
                });
                products = products.concat(outstandingProducts);
            }

            let object = {
                category_id: this.id,
                products: products
            };

            jsonfile.writeFileSync(this.file, object);
        });
    }
}

module.exports = Scraper;