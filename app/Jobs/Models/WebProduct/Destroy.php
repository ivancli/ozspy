<?php

namespace OzSpy\Jobs\Models\WebProduct;

use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Models\Base\WebProduct;

class Destroy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebProduct
     */
    protected $webProduct;

    /**
     * Create a new job instance.
     *
     * @param WebProduct $webProduct
     */
    public function __construct(WebProduct $webProduct)
    {
        $this->webProduct = $webProduct;

    }

    /**
     * Execute the job.
     *
     * @param WebProductContract $webProductRepo
     * @return void
     */
    public function handle(WebProductContract $webProductRepo)
    {
        $webProductRepo->delete($this->webProduct);
    }
}
