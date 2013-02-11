# PHP Integration Package API for SveaWebPay

## Index
Introduction
Configuration
1. CreateOrder
2. GetPaymentPlanParams
3. GetAddresses
4. DeliverOrder
5. CloseOrder
6. Response


## Introduction
This integration package is built for developers to simplify the integration of Svea WebPay services. 
Using this package will make your implementation sustainable and unaffected for changes
in our payment system. Just make sure to update the package regularly.

The API is built as a *Fluent API* so you can use *method chaining* when implementing it in your code.
Make sure to open your implementation as a project in your IDE to make the code completion work properly. 
Package is developed and tested in NetBeans IDE 7.2. 
Include the file *Includes.php* from the php integration package in your file.
Call class WebPay and the suitable static function for your action.
   
Ex:
```php
require_once 'Includes.php';

$foo = WebPay::createOrder();
$requestObject = 
$foo->...
    ->..;
```
## Configuration
There are three ways to configure Svea authorization. Choose one of the following:
1. Drop a file named SveaConfig.php in folder Config looking the same as existing file
   but with your own authorization values. This method is preffered as it simplyfies updates in the package.
2. Modify function __construct() in file SveaConfig.php in folder Config with your authorization values.
3. Everytime when creating an order, after choosing payment type, use function setPasswordBasedAuthorization() for Invoice/Payment plan 
    or setMerchantIdBasedAuthorization() for other hosted payments like card and direct bank payments.

## 1. createOrder
Creates an order and performs payment for all payment forms. Invoice and Payment plan will perform 
a synchronous payment and return a response. 
Other hosted payments, like card, direct bank and other payments from the *PayPage*,
on the other hand are asynchronous. They will return an html form with formatted message to send from your store.
For every new payment type implementation, you follow the steps from the beginning and chose your payment type preffered in the end:
Build order -> choose payment type -> do payment/get payment form

Create order Ex.
```php

$response = WebPay::createOrder()
//if testmode
    ->setTestmode()
//For all products and other items
    ->beginOrderRow()
    ...
    ->endOrderRow()
//If shipping fee
    ->beginShippingFee()
    ...
    ->endShippingFee()
//If invoice with invoice fee
    ->beginInvoiceFee()
    ...
    ->endInvoiceFee()
//If discount or coupon with fixed amount
    ->beginFixedDiscount()
    ...
    ->endFixedDiscount()
//If discount or coupon with percent discount
    ->beginRelativeDiscount()
    ...	
    ->endRelativeDiscount()
//Customer values. Different validation depending on country code.
    ->setCustomerSsn(194605092222)
    ->setCustomerInitials("SB")
    ->setCustomerBirthDate(1923, 12, 12)
    ->setCustomerName("Tess", "Testson")
    ->setCustomerEmail("test@svea.com")
    ->setCustomerPhoneNumber(999999)
    ->setCustomerIpAddress("123.123.123")
    ->setCustomerStreetAddress("Gatan", 23)
    ->setCustomerCoAddress("c/o Eriksson")
    ->setCustomerZipCode(9999)
    ->setCustomerLocality("Stan")
//If customer is a company values
    ->setCustomerCompanyIdNumber("2345234")
    ->setCustomerCompanyName("TestCompagniet")
    ->setCustomerCompanyVatNumber(NL123123)
//Other values
    ->setCountryCode("SE")
    ->setOrderDate("2012-12-12")
    ->setCustomerReference("33")
    ->setClientOrderNumber("nr26")
    ->setCurrency("SEK")
    ->setAddressSelector("7fd7768")

//Continue by choosing one of the following paths
    //Continue as a card payment
    ->usePayPageCardOnly() 
        ...
        ->getPaymentForm();
    //Continue as a direct bank payment		
    ->usePayPageDirectBankOnly()
        ...
        ->getPaymentForm();
    //Continue as a *PayPage* payment
    ->usePayPage()
        ...
        ->getPaymentForm();
    //Continue as a *PayPage* payment
    ->usePaymentMethod (PaymentMethod::DBSEBSE) //see APPENDIX for Constants
        ...
        ->getPaymentForm();
    //Continue as an invoice payment
    ->useInvoicePayment()
    ...
        ->doRequest();
    //Continue as a payment plan payment
    ->usePaymentPlanPayment("campaigncode", 0)
    ...
        ->doRequest();
```
### 1.1 Test mode
Set test mode while developing to make the calls to our test server.
Remove when you change to production mode.
Ex.	
```php
    ->setTestmode()
```
	
