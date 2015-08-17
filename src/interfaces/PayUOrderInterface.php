<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2014 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\payu\interfaces;

use dlds\payu\interfaces\PayUOrderSourceInterface;

/**
 * Interface which should be ingerited by any model which would be used as
 * source model for creating PayU order
 */
interface PayUOrderInterface {

    /**
     * Retrieves order sessions id which is unique identification of order
     * @return string session id
     */
    public function getSessionId();

    /**
     * Retrieves source model this order was created based on
     * @return PayUOrderSourceInterface
     */
    public function getSource();

    /**
     * Retrieves order status
     * @return int status
     */
    public function getStatus();

    /**
     * Retrieves order payment type
     * @return string payment type
     */
    public function getPaymentType();

    /**
     * Sets source based on this order is created
     */
    public function setSource(PayUOrderSourceInterface $source);

    /**
     * Sets order status
     */
    public function setStatus($status);

    /**
     * Sets order payment type
     */
    public function setType($type);

    /**
     * Indicates if payment state is invariant
     * @return boolean
     */
    public function isInvariable();
}