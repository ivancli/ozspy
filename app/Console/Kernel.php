<?php

namespace OzSpy\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use OzSpy\Console\Commands\Crawl\Proxy as CrawlProxy;
use OzSpy\Console\Commands\Clean\Proxy as CleanProxy;
use OzSpy\Console\Commands\Scrape\WebCategory as ScrapeWebCategory;
use OzSpy\Console\Commands\Scrape\WebProduct as ScrapeWebProduct;
use OzSpy\Console\Commands\Update\WebCategory as UpdateWebCategory;
use OzSpy\Console\Commands\Update\WebProduct as UpdateWebProduct;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CrawlProxy::class,
        CleanProxy::class,
        ScrapeWebCategory::class,
        ScrapeWebProduct::class,
        UpdateWebCategory::class,
        UpdateWebProduct::class,
    ];

    /**
     * @var Schedule
     */
    protected $schedule;

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->schedule = $schedule;

        $this->__scheduleCrawlProxy();
        $this->__scheduleCleanProxy();
        $this->__scheduleScrapeWebCategory();
        $this->__scheduleScrapeWebProduct();
        $this->__scheduleUpdateWebCategory();
        $this->__scheduleUpdateWebProduct();
    }

    /**
     * schedule crawl:proxy command
     * @return void
     */
    private function __scheduleCrawlProxy()
    {
        $this->schedule->command('crawl:proxy')
            ->everyMinute();
    }

    /**
     * schedule clean:proxy command
     * @return void
     */
    private function __scheduleCleanProxy()
    {
        $this->schedule->command('clean:proxy')
            ->everyMinute();
    }

    private function __scheduleScrapeWebCategory()
    {
        $this->schedule->command('scrape:web-category --active')
            ->twiceDaily(2, 14);
    }

    private function __scheduleScrapeWebProduct()
    {
        $this->schedule->command('scrape:web-product --active')
            ->dailyAt("3:00");
    }

    private function __scheduleUpdateWebCategory()
    {
        $this->schedule->command('update:web-category --active')
            ->twiceDaily(4, 16);
    }

    private function __scheduleUpdateWebProduct()
    {
        $this->schedule->command('update:web-product --active')
            ->dailyAt("5:00");
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
