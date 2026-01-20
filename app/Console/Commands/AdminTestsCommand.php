<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class AdminTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:tests {--filter= : Filter tests by name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Admin module feature tests with clear pass/fail status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Running Admin API Tests...');
        $this->newLine();

        $filter = $this->option('filter') ?? 'Admin';

        // Build PHPUnit command
        $command = [
            'php',
            'artisan',
            'test',
            '--testsuite=Feature',
            "--filter={$filter}",
            '--colors',
        ];

        $process = new Process($command);
        $process->setTimeout(300); // 5 minute timeout
        $process->setWorkingDirectory(base_path());

        try {
            $process->mustRun(function ($type, $buffer) {
                $this->output->write($buffer);
            });

            // If we reach here, tests passed
            $this->newLine(2);
            $this->info('╔═══════════════════════════════════════════════════╗');
            $this->info('║                                                   ║');
            $this->info('║   ✅ ADMIN API TESTS PASSED — EVERYTHING OK       ║');
            $this->info('║                                                   ║');
            $this->info('╚═══════════════════════════════════════════════════╝');
            $this->newLine();

            return self::SUCCESS;
        } catch (\Exception $e) {
            // Tests failed
            $this->newLine(2);
            $this->error('╔═══════════════════════════════════════════════════╗');
            $this->error('║                                                   ║');
            $this->error('║   ❌ ADMIN API TESTS FAILED — CHECK FAILURES ABOVE ║');
            $this->error('║                                                   ║');
            $this->error('╚═══════════════════════════════════════════════════╝');
            $this->newLine();

            return self::FAILURE;
        }
    }
}
