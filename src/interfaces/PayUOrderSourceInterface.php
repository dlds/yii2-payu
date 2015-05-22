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

    /**
     * Retrieves order id
     * @return int identification
     */
    public function getOrderId();

    /**
     * Retrieves order amount
     * @return float order amount
     */
    public function getOrderAmount();

    /**
     * Retrieves order desc
     * @return string description
     */
    public function getOrderDesc();

    /**
     * Retrieves order assigned customer's first name
     * @return string customer first name
     */
    public function getOrderCustomerFirstName();

    /**
     * Retrieves order assigne customer's last name
     * @return string customer last name
     */
    public function getOrderCustomerLastName();

    /**
     * Retrieves order assigned customer's email
     * @return string customer email
     */
    public function getOrderCustomerEmail();

    /**
     * Retrieves order assigned customer's preffered language
     * @return string customer language
     */
    public function getOrderCustomerLanguage();

    /**
     * Retrieves order assigne customer's IP
     * @return string customer IP
     */
    public function getOrderCustomerIP();

    /**
     * Retrieves order ts
     * @return string random string
     */
    public function getOrderTs();
}