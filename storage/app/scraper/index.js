let argvs = {};

process.argv.forEach((argv) => {
    if (argv.indexOf('=') > -1) {
        let element = argv.split('=');
        if (element.length === 2) {
            let key = element[0].replace('--', '');
            argvs[key] = element[1];
        }
    }
});

if (!argvs.scraper) {
    argvs.scraper = 'categories';
}

if (!argvs.id) {
    throw new Error('Please provide target ID for output purpose.');
}

if (argvs.scraper === 'products' && !argvs.url) {
    throw new Error('Please provide URL for crawling purpose.');
}

if (!argvs.retailer) {
    throw new Error('Please provide retailer for crawling purpose.');
}

let Scraper = require('./scrapers/' + argvs.retailer + '/' + argvs.scraper);

let scraper = new Scraper(argvs.id, argvs.url);
scraper.scrape();