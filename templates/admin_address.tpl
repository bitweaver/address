{* $Header: /cvsroot/bitweaver/_bit_address/templates/admin_address.tpl,v 1.2 2010/01/14 21:49:42 dansut Exp $ *}
{strip}
{form}
	<input type="hidden" name="page" value="{$page}" />
	{jstabs}
		{jstab title="Listing"}
			{legend legend="Address List Field Visibility Settings"}
				{formfields fields=$list_fields grpname=$list_grpname}
			{/legend}
		{/jstab}

		{jstab title="Country"}
			{legend legend="Country Settings"}
				{formfields fields=$country_fields errors=$errors grpname=$country_grpname}
			{/legend}
		{/jstab}

		<div class="row submit">
			<input type="submit" name="{$grpname}_submit" value="{tr}Change preferences{/tr}" />
		</div>
	{/jstabs}
{/form}
{/strip}
