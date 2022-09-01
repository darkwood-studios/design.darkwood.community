{capture assign='contentTitle'}{lang}design.darkwood.topic.{$action}{/lang}{/capture}

{include file='header'}

{@$form->getHtml()}

<footer class="contentFooter">
    {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}{event name='contentFooterNavigation'}{/content}
            </ul>
        </nav>
    {/hascontent}
</footer>

{include file='footer'}
