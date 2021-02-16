<?php

namespace Oza75\MakeRepository\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name : The name of the repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $this->createBaseDirectory();
        $this->createRepositoryDirectory($name);
        $this->createInterface($name);
        $this->createClass($name);

        $this->info("$name repository created");

        return 0;
    }

    private function createBaseDirectory()
    {
        if (!File::exists(app_path('Repositories'))) {
            File::makeDirectory(app_path('Repositories'));
            File::put(app_path('Repositories/BaseRepository.php'), $this->content('base.stub'));
        }
    }

    /**
     * @param string $name
     * @throws Exception
     */
    private function createRepositoryDirectory(string $name)
    {
        if (File::exists(app_path("Repositories/$name"))) {
            throw new Exception("$name repository already exists");
        }

        File::makeDirectory(app_path("Repositories/$name"));
    }

    /**
     * @param string $path
     * @return string|null
     * @throws FileNotFoundException
     */
    private function content(string $path): ?string
    {
        return File::get(realpath(__DIR__."/../../stubs/{$path}"));
    }

    /**
     * @param string $name
     * @throws FileNotFoundException
     * @throws Exception
     */
    private function createInterface(string $name)
    {
        $path = "Repositories/{$name}/{$name}Repository.php";
        if (File::exists(app_path($path))) {
            throw new Exception("$path file already exists");
        }

        $content = str_replace("{{name}}", $name, $this->content('interface.stub'));

        File::put(app_path($path), $content);
    }

    /**
     * @param string $name
     * @throws FileNotFoundException
     * @throws Exception
     */
    private function createClass(string $name)
    {
        $path = "Repositories/{$name}/Default{$name}Repository.php";
        if (File::exists(app_path($path))) {
            throw new Exception("$path file already exists");
        }

        $content = str_replace("{{name}}", $name, $this->content('repository.stub'));

        File::put(app_path($path), $content);
    }
}
