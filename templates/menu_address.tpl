{strip}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	{if $gBitUser->hasPermission( 'p_address_view' )}
		<li><a class="item" href="{$smarty.const.ADDRESS_PKG_URL}">{booticon iname="icon-list" ipackage="icons" iexplain="List Addresses" iforce="icon"}&nbsp;{tr}List&nbsp;Addresses{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_address_create' )}
		<li><a class="item" href="{$smarty.const.ADDRESS_PKG_URL}edit.php">{booticon iname="icon-file" ipackage="icons" iexplain="Create Address" iforce="icon"}&nbsp;{tr}Create&nbsp;Address{/tr}</a></li>
	{/if}
</ul>
{/strip}
