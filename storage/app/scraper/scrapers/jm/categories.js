var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');

class Scraper {
    constructor(argvs) {
        this.retailer = argvs.retailer;
        this.url = 'https://www.joycemayne.com.au/sitemap';
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
        $('#sitemap a').each(function () {
            let category = {};
            category.name = $(this).text();
            category.url = $(this).attr('href');
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