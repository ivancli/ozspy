var request = require('request');
var cherrio = require('cheerio');
var jsonfile = require('jsonfile');


class Scraper {
    constructor(id) {
        this.id = id;
        this.url = 'https://www.joycemayne.com.au/sitemap';
        this.categories = [];
        this.file = __dirname + '/../../storage/categories/' + this.id + '.json';
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
        $('#sitemap a').each(function () {
            let category = {};
            category.name = $(this).text();
            category.url = $(this).attr('href');
            $this.categories.push(category);
        });
        this.save();
    }

    save() {
        jsonfile.readFile(this.file, (err, existingObject) => {
            let categories = this.categories;
            let category_urls = categories.map(category => category.url);

            if (typeof existingObject !== 'undefined') {
                let outstandingCategories = existingObject.categories.filter(function (category) {
                    return category_urls.indexOf(category.url) === -1;
                });
                categories = categories.concat(outstandingCategories);
            }

            let object = {
                retailer_id: this.id,
                categories: categories
            };

            jsonfile.writeFileSync(this.file, object);
        });
    }
}

module.exports = Scraper;