<form action="{$edit_url}&save=1" method="post">

    <p><label for="channel">Channel to post to: </label><select id="channel" name="channel">
        {foreach from=$channels key='channel_id' item='channel_name'}
            <option value="{$channel_id|escape}"{if $channel_id==$config.channel} selected{/if}>{$channel_name|escape}</option>
        {/foreach}
    </select></p>

    <p><label for="botname">Bot name: </label><input id="botname" type="text" name="botname" value="{$config.botname|escape}" /></p>

    <p><input type="submit" value="Save Changes" class="btn" /></p>

</form>