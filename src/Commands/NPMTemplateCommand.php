<?php

namespace Outl1ne\PageManager\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Outl1ne\PageManager\Template;
use Illuminate\Filesystem\Filesystem;

class NPMTemplateCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     **/
    protected $files;

    protected $typeOptions = [Template::TYPE_PAGE, Template::TYPE_REGION];

    protected $signature = 'npm:template {className?}';

    protected $description = 'Creates a new page or region template file.';

    protected $className;
    protected $type;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $this->className = $this->getClassName();
        $this->type = $this->getType();
        $path = $this->getPath();
        $this->files->put($path, $this->buildClass());
        $this->info('Successfully created template at ' . $path);
    }

    public function getClassName()
    {
        if (!$this->argument('className')) {
            return $this->ask('Please enter a name for the template class (ie HomePageTemplate)');
        }
        return $this->argument('className');
    }

    public function getType()
    {
        $default = Str::contains($this->className, 'region', true) ? 1 : 0;
        return $this->choice('Choose a type', $this->typeOptions, $default);
    }

    protected function getPath()
    {
        return $this->makeDirectory(
            app_path('Nova/Templates/' . $this->className . '.php')
        );
    }

    protected function makeDirectory($path)
    {
        $directory = dirname($path);
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true, true);
        }
        return $path;
    }

    protected function buildClass()
    {
        $replace = [
            ':className' => $this->className,
        ];

        $templateFilePath = ($this->type === Template::TYPE_PAGE)
            ? __DIR__ . '/../Stubs/PageTemplate.php'
            : __DIR__ . '/../Stubs/RegionTemplate.php';

        $template = $this->files->get($templateFilePath);

        foreach ($replace as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }
}
