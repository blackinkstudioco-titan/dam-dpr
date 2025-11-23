<?php

namespace App\Console\Commands;

use App\Models\DataFoto;
use Illuminate\Console\Command;

class ProcessPhotoSchedule extends Command
{
    protected $signature = 'photos:process-schedule';
    protected $description = 'Process scheduled photo publishing and unpublishing';

    public function handle()
    {
        $this->info('Processing scheduled photos...');

        // Proses foto yang dijadwalkan untuk publish
        $toPublish = DataFoto::scheduledToPublish()->get();
        
        foreach ($toPublish as $foto) {
            $foto->update([
                'publish' => 1,
                'schedule_status' => 'published',
            ]);
            $this->info("Published: {$foto->judul} (ID: {$foto->id})");
        }

        // Proses foto yang dijadwalkan untuk unpublish
        $toUnpublish = DataFoto::scheduledToUnpublish()->get();
        
        foreach ($toUnpublish as $foto) {
            $foto->update([
                'publish' => 0,
                'schedule_status' => 'unpublished',
            ]);
            $this->info("Unpublished: {$foto->judul} (ID: {$foto->id})");
        }

        $this->info("Processed {$toPublish->count()} publish and {$toUnpublish->count()} unpublish schedules.");
        
        return Command::SUCCESS;
    }
}