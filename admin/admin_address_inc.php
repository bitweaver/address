<?php
// @version $Header: /cvsroot/bitweaver/_bit_address/admin/admin_address_inc.php,v 1.1 2009/09/23 15:16:44 spiderr Exp $
require_once(ADDRESS_PKG_PATH.'BitAddress.php');

$formAddressLists = array(
	"address_list_country" => array(
		'label' => 'Country',
		'note' => 'Display the country.',
	),
	"address_list_description" => array(
		'label' => 'Description',
		'note' => 'Display the address description field.',
	),
);
$gBitSmarty->assign('formAddressLists', $formAddressLists);

// Process the form if we've made some changes
if(!empty($_REQUEST['address_settings'])) {
	$addressToggles = array_merge($formAddressLists);
	foreach($addressToggles as $item => $data) {
		simple_set_toggle($item, ADDRESS_PKG_NAME);
	}
	@BitAddressCountry::setDefault($_REQUEST['address']['country_def']);
	@BitAddressCountry::setActive($_REQUEST['address']['countries']);
}

$countries = @BitAddressCountry::getPossibles('country_name');
$fields = array(
	"country_def" => array(
		"description" => "Default Country",
		"type" => "options",
		"options" => $countries,
		"value" => @BitAddressCountry::getDefault(),
		"required" => TRUE,
		"helptext" => "This selects the country that is selected by default when a new address is created."
	),
	"countries" => array(
		"description" => "Available Countries",
		"type" => "options",
		"typopt" => "multiple",
		"options" => $countries,
		"value" => array_keys(BitAddressCountry::getPossibles('isocode2', TRUE)),
		"required" => TRUE,
		"helptext" => "Select the multiple countries available by holding down Shift/Ctrl."
	)
);
$gBitSmarty->assign_by_ref('fields', $fields);
?>
