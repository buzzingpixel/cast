<?php

declare(strict_types=1);

use BuzzingPixel\Cast\Cast\Constants;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

class Cast_ft extends EE_Fieldtype
{
    /** @var mixed[] */
    public $info = [
        'name' => Constants::NAME,
        'version' => Constants::VERSION,
    ];

    /**
     * @param mixed $data
     */
    public function display_field($data) : string
    {
        // TODO: Implement display_field() method.
        return 'TODO: Implement display_field() method.';
    }
}
