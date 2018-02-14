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

        this.apiUrl = 'https://www.appliancecentral.com.au/index.php?route=module/filterpro/getproducts';

        this.path = null;
        this.category_id = null;
        this.page = 1;
        this.max_price = 9999999;
        this.min_price = 1;
        this.limit = 25;
        this.sort = 'p.price';
        this.order = 'ASC';
        this.route = 'product/category';
    }

    scrape() {
        this.fetchQueryParameters();
    }

    fetchQueryParameters() {
        let $this = this;
        request(this.category.url, (error, response, html) => {
            let $ = cheerio.load(html);
            $this.category_id = $('input[name="category_id"]').attr('value');
            $this.path = $('input[name="path"]').attr('value');
            this.fetch(this.apiUrl);
        });
    }

    fetch(url) {
        request.post(url, {form: this.composerData()}, (error, response, content) => {
            let jsonContent = JSON.parse(content);
            if (jsonContent.pagination) {
                this.parsePaging(jsonContent.pagination)
            }
            if (jsonContent.result_html) {
                this.parse(jsonContent.result_html);
            }
        })
    }

    parsePaging(html) {
        let $ = cheerio.load(html);
        if ($('b').next()) {
            this.page = $('b').next().text();
        } else {
            this.page = null;
        }
    }

    composerData() {
        return {
            path: this.path,
            category_id: this.category_id,
            page: this.page,
            max_price: this.max_price,
            min_price: this.min_price,
            limit: this.limit,
            sort: this.sort,
            order: this.order,
            route: this.route,
        }
    }

    parse(html) {
        let $ = cheerio.load(html);
        let $this = this;
        $('<div>').append(html).find(' > div').each(function () {
            let product = {};
            product.name = $(this).find('.name > a').text();
            product.model = $(this).find('.name > .filter-model').text();
            if ($(this).find('.price .price-new').length > 0) {
                let priceText = $(this).find('.price .price-new').text().replace('$', '').replace(',', '');
                product.price = parseFloat(priceText) > 0 ? parseFloat(priceText) : null;
            } else {
                $(this).find('.price').find('br').remove();
                $(this).find('.price').find('span').remove();
                let priceText = $(this).find('.price').text().replace('$', '').replace(',', '').replace(/\n/g, '').trim();
                product.price = parseFloat(priceText) > 0 ? parseFloat(priceText) : null;
            }

            let onclickAttribute = $(this).find('.cart input').attr('onclick');
            let idMatches = /\(\'(.*?)\'\);/.exec(onclickAttribute);
            if (idMatches[1]) {
                product.retailer_product_id = idMatches[1];
            }

            product.url = $(this).find('.name > a').attr('href');
            $this.products.push(product);
        });

        //
        if (this.page !== null && this.page.trim() != '') {
            this.fetch(this.apiUrl);
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