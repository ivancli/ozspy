var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');

class Scraper {
    constructor(argvs) {
        this.retailer = argvs.retailer;
        this.url = 'https://www.appliancecentral.com.au/index.php?route=information/sitemap';
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
        let $ = cheerio.load(html);
        let $this = this;
        $('.sitemap-info .left > ul > li').each(function () {
            let category = {};
            category.name = $(this).find('> a').text();
            category.url = $(this).find('> a').attr('href');
            category.categories = [];

            $(this).find('> ul > li').each(function () {
                let subCategory = {};
                subCategory.name = $(this).find('> a').text();
                subCategory.url = $(this).find('> a').attr('href');
                subCategory.categories = [];

                $(this).find('> ul > li').each(function () {
                    let subSubCategory = {};
                    subSubCategory.name = $(this).find('> a').text();
                    subSubCategory.url = $(this).find('> a').attr('href');
                    subSubCategory.categories = [];
                    subCategory.categories.push(subSubCategory);
                });

                category.categories.push(subCategory);
            });

            $this.categories.push(category);
        });
        this.save();
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