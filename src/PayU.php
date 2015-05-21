<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\payu;

use dlds\payu\PayUOrderSourceInterface;
use dlds\payu\PayUOrderInterface;

/**
 * This is the main class of the dlds\payu component
 * that should be registered as an application component.
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package payu
 * @see http://www.payu.cz/sites/czech/files/dock/payu_implementacni_manual_arp_sablona.pdf
 */
class PayU extends \yii\base\Component {

    /**
     * Payment statuses
     */
    const PAYMENT_STATUS_NEW = 1;
    const PAYMENT_STATUS_CANCELLED = 2;
    const PAYMENT_STATUS_REJECTED = 3;
    const PAYMENT_STATUS_STARTED = 4;
    const PAYMENT_STATUS_AWAITING = 5;
    const PAYMENT_STATUS_RETRIEVED = 7;
    const PAYMENT_STATUS_ENDED = 99;
    const PAYMENT_STATUS_UNKNOWN = 888;

    /**
     * Payment types
     */
    const PAYMENT_TYPE_CS = 'cs';
    const PAYMENT_TYPE_MBANK = 'mp';
    const PAYMENT_TYPE_KB = 'kb';
    const PAYMENT_TYPE_RF = 'rf';
    const PAYMENT_TYPE_GE = 'pg';
    const PAYMENT_TYPE_SB = 'pv';
    const PAYMENT_TYPE_FIO = 'pf';
    const PAYMENT_TYPE_ERA = 'era';
    const PAYMENT_TYPE_CSOB = 'cb';
    const PAYMENT_TYPE_PAYSEC = 'psc';
    const PAYMENT_TYPE_GPE = 'c';
    const PAYMENT_TYPE_MOBITO = 'mo';
    const PAYMENT_TYPE_BANKWIRE = 'bt';
    const PAYMENT_TYPE_POST = 'pt';
    const PAYMENT_TYPE_TEST = 't';

    /**
     * @var int ID of PayU point of sale
     */
    public $posId;

    /**
     * @var string authorization key for point of sale
     */
    public $posAuthKey;

    /**
     * Inits module
     */
    public function init()
    {
        if (!$this->posId)
        {
            throw new \yii\base\Exception('Post ID parameter is not set');
        }

        if (!$this->posAuthKey)
        {
            throw new \yii\base\Exception('PosAuthKey parameter is not set');
        }
    }

    /**
     * Creates new payment order through PayU API
     */
    public function createOrder(PayUOrderSourceInterface $source, PayUOrderInterface $template)
    {
        $order = handlers\PayUOrderHandler::createFromSource($source, $template);

        /*
          if ($order)
          {
          $handler = handlers\PayUApiHandler::instance($this->posId, $this->posAuthKey);

          return $handler->createOrder($template);
          }
         *
         */

        return $order;
    }
}