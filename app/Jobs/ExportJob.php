<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected object $exporter;
    protected string $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(object $exporter, string $filePath)
    {
        $this->exporter = $exporter;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->exporter->store($this->filePath);
    }

    public function fail($exception = null)
    {

    }
}
