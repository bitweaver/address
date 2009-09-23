<?php
global $gContent;
require_once(ADDRESS_PKG_PATH.'BitAddress.php');

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if(empty($gContent) || !is_object($gContent) || !$gContent->isValid()) {
	// if address_id supplied, use that
	if(@BitBase::verifyId($_REQUEST['address_id'])) {
		$gContent = new BitAddress($_REQUEST['address_id']);
	} elseif(@BitBase::verifyId($_REQUEST['content_id'])) {
		$gContent = new BitAddress( NULL, $_REQUEST['content_id']);
	} elseif(@BitBase::verifyId($_REQUEST['address']['address_id'])) {
		$gContent = new BitAddress($_REQUEST['address']['address_id']);
	} else { // otherwise create new empty object
		$gContent = new BitAddress();
	}

	$gContent->load();
	$gBitSmarty->assign_by_ref("gContent", $gContent);
}
?>
