<?php

class BuyerPartyDetails {

			// <xs:element name="BuyerPartyIdentifier" type="genericStringType0_35" minOccurs="0"/>
			// <xs:element name="BuyerOrganisationName" type="genericStringType2_70" maxOccurs="unbounded"/>
			// <xs:element name="BuyerOrganisationDepartment" type="genericStringType0_35" minOccurs="0" maxOccurs="2"/>
			// <xs:element name="BuyerOrganisationTaxCode" type="genericStringType0_35" minOccurs="0"/>
			// <xs:element name="BuyerCode" type="PartyIdentifierType" minOccurs="0"/>
			// <xs:element name="BuyerPostalAddressDetails" type="BuyerPostalAddressDetailsType" minOccurs="0"/>

	function BuyerPartyDetails() {
		$this->BuyerPartyIdentifier = $BuyerPartyIdentifier;
		$this->BuyerOrganisationName = $BuyerOrganisationName;
		$this->BuyerOrganisationDepartment = $BuyerOrganisationDepartment;
		$this->BuyerOrganisationTaxCode = $BuyerOrganisationTaxCode;
		$this->BuyerCode = $BuyerCode;
		$this->BuyerPostalAddressDetails = $BuyerPostalAddressDetails;
	}
}

?>