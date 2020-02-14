<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Avatar as NovaAvatar;
use OptimistDigital\NovaPageManager\ResolveResponseCallback;

class Avatar extends NovaAvatar {
    use ResolveResponseCallback;
}
