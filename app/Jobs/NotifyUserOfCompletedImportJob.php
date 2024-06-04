<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ImportCompletedNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserOfCompletedImportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected string $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new ImportCompletedNotification($this->user));
        $this->appendToChain(
            new DeleteFileJob($this->filePath)
        );
    }
}
