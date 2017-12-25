var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.retailer = argvs.retailer;
        this.url = 'https://www.kogan.com/au/';
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
        let matchingCategories = /JSON.parse\(\'(.*?)\'\);/.exec(html);
        if (matchingCategories[1]) {
            matchingCategories = matchingCategories[1];
            let categories = JSON.parse('"' + matchingCategories + '"');
            categories = JSON.parse(categories);
            if (categories.departments) {
                categories.departments.forEach(department => {
                    let category = {};
                    category.name = cheerio.load(department.title).text();
                    category.slug = department.slug;
                    category.url = this.retailer.domain + department.href;

                    category.categories = [];
                    if (department.categories) {
                        department.categories.forEach(childCategory => {
                            let newChildCategory = {};
                            newChildCategory.name = cheerio.load(childCategory.title).text();
                            newChildCategory.field = childCategory.field;
                            newChildCategory.categories = [];

                            if (childCategory.items) {
                                childCategory.items.forEach(item => {
                                    let newItem = {};
                                    newItem.name = cheerio.load(item.title).text();
                                    newItem.url = this.retailer.domain + item.href;
                                    if (childCategory.field === 'category') {
                                        let parts = item.href.split("/").filter(linkPart => linkPart !== "");
                                        newItem.slug = parts[parts.length - 1];
                                    }
                                    newChildCategory.categories.push(newItem);
                                });
                            }
                            category.categories.push(newChildCategory);
                        })
                    }
                    this.categories.push(category);
                });
            }
        }
        this.save();
    }

    save() {
        let categories = this.categories;
        let object = {
            retailer_id: this.retailer.id,
            scraped_at: moment().format(),
            categories: categories
        };

        jsonfile.writeFileSync(this.file, object);

        if (global.gc) {
            global.gc();
        } else {
            console.log('Garbage collection unavailable.  Pass --expose-gc when launching node to enable forced garbage collection.');
        }
    }
}

module.exports = Scraper;