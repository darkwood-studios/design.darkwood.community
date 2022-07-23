<div class="section">
	<ul class="communityCategoryList">
        {foreach from=$categoryList item=categoryItem}
            {if $categoryItem->getPermission('canViewCategory', $__wcf->getUser()) !== 0}
				<li class="communityCategory" data-category-id="{@$categoryItem->categoryID}">
					<header>
						<h2>
                            {if $categoryItem->hasChildren()}<span
								class="collapsibleButton pointer icon icon16 fa-chevron-down jsStaticCollapsibleButton jsTooltip"
								title="{lang}wcf.global.button.collapsible{/lang}"
								aria-hidden="true"></span>{/if}
							<a href="{link application='community' controller='Category' object=$categoryItem->getDecoratedObject()}{/link}">{$categoryItem->getTitle()}</a>
						</h2>
                        {if $categoryItem->description}
							<p class="desciption">{@$categoryItem->description|language}</p>
                        {/if}
					</header>
                    {if $categoryItem->hasChildren()}
						<ol>
                            {foreach from=$categoryItem item=subCategoryItem}
                                {if $subCategoryItem->getPermission('canViewCategory', $__wcf->getUser()) !== 0}
									<li class="communitySubCategory"
										data-category-id="{@$subCategoryItem->categoryID}">
										<div>
											<h3>
												<a href="{link application='community' controller='Category' object=$subCategoryItem->getDecoratedObject()}{/link}">{$subCategoryItem->getTitle()}</a>
											</h3>
                                            {if $subCategoryItem->description}
												<p class="desciption">{@$subCategoryItem->description|language}</p>
                                            {/if}
										</div>
									</li>
                                {/if}
                            {/foreach}
						</ol>
                    {/if}
				</li>
            {/if}
        {/foreach}
	</ul>
</div>
