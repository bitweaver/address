{* $Header: /cvsroot/bitweaver/_bit_address/templates/list.tpl,v 1.2 2009/12/09 21:59:53 dansut Exp $ *}
{strip}
{assign var=awidth value=85}
{if $gBitSystem->isFeatureActive( 'address_list_country' )}
	{assign var=cwidth value=10}
{/if}
{if $gBitSystem->isFeatureActive( 'address_list_description' )}
	{assign var=dwidth value=15}
{/if}
<div class="floaticon">
  {bithelp}
	{assign var=iconsize value=$gBitSystem->getConfig("site_icon_size")}
	{biticon ipackage="address" iname="pkg_address" iexplain="address" iclass="$iconsize icon"}
</div>

<div class="listing address">
	<div class="header">
		<h1>{tr}Addresses{/tr}</h1>
	</div>

	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<table class="data">
				<tr>
					<th width="5%" class="listright">{smartlink ititle="Id" isort=address_id offset=$control.offset iorder=address_id idefault=1}</th>
					<th width="{$awidth-$cwidth-$dwidth}%" class="listleft">{smartlink ititle="Address" isort=town iatitle="Town" offset=$control.offset}</th>
					{if $cwidth}<th width="{$cwidth}%" class="listcntr">{smartlink ititle="Country" isort=country offset=$control.offset}</th>{/if}
					{if $dwidth}<th width="{$dwidth}%" class="listleft">{smartlink ititle="Description" isort=title offset=$control.offset}</th>{/if}
					<th width="10%" class="listright">{tr}Actions{/tr}</th>
				</tr>
				{foreach item=address from=$addressList}
					{assign var=id value=`$address.id`}
					<tr class="{cycle values="even,odd"}">
						<td class="listright"><a href="{$address.display_url}" title="{$id}">{$id}</a></td>
						<td class="listleft">{$address.text|escape}</td>
						{if $cwidth}<td class="listcntr">{$address.country|escape}</td>{/if}
						{if $dwidth}<td class="listleft">{$address.title|escape}</td>{/if}
						<td class="actionicon">
						{if $gBitUser->hasPermission( 'p_address_update' )}
							<a title="{tr}Edit{/tr}" href="{$address.edit_url}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Edit Address"}</a>
						{/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="16">{tr}No records found{/tr}</td></tr>
				{/foreach}
			</table>

		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
