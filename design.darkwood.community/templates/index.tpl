{capture assign='headerNavigation'}
	<li class="jsOnly"><a href="#" title="{lang}community.index.markAsRead{/lang}"
						  class="markAllAsReadButton jsTooltip"><span
					class="icon icon16 fa-check"></span> <span
					class="invisible">{lang}community.index.markAsRead{/lang}</span></a></li>
{/capture}

{include file='header'}

{include file='categoryList' application='community'}

{hascontent}
	<footer class="contentFooter">
		<nav class="contentFooterNavigation">
			<ul>
                {content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	</footer>
{/hascontent}

{include file='footer'}
