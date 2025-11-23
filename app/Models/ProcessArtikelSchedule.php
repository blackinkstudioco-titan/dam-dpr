<?php

namespace App\Console\Commands;

use App\Models\ArtikelPublish;
use Illuminate\Console\Command;

class ProcessArtikelSchedule extends Command
{
    protected $signature = 'artikel:process-schedule';
    protected $description = 'Process scheduled artikel publishing and unpublishing';

    public function handle()
    {
        $this->info('=================================');
        $this->info('Processing scheduled artikel...');
        $this->info('Current time: ' . now());
        $this->info('Timezone: ' . config('app.timezone'));
        $this->info('=================================');

        // Debug: Cek semua artikel yang punya jadwal
        $allScheduled = ArtikelPublish::where(function($q) {
            $q->whereNotNull('scheduled_publish_at')
              ->orWhereNotNull('scheduled_unpublish_at');
        })->get();
        
        $this->info("Total artikel with schedule: {$allScheduled->count()}");
        
        foreach ($allScheduled as $artikel) {
            $this->line("  - {$artikel->judul}:");
            $this->line("    Scheduled Publish: {$artikel->scheduled_publish_at}");
            $this->line("    Scheduled Unpublish: {$artikel->scheduled_unpublish_at}");
            $this->line("    Status: {$artikel->schedule_status}");
            $this->line("    Active: {$artikel->active}");
        }
        
        $this->info('=================================');

        // Proses artikel yang dijadwalkan untuk publish
        $toPublish = ArtikelPublish::where('scheduled_publish_at', '<=', now())
            ->where('schedule_status', 'pending')
            ->where(function($q) {
                $q->where('active', 0)
                  ->orWhere('active', '0')
                  ->orWhereNull('active');
            })
            ->get();
        
        $this->info("Found {$toPublish->count()} artikel to PUBLISH");
        
        foreach ($toPublish as $artikel) {
            try {
                $artikel->update([
                    'active' => 1,
                    'schedule_status' => 'published',
                ]);
                $this->info("✓ Published: {$artikel->judul} (ID: {$artikel->id})");
            } catch (\Exception $e) {
                $this->error("✗ Failed to publish {$artikel->judul}: {$e->getMessage()}");
            }
        }

        $this->info('=================================');

        // Proses artikel yang dijadwalkan untuk unpublish
        $toUnpublish = ArtikelPublish::where('scheduled_unpublish_at', '<=', now())
            ->where('schedule_status', 'published')
            ->where(function($q) {
                $q->where('active', 1)
                  ->orWhere('active', '1');
            })
            ->get();
        
        $this->info("Found {$toUnpublish->count()} artikel to UNPUBLISH");
        
        foreach ($toUnpublish as $artikel) {
            try {
                $artikel->update([
                    'active' => 0,
                    'schedule_status' => 'unpublished',
                ]);
                $this->info("✓ Unpublished: {$artikel->judul} (ID: {$artikel->id})");
            } catch (\Exception $e) {
                $this->error("✗ Failed to unpublish {$artikel->judul}: {$e->getMessage()}");
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