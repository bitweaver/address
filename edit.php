<?php
// $Header: /cvsroot/bitweaver/_bit_address/edit.php,v 1.2 2009/11/11 15:19:44 dansut Exp $

// Initialization
require_once('../bit_setup_inc.php');

// Is package installed and enabled
$gBitSystem->verifyPackage('address');

require_once(ADDRESS_PKG_PATH.'lookup_inc.php');

// Now check permissions to access this page
if($gContent->isValid()) {
	$gContent->verifyUpdatePermission();
} else {
	$gContent->verifyCreatePermission();
}

// Set up access to edit services needed by Address
$gContent->invokeServices('content_edit_function');

// Check if the page has changed
if(!empty($_REQUEST["save_address"])) {
	// Check if all Request values are delivered, and if not, set them to avoid error messages.
	// This can happen if some features are disabled
	if($gContent->store($_REQUEST['address'])) {
		bit_redirect($gContent->getDisplayUrl());
	} else {
		$gBitSmarty->assign_by_ref('errors', $gContent->mErrors);
	}
}

// Load field attributes
$gBitSmarty->assign_by_ref('fields', $gContent->getFields());

// Display the template
$gBitSystem->display('bitpackage:address/edit.tpl', tra('Address'), array('display_mode' => 'edit'));
?>
