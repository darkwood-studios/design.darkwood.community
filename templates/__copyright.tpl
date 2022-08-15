{if $__community->isActiveApplication()}
    {if 'COMMUNITY_COPYFREE'|defined && COMMUNITY_COPYFREE}{else}
		<div class="copyright">{lang}community.global.index.copyright{/lang}</div>
    {/if}
{/if}
