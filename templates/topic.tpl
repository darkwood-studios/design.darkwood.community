{capture assign='pageTitle'}{$topic}{/capture}

{capture assign='contentTitle'}{$topic}{/capture}

{capture assign='contentHeader'}
	<header class="contentHeader topicHeader"
		data-object-id="{@$topic->topicID}"
		data-topic-id="{@$topic->topicID}"
		data-is-done="{if $topic->isDone}true{else}false{/if}"
		data-is-closed="{if $topic->isClosed}true{else}false{/if}"
		data-is-deleted="{if $topic->isDeleted}true{else}false{/if}"
	>
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
						<span class="icon icon16 fa-check-square-o" data-tooltip="{lang}design.darkwood.community.isDone{/lang}" aria-label="{lang}design.darkwood.community.isDone{/lang}"></span>
						<span class="doneTitle">{lang}design.darkwood.community.isDone{/lang}</span>
					{else}
						<span class="icon icon16 fa-square-o" data-tooltip="{lang}design.darkwood.community.isUndone{/lang}" aria-label="{lang}design.darkwood.community.isUndone{/lang}"></span>
						<span class="doneTitle">{lang}design.darkwood.community.isUndone{/lang}</span>
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


{include file='__commentJavaScript' commentContainerID='topicCommentList'}

<div class="section">
    <ul id="topicCommentList" class="commentList containerList" {*
        *}data-can-add="{if $commentCanAdd}true{else}false{/if}" {*
        *}data-object-id="{@$topic->topicID}" {*
        *}data-object-type-id="{@$commentObjectTypeID}" {*
        *}data-comments="{if $topic->comments}{@$commentList->countObjects()}{else}0{/if}" {*
        *}data-last-comment-time="{@$lastCommentTime}" {*
    *}>

            <li class="comment">
            <div class="box48{if $__wcf->getUserProfileHandler()->isIgnoredUser($topic->userID, 2)} ignoredUserContent{/if}">
                {user object=$topic->getUserProfile() type='avatar48' ariaHidden='true' tabindex='-1'}
                
                <div class="commentContentContainer">
                    <div class="commentContent">
                        <meta itemprop="dateCreated" content="{@$topic->time|date:'c'}">
                        
                        <div class="containerHeadline">
                            <h3 itemprop="author" itemscope itemtype="http://schema.org/Person">
                                {if $topic->userID}
                                    <a href="{$topic->getUserProfile()->getLink()}" class="userLink" data-object-id="{@$topic->userID}" itemprop="url">
                                        <span itemprop="name">{@$topic->getUserProfile()->getFormattedUsername()}</span>
                                    </a>
                                {else}
                                    <span itemprop="name">{$topic->username}</span>
                                {/if}
                                
                                <small class="separatorLeft">{@$topic->time|time}</small>
                                
                                {if $topic->isDone}
                                    <span class="badge label green jsIconDisabled">{lang}design.darkwood.community.isDone{/lang}</span>
                                {/if}

                                {if $topic->isDeleted}
                                    <span class="badge label red jsIconDisabled">{lang}design.darkwood.community.isDeleted{/lang}</span>
                                {/if}

                                {if $topic->isClosed}
                                    <span class="badge label blue jsIconDisabled">{lang}design.darkwood.community.isClosed{/lang}</span>
                                {/if}
                            </h3>
                        </div>
                        
                        <div class="htmlContent userMessage" itemprop="text">{@$topic->getFormattedMessage()}</div>

                        {* {if MODULE_LIKE && $commentManager->supportsLike() && $likeData|isset}{include file="reactionSummaryList" isTiny=true reactionData=$likeData[comment] objectType="com.woltlab.wcf.comment" objectID=$comment->commentID}{else}<a href="#" class="reactionSummaryList reactionSummaryListTiny jsOnly" data-object-type="com.woltlab.wcf.comment" data-object-id="{$comment->commentID}" title="{lang}wcf.reactions.summary.listReactions{/lang}" style="display: none;"></a>{/if} *}
                        
                        <nav class="jsMobileNavigation buttonGroupNavigation">
                            <ul class="buttonList iconList">
                                {*{if $comment->isDisabled && $commentCanModerate}
                                    <li class="jsOnly"><a href="#" class="jsEnableComment">{icon size=16 name='check'} <span class="invisible">{lang}wcf.comment.approve{/lang}</span></a></li>
                                {/if}
                                {if $commentManager->supportsReport() && $__wcf->session->getPermission('user.profile.canReportContent')}
                                    <li class="jsReportCommentComment jsOnly" data-object-id="{@$comment->commentID}"><a href="#" title="{lang}wcf.moderation.report.reportContent{/lang}" class="jsTooltip">{icon size=16 name='triangle-exclamation'} <span class="invisible">{lang}wcf.moderation.report.reportContent{/lang}</span></a></li>
                                {/if}
                                
                                {if MODULE_LIKE && $commentManager->supportsLike() && $__wcf->session->getPermission('user.like.canLike') && $comment->userID != $__wcf->user->userID}
                                    <li class="jsOnly"><a href="#" class="reactButton jsTooltip {if $likeData[comment][$comment->commentID]|isset && $likeData[comment][$comment->commentID]->reactionTypeID} active{/if}" title="{lang}wcf.reactions.react{/lang}" data-reaction-type-id="{if $likeData[comment][$comment->commentID]|isset && $likeData[comment][$comment->commentID]->reactionTypeID}{$likeData[comment][$comment->commentID]->reactionTypeID}{else}0{/if}">{icon size=16 name='face-smile'} <span class="invisible">{lang}wcf.reactions.react{/lang}</span></a></li>
                                {/if}*}
                                
                                {event name='commentOptions'}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </li>

        {if $commentList|count}{include file='commentList'}{/if}
        {if $commentCanAdd}{include file='commentListAddComment' wysiwygSelector='topicCommentListAddComment'}{/if}
    </ul>
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
