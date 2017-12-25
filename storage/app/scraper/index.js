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

if (argvs.category) {
    argvs.category = JSON.parse(argvs.category);
}

if (!argvs.retailer) {
    throw new Error('Retailer not found.');
}

argvs.retailer = JSON.parse(argvs.retailer);

let Scraper = require('./scrapers/' + argvs.retailer.abbreviation + '/' + argvs.scraper);

let scraper = new Scraper(argvs);
scraper.scrape();