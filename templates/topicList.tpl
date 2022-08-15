<div class="section tabularBox messageGroupList communityTopicList jsClipcategoryContainer"
	 data-type="design.darkwood.community.topic">
	<ol class="tabularList">

        {foreach from=$objects item=topic}
			<li class="tabularListRow">
				<ol
						id="topic{@$topic->topicID}"
						class="tabularListColumns messageGroup communityTopic jsClipcategoryObject{if $topic->isNew()} new{/if}{if $topic->isDeleted} messageDeleted{/if}{if $__wcf->getUserProfileHandler()->isIgnoredUser($topic->userID)} ignoredUserContent{/if}"
						data-topic-id="{@$topic->topicID}"
						data-element-id="{@$topic->topicID}"
						data-is-closed="{@$topic->isClosed}"
						data-is-deleted="{@$topic->isDeleted}"
						data-is-done="{@$topic->isDone}">


					<li class="columnIcon columnAvatar">
						<div>
							<p{if $topic->isNew()} title="{lang}community.topic.markAsRead.doubleClick{/lang}"{/if}>{@$topic->getUserProfile()->getAvatar()->getImageTag(48)}</p>
						</div>
					</li>

					<li class="columnSubject">
                        {if $topic->hasLabels()}
							<ul class="labelList">
                                {foreach from=$topic->getLabels() item=label}
									<li>
										<a href="{link application='community' controller='Category' object=$topic->getCategory()}labelIDs[{@$label->groupID}]={@$label->labelID}{/link}"
										   class="badge label{if $label->getClassNames()} {$label->getClassNames()}{/if} jsTooltip"
										   title="{lang}community.topic.labeledTopics{/lang}">{$label->getTitle()}</a>
									</li>
                                {/foreach}
							</ul>
                        {/if}

						<h3>

							<a href="{$topic->getLink()}"
							   class="messageGroupLink communityTopicLink"
							   data-topic-id="{@$topic->topicID}">{$topic->subject}</a>

                            {if $topic->comments}
								<span class="badge messageGroupCounterMobile">{@$topic->comments|shortUnit}</span>
                            {/if}
						</h3>

						<aside class="statusDisplay" role="presentation">
							<ul class="inlineList statusIcons">
                                {if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && $topic->cumulativeLikes}
									<li>{include file='__topReaction' cachedReactions=$topic->cachedReactions render='short'}</li>{/if}
                                {if $topic->isClosed}
									<li><span
											class="icon icon16 fa-lock jsIconClosed jsTooltip"
											title="{lang}community.topic.closed{/lang}"></span>
									</li>{/if}

								<li>
                                    {if $topic->isDone}
										<span class="icon icon16 fa-check-square-o jsTooltip jsMarkAsDone"
											  title="{lang}community.topic.done{/lang}"></span>
                                    {else}
										<span class="icon icon16 fa-square-o jsTooltip jsMarkAsDone"
											  title="{lang}community.topic.undone{/lang}"></span>
                                    {/if}
								</li>
							</ul>
						</aside>

						<ul class="inlineList dotSeparated small messageGroupInfo">
							<li class="messageGroupAuthor">{if $topic->userID}<a
									href="{link controller='User' object=$topic->getUserProfile()->getDecoratedObject()}{/link}"
									class="userLink"
									data-user-id="{@$topic->userID}">{$topic->username}</a>{else}{$topic->username}{/if}
							</li>
							<li class="messageGroupTime">{@$topic->time|time}</li>
							<li class="communityTopicCategoryLink"><a
										href="{link application='community' controller='Category' object=$topic->getCategory()}{/link}">{$topic->getCategory()->getTitle()}</a>
							</li>
						</ul>

						<ul class="messageGroupInfoMobile">
							<li class="messageGroupAuthorMobile">{$topic->username}</li>
							<li class="messageGroupLastPostTimeMobile">{@$topic->lastPostTime|time}</li>
						</ul>
					</li>
					<li class="columnStats">
						<dl class="plain statsDataList">
							<dt>{lang}community.topic.replies{/lang}</dt>
							<dd>{@$topic->comments|shortUnit}</dd>
						</dl>
						<dl class="plain statsDataList">
							<dt>{lang}community.topic.views{/lang}</dt>
							<dd>{@$topic->views|shortUnit}</dd>
						</dl>

						<div class="messageGroupListStatsSimple">{if $topic->comments}<span
								class="icon icon16 fa-comment-o"
								aria-label="{lang}community.topic.replies{/lang}"></span> {@$topic->comments|shortUnit}{/if}
						</div>
					</li>

					<li class="columnLastPost">
                        {if $topic->comments != 0}
							<div class="box32{if $__wcf->getUserProfileHandler()->isIgnoredUser($topic->getLastCommentUserProfile()->userID)} ignoredUserContent{/if}">
								<a href="{link application='community' controller='Topic' object=$topic}action=lastPost{/link}"
								   class="jsTooltip"
								   title="{lang}community.topic.gotoLastComment{/lang}">{@$topic->getLastCommentUserProfile()->getAvatar()->getImageTag(32)}</a>
								<div>
									<p>
                                        {if $topic->lastCommentUserID}
											<a href="{link controller='User' object=$topic->getLastCommentUserProfile()->getDecoratedObject()}{/link}"
											   class="userLink"
											   data-user-id="{@$topic->getLastCommentUserProfile()->userID}">{$topic->lastCommentUsername}</a>
                                        {else}
                                            {$topic->lastCommentUsername}
                                        {/if}
									</p>
									<small>{@$topic->lastCommentTime|time}</small>
								</div>
							</div>
                        {/if}
					</li>
				</ol>
			</li>
        {/foreach}

	</ol>
</div>
