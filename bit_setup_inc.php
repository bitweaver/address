<?php
global $gBitSystem, $gBitThemes;

$registerHash = array(
	'package_name' => 'address',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => FALSE,
);
$gBitSystem->registerPackage( $registerHash );

// If package is active then setup up some stuff for actual package usage
if($gBitSystem->isPackageActive('address')) {
	// If the user has view auth then ...
	if($gBitUser->hasPermission('p_address_view')) {
		// Register the package menu
		$menuHash = array(
			'package_name'  => ADDRESS_PKG_NAME,
			'index_url'     => ADDRESS_PKG_URL.'index.php',
			'menu_template' => 'bitpackage:address/menu_address.tpl',
		);
		$gBitSystem->registerAppMenu($menuHash);
	}

	$gBitThemes->loadCss(ADDRESS_PKG_PATH.'bit_pkgstyle.css');
}
?>
