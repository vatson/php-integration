<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once 'OrderValidator.php';

/**
 * Description of WebServiceOrderValidator
 *
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class WebServiceOrderValidator extends OrderValidator {

    public $errors = array();
    protected $isCompany;

    function __construct() {
        $this->isCompany = FALSE;
    }

    /**
     * @param type $order
     */
    public function validate($order) {
       if(isset($order->orgNumber) || isset($order->companyVatNumber) || isset($order->companyName)){
            $this->isCompany = TRUE;

        }
        //new!
        if(isset($order->customerIdentity->orgNumber) || isset($order->customerIdentity->companyVatNumber) || isset($order->customerIdentity->companyName)){
            $this->isCompany = TRUE;
        }
        $identityValidator = new IdentityValidator($this->isCompany);
        //$this->errors = $identityValidator->validateThatCustomerIdentityExists($order,  $this->errors); //use countries validators instead

        if($order->orderType == "PaymentPlan" && $this->isCompany == TRUE){
            $this->errors["Wrong customer type"] = "PaymentPlanPayment not allowed for Company customer.";
        }
        if (isset($order->countryCode)) {
            if ($order->countryCode == "SE"
                    || $order->countryCode == "NO"
                    || $order->countryCode == "DK"
                    || $order->countryCode == "FI") {
                $this->errors = $identityValidator->validateNordicIdentity($order, $this->errors);
            } elseif ($order->countryCode == "NL") {

                $this->errors = $identityValidator->validateNLidentity($order, $this->errors);

            } elseif ($order->countryCode == "DE") {
                $this->errors = $identityValidator->validateDEidentity($order, $this->errors);
            } else {
                $this->errors['not valid'] = "Given countrycode does not exist in our system.";
            }
        }else{
            $this->errors['missing value'] = "CountryCode is required. Use function setCountryCode().";
        }

        $this->errors = $identityValidator->validateDoubleIdentity($order,$this->errors);
        $this->errors = $this->validateRequiredFieldsForOrder($order,$this->errors);
        $this->errors = $this->validateOrderRows($order, $this->errors);
        if(isset($order->orderDate) == false){

            $this->errors["missing value"] = "OrderDate is Required. Use function setOrderDate().";
        }
        return $this->errors;
    }


}