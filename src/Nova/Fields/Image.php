<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Image as NovaImage;
use OptimistDigital\NovaPageManager\ResolveResponseCallback;

class Image extends NovaImage {
    use ResolveResponseCallback;
}
