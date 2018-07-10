<?php
namespace inkpro\wannafind;

class Order{
    public $Currency;
    public $CurrencyId;
    public $Customer;
    public $CustomerComment;
    public $CustomerId;
    public $DateDelivered;
    public $DateDue;
    public $DateSent;
    public $DateUpdated;
    public $Delivery;
    public $DeliveryComment;
    public $DeliveryId;
    public $DeliveryTime;
    public $DiscountCodes;
    public $Id;
    public $InvoiceNumber;
    public $LanguageISO;
    public $OrderComment;
    public $OrderCommentExternal;
    public $OrderLines;
    public $Origin;
    public $Packing;
    public $PackingId;
    public $Payment;
    public $PaymentId;
    public $ReferenceNumber;
    public $Site;
    public $Status;
    public $Total;
    public $TrackingCode;
    public $Transactions;
    public $User;
    public $UserId;
    public $Vat;

    function __construct($data){
        $data = (array)$data;
        foreach($data as $key=>$row){
            $this->$key = $row;
        }
    }
}