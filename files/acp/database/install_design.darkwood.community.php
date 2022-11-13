<?php

use wcf\system\database\table\column\MediumintDatabaseTableColumn;
use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\SmallintDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\TinyintDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
    /*
     * Topics
     */
    DatabaseTable::create('community1_topic')
        ->columns(
            [
                ObjectIdDatabaseTableColumn::create('topicID'),
                NotNullVarchar255DatabaseTableColumn::create('subject'),
                MediumtextDatabaseTableColumn::create('message'),
                IntDatabaseTableColumn::create('categoryID')->length(10)->defaultValue(null),
                IntDatabaseTableColumn::create('userID')->length(10)->defaultValue(null),
                NotNullVarchar255DatabaseTableColumn::create('username'),
                NotNullInt10DatabaseTableColumn::create('time'),
                MediumintDatabaseTableColumn::create('cumulativeLikes')->length(7),
                SmallintDatabaseTableColumn::create('comments')->length(5)->defaultValue(0),
                SmallintDatabaseTableColumn::create('views')->length(5)->defaultValue(0),
                NotNullInt10DatabaseTableColumn::create('lastCommentID')->length(10)->defaultValue(null),
                NotNullInt10DatabaseTableColumn::create('lastCommentTime')->defaultValue(0),
                IntDatabaseTableColumn::create('lastCommentUserID')->length(10)->defaultValue(null),
                NotNullVarchar255DatabaseTableColumn::create('lastCommentUsername')->defaultValue(''),
                MediumintDatabaseTableColumn::create('responses')->length(7), //todo wird das benötigt?
                NotNullVarchar255DatabaseTableColumn::create('responseIDs')->defaultValue(''), //todo wird das benötigt?
                TinyintDatabaseTableColumn::create('isDone')->length(1)->defaultValue(0),
                TinyintDatabaseTableColumn::create('isDeleted')->length(1)->defaultValue(0),
                TinyintDatabaseTableColumn::create('isClosed')->length(1)->defaultValue(0),
                IntDatabaseTableColumn::create('languageID')->length(10)->defaultValue(null),
            ]
        )
        ->indices(
            [
                DatabaseTablePrimaryIndex::create()
                    ->columns(['topicID']),
            ]
        )
        ->foreignKeys(
            [
                DatabaseTableForeignKey::create()
                    ->columns(['userID'])
                    ->referencedTable('wcf1_user')
                    ->referencedColumns(['userID'])
                    ->onDelete('SET NULL'),
                DatabaseTableForeignKey::create()
                    ->columns(['lastCommentID'])
                    ->referencedTable('wcf1_comment')
                    ->referencedColumns(['commentID'])
                    ->onDelete('SET NULL'),
                DatabaseTableForeignKey::create()
                    ->columns(['lastCommentUserID'])
                    ->referencedTable('wcf1_user')
                    ->referencedColumns(['userID'])
                    ->onDelete('SET NULL'),
                DatabaseTableForeignKey::create()
                    ->columns(['categoryID'])
                    ->referencedTable('wcf1_category')
                    ->referencedColumns(['categoryID'])
                    ->onDelete('SET NULL'),
                DatabaseTableForeignKey::create()
                    ->columns(['languageID'])
                    ->referencedTable('wcf1_language')
                    ->referencedColumns(['languageID'])
                    ->onDelete('SET NULL'),
            ]
        ),
];
