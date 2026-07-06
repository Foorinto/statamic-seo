<?php

namespace Foorintodev\Seo\Fieldtypes;

use Foorintodev\Seo\Concerns\PreloadsSeoConfig;
use Statamic\Fields\Fieldtype;

class SeoSocialPreview extends Fieldtype
{
    use PreloadsSeoConfig;

    protected $icon = 'social';
}
