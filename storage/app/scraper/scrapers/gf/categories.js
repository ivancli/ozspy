var fs = require('fs');
var request = require('request');
var jsonfile = require('jsonfile');
var moment = require('moment');
var convert = require('xml-to-json-promise');


class Scraper {
    constructor(argvs) {
        this.retailer = argvs.retailer;
        this.url = 'https://www.godfreys.com.au/sitemap.xml';
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
        convert.xmlDataToJSON(html).then(json => {
            if (typeof json.urlset.url === 'object') {
                let urls = json.urlset.url.filter(url => url.priority == '0.5');
                urls = urls.map(url => url.loc[0]);
                urls.forEach(url => {
                    if (url.toLowerCase().indexOf('store-locator') === -1 && url.toLowerCase().indexOf('service-repairs') === -1) {
                        let paths = this.stripUrl(url);
                        this.generateCategory(paths, this.categories, url);
                    }
                });
                this.save();
            }
        });
    }

    generateCategory(paths, attr, url) {
        if (paths.length > 0) {
            let path = paths[0];
            let existingCategories = attr.filter(category => category.slug === path);
            let existingCategory = null;
            if (existingCategories.length > 0) {
                existingCategory = existingCategories[0];
            } else {
                existingCategory = {};
                existingCategory.name = this.toTitleCase(path.replace((new RegExp('-', 'g')), ' '));
                existingCategory.slug = path;
                if (paths.length === 1) {
                    existingCategory.url = url;
                } else {
                    existingCategory.url = null;
                }
                existingCategory.active = paths.length === 1;
                existingCategory.categories = [];
                attr.push(existingCategory);
            }
            paths.splice(0, 1);
            this.generateCategory(paths, existingCategory.categories, url);
        }
    }

    stripUrl(url) {
        return url.substr(url.indexOf(this.retailer.domain) + this.retailer.domain.length).split('/').filter(result => result !== "");
    }

    toTitleCase(str) {
        return str.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
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
    }
}

module.exports = Scraper;