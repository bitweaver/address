<?php
/**
 * @version $Header$
 * @package address
 */
require_once(ADDRESS_PKG_PATH.'BitAddress.php');

$pkgname = ADDRESS_PKG_NAME;
$grpname = $pkgname.'_admin';
$gBitSmarty->assign('grpname', $grpname);
$list_grpname = $grpname.'_list';
$gBitSmarty->assign('list_grpname', $list_grpname);
$country_grpname = $grpname.'_country';
$gBitSmarty->assign('country_grpname', $country_grpname);

// Process the form if we've made some changes
if(isset($_REQUEST[$grpname.'_submit'])) {
	LibertyForm::storeConfigs($_REQUEST[$list_grpname], $pkgname);
	@BitAddressCountry::setDefault($_REQUEST[$country_grpname]['country_def']);
	@BitAddressCountry::setActive($_REQUEST[$country_grpname]['countries']);
}

$list_fields = array(
	"list_country" => array(
		'description' => 'Country',
		'helptext' => 'Display the country.',
		'type' => 'checkbox',
		'value' => $gBitSystem->getConfig($pkgname.'_list_country'),
	),
	"list_description" => array(
		'description' => 'Description',
		'helptext' => 'Display the address description field.',
		'type' => 'checkbox',
		'value' => $gBitSystem->getConfig($pkgname.'_list_description'),
	),
);
$gBitSmarty->assign('list_fields', $list_fields);

$countries = @BitAddressCountry::getPossibles('country_name');
$country_fields = array(
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
$gBitSmarty->assign_by_ref('country_fields', $country_fields);
?>
