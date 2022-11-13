{capture assign='pageTitle'}{$topic}{/capture}

{capture assign='contentTitle'}{$topic}{/capture}

{capture assign='contentHeader'}
	<header class="contentHeader topicHeader"
			data-object-id="{@$topic->topicID}"
			data-topic-id="{@$topic->topicID}"
			data-is-done="{if $topic->isDone}true{else}false{/if}"
			data-is-closed="{if $topic->isClosed}true{else}false{/if}"
			data-is-deleted="{if $topic->isDeleted}true{else}false{/if}">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">{@$contentTitle}{if !$contentTitleBadge|empty} {@$contentTitleBadge}{/if}</h1>
            {if !$contentDescription|empty}<p class="contentHeaderDescription">{@$contentDescription}</p>{/if}

			<ul class="inlineList contentHeaderMetaData">
                {event name='beforeMetaData'}

				<li itemprop="author" itemscope itemtype="http://schema.org/Person">
					<span class="icon icon16 fa-user"></span>

                    {if $topic->userID}
                        {user object=$topic->getUserProfile()}
                    {else}
						<span>{$topic->username}</span>
                    {/if}
				</li>

				<li>
					<span class="icon icon16 fa-clock-o"></span>
                    {@$topic->time|time}
					<meta itemprop="dateCreated" content="{@$topic->time|date:'c'}">
					<meta itemprop="datePublished" content="{@$topic->time|date:'c'}">
					<meta itemprop="operatingSystem" content="N/A">
				</li>

				<li class="jsMarkAsDone" data-object-id="{@$topic->topicID}">
                    {if $topic->isDone}
						<span class="icon icon16 fa-check-square-o" data-tooltip="{lang}community.topic.isDone{/lang}"
							  aria-label="{lang}community.topic.isDone{/lang}"></span>
						<span class="doneTitle">{lang}community.topic.isDone{/lang}</span>
                    {else}
						<span class="icon icon16 fa-square-o" data-tooltip="{lang}community.topic.isUndone{/lang}"
							  aria-label="{lang}community.topic.isUndone{/lang}"></span>
						<span class="doneTitle">{lang}community.topic.isUndone{/lang}</span>
                    {/if}
				</li>

                {event name='afterMetaData'}
			</ul>
		</div>

        {hascontent}
			<nav class="contentHeaderNavigation">
				<ul>
                    {content}
                    {if !$contentHeaderNavigation|empty}{@$contentHeaderNavigation}{/if}

                    {event name='contentHeaderNavigation'}
                    {/content}
				</ul>
			</nav>
        {/hascontent}
	</header>
{/capture}

{include file='header'}

<div class="section">
    {include file='topicMessage' application='community'}
    {include file='topicComments' application='community'}
</div>

{event name='beforeContentFooter'}

<footer class="contentFooter">
    {hascontent}
		<nav class="contentFooterNavigation">
			<ul>
                {content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
    {/hascontent}
</footer>

{event name='additionalJavascript'}

{include file='footer'}
