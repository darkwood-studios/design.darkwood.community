<?php

use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * Add default category
 */
$sql = "SELECT  objectTypeID
	FROM    wcf".WCF_N."_object_type
	WHERE   definitionID = ?
		AND objectType = ?";
$statement = WCF::getDB()->prepareStatement($sql, 1);
$statement->execute([
    ObjectTypeCache::getInstance()->getDefinitionByName('com.woltlab.wcf.category')->definitionID,
    'design.darkwood.community.topic.category'
]);

CategoryEditor::create([
    'objectTypeID' => $statement->fetchColumn(),
    'title' => 'Default Category',
    'time' => TIME_NOW
]);
