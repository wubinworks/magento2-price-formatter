<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Framework\Component\ComponentRegistrar;

(static function () {
    $moduleName = 'Wubinworks_PriceFormatter';
    if (!class_exists('\Wubinworks\Base\Console\Command\HashModuleCommand')
            || \Wubinworks\Base\Console\Command\HashModuleCommand::check(__DIR__)
    ) {
        ComponentRegistrar::register(ComponentRegistrar::MODULE, $moduleName, __DIR__);
    }
})();
