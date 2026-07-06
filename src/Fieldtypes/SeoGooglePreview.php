<?php

namespace Foorintodev\Seo\Fieldtypes;

use Foorintodev\Seo\Concerns\PreloadsSeoConfig;
use Statamic\Fields\Fieldtype;

class SeoGooglePreview extends Fieldtype
{
    use PreloadsSeoConfig;

    protected $icon = 'seo';
}
