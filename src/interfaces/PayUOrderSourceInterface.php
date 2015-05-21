<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\payu\interfaces;

/**
 * Interface which should be ingerited by any model which would be used as
 * source model for creating PayU order
 */
interface PayUOrderSourceInterface {

    public function getOrderId();

    public function getOrderAmount();

    public function getOrderDesc();

    public function getOrderPaymentType();

    public function getOrderCustomerFirstName();

    public function getOrderCustomerLastName();

    public function getOrderCustomerEmail();

    public function getOrderCustomerLanguage();

    public function getOrderCustomerIP();

    public function getOrderSig();

    public function getOrderTs();
}