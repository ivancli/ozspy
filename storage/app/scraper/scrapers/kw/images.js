var fs = require('fs');
var request = require('request');
var cheerio = require('cheerio');
var jsonfile = require('jsonfile');
var moment = require('moment');


class Scraper {
    constructor(argvs) {
        this.product = argvs.product;
        this.retailer = argvs.retailer;
        this.images = [];
        this.file = __dirname + '/../../storage/images/' + this.product.id + '.json';

        if (fs.existsSync(this.file)) {
            fs.unlink(this.file, err => {
                console.log(err);
            });
        }
        this.imageUrls = [];
    }

    scrape() {
        let $this = this;
        request(this.product.url, (error, response, content) => {
            let $ = cheerio.load(content);

            $("#pdp-carousel .carousel-inner .img-responsive").each(function () {
                if ($(this).attr("src")) {
                    $this.imageUrls.push($(this).attr("src"));
                }
            });
            if ($this.imageUrls.length > 0) {
                this.getImageContent($this.imageUrls[0]);
            }
        })
    }

    getImageContent(url) {
        let $this = this;
        return new Promise(function (resolve, reject) {
            let options = {
                url: url,
                encoding: null,
            };
            request(options, function (error, res, body) {
                if (!error && res.statusCode === 200) {
                    let data = "data:" + res.headers["content-type"] + ";base64," + new Buffer(body).toString('base64');
                    $this.images.push({
                        url: url,
                        data: data,
                    });
                }
                $this.imageUrls.splice($this.imageUrls.indexOf(url), 1);
                if ($this.imageUrls.length > 0) {
                    $this.getImageContent($this.imageUrls[0]);
                } else {
                    $this.save();
                }
            });
        });
    }

    save() {
        let object = {
            product_id: this.product.id,
            scraped_at: moment().format(),
            images: this.images
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