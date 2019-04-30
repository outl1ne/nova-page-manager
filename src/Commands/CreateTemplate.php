<?php

namespace OptimistDigital\NovaPageManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateTemplate extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     **/
    protected $files;

    protected $typeOptions = ['page', 'region'];

    protected $signature = 'pagemanager:template {className?} {name?} {type?}';

    protected $description = 'Creates a new Template file and its boilerplate.';

    protected $className;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $this->className = $this->getClassNameArgument();
        $this->type = $this->getTypeArgument();
        $this->name = $this->getNameArgument();
        $path = $this->getPath();
        $this->files->put($path, $this->buildClass());
        $this->info('Successfully created Template at ' . $path);
    }

    /**
     * Gets the class name argument - if missing, asks the user to enter it.
     *
     * @return string
     **/
    public function getClassNameArgument()
    {
        if (!$this->argument('className')) {
            return $this->ask('Please enter a name for the Template class');
        }
        return $this->argument('className');
    }

    /**
     * Gets the name argument - if missing, asks the user to enter it.
     *
     * @return string
     **/
    public function getNameArgument()
    {
        if (!$this->argument('name')) {
            return $this->ask('Please enter a name for the Template (ie about-page)');
        }
        return $this->argument('name');
    }

    /**
     * Gets the name argument - if missing, asks the user to enter it.
     *
     * @return string
     **/
    public function getTypeArgument()
    {
        if (!$this->argument('type') || !in_array($this->argument('type'), $this->typeOptions)) {
            return $this->choice('Please choose a type for the Template', $this->typeOptions);
        }
        return $this->argument('type');
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

        $templateFilePath = ($this->type === 'page')
            ? __DIR__ . '/../Stubs/PageTemplate.php'
            : __DIR__ . '/../Stubs/RegionTemplate.php';

        $template = $this->files->get($templateFilePath);

        foreach ($replace as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }
}
