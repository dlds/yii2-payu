<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\payu\handlers;

use yii\helpers\Html;
use dlds\payu\interfaces\PayUOrderSourceInterface;
use dlds\payu\interfaces\PayUOrderInterface;

/**
 * This is the main class of the dlds\mlm component that should be registered as an application component.
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package mlm
 */
class PayUApiHandler {

    /**
     * Actions
     */
    const ACTION_NEW_PAYMENT = 'NewPayment';

    /**
     * @var string given POS ID
     */
    protected $posId;

    /**
     * @var string given POS AUTH KEY
     */
    protected $posAuthKey;

    /**
     * @var string given url tmpl
     */
    protected $urlTmpl;
    
    /**
     * @var PayUApiHandler instance
     */
    private static $_instance = null;

    /**
     * Private constructor to ensure thas handler would be singleton
     * @param string $posId
     * @param string $posAuthKey
     */
    private function __construct($posId, $posAuthKey, $urlTmpl)
    {
        $this->posId = $posId;
        $this->posAuthKey = $posAuthKey;
        $this->urlTmpl = $urlTmpl;
    }

    /**
     * Retrieves singleton instance of PayUApiHandler
     * @param string $posId
     * @param string $posAuthKey
     * @return PayUApiHandler instance
     */
    public static function instance($posId, $posAuthKey, $urlTmpl)
    {
        if (!self::$_instance)
        {
            self::$_instance = new self($posId, $posAuthKey, $urlTmpl);
        }

        return self::$_instance;
    }

    /**
     * Retrieves gateway url
     * @param string $action given action
     * @return string url
     */
    public function getGatewayUrl($action)
    {
        return str_replace('{action}', $action, $this->urlTmpl);
    }

    /**
     * Retrieves required form body fields to create new payment request
     * @param PayUOrderInterface $order
     */
    public function getPaymentstatus(PayUOrderInterface $order, $key)
    {
        die('ee');
    }

    /**
     * Retrieves required form body fields to create new payment request
     * @param PayUOrderInterface $order
     */
    public function getPaymentFields(PayUOrderInterface $order, $key)
    {
        $fields = [];

        foreach (self::generatePaymentFields($order, $key) as $name => $value)
        {
            $fields[] = Html::hiddenInput($name, $value);
        }

        return $fields;
    }

    /**
     * Retrieves all required field for new payment
     * @param PayUOrderInterface $order
     * @param string $key private PayU key
     */
    protected function generatePaymentFields(PayUOrderInterface $order, $key)
    {
        if (!$order->getSource())
        {
            return [];
        }

        // array values must be in this order otherwise SIG hash would be denied
        $data = [
            'pos_id' => $this->posId,
            'pay_type' => $order->getPaymentType(),
            'session_id' => $order->getSessionId(),
            'pos_auth_key' => $this->posAuthKey,
            //'amount' => $order->getSource()->getOrderAmount() * 100,
            'amount' => $order->getSource()->getOrderAmount(),
            'desc' => $order->getSource()->getOrderDesc(),
            'order_id' => $order->getSource()->getOrderId(),
            'first_name' => $order->getSource()->getOrderCustomerFirstName(),
            'last_name' => $order->getSource()->getOrderCustomerLastName(),
            //'street' => 'ulice',
            //'street_hn' => '12',
            //'street_an' => '3',
            //'city' => 'praha',
            //'post_code' => '16901',
            //'country' => 'cz',
            'email' => $order->getSource()->getOrderCustomerEmail(),
            //'phone' => '765765765',
            'language' => $order->getSource()->getOrderCustomerLanguage(),
            'client_ip' => $order->getSource()->getOrderCustomerIP(),
            //'js' => 0,
            'ts' => $order->getSource()->getOrderTs(),
        ];

        $sig = md5(implode('', $data).$key);

        $data['sig'] = $sig;

        return $data;
    }
}