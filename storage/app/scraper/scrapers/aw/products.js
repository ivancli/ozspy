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

        if (fs.existsSync(this.file)) {
            fs.unlink(this.file, err => {
                console.log(err);
            });
        }

        this.page = 1;
    }

    scrape() {
        let url = this.setUrl();
        this.fetch(url);
    }

    setUrl() {
        if (this.page !== null) {
            return this.url + '?is_ajax=1&is_scroll=1&p=' + this.page;
        }
        return null;
    }

    fetch(url) {
        console.log(url);
        let options = {
            url: url,
            headers: {
                'user-agent': 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
                'accept': 'application/json',
                'content-type': 'application/json',
            }
        };
        request(options, (error, response, content) => {
            this.parse(content);
        })
    }

    parse(html) {
        let $ = cheerio.load(html);
        let $this = this;
        $('.products-grid > .item').each(function () {
            let product = {};
            product.name = $(this).find('.product-name a').text();
            product.model = $(this).find('td > center > font[size=1] > i').text();
            if ($(this).find('.regular-price .price').length > 0) {
                let priceText = $(this).find('.regular-price .price').text().replace('$', '').replace(',', '');
                product.price = parseFloat(priceText) > 0 ? parseFloat(priceText) : null;
            }

            let priceBoxID = $(this).find('.price-box > span').attr('id');
            if (priceBoxID) {
                let idMatches = priceBoxID.match(/product-price-(\d+)/);
                if (idMatches) {
                    product.retailer_product_id = idMatches[1];
                }
            }

            product.url = $(this).find('.product-name a').attr('href');
            if (product.retailer_product_id) {
                $this.products.push(product);
            }
        });
        //
        if ($('.toolbar-bottom .pages li.current').next().length > 0) {
            $this.page = $('.toolbar-bottom .pages li.current').next().find('> a').text();
            let url = $this.setUrl();
            $this.fetch(url);
        } else {
            $this.save();
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