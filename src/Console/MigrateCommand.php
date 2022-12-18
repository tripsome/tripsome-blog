<?php

namespace Tripsome\Blog\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tripsome\Blog\BlogAuthor;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:migrate {email?} {password?}
                {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migrations for Blog';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $shouldCreateNewAuthor =
            ! Schema::connection(config('blog.database_connection'))->hasTable('blog_authors') ||
            ! BlogAuthor::count();

        $this->call('migrate', [
            '--database' => config('blog.database_connection'),
            '--path' => 'vendor/themsaid/blog/src/Migrations',
            '--force' => $this->option('force') ?? true,
        ]);

        if ($shouldCreateNewAuthor) {
            $email = ! $this->argument('email') ? 'admin@mail.com' : $this->argument('email');
            $password = ! $this->argument('password') ? Str::random() : $this->argument('password');

            BlogAuthor::create([
                'id' => (string) Str::uuid(),
                'name' => 'Regina Phalange',
                'slug' => 'regina-phalange',
                'bio' => 'This is me.',
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->line('');
            $this->line('');
            $this->line('Blog is ready for use. Enjoy!');
            $this->line('You may log in using <info>'.$email.'</info> and password: <info>'.$password.'</info>');
        }
    }
}