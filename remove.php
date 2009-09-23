<?php
/**
 * $Id: remove.php,v 1.1 2009/09/23 15:16:44 spiderr Exp $
 * @package address
 * @subpackage 
 */

// required setup
require_once('../bit_setup_inc.php');

$gBitSystem->verifyPackage('address');
include_once(ADDRESS_PKG_PATH.'lookup_inc.php');

if(!$gContent->isValid()) {
	$gBitSystem->fatalError("No address indicated");
}

$gContent->verifyExpungePermission();

if(isset($_REQUEST["confirm"])) {
	if($gContent->expunge()) {
		bit_redirect(BIT_ROOT_URL);
	} else {
		vd($gContent->mErrors);
	}
}

$gBitSystem->setBrowserTitle(tra('Confirm delete of: ').$gContent->getTitle());
$formHash['remove'] = TRUE;
$formHash['address_id'] = $_REQUEST['address_id'];
$msgHash = array(
	'label' => tra('Delete Address'),
	'confirm_item' => $gContent->getTitle(),
	'warning' => tra('This address will be completely deleted.<br />This cannot be undone!'),
);
$gBitSystem->confirmDialog($formHash, $msgHash);

?>
