<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand;
use Symfony\Component\Process\Process;

class ServeWithReverbCommand extends ServeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->components->info('Starting Reverb WebSocket server on port ' . env('REVERB_PORT', 8090) . '...');

        // Start Reverb server asynchronously
        $reverbProcess = new Process(['php', 'artisan', 'reverb:start', '--debug']);
        $reverbProcess->setTimeout(null);
        $reverbProcess->start(function ($type, $buffer) {
            // Optional: output Reverb output if needed for debugging
            if (Process::ERR === $type) {
                $this->line('<fg=red>Reverb Error: ' . trim($buffer) . '</>');
            } else {
                $this->line('<fg=gray>Reverb: ' . trim($buffer) . '</>');
            }
        });

        // Ensure Reverb stops when serve exits
        register_shutdown_function(function () use ($reverbProcess) {
            $reverbProcess->stop();
        });

        // Run the original Laravel serve command (this blocks)
        try {
            return parent::handle();
        } finally {
            $reverbProcess->stop();
        }
    }
}
