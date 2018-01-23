var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        // this.product = argvs.product;
        // this.retailer = argvs.retailer;
        this.images = [];
        // this.file = __dirname + '/../../storage/products/' + this.product.id + '.json';

        // if (fs.existsSync(this.file)) {
        //     fs.unlink(this.file, err => {
        //         console.log(err);
        //     });
        // }
    }

    scrape() {
        let $this = this;
        request('http://www.kitchenwarehouse.com.au/Cuisinart-Power-Advantage-PLUS-Hand-Mixer-Silver', (error, response, content) => {
            let $ = cheerio.load(content);

            let imageUrls = [];
            $("#pdp-carousel .carousel-inner .img-responsive").each(function () {
                if ($(this).attr("src")) {
                    imageUrls.push($(this).attr("src"));
                }
            });
            imageUrls.forEach(imageUrl => {
                 this.getImageContent(imageUrl);
            })
            console.log(this.images);
        })
    }

    getImageContent(url) {
        let $this = this;
        return new Promise(function (resolve, reject) {
            request(url, function (error, res, body) {
                console.log("called");
                if (!error && res.statusCode === 200) {
                    let data = "data:" + res.headers["content-type"] + ";base64," + new Buffer(body).toString('base64');
                    $this.images.push({
                        url: url,
                        data: data,
                    });
                } else {

                }
            });
        });
    }
}

module.exports = Scraper;