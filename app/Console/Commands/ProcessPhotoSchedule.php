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
        $this->info('=================================');
        $this->info('Processing scheduled photos...');
        $this->info('Current time: ' . now());
        $this->info('=================================');

        // Debug: Cek semua foto yang punya jadwal
        $allScheduled = DataFoto::whereNotNull('scheduled_publish_at')
            ->orWhereNotNull('scheduled_unpublish_at')
            ->get();
        
        $this->info("Total photos with schedule: {$allScheduled->count()}");
        
        foreach ($allScheduled as $foto) {
            $this->line("  - {$foto->judul}:");
            $this->line("    Scheduled Publish: {$foto->scheduled_publish_at}");
            $this->line("    Scheduled Unpublish: {$foto->scheduled_unpublish_at}");
            $this->line("    Status: {$foto->schedule_status}");
            $this->line("    Publish: {$foto->publish}");
        }
        
        $this->info('=================================');

        // Proses foto yang dijadwalkan untuk publish
        $toPublish = DataFoto::where('scheduled_publish_at', '<=', now())
            ->where('schedule_status', 'pending')
            ->where(function($q) {
                $q->where('publish', 0)
                  ->orWhere('publish', '0')
                  ->orWhereNull('publish');
            })
            ->get();
        
        $this->info("Found {$toPublish->count()} photos to PUBLISH");
        
        foreach ($toPublish as $foto) {
            try {
                $foto->update([
                    'publish' => 1,
                    'schedule_status' => 'published',
                ]);
                $this->info("✓ Published: {$foto->judul} (ID: {$foto->id})");
            } catch (\Exception $e) {
                $this->error("✗ Failed to publish {$foto->judul}: {$e->getMessage()}");
            }
        }

        $this->info('=================================');

        // Proses foto yang dijadwalkan untuk unpublish
        $toUnpublish = DataFoto::where('scheduled_unpublish_at', '<=', now())
            ->where('schedule_status', 'published')
            ->where(function($q) {
                $q->where('publish', 1)
                  ->orWhere('publish', '1');
            })
            ->get();
        
        $this->info("Found {$toUnpublish->count()} photos to UNPUBLISH");
        
        foreach ($toUnpublish as $foto) {
            try {
                $foto->update([
                    'publish' => 0,
                    'schedule_status' => 'unpublished',
                ]);
                $this->info("✓ Unpublished: {$foto->judul} (ID: {$foto->id})");
            } catch (\Exception $e) {
                $this->error("✗ Failed to unpublish {$foto->judul}: {$e->getMessage()}");
            }
        }

        $this->info('=================================');
        $this->info("Summary:");
        $this->info("  Published: {$toPublish->count()}");
        $this->info("  Unpublished: {$toUnpublish->count()}");
        $this->info('=================================');
        
        return Command::SUCCESS;
    }
}