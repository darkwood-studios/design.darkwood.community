{assign var='userProfile' value=$topic->getUserProfile()}
<article class="message messageSidebarOrientation{@$__wcf->getStyleHandler()->getStyle()->getVariable('messageSidebarOrientation')|ucfirst} jsClipboardObject jsMessage{if $userProfile->userOnlineGroupID} userOnlineGroupMarking{@$userProfile->userOnlineGroupID}{/if}">

	<meta itemprop="dateCreated" content="{@$topic->time|date:'c'}">

    {include file='messageSidebar' enableMicrodata=true}

	<div class="messageContent">
		<header class="messageHeader">
			<div class="messageHeaderBox">
				<ul class="messageHeaderMetaData">
					<li>
						<a href="{link application='community' controller='Topic' object=$topic}{/link}"
						   class="permalink messagePublicationTime">{@$topic->time|time}</a>
					</li>
				</ul>
			</div>
		</header>

		<div class="messageBody">
			<div>
				<div class="messageText">
                    {@$topic->getFormattedMessage()}
				</div>
			</div>
		</div>

		<footer class="messageFooter">
            {* todo *}
            {*include file='topicAttachments' application='community'*}

			<div class="messageFooterGroup">
				<ul class="messageFooterButtons buttonList smallButtons jsMobileNavigation">
                    {if $topic->canEdit()}
						<li class="dropdown">
							<a href="{link application='community' controller='TopicEdit' id=$topicID}{/link}"
							   title="Bearbeiten"
							   class="button">
								<span class="icon icon16 fa-pencil"></span>
								<span>Bearbeiten</span></a>
						</li>
                    {/if}
				</ul>
			</div>
		</footer>
	</div>
</article>