### 1.2 Specify order
Continue by adding rows for products and other. You can add OrderRow, InvoiceFee, ShippingFee, RelativeDiscount, FixedDiscount.	
	
#### 1.2.1 OrderRow
All products and other items. It´s required to have a minimum of one order row.
**The price can be set in a combination by using a minimum of two out of three functions: setAmountExVat(), setAmountIncVat(), setVatPercent().**
Ex.
```php
    ->beginOrderRow()        
        ->setQuantity(2)                    //Required
        ->setAmountExVat(100.00)            //Optional, see info above
        ->setAmountIncVat(125.00)           //Optional, see info above
        ->setVatPercent(25)                 //Optional, see info above
        ->setArticleNumber(1)               //Optional
        ->setDescription("Specification")   //Optional
        ->setName('Prod')                   //Optional
        ->setUnit("st")                     //Optional              
        ->setDiscountPercent(0)             //Optional
    ->endOrderRow()
```

#### 1.2.2 ShippingFee
**The price can be set in a combination by using a minimum of two out of three functions: setAmountExVat(), setAmountIncVat(), setVatPercent().**
Ex.
```php
    ->beginShippingFee()
        ->setShippingId('33')               //Optional
        ->setName('shipping')               //Optional
        ->setDescription("Specification")   //Optional
        ->setAmountExVat(50)                //Optional, see info above
        ->setAmountIncVat(62.50)            //Optional, see info above
        ->setVatPercent(25)                 //Optional, see info above
        ->setUnit("st")                     //Optional             
        ->setDiscountPercent(0)             //Optional
    ->endShippingFee()
```
#### 1.2.3 InvoiceFee
**The price can be set in a combination by using a minimum of two out of three functions: setAmountExVat(), setAmountIncVat(), setVatPercent().**
Ex. 
```php
    ->beginInvoiceFee()
        ->setName('Svea fee')               //Optional
        ->setDescription("Fee for invoice") //Optional
        ->setAmountExVat(50)                //Optional, see info above
        ->setAmountIncVat(62.50)            //Optional, see info above
        ->setVatPercent(25)                 //Optional, see info above
        ->setUnit("st")                     //Optional
        ->setDiscountPercent(0)             //Optional
    ->endInvoiceFee()
```
#### 1.2.4 Fixed Discount
When discount or coupon is a fixed amount on total product amount.
Ex. 
```php
    ->beginFixedDiscount()                  
        ->setAmountIncVat(100.00)           //Required
        ->setDiscountId("1")                //Optional        
        ->setUnit("st")                     //Optional
        ->setDescription("FixedDiscount")   //Optional
        ->setName("Fixed")                  //Optional
    ->endFixedDiscount(0)
```
#### 1.2.5 Relative Discount
When discount or coupon is a percentage on total product amount.
Ex. 
```php
    ->beginRelativeDiscount()
        ->setDiscountPercent(50)            //Required
        ->setDiscountId("1")                //Optional      
        ->setUnit("st")                     //Optional
        ->setName('Relative')               //Optional
        ->setDescription("RelativeDiscount")//Optional
    ->endRelativeDiscount()
```
### 1.3 Customer Identity
Customer Identity is required for invoice and payment plan orders. Required values varies 
depending on country and customer type. For SE, NO, DK and FI ssn (Social Security Number)
or companyIdNumber is required. Email and Ip Address are desirable.
Ex. 
```php
    //Options for individual customers
    ->setCustomerSsn(194605092222)          //Required for individual customers in SE, NO, DK, FI
    ->setCustomerInitials("SB")             //Required for individual customers in NL 
    ->setCustomerBirthDate(1923, 12, 12)    //Required for individual customers in NL and DE
    ->setCustomerName("Tess", "Testson")    //Required for individual customers in NL and DE    
    ->setCustomerStreetAddress("Gatan", 23) //Required in NL and DE    
    ->setCustomerZipCode(9999)              //Required in NL and DE
    ->setCustomerLocality("Stan")           //Required in NL and DE    
    ->setCustomerEmail("test@svea.com")     //Optional but desirable    
    ->setCustomerIpAddress("123.123.123")   //Optional but desirable
    ->setCustomerCoAddress("c/o Eriksson")  //Optional
    ->setCustomerPhoneNumber(999999)        //Optional

    //Additional options for company customers
    ->setCustomerCompanyIdNumber("2345234")     //Required for company customers in SE, NO, DK, FI
    ->setCustomerCompanyVatNumber(NL2345234)    //Required for NL and DE
    ->setCustomerCompanyName("TestCompagniet")  //Required for Eu countries like NL and DE
```
### 1.5 Other values
Ex. 
```php
    ->setCountryCode("SE")                      //Required for web services    
    ->setCurrency("SEK")                        //Required for card payment, direct payment and *PayPage* payment.
    ->setClientOrderNumber("nr26")              //Required for card payment, direct payment, PaymentMethod payment and *PayPage* payments.
    ->setAddressSelector("7fd7768")             //Recieved from getAddresses
    ->setOrderDate("2012-12-12")                //Optional
    ->setCustomerReference("33")                //Optional
```
### 1.6 Choose payment
End process by choosing the payment method you desire.

