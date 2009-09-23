<?php
// Initialization
require_once('../bit_setup_inc.php');

// Is package installed and enabled
$gBitSystem->verifyPackage('address');

// Check permissions to access this page before even try to get content
$gBitSystem->verifyPermission('p_address_view');

// Attempt to get content if specified, otherwise will just initialise a $gContent
require_once(ADDRESS_PKG_PATH.'lookup_inc.php');

// If there is an address has been specified by id, then display just it
if(!empty($_REQUEST['address_id']) || !empty($_REQUEST['content_id'])) {
	if(!$gContent->isValid()) { // If the content failed to load
		$gBitSystem->setHttpStatus(404);
		if(!empty($_REQUEST['address_id'])) { // Specified by address ID
			$gBitSystem->fatalError(tra("The requested address (id=".$_REQUEST['address_id'].") has invalid content."));
		} else { // Specified by content ID
			$gBitSystem->fatalError(tra("The requested content (id=".$_REQUEST['content_id'].") has invalid address content."));
		}
	}

	$gContent->verifyViewPermission();
	$gContent->addHit();

	$address = $gContent->getDataShort();
	if($country_id = $gContent->mInfo['country_id']) {
		if(!empty($address)) $address .= ', ';
		$address .= BitAddressCountry::getText($country_id, 'isocode3');
	}
	$gBitSmarty->assign_by_ref('address', $address);

	$gBitSystem->display('bitpackage:address/view.tpl', tra('Address'), array('display_mode' => 'display'));

} else { // List the available addresses
	$addressList = $gContent->getList($_REQUEST);
	$gBitSmarty->assign_by_ref('addressList', $addressList);
	// getList() places all the pagination information in $_REQUEST['listInfo']
	$gBitSmarty->assign_by_ref('listInfo', $_REQUEST['listInfo']);

	$gBitSystem->display('bitpackage:address/list.tpl', tra('Address'), array('display_mode' => 'list'));
}
?>
