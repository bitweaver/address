{* $Header$ *}
{strip}
<div class="floaticon">
	{bithelp}
	{if $gContent->hasExpungePermission() && $gContent->isValid()}
		<a title="{tr}Remove this Address{/tr}" href="{$gContent->getRemoveUrl()}">{booticon iname="icon-trash" ipackage="icons" iexplain="Remove Address"}</a>
	{/if}
	{assign var=iconsize value=$gBitSystem->getConfig("site_icon_size")}
	{biticon ipackage="address" iname="pkg_address" iexplain="address" iclass="$iconsize icon"}
</div>

<div class="admin address">
	<div class="header">
		<h1>{if $gContent->isValid()}{tr}Edit{/tr}{else}{tr}Create New{/tr}{/if} {tr}Address{/tr}</h1>
	</div>

	<div class="body">
		{form enctype="multipart/form-data" id="editaddressform"}
			{jstabs}
				{jstab title="Address"}
					{legend legend="Location Details"}
						<input type="hidden" name="address[address_id]" value="{$gContent->mInfo.address_id}" />
						{formfeedback warning=$errors.store}

						{formfields fields=$fields errors=$errors grpname="address"}

						<div class="control-group">
							{formfeedback warning=$errors.title}
							{formlabel label="Description" for="title"}
							{forminput}
								<input type="text" size="50" name="address[title]" id="title" value="{$gContent->mInfo.title|escape}" />
								{formhelp note="Text that could be useful to help identify the address later."}
							{/forminput}
						</div>

					{/legend}
				{/jstab}

				{jstab title="Notes"}
					{legend legend="Optional expanded details"}
						{textarea name="address[edit]" edit=$gContent->mInfo.data}
						{* any simple service edit options *}
						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}
					{/legend}
				{/jstab}

				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl"}
			{/jstabs}
			<div class="control-group submit">
				<input type="submit" class="btn btn-default" name="save_address"
					value="{tr}{if $gContent->mInfo.address_id}Update{else}Create{/if} Address{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .address -->

{/strip}
