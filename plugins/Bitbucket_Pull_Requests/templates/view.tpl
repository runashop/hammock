{if $smarty.get.newtoken}
	<p class="alert">Your token has been updated - the webhook URL has changed!</p>
{/if}

<p>Go to your repo's settings page and add this hook URL:</p>

<p><code>{$hook_url}</code></p>

<p><b>Post to channel:</b> {$config.channel_name|escape}</p>
<p><b>Bot name:</b> {$config.botname|escape}</p>

<p><a href="{$edit_url}" class="btn">Edit settings</a></p>