Invoice and payment plan will perform a synchronous payment and return a response as object. 

Other hosted payments (card, direct bank and other payments from the *PayPage*)
on the other hand are asynchronous. They will return an html form with formatted message to send from your store.
The response is then returned to the returnUrl you have specified in function setReturnUrl(). If you
use class Response with the xml response as parameter, you will receive a
formatted object as well. 

#### Asynchronous payments - Hosted solutions
Build order and recieve a PaymentForm object. Send the PaymentForm parameters: *merchantid*, *xmlMessageBase64* and *mac* by POST to
SveaConfig::SWP_TEST_URL or SveaConfig::SWP_PROD_URL. The PaymentForm object also contains a complete html form as string 
and the html form element as array.
Ex.
```html
    <form name='paymentForm' id='paymentForm' method='post' action='SveaConfig::SWP_TEST_URL'>
        <input type='hidden' name='merchantid' value='{$this->merchantid}' />
        <input type='hidden' name='message' value='{$this->xmlMessageBase64}' />
        <input type='hidden' name='mac' value='{$this->mac}' />
        <noscript><p>Javascript is inactivated.</p></noscript>'
        <input type="submit" name="submit" value="Submit" />
    </form>
```


#### 1.6.1 PayPage with Card payment options
*PayPage* with availible card payments only.

##### 1.6.1.1 Request
If Config/SveaConfig.php is not modified you can set your store authorization here.
Ex. 
```php
$form = WebPay::createOrder()
    ->beginOrderRow()
        ->setArticleNumber(1)
        ->setQuantity(2)
        ->setAmountExVat(100.00)
        ->setDescription("Specification")
        ->setName('Prod')
        ->setUnit("st")
        ->setVatPercent(25)
        ->setDiscountPercent(0)
    ->endOrderRow()                
    ->setCountryCode("SE")
    ->setClientOrderNumber("33")
    ->setOrderDate("2012-12-12")
    ->setCurrency("SEK")
 ** ->usePayPageCardOnly()
        ->setMerchantIdBasedAuthorization("merchantId", "f78hv9")   //Optional
        ->setReturnUrl("http://myurl.se")                   //Required
        ->getPaymentForm();**

```
##### 1.6.1.2 Return
The values of *message*, *merchantId* and *mac* are to be sent as xml to SveaWebPay.
Function getPaymentForm() returns Object type PaymentForm with accessible members:

| Member                            | Description                               |
| --------------------------------- |:-----------------------------------------:|
| xmlMessageBase64                  | Payment information in XML-format, Base64 encoded.| 
| xmlMessage                        | Payment information in XML-format.        |
| merchantid                        | Authorization                             |
| secretWord                        | Authorization                             |
| mac                               | Message authentication code.              |    
| completeHtmlFormWithSubmitButton  | A complete Html form with method= "post" with submit button to include in your code. |
| htmlFormFieldsAsArray             | Array of Html form fields to include.     |
| rawFields                         | Array of values to send in Html form. ($merchantid, $message, $mac) |
            


#### 1.6.2 PayPage with Direct bank payment options
*PayPage* with available direct bank payments only.
                
##### 1.6.2.1 Request
If Config/SveaConfig.php is not modified you can set your store authorization here.
Ex. 
```php
$form = WebPay::createOrder()
    ->setTestmode()
    ->beginOrderRow()
        ->setArticleNumber(1)
        ->setQuantity(2)
        ->setAmountExVat(100.00)
        ->setDescription("Specification")
        ->setName('Prod')
        ->setUnit("st")
        ->setVatPercent(25)
        ->setDiscountPercent(0)
    ->endOrderRow()                   
    ->setCountryCode("SE")
    ->setCustomerReference("33")
    ->setOrderDate("2012-12-12")
    ->setCurrency("SEK")
  **  ->usePayPageDirectBankOnly()
        ->setMerchantIdBasedAuthorization(1200, "f78hv9")   //Optional
        ->setReturnUrl("http://myurl.se")                   //Required
        ->getPaymentForm();**
```
##### 1.6.2.2 Return
Returns Object type PaymentForm:
           
