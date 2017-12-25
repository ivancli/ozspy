var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.apiUrl = 'https://www.dicksmith.com.au/api/v1/products';

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

        this.offset = 0;

        this.hasNext = false;

        this.setUpSlugs();
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

        if (content.meta) {
            this.hasNext = content.meta.has_next;
            this.offset += content.meta.limit;
        }
        if (content.objects) {
            content.objects.forEach(product => {
                let webProduct = {};
                webProduct.name = cheerio.load(product.title).text();
                webProduct.slug = product.slug;
                webProduct.url = this.retailer.domain + product.url;
                webProduct.price = product.price !== null && parseFloat(product.price) > 0 ? product.price : null;
                webProduct.retailer_product_id = product.id;
                webProduct.brand = product.brand;
                webProduct.sku = product.sku;
                this.products.push(webProduct);
            });
            this.save();
        }
        if (this.hasNext === true) {
            this.setUrl();
            this.fetch(this.url);
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

    setUrl() {
        this.url = this.apiUrl + '?' +
            'group_variants=false&' +
            'store=au&' +
            ((this.department !== null) ? ("department=" + this.department + "&") : '') +
            ((this.filterFieldName !== null && this.filterFieldValue !== null) ? (this.filterFieldName + '=' + this.filterFieldValue + '&') : null) +
            'offset=' + this.offset;
    }

    setUpSlugs() {
        if (this.category.recursive_parent_category !== null) {
            let parentCategory = this.category.recursive_parent_category;
            if (parentCategory.recursive_parent_category !== null) {
                this.department = parentCategory.recursive_parent_category.slug;
                this.filterFieldName = parentCategory.field;
                if (parentCategory.field !== 'category' && parentCategory.field !== 'collection') {
                    this.filterFieldValue = encodeURIComponent(this.category.name);
                } else {
                    this.filterFieldValue = encodeURIComponent(this.category.slug);
                }
            } else {
                this.available = false;
                return false;
            }
        } else {
            this.department = this.category.slug;
        }
    }
}

module.exports = Scraper;