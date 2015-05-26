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
    const ACTION_GET_SATUS = 'Payment/get/xml';

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
    public function getPaymentstatus(PayUOrderInterface $order, $key1, $key2)
    {
        // process payment status request to gateway
        $response = $this->processPaymentStatusRequest($order, $key1);

        // parses XML response using simple xml
        $xml = simplexml_load_string($response);

        // verifies if response is valid
        if ($xml && $this->verifyResponse($xml, $key2))
        {
            // retrieves status as integer value
            return (int) $xml->trans->status;
        }

        // retrieves false if response was unsuccessful
        return false;
    }

    /**
     * Retrieves required form body fields to create new payment request
     * @param PayUOrderInterface $order
     */
    public function getNewPaymentFields(PayUOrderInterface $order, $key1)
    {
        $fields = [];

        foreach ($this->generateNewPaymentFields($order, $key1) as $name => $value)
        {
            $fields[] = Html::hiddenInput($name, $value);
        }

        return $fields;
    }

    /**
     * Retrieves all required field for new payment
     * @param PayUOrderInterface $order
     * @param string $key1 private PayU key
     */
    protected function generateNewPaymentFields(PayUOrderInterface $order, $key1)
    {
        // if order has no source retrieves empty array
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

        // generate SIG hash
        $data['sig'] = md5(implode('', $data).$key1);

        return $data;
    }

    /**
     * Processes payment status request
     * @param PayUOrderInterface $order
     * @param string $key1
     */
    protected function processPaymentStatusRequest(PayUOrderInterface $order, $key1)
    {
        // return false if order has no source
        if (!$order->getSource())
        {
            return false;
        }

        // use order source TS value as request TS
        $ts = $order->getSource()->getOrderTs();

        // set required parameters for request
        $parameters = [
            'pos_id' => $this->posId,
            'session_id' => $order->getSessionId(),
            'ts' => $ts,
        ];

        // generate SIG hash
        $parameters['sig'] = md5(implode('', $parameters).$key1);

        // create request using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getGatewayUrl(self::ACTION_GET_SATUS));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $payu_response = curl_exec($ch);
        curl_close($ch);

        return $payu_response;
    }

    /**
     * Verifies PayU response
     * @param \dlds\payu\handlers\SimpleXMLElement $xml
     */
    protected function verifyResponse(\SimpleXMLElement $xml, $key2)
    {
        // generate expected SIG string
        $sig = md5(implode('', [
            $this->posId,
            $xml->trans->session_id,
            $xml->trans->order_id,
            $xml->trans->status,
            $xml->trans->amount,
            $xml->trans->desc,
            $xml->trans->ts,
            $key2,
        ]));

        // check if generated SIG is same as recieved SIG
        return $sig === (string) $xml->trans->sig;
    }
}