<?php

namespace OzSpy\Jobs\Models\WebCategory;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Models\Base\WebCategory;

class Destroy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebCategory
     */
    protected $webCategory;

    /**
     * Create a new job instance.
     *
     * @param WebCategory $webCategory
     */
    public function __construct(WebCategory $webCategory)
    {
        $this->webCategory = $webCategory;
    }

    /**
     * Execute the job.
     *
     * @param WebCategoryContract $webCategoryRepo
     * @return void
     */
    public function handle(WebCategoryContract $webCategoryRepo)
    {
        $webCategoryRepo->delete($this->webCategory);
    }
}
