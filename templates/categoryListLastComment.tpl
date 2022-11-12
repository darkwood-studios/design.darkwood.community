<aside class="communityLastComment">
	<div class="box32">
        {user object=$comment.userProfile type='avatar32' ariaHidden='true' tabindex='-1'}

		<div>
			<p>
				<a href="{link application='community' controller='Topic' id=$comment.topicID}#comment={$comment.commentID}{/link}" class="communityTopicLink" data-topic-id="{$comment.topicID}">{$comment.subject}</a>
			</p>
			<small>
                {if $comment.userID}
					<a href="{link controller='User' id=$comment.userID title=$comment.username}{/link}" class="communityLastCommentAuthor userLink" data-user-id="{$comment.userID}">{$comment.username}</a>
                {else}
					<span class="wbbLastPostAuthor">{$comment.username}</span>
                {/if}

				<span class="separatorLeft">{@$comment.time|time}</span>
			</small>
		</div>
	</div>
</aside>