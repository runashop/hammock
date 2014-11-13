{$pr.author.display_name} created pull request: <{$pr.link}|{$pr.title}>{if count($pr.reviewers) > 0} and added reviewers:
{foreach from=$pr.reviewers item='reviewer'}
    {$reviewer.display_name}
{/foreach}{/if}