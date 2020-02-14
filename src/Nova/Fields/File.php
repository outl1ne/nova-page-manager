<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\File as NovaFile;
use OptimistDigital\NovaPageManager\ResolveResponseCallback;

class File extends NovaFile {
    use ResolveResponseCallback;
}
