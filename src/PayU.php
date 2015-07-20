<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\payu;

use dlds\payu\interfaces\PayUOrderSourceInterface;
use dlds\payu\interfaces\PayUOrderInterface;

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
    const PAYMENT_STATUS_ERROR = 900;

    /**
     * Payment types
     */
    const PAYMENT_TYPE_CS = 'cs';
    const PAYMENT_TYPE_KB = 'kb';
    const PAYMENT_TYPE_RB = 'rf';
    const PAYMENT_TYPE_MB = 'mp';
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
     * Payment types groups
     */
    const PAYMENT_GROUP_ALL = 0;
    const PAYMENT_GROUP_CREDIT_CARD = 10;
    const PAYMENT_GROUP_BANK_ONLINE = 20;
    const PAYMENT_GROUP_BANKWIRE = 30;
    const PAYMENT_GROUP_OTHERS = 40;

    /**
     * @var int ID of PayU point of sale
     */
    public $posId;

    /**
     * @var string authorization key for point of sale
     */
    public $posAuthKey;

    /**
     * @var string key1 for point of sale
     */
    public $key1;

    /**
     * @var string key2 for point of sale
     */
    public $key2;

    /**
     * @var string gateway url
     */
    public $urlTmpl;

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

        if (!$this->key1)
        {
            throw new \yii\base\Exception('Key1 parameter is not set');
        }

        if (!$this->key2)
        {
            throw new \yii\base\Exception('Key2 parameter is not set');
        }

        if (!$this->urlTmpl)
        {
            throw new \yii\base\Exception('Url parameter is not set');
        }
    }

    /**
     * Creates new payment order through PayU API
     */
    public function createOrder(PayUOrderSourceInterface $source, PayUOrderInterface $template)
    {
        return handlers\PayUOrderHandler::createFromSource($source, $template);
    }

    /**
     * Retrieves payment status
     * @param PayUOrderInterface $order
     */
    public function getPaymentStatus(PayUOrderInterface $order)
    {
        $handler = handlers\PayUApiHandler::instance($this->posId, $this->posAuthKey, $this->urlTmpl);

        return $handler->getPaymentStatus($order, $this->key1, $this->key2);
    }

    /**
     * Generates required form body for new payment
     * @param PayUOrderInterface $order
     */
    public function getPaymentFormFields(PayUOrderInterface $order)
    {
        $handler = handlers\PayUApiHandler::instance($this->posId, $this->posAuthKey, $this->urlTmpl);

        return $handler->getNewPaymentFields($order, $this->key1);
    }

    /**
     * Retrieves PayU gateway url
     * @return string url
     */
    public function getGatewayUrl($action = handlers\PayUApiHandler::ACTION_NEW_PAYMENT)
    {
        $handler = handlers\PayUApiHandler::instance($this->posId, $this->posAuthKey, $this->urlTmpl);

        return $handler->getGatewayUrl($action);
    }

    /**
     * Retrieves all possible payment statuses
     * @return array payment statuses
     */
    public static function paymentStatuses()
    {
        return [
            self::PAYMENT_STATUS_NEW => \Yii::t('dlds/payu', 'text_payment_status_new'),
            self::PAYMENT_STATUS_CANCELLED => \Yii::t('dlds/payu', 'text_payment_status_cancelled'),
            self::PAYMENT_STATUS_REJECTED => \Yii::t('dlds/payu', 'text_payment_status_rejected'),
            self::PAYMENT_STATUS_STARTED => \Yii::t('dlds/payu', 'text_payment_status_started'),
            self::PAYMENT_STATUS_AWAITING => \Yii::t('dlds/payu', 'text_payment_status_awaiting'),
            self::PAYMENT_STATUS_RETRIEVED => \Yii::t('dlds/payu', 'text_payment_status_retrieved'),
            self::PAYMENT_STATUS_ENDED => \Yii::t('dlds/payu', 'text_payment_status_ended'),
            self::PAYMENT_STATUS_UNKNOWN => \Yii::t('dlds/payu', 'text_payment_status_unknown'),
            self::PAYMENT_STATUS_ERROR => \Yii::t('dlds/payu', 'text_payment_status_error'),
        ];
    }

    /**
     * Retrieves all possible payment types
     * @return array payment types
     */
    public static function paymentTypes($group = self::PAYMENT_GROUP_ALL, $allowGroups = true)
    {
        $types[self::PAYMENT_GROUP_BANK_ONLINE] = [
            self::PAYMENT_TYPE_CS => \Yii::t('dlds/payu', 'text_payment_method_cs'),
            self::PAYMENT_TYPE_KB => \Yii::t('dlds/payu', 'text_payment_method_kb'),
            self::PAYMENT_TYPE_RB => \Yii::t('dlds/payu', 'text_payment_method_rb'),
            self::PAYMENT_TYPE_MB => \Yii::t('dlds/payu', 'text_payment_method_mb'),
            self::PAYMENT_TYPE_FIO => \Yii::t('dlds/payu', 'text_payment_method_fio'),
            self::PAYMENT_TYPE_CSOB => \Yii::t('dlds/payu', 'text_payment_method_csob'),
            self::PAYMENT_TYPE_GE => \Yii::t('dlds/payu', 'text_payment_method_ge'),
            self::PAYMENT_TYPE_ERA => \Yii::t('dlds/payu', 'text_payment_method_era'),
            self::PAYMENT_TYPE_SB => \Yii::t('dlds/payu', 'text_payment_method_sb'),
        ];

        $types[self::PAYMENT_GROUP_CREDIT_CARD] = [
            self::PAYMENT_TYPE_GPE => \Yii::t('dlds/payu', 'text_payment_method_gpe'),
        ];

        $types[self::PAYMENT_GROUP_BANKWIRE] = [
            self::PAYMENT_TYPE_BANKWIRE => \Yii::t('dlds/payu', 'text_payment_method_bankwire'),
        ];

        $types[self::PAYMENT_GROUP_OTHERS] = [
            self::PAYMENT_TYPE_PAYSEC => \Yii::t('dlds/payu', 'text_payment_method_paysec'),
            self::PAYMENT_TYPE_MOBITO => \Yii::t('dlds/payu', 'text_payment_method_mobito'),
            self::PAYMENT_TYPE_POST => \Yii::t('dlds/payu', 'text_payment_method_post'),
        ];

        if (!YII_ENV_PROD)
        {
            $types[self::PAYMENT_GROUP_OTHERS][self::PAYMENT_TYPE_TEST] = \Yii::t('dlds/payu', 'text_payment_method_test');
        }

        if (isset($types[$group]))
        {
            return $types[$group];
        }

        if (!$allowGroups)
        {
            $merged = [];

            foreach ($types as $payments)
            {
                foreach ($payments as $key => $label)
                {
                    $merged[$key] = $label;
                }
            }

            return $merged;
        }

        return $types;
    }
}