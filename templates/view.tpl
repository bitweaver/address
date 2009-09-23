{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$gContent->mInfo}
<div class="display address">
	<div class="floaticon">
		{if $print_page ne 'y'}
			{if $gContent->hasUpdatePermission()}
				<a title="{tr}Edit this address{/tr}" href="{$smarty.const.ADDRESS_PKG_URL}edit.php?address_id={$gContent->mInfo.address_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Edit Address"}</a>
			{/if}
		{/if}<!-- end print_page -->
		{assign var=iconsize value=$gBitSystem->getConfig("site_icon_size")}
		{biticon ipackage="address" iname="pkg_address" iexplain="address" iclass="$iconsize icon"}
	</div><!-- end .floaticon -->

	<div class="header">
		<h1>Address: {$gContent->mInfo.title|escape}</h1>
		<div class="date">
			{tr}Created by{/tr}: {displayname user=$gContent->mInfo.creator_user user_id=$gContent->mInfo.creator_user_id real_name=$gContent->mInfo.creator_real_name}, {tr}Last modification by{/tr}: {displayname user=$gContent->mInfo.modifier_user user_id=$gContent->mInfo.modifier_user_id real_name=$gContent->mInfo.modifier_real_name}, {$gContent->mInfo.last_modified|bit_long_datetime}
		</div>
		<br />
	</div><!-- end .header -->

	<div class="body">
		<h2>{$address|escape}</h2>
		<hr />
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$gContent->mInfo}
			{$gContent->mInfo.parsed_data}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .address -->
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
