{strip}
<ul>
	{if $gBitUser->hasPermission( 'p_address_view' )}
		<li><a class="item" href="{$smarty.const.ADDRESS_PKG_URL}">{biticon ipackage="icons" iname="format-justify-fill" iexplain="List Addresses" iforce="icon"}&nbsp;{tr}List&nbsp;Addresses{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_address_create' )}
		<li><a class="item" href="{$smarty.const.ADDRESS_PKG_URL}edit.php">{biticon ipackage="icons" iname="document-new" iexplain="Create Address" iforce="icon"}&nbsp;{tr}Create&nbsp;Address{/tr}</a></li>
	{/if}
</ul>
{/strip}
