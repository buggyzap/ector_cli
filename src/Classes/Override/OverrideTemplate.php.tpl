<?php

require_once _PS_MODULE_DIR_ . 'ector_core/vendor/autoload.php';

class {{className}}Override extends {{className}}
{

    use Ector\Core\EctorOverride;

    {{overrideMethods}}

}