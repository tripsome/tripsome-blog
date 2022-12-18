<?php

namespace Tripsome\Blog\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Blog resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Blog Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'blog-assets']);

        $this->comment('Publishing Blog Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'blog-config']);

        $this->info('Blog was installed successfully.');
    }
}
