<dl>
	<dt><label for="topicCategoryID">{lang}community.search.categories{/lang}</label></dt>
	<dd>
		<select name="topicCategoryID" id="topicCategoryID">
			<option value="">{lang}wcf.global.language.noSelection{/lang}</option>
			{foreach from=$topicCategoryList item=category}
				<option value="{@$category->categoryID}">{$category->getTitle()}</option>
			{/foreach}
		</select>
	</dd>
</dl>

{event name='fields'}
