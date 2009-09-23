<?php
// @version $header$
global $gBitInstaller;

$infoHash = array(
	'package' => ADDRESS_PKG_NAME,
	'version' => str_replace('.php', '', basename( __FILE__ )),
	'description' => "Initial version of the address package",
	'post_upgrade' => NULL,
);

$gBitInstaller->registerPackageUpgrade($infoHash, array(
// Empty
)); // registerPackageUpgrade
?>