| Member                            | Description                               |
| --------------------------------- |:-----------------------------------------:|
| xmlMessageBase64                  | Payment information in XML-format, Base64 encoded.| 
| xmlMessage                        | Payment information in XML-format.        |
| merchantid                        | Authorization                             |
| secretWord                        | Authorization                             |
| mac                               | Message authentication code.              |
| completeHtmlFormWithSubmitButton  | A complete Html form with method= "post" with submit button to include in your code. |
| htmlFormFieldsAsArray             | Array of Html form fields to include.     |
| rawFields                         | Array of values to send in Html form. ($merchantid, $message, $mac) |
            
#### 1.6.3 PayPagePayment
*PayPage* with all available payments. You can also custom the *PayPage* by using one of the methods for PayPagePayments:
setPaymentMethod, includePaymentMethods, excludeCardPaymentMethods or excludeDirectPaymentMethods.
                
##### 1.6.3.1 Request
Ex. 
```php
$form = WebPay::createOrder()
        ->setTestmode()
        ->beginOrderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        ->endOrderRow()
        ->setCountryCode("SE")
        ->setCustomerReference("33")
        ->setOrderDate("2012-12-12")
        ->setCurrency("SEK")
**        ->usePayPage()
            ->setMerchantIdBasedAuthorization(1200, "f78hv9")   //Optional
            ->setReturnUrl("http://myurl.se")                   //Required
            ->getPaymentForm();**
```               

###### 1.6.3.1.1 Exclude specific payment methods
Optional if you want to include specific paymentmethods for paypage
Ex. 
```php   
    ->usePayPage()
        ->setReturnUrl("http://myurl.se") //Required
        ->excludePaymentMethods(PaymentMethod::DBSEBSE,PaymentMethod::SVEAINVOICE_SE)
        ->getPaymentForm();
```
###### 1.6.3.1.2 Include specific payment methods
Optional if you want to include specific payment methods for *PayPage*
Ex. 
```php   
    ->usePayPage()
        ->setReturnUrl("http://myurl.se")       //Required
        ->includePaymentMethods(PaymentMethod::DBSEBSE,PaymentMethod::SVEAINVOICE_SE)   //Optional
        ->getPaymentForm();
```

###### 1.6.3.1.3 Exclude Card payments
Optional if you want to exclude all cardpayment methods from *PayPage*.
Ex. 
```php
   ->usePayPage()
        ->setReturnUrl("http://myurl.se")       //Required
        ->excludeCardPaymentMethods()           //Optional
        ->getPaymentForm();
```

###### 1.6.3.1.4 Exclude Direct payments
Optional if you want to exclude all direct bank payments methods from *PayPage*.
Ex. 
```php  
->usePayPage()
    ->setReturnUrl("http://myurl.se")           //Required
    ->excludeDirectPaymentMethods()             //Optional
    ->getPaymentForm();
```
##### 1.6.3.6 Return
Returns Object type PaymentForm:
                
| Member                            | Description                               |
| --------------------------------- |:-----------------------------------------:|
| xmlMessageBase64                  | Payment information in XML-format, Base64 encoded.| 
| xmlMessage                        | Payment information in XML-format.        |
| merchantid                        | Authorization                             |
| secretWord                        | Authorization                             |
| mac                               | Message authentication code.              |
| completeHtmlFormWithSubmitButton  | A complete Html form with method= "post" with submit button to include in your code. |
| htmlFormFieldsAsArray             | Array of Html form fields to include.     |
| rawFields                         | Array of values to send in Html form. ($merchantid, $message, $mac) |
     
#### 1.6.4 Response handler
After sending the values *mac*, *merchantid* and *xmlMessageBase64* to the server,
an answer will be sent to the *returnUrl* with POST or GET as XML. If you process
the answer with the class SveaResponse, you will have a structured object 
similar to the synchronous aswer instead.
Ex.
```php
  $resp = new SveaResponse($_REQUEST['response']); 
```

#### Synchronous solutions - Invoice and PaymentPlan
       
