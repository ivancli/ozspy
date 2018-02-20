var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');
var $ = null;

class Scraper {
    constructor(argvs) {
        this.retailer = argvs.retailer;
        this.url = 'https://www.appliancewarehouse.com.au/sitemap';
        this.categories = [];
        this.file = __dirname + '/../../storage/categories/' + this.retailer.id + '.json';

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
        $ = cheerio.load(html);
        let $this = this;
        $('.xsitemap-categories > .categories > .category').each(function () {
            let category = {
                name: $(this).find('> a').text(),
                url: $(this).find('> a').attr('href'),
                categories: [],
            };
            if ($(this).find(' > ul.categories').length > 0) {
                category.categories = $this.parseRecursiveCategories(this);
            }
            $this.categories.push(category);
        });

        this.save();
    }

    parseRecursiveCategories(el) {
        let categories = [];
        let $this = this;
        $(el).find(' > ul.categories > .category').each(function () {
            let category = {
                name: $(this).find('> a').text(),
                url: $(this).find('> a').attr('href'),
                categories: [],
            };
            if ($(this).find(' > ul.categories').length > 0) {
                category.categories = $this.parseRecursiveCategories(this);
            }
            categories.push(category);
        });
        return categories;
    }

    save() {
        let object = {
            retailer_id: this.retailer.id,
            scraped_at: moment().format(),
            categories: this.categories
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