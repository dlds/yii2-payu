<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\payu\handlers;

use dlds\payu\interfaces\PayUOrderSourceInterface;
use dlds\payu\interfaces\PayUOrderInterface;

/**
 * This is the main class of the dlds\mlm component that should be registered as an application component.
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package mlm
 */
class PayUOrderHandler {

    /**
     * Inits and saves given PayUOrderInterface tepmplate
     * @param PayUOrderSourceInterface $source given source
     * @param PayUOrderInterface $template given template
     */
    public static function createFromSource(PayUOrderSourceInterface $source, PayUOrderInterface $template)
    {
        
    }
}