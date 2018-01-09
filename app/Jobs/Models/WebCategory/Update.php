<?php

namespace OzSpy\Jobs\Models\WebCategory;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Models\Base\WebCategory;

class Update implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebCategory
     */
    protected $webCategory;

    /**
     * @var array
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param WebCategory $webCategory
     * @param array $data
     */
    public function __construct(WebCategory $webCategory, array $data)
    {
        $this->webCategory = $webCategory;

        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->webCategory->update($this->__getData($this->data));
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webCategory->getFillable());
    }
}
