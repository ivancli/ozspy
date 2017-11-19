<?php

namespace OzSpy\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use OzSpy\Console\Commands\Crawl\WebCategory as CrawlWebCategory;
use OzSpy\Console\Commands\Crawl\Proxy as CrawlProxy;
use OzSpy\Console\Commands\Clean\Proxy as CleanProxy;

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
        CrawlWebCategory::class
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
        $this->__scheduleCrawlWebCategory();
    }

    /**
     * schedule crawl:proxy command
     * @return void
     */
    private function __scheduleCrawlProxy()
    {
        $this->schedule->command('crawl:proxy')
            ->daily();
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

    private function __scheduleCrawlWebCategory()
    {
        $this->schedule->command('crawl:web-category')
            ->twiceDaily();
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
