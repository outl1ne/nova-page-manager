<?php

namespace Outl1ne\PageManager\Commands;

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

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $this->className = $this->getClassName();
        $path = $this->getPath();
        $this->files->put($path, $this->buildClass());
        $this->info('Successfully created template at ' . $path);
    }

    /**
     * Gets the class name argument - if missing, asks the user to enter it.
     *
     * @return string
     **/
    public function getClassName()
    {
        if (!$this->argument('className')) {
            return $this->ask('Please enter a name for the template class (ie HomePageTemplate)');
        }
        return $this->argument('className');
    }

    /**
     * Creates the directory for the template files and returns the file path.
     *
     * @return string
     **/
    protected function getPath()
    {
        return $this->makeDirectory(
            app_path('Nova/Templates/' . $this->className . '.php')
        );
    }

    /**
     * Creates the directory for the template file.
     *
     * @param string $path Expected path of the Template file.
     * @return string
     **/
    protected function makeDirectory($path)
    {
        $directory = dirname($path);
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true, true);
        }
        return $path;
    }

    /**
     * Create the template file content.
     *
     * @return string
     */
    protected function buildClass()
    {
        $replace = [
            ':className' => $this->className,
            ':name' => $this->name,
            ':type' => $this->type,
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
