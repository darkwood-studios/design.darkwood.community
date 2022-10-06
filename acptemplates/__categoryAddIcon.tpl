{js application='wcf' file='WCF.ColorPicker' bundle='WCF.Combined'}
{include file='fontAwesomeJavaScript'}

<script data-relocate="true">
	require(['Language', 'WoltLabSuite/Core/Acp/Ui/Trophy/Badge'], function (Language, BadgeHandler) {
		Language.addObject({
			'wcf.style.colorPicker': '{jslang}wcf.style.colorPicker{/jslang}',
			'wcf.style.colorPicker.new': '{jslang}wcf.style.colorPicker.new{/jslang}',
			'wcf.style.colorPicker.current': '{jslang}wcf.style.colorPicker.current{/jslang}',
			'wcf.style.colorPicker.button.apply': '{jslang}wcf.style.colorPicker.button.apply{/jslang}',
			'wcf.acp.style.image.error.invalidExtension': '{jslang}wcf.acp.style.image.error.invalidExtension{/jslang}',
			'wcf.acp.trophy.badge.edit': '{jslang}wcf.acp.trophy.badge.edit{/jslang}',
			'wcf.acp.trophy.imageUpload.error.notSquared': '{jslang}wcf.acp.trophy.imageUpload.error.notSquared{/jslang}',
			'wcf.acp.trophy.imageUpload.error.tooSmall': '{jslang}wcf.acp.trophy.imageUpload.error.tooSmall{/jslang}',
			'wcf.acp.trophy.imageUpload.error.noImage': '{jslang}wcf.acp.trophy.imageUpload.error.noImage{/jslang}',
			'wcf.style.colorPicker.color': '{jslang}wcf.style.colorPicker.color{/jslang}',
			'wcf.style.colorPicker.error.invalidColor': '{jslang}wcf.style.colorPicker.error.invalidColor{/jslang}',
			'wcf.style.colorPicker.hexAlpha': '{jslang}wcf.style.colorPicker.hexAlpha{/jslang}',
			'wcf.style.colorPicker.hue': '{jslang}wcf.style.colorPicker.hue{/jslang}',
			'wcf.style.colorPicker.lightness': '{jslang}wcf.style.colorPicker.lightness{/jslang}',
			'wcf.style.colorPicker.saturation': '{jslang}wcf.style.colorPicker.saturation{/jslang}',
		});

		BadgeHandler.init();
	});
</script>

{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
	<script data-relocate="true">
        {include file='mediaJavaScript'}

		require(['WoltLabSuite/Core/Media/Manager/Select'], function(MediaManagerSelect) {
			new MediaManagerSelect({
				dialogTitle: '{jslang}wcf.media.chooseImage{/jslang}',
				imagesOnly: 1
			});
		});
	</script>
{/if}

<section id="imageContainer" class="section">
	<header class="sectionHeader">
		<h2 class="sectionTitle">Foren-Bild</h2>
	</header>
	{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
		<dl{if $errorField == 'image'} class="formError"{/if}>
			<dt><label for="image">Bild</label></dt>
			<dd>
				<div id="imageDisplay" class="selectedImagePreview">
					{if $image|isset && $image->hasThumbnail('small')}
						{@$image->getThumbnailTag('small')}
					{/if}
				</div>
				<p class="button jsMediaSelectButton" data-store="imageID" data-display="imageDisplay">{lang}wcf.media.chooseImage{/lang}</p>
				<small>todo beschreibung...</small>
				<input type="hidden" name="imageID" id="imageID"{if $imageID|isset} value="{@$imageID}"{/if}>
				{if $errorField == 'image'}
					<small class="innerError">todo Fehler...</small>
				{/if}
			</dd>
		</dl>
	{elseif $action == 'edit' && $image|isset && $image->hasThumbnail('small')}
		<dl>
			<dt>Bild</dt>
			<dd>
				<div id="imageDisplay">{@$image->getThumbnailTag('small')}</div>
			</dd>
		</dl>
	{/if}
</section>

<section id="badgeContainer" class="section">
	<header class="sectionHeader">
		<h2 class="sectionTitle">{lang}wcf.acp.trophy.type.badge{/lang}</h2>
	</header>

	<dl>
		<dt>{lang}wcf.acp.trophy.type.badge{/lang}</dt>
		<dd>
			<span class="icon icon64 fa-{$iconName} jsTrophyIcon trophyIcon giftIcon" style="color: {$iconColor}; background-color: {$badgeColor}"></span>
			<span class="button small">{lang}wcf.global.button.edit{/lang}</span>

			<input type="hidden" name="iconName" value="{$iconName}">
			<input type="hidden" name="iconColor" value="{$iconColor}">
			<input type="hidden" name="badgeColor" value="{$badgeColor}">
		</dd>
	</dl>
</section>


<div id="trophyIconEditor" style="display: none;">
	<div class="box128">
		<span class="icon icon144 fa-{$iconName} jsTrophyIcon trophyIcon" style="color: {$iconColor}; background-color: {$badgeColor}"></span>
		<div>
			<dl>
				<dt>{lang}wcf.acp.trophy.badge.iconName{/lang}</dt>
				<dd>
					<span class="jsTrophyIconName">{$iconName}</span>
					<a href="#" class="button small"><span class="icon icon16 fa-search"></span></a>
				</dd>
			</dl>

			<dl id="jsIconColorContainer">
				<dt>{lang}wcf.acp.trophy.badge.iconColor{/lang}</dt>
				<dd>
					<span class="colorBox">
						<span id="iconColorValue" class="colorBoxValue jsColorPicker" data-store="iconColorValue"></span>
						<input type="hidden" id="iconColorValue">
					</span>
					<a href="#" class="button small jsButtonIconColorPicker"><span class="icon icon16 fa-paint-brush"></span></a>
				</dd>
			</dl>

			<dl id="jsBadgeColorContainer">
				<dt>{lang}wcf.acp.trophy.badge.badgeColor{/lang}</dt>
				<dd>
					<span class="colorBox">
						<span id="badgeColorValue" class="colorBoxValue jsColorPicker" data-store="badgeColorValue"></span>
						<input type="hidden" id="badgeColorValue">
					</span>
					<a href="#" id="test" class="button small jsButtonBadgeColorPicker"><span class="icon icon16 fa-paint-brush"></span></a>
				</dd>
			</dl>
		</div>
	</div>

	<div class="formSubmit">
		<button class="buttonPrimary">{lang}wcf.global.button.save{/lang}</button>
	</div>
</div>