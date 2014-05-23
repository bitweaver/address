{* $Header$ *}
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

		<div class="control-group submit">
			<input type="submit" class="btn btn-default" name="{$grpname}_submit" value="{tr}Change preferences{/tr}" />
		</div>
	{/jstabs}
{/form}
{/strip}
