<div class="section">
	<ul class="communityCategoryList">
        {if !$categoryList->hasChildren()}
			<p class="info">{lang}wcf.global.noItems{/lang}</p>
        {/if}
        {foreach from=$categoryList item=categoryItem}
            {if $categoryItem->getPermission('canViewCategory', $__wcf->getUser()) !== 0}
				<li class="communityCategory" data-category-id="{@$categoryItem->categoryID}">

					<div class="box48" style="align-items: center;{* todo: move css *}">
                        {if $categoryItem->getIcon(48)}
							<a href="{link application='community' controller='Category' object=$categoryItem->getDecoratedObject()}{/link}">
                                {@$categoryItem->getIcon(48)}
							</a>
                        {else}
							<span></span>
                        {/if}
						<div class="containerHeadline">
							<h3>
								<a href="{link application='community' controller='Category' object=$categoryItem->getDecoratedObject()}{/link}">
                                    {$categoryItem->getTitle()}
								</a>
							</h3>
						</div>
						<div>
                            {@$categoryItem->description|language}
						</div>
					</div>

                    {if $categoryItem->hasChildren()}
						<ol style="margin-left: 18px;margin-top: 8px;{* todo: move css *}">
                            {foreach from=$categoryItem item=subCategoryItem}
                                {if $subCategoryItem->getPermission('canViewCategory', $__wcf->getUser()) !== 0}
									<li class="communitySubCategory" data-category-id="{@$subCategoryItem->categoryID}">
										<div class="box32" style="align-items: center;">
                                            {if $subCategoryItem->getIcon()}
												<a href="{link application='community' controller='Category' object=$subCategoryItem->getDecoratedObject()}{/link}">
                                                    {@$subCategoryItem->getIcon()}
												</a>
                                            {else}
												<span></span>
                                            {/if}
											<div>
												<p>
													<a href="{link application='community' controller='Category' object=$subCategoryItem->getDecoratedObject()}{/link}">
                                                        {$subCategoryItem->getTitle()}
													</a>
												</p>
												<small>
                                                    {@$subCategoryItem->description|language}
												</small>
											</div>
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
