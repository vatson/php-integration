<?php

require_once 'HostedPayment.php';
require_once  SVEA_REQUEST_DIR.'/Constant/PaymentMethod.php';

/**
  Extends HostedPayment
 * Goes to PayPage and excludes all methods that are not direct payments
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 * @package HostedRequests/Payment
 */
class DirectPayment extends HostedPayment {

    public $langCode = "en";

    /**
     * @param type $order
     */
    public function __construct($order) {
        parent::__construct($order);
    }

    protected function configureExcludedPaymentMethods($request) {
         //card
        $methods[] = SystemPaymentMethod::KORTCERT;
        $methods[] = SystemPaymentMethod::SKRILL;
       //other
        $methods[] = SystemPaymentMethod::PAYPAL;
       /**
        //countrycheck
       if($this->order->countryCode != "SE") {
            $methods[] = SystemPaymentMethod::DBNORDEASE;
            $methods[] = SystemPaymentMethod::DBSEBSE;
            $methods[] = SystemPaymentMethod::DBSEBFTGSE;
            $methods[] = SystemPaymentMethod::DBSHBSE;
            $methods[] = SystemPaymentMethod::DBSWEDBANKSE;
        }
        *
        */
        $exclude = new ExcludePayments();
        $methods = array_merge((array)$methods, (array)$exclude->excludeInvoicesAndPaymentPlan($this->order->countryCode));

        $request['excludePaymentMethods'] = $methods;
        return $request;
    }

    /**
     * Set return Url for redirect when payment is completed
     * @param type $returnUrlAsString
     * @return \HostedPayment
     */
    public function setReturnUrl($returnUrlAsString) {
        $this->returnUrl = $returnUrlAsString;
        return $this;
    }

    /**
     *
     * @param type $cancelUrlAsString
     * @return \HostedPayment
     */
    public function setCancelUrl($cancelUrlAsString) {
        $this->cancelUrl = $cancelUrlAsString;
        return $this;
    }

    /**
     * Alternative drop or change file in Config/SveaConfig.php
     * Note! This fuction may change in future updates.
     * @param type $merchantId
     * @param type $secret
     * @return \HostedPayment

    public function setMerchantIdBasedAuthorization($merchantId,$secret){
        $this->order->conf->merchantId = $merchantId;
        $this->order->conf->secret = $secret;
        return $this;
    }
     *
     */
     /**
     * @param type $languageCodeAsISO639
     * @return \HostedPayment|\DirectPayment
     */

         public function setPayPageLanguage($languageCodeAsISO639){
        switch ($languageCodeAsISO639) {
            case "sv":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "en":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "da":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "fi":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "no":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "de":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "es":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "fr":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "it":
                $this->langCode = $languageCodeAsISO639;

                break;
            case "nl":
                $this->langCode = $languageCodeAsISO639;

                break;
            default:
                 $this->langCode = "en";
                break;
        }

        return $this;
    }
}