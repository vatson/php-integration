<?php
require_once SVEA_REQUEST_DIR . '/Includes.php';
/**
 * Description of HostedResponse
 *
 * @author anne-hal
 */
class HostedResponse {
   
    public $accepted;
    public $resultcode;
    public $transactionId;
    public $clientOrderNumber;
    public $paymentMethod;
    public $merchantId;
    public $amount;
    public $currency;
    


    function __construct($encodedXml) {
           $decodedXml = base64_decode($encodedXml);
        $this->formatXml($decodedXml);
    }
    
    protected function formatXml($xml){
     $xmlElement = new SimpleXMLElement($xml);
     if((string)$xmlElement->statuscode == 0){
          $this->accepted = 1;
     }else{
         $this->accepted = 0;
         $this->resultcode = (int)$xmlElement->statuscode;
     }
     $this->transactionId = (string)$xmlElement->transaction['id'];
     $this->paymentMethod = (string)$xmlElement->transaction->paymentmethod;
     $this->merchantId = (string)$xmlElement->transaction->merchantid;     
     $this->clientOrderNumber = (string)$xmlElement->transaction->customerrefno;
     $minorAmount = (int)($xmlElement->transaction->amount);
     $this->amount = $minorAmount * 0.01;
     $this->currency = (string)$xmlElement->transaction->currency;
     if(property_exists($xmlElement->transaction, "subscriptionid")){
         $this->subscriptionId = (string)$xmlElement->transaction->subscriptionid;    
         $this->subscriptionType = (string)$xmlElement->transaction->subscriptiontype;    
     }
     if(property_exists($xmlElement->transaction, "cardtype")){
        $this->cardType = (string)$xmlElement->transaction->cardtype;    
        $this->maskedCardNumber = (string)$xmlElement->transaction->maskedcardno;    
        $this->expiryMonth = (string)$xmlElement->transaction->expirymonth;    
        $this->expiryYear = (string)$xmlElement->transaction->expiryyear;    
        $this->authCode = (string)$xmlElement->transaction->authcode;    
     }
     
       
    }
    
    
}

?>