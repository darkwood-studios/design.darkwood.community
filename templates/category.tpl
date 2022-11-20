{capture assign='pageTitle'}{$category->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}

{capture assign='contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">{$category->getTitle()}</h1>
            {hascontent}
				<p class="contentHeaderDescription">{content}{if $category->descriptionUseHtml}{@$category->description|language}{else}{$category->description|language}{/if}{/content}</p>
            {/hascontent}
		</div>

        {hascontent}
			<nav class="contentHeaderNavigation">
				<ul>
                    {content}
                        {include application='community' file='__topicAddButton'}
                    {/content}
				</ul>
			</nav>
        {/hascontent}
	</header>
{/capture}

{capture assign='contentInteractionButtons'}
    {if $__wcf->user->userID}
        <a href="#" class="contentInteractionButton jsSubscribeButton jsOnly button small{if $category->isSubscribed()} active{/if}" data-object-type="design.darkwood.community.topic.category" data-object-id="{@$category->categoryID}"><span class="icon icon16 fa-bookmark{if !$category->isSubscribed()}-o{/if}"></span> <span>{lang}wcf.user.objectWatch.button.subscribe{/lang}</span></a>
        <script data-relocate="true">
            $(function() {
                WCF.Language.addObject({
                    'wcf.user.objectWatch.manageSubscription': '{jslang}wcf.user.objectWatch.manageSubscription{/jslang}'
                });
                
                new WCF.User.ObjectWatch.Subscribe();
            });
        </script>
    {/if}
{/capture}

{capture assign='sidebarRight'}
    {include application='community' file='categorySidebar'}
{/capture}

{include file='header'}

{hascontent}
	<div class="paginationTop">
        {content}{pages print=true assign=pagesLinks application='community' controller='Category' object=$category link="pageNo=%d"}{/content}
	</div>
{/hascontent}


{if $objects|count}
    {include application='community' file='topicList'}
{else}
	<p class="info" role="status">{lang}community.category.noTopics{/lang}</p>
	<div class="jsClipcategoryContainer" data-type="com.woltlab.community.topic"></div>
{/if}


<footer class="contentFooter">
    {hascontent}
		<div class="paginationBottom">
            {content}{@$pagesLinks}{/content}
		</div>
    {/hascontent}

    {hascontent}
		<nav class="contentFooterNavigation">
			<ul>
                {content}
                    {include application='community' file='__topicAddButton'}
                {/content}
			</ul>
		</nav>
    {/hascontent}
</footer>

{include file='footer'}