#### 1.6.4 InvoicePayment
Perform invoice payment. This payment form will perform an synchronous payment and return a response.
Returns CreateOrderResponse object.
If Config/SveaConfig.php is not modified you can set your store authorization here.
Ex. 
```php
    $response = WebPay::createOrder()
        ->setTestmode()
        ->beginOrderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        ->endOrderRow()
         ->setCountryCode("SE")
         ->setCustomerReference("33")
         ->setOrderDate("2012-12-12")
         ->setCurrency("SEK")
**         ->useInvoicePayment()
             ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
             ->doPayment();**
```
#### 1.6.5 PaymentPlanPayment
Perform PaymentPlanPayment. This payment form will perform an synchronous payment and return a response.
Returns CreateOrderResponse object. Preceded by WebPay::getPaymentPlanParams().
If Config/SveaConfig.php is not modified you can set your store authorization here.
Param: Campaign code recieved from getPaymentPlanParams().
Ex. 
```php
    $response = WebPay::createOrder()
       ->setTestmode()
       ->beginOrderRow()
           ->setArticleNumber(1)
           ->setQuantity(2)
           ->setAmountExVat(100.00)
           ->setDescription("Specification")
           ->setName('Prod')
           ->setUnit("st")
           ->setVatPercent(25)
           ->setDiscountPercent(0)
       ->endOrderRow()
       ->setCountryCode("SE")
       ->setCustomerReference("33")
       ->setOrderDate("2012-12-12")
       ->setCurrency("SEK")
 **      ->usePaymentPlanPayment("camp1")  //Parameter: campaign code recieved from getPaymentPlanParams
           ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
           ->doPayment();**
```
## 2. getPaymentPlanParams
Use this function to retrieve campaign codes for possible payment plan options. Use prior to create payment plan payment.
Returns PaymentPlanParamsResponse object.
If Config/SveaConfig.php is not modified you can set your store authorization here.
Ex. 
```php
    $response = WebPay::getPaymentPlanParams()
        ->setTestmode()
            ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
            ->doRequest();
```
## 3. getAddresses
Returns getAddressesResponse object with an *AddressSelector* for the associated addresses for a specific SecurityNumber. 
Can be used when creating an order.  Only applicable for SE, NO and DK.
If Config/Config.php is not modified you can set your store authorization here.

### 3.1 Order type 
```php
    ->setOrderTypeInvoice()         //Required if this is an invoice order
or
    ->setOrderTypePaymentPlan()     //Required if this is a payment plan order
```
### 3.2 Customer type 
```php
    ->setIndividual(194605092222)   //Required if this is an Individual customer
or
    ->setCompany("CompanyId")       //Required if this is a Company customer
```
### 3.3
Ex.	
```php
    $response = WebPay::getAddresses()
        ->setTestmode()
        ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
        ->setOrderTypeInvoice()                                              //See 3.1   
        ->setCountryCode("SE")                                               //Required
        ->setIndividual(194605092222)                                        //See 3.2   
        ->doRequest();
```

## 4. deliverOrder
Updates the status on a previous created order as *delivered*. Add rows that you want delivered. The rows will automatically be
matched with the rows that was sent when creating the order.
Only applicable for invoice and payment plan payments.
Returns DeliverOrderResult object.
If Config/SveaConfig.php is not modified you can set your store authorization here.

### 4.1 Testmode
Set test mode while developing to make the calls to our test server.
Remove when you change to production mode.

Ex. 
```php
    ->setTestmode()
```
### 4.2 Rows
Continue by adding rows for products and other. You can also add InvoiceFee and ShippingFee.

#### 4.2.1 OrderRow
All products and other items. It is required to have a minimum of one row.
Ex.
```php
    ->beginOrderRow()       
       ->setQuantity(2)                     //Required
       ->setAmountExVat(100.00)             //Required
       ->setVatPercent(25)                  //Required
       ->setArticleNumber(1)                //Optional
       ->setDescription("Specification")    //Optional    
       ->setName('Prod')                    //Optional
       ->setUnit("st")                      //Optional
       ->setDiscountPercent(0)              //Optional
   ->endOrderRow()
```

