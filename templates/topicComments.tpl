{include file='__commentJavaScript' commentContainerID='topicCommentList' commentHandlerClass='Community.TopicComment.Handler'}

<ul id="topicCommentList" class="commentList containerList" {*
        *}data-can-add="{if $commentCanAdd}true{else}false{/if}" {*
        *}data-object-id="{@$topic->topicID}" {*
        *}data-object-type-id="{@$commentObjectTypeID}" {*
        *}data-comments="{if $topic->comments}{@$commentList->countObjects()}{else}0{/if}" {*
        *}data-last-comment-time="{@$lastCommentTime}" {*
    *}>

    {if $commentList|count}{include file='commentList'}{/if}
    {if $commentCanAdd}{include file='commentListAddComment' wysiwygSelector='topicCommentListAddComment'}{/if}
</ul>