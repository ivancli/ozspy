let fs = require('fs');
let request = require('request');
let jsonfile = require('jsonfile');
let moment = require('moment');
let cheerio = require('cheerio');


class Scraper {
    constructor(argvs) {
        this.retailer = argvs.retailer;
        this.url = this.retailer.domain;
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
        let $this = this;
        let $ = cheerio.load(html);
        $("li.redesign").each(function () {
            let categoryName = $(this).find('a.mainsub-menu-link').text();
            let category = {
                name: categoryName.replace(new RegExp('\\t', 'g'), '').replace(new RegExp('\\n', 'g'), '').trim(),
                url: $this.retailer.domain + $(this).find('a.mainsub-menu-link').attr('href'),
                categories: [],
            };
            $(this).find('div.submenu-box').each(function () {
                $(this).find('span.sub-menu-title a, div a').each(function () {
                    let subCategory = {
                        name: $(this).text().replace(new RegExp('\\t', 'g'), '').replace(new RegExp('\\n', 'g'), '').trim(),
                        url: $this.retailer.domain + $(this).attr('href'),
                    };
                    category.categories.push(subCategory);
                });
            });
            $this.categories.push(category);
        });
        $this.save();
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