#### 4.2.2 ShippingFee
Ex. 
```php
    ->beginShippingFee()
        ->setAmountExVat(50)                //Required
        ->setVatPercent(25)                 //Required
        ->setShippingId('33')               //Optional
        ->setName('shipping')               //Optional
        ->setDescription("Specification")   //Optional        
        ->setUnit("st")                     //Optional        
        ->setDiscountPercent(0)
    ->endShippingFee()
```
#### 4.2.3 InvoiceFee
Ex. 
```php
    ->beginInvoiceFee()
        ->setAmountExVat(50)                //Required
        ->setVatPercent(25)                 //Required
        ->setName('Svea fee')               //Optional
        ->setDescription("Fee for invoice") //Optional       
        ->setUnit("st")                     //Optional
        ->setDiscountPercent(0)             //Optional
    ->endInvoiceFee()
```
### 4.3 Other values
Required is the Order id received when creating the order. Required for InvoiceOrders are InvoiceDistributionType. 
If invoice order is credit invoice use setCreditInvoice($invoiceId) and setNumberOfCreditDays($creditDaysAsInt)
Ex. 
```php
    ->setOrderId($orderId)                  //Required. Received when creating order.
    ->setNumberOfCreditDays(1)              //Use for Invoice orders.
    ->setInvoiceDistributionType('Post')    //Use for Invoice orders. "Post" or "Email"
    ->setCreditInvoice                      //Use for invoice orders, if this should be a credit invoice.
    ->setNumberOfCreditDays(1)              //Use for invoice orders.
```
	
Ex. 
```php
    $response = WebPay::deliverOrder()
        ->setTestmode()
        ->beginOrderRow()
            ->setArticleNumber(1)
            ->setQuantity(2)
            ->setAmountExVat(100.00)
            ->setDescription("Specification")
            ->setName('Prod')
            ->setUnit("st")
            ->setVatPercent(25)
            ->setDiscountPercent(0)
        ->endOrderRow()
        ->setOrderId("id")
        ->setInvoiceDistributionType('Post')
**        ->deliverInvoiceOrder()
            ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
            ->doRequest();**
```

## 5. closeOrder
    Use when you want to cancel an undelivered order. Valid only for invoice and payment plan orders.
    Required is the order id received when creating the order.
    If Config/SveaConfig.php is not modified you can set your store authorization here.

### 5.1 Close by payment type
```php
    ->closeInvoiceOrder()
or
    ->closePaymentPlanOrder()
```
Ex. 
```php
    $request =  WebPay::closeOrder()
        ->setTestmode()
        ->setOrderId($orderId)                  //Required. Received when creating an order.
        ->closeInvoiceOrder()
            ->setPasswordBasedAuthorization("sverigetest", "sverigetest", 79021) //Optional
            ->doRequest();
```

# APPENDIX

## PayMentMethods
Used in usePaymentMethod($paymentMethod) and in usePayPage(), 
->includePaymentMethods(...,...,...), ->excludeCardPaymentMethods(...,...,...), ->excludeDirectPaymentMethods(), ->excludeCardPaymentMethods().

PaymentMethod::DBNORDEASE = Direct bank payment, Nordea, Sweden.
PaymentMethod::DBSEBSE = Direct bank payment, private, SEB, Sweden.
PaymentMethod::DBSEBFTGSE = Direct bank payment, company, SEB, Sweden.
PaymentMethod::DBSHBSE = Direct bank payment, Handelsbanken, Sweden.
PaymentMethod::DBSWEDBANKSE = Direct bank payment, Swedbanke, Sweden.
PaymentMethod::KORTCERT = Card payments, Certitrade.
PaymentMethod::PAYPAL = Paypal
PaymentMethod::SKRILL = Card payment with Dankort, Skrill.

PaymentMethod::SVEAINVOICESE = Invoice by PayPage in SE only.
PaymentMethod::SVEASPLITSE = PaymentPlan by PayPage in SE only.
PaymentMethod::SVEAINVOICEEU_SE = Invoice by PayPage in SE.
PaymentMethod::SVEAINVOICEEU_NO = Invoice by PayPage in NO.
PaymentMethod::SVEAINVOICEEU_DK = Invoice by PayPage in DK.
PaymentMethod::SVEAINVOICEEU_FI = Invoice by PayPage in FI.
PaymentMethod::SVEAINVOICEEU_NL = Invoice by PayPage in NL.
PaymentMethod::SVEAINVOICEEU_DE = Invoice by PayPage in DL.
PaymentMethod::SVEASPLITEU_SE = PaymentPlan by PayPage in SE.
PaymentMethod::SVEASPLITEU_NO = PaymentPlan by PayPage in NO.
PaymentMethod::SVEASPLITEU_DK = PaymentPlan by PayPage in DK.
PaymentMethod::SVEASPLITEU_FI = PaymentPlan by PayPage in FI.
PaymentMethod::SVEASPLITEU_DE = PaymentPlan by PayPage in SE.
PaymentMethod::SVEASPLITEU_NL = PaymentPlan by PayPage in NL.