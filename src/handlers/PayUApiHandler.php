<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\payu\handlers;

use dlds\payu\PayUOrderSourceInterface;
use dlds\payu\PayUOrderInterface;

/**
 * This is the main class of the dlds\mlm component that should be registered as an application component.
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package mlm
 */
class PayUApiHandler {

    private static $_instance = null;

    /**
     * @var string given POS ID
     */
    protected $posId;

    /**
     * @var string given POS AUTH KEY
     */
    protected $posAuthKey;

    /**
     * Private constructor to ensure thas handler would be singleton
     * @param string $posId
     * @param string $posAuthKey
     */
    private function __construct($posId, $posAuthKey)
    {
        $this->posId = $posId;
        $this->posAuthKey = $posAuthKey;
    }

    /**
     * Retrieves singleton instance of PayUApiHandler
     * @param string $posId
     * @param string $posAuthKey
     * @return PayUApiHandler instance
     */
    public static function instance($posId, $posAuthKey)
    {
        if (!self::$_instance)
        {
            self::$_instance = new self($posId, $posAuthKey);
        }

        return self::$_instance;
    }
}