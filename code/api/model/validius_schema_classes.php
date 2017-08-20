<?php

// For testing
class DummySoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return $request;
    }
}

class Login {
	function Login($customerId, $securityKey/*, $userLogin*/) {
		$this->customerid = $customerId;
		$this->securitykey = $securityKey;
		// $this->userlogin = $userLogin;
	}
}

// <xsd:element name="reference" type="tns:string_1_25" minOccurs="0" />
// <xsd:element name="customernumber" type="tns:string_1_35" minOccurs="0" />
// <xsd:element name="invoicenumber" type="tns:string_1_20" minOccurs="0" />
// <xsd:element name="original_invoicenumber" type="tns:string_1_20" minOccurs="0" />
// <xsd:element name="ordernumber" type="tns:string_1_35" minOccurs="0" />
// <xsd:element name="ourcode" type="tns:string_1_35" minOccurs="0" />
// <xsd:element name="yourcode" type="tns:string_1_35" minOccurs="0" />
// <xsd:element name="freetext" type="tns:string_1_512" minOccurs="0" />
// <xsd:element name="language" type="tns:invoiceLanguageType" minOccurs="0" />
// <xsd:element name="invoicedate" type="xsd:date" />
// <xsd:element name="duedate" type="xsd:date" />
// <xsd:element name="vatamount" type="xsd:decimal" />
// <xsd:element name="netamount" type="xsd:decimal" />
// <xsd:element name="amount" type="xsd:decimal" />
// <xsd:element name="overdue_interest" type="tns:decimal_0_99" />
// <xsd:element name="payment_control" type="xsd:boolean" />
// <xsd:element name="sendtype" type="tns:sendtypeType" />
// <xsd:element name="autoconfirm" type="xsd:boolean" />
// <xsd:element name="senderref" type="tns:string_1_35" minOccurs="0" />


class Invoice {

	function Invoice(
		$invoicedate,
		$duedate,
		$vatamount,
		$netamount,
		$amount,
		$overdue_interest,
		$payment_control,
		$sendtype,
		$autoconfirm,
		$ourcode,
		$invoiceByerAddress,
		$invoicerow = []
		) {
		
			$this->invoicedate = $invoicedate;
			$this->duedate = $duedate;
			$this->vatamount = $vatamount;
			$this->netamount = $netamount;
			$this->amount = $amount;
			$this->overdue_interest = $overdue_interest;
			$this->payment_control = $payment_control;
			$this->sendtype = $sendtype;
			$this->autoconfirm = $autoconfirm;
			$this->ourcode = $ourcode;
			$this->invoice_buyer_address = $invoiceByerAddress;
			$this->invoicerow = $invoicerow;
	}

	function addInvoiceRow($invoicerow) {
		$this->invoicerow[] = $invoicerow;
	}

}

class InvoiceBuyerAddress {

	function InvoiceBuyerAddress($name, $street, $postcode, $city, $email, $customertype = "person") {
		$this->name = $name;
		$this->street = $street;
		$this->postcode = $postcode;
		$this->city = $city;
		$this->email = $email;
		$this->customertype = $customertype;
	}
}

class InvoiceRow {

	function InvoiceRow(
		$productname,
		$invoiced_quantity,
		$unitprice,
		$discountpercent,
		$vatpercent,
		$vatamount,
		$netamount,
		$amount,
		$freetext = null,
		$productcode = "UK1",
		$invoiced_quantity_type = "kpl"
		) {

		$this->productcode = $productcode;
		$this->productname = $productname;
		$this->freetext = $freetext;
		$this->invoiced_quantity = $invoiced_quantity;
		$this->invoiced_quantity_type = $invoiced_quantity_type;
		$this->unitprice = $unitprice;
		$this->discountpercent = $discountpercent;
		$this->vatpercent = $vatpercent;
		$this->vatamount = $vatamount;
		$this->netamount = $netamount;
		$this->amount = $amount;
	}
}

class SaveInvoiceIn {
	function SaveInvoiceIn($login, $invoice) {
		$this->login = $login;
		$this->invoice = $invoice;
	}
}

?>