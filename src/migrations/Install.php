<?php

namespace paxxion\crafteternalslug\migrations;

use Craft;
use craft\db\Migration;

class Install extends Migration
{
    public function safeUp(): bool
    {
        $stash = '{{%eternal_slug_stash}}';
        if (Craft::$app->db->schema->getTableSchema($stash) === null) {
            $this->createTable($stash, [
                'id' => $this->primaryKey(),
                'uid' => $this->uid(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'siteId' => $this->integer()->notNull(),
                'entryId' => $this->integer()->notNull(),
                'entryOldUrl' => $this->string(512)->notNull(),
            ]);

            $this->createIndex(null, $stash, ['siteId'], false);
            $this->createIndex(null, $stash, ['entryId'], false);
            $this->createIndex(null, $stash, ['siteId', 'entryId'], false);            

            $this->addForeignKey(null, $stash, 'siteId', '{{%sites}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, $stash, 'entryId', '{{%entries}}', 'id', 'CASCADE', 'CASCADE');
        }

        $redirects = '{{%eternal_slug_redirects}}';
        if (Craft::$app->db->schema->getTableSchema($redirects) === null) {
            $this->createTable($redirects, [
                'id' => $this->primaryKey(),
                'uid' => $this->uid(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'siteId' => $this->integer()->notNull(),
                'entryId' => $this->integer()->notNull(),
                'entryOldUrl' => $this->string(512)->notNull(),
                'entryNewUrl' => $this->string(512),
                'totalHits' => $this->integer()->notNull()->defaultValue(0),
            ]);

            $this->createIndex(null, $redirects, ['siteId'], false);
            $this->createIndex(null, $redirects, ['entryId'], false);
            $this->createIndex(null, $redirects, ['siteId', 'entryId'], false);

            $this->addForeignKey(null, $redirects, 'siteId', '{{%sites}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, $redirects, 'entryId', '{{%entries}}', 'id', 'CASCADE', 'CASCADE');
        }

        $history = '{{%eternal_slug_history}}';
        if (Craft::$app->db->schema->getTableSchema($history) === null) {
            $this->createTable($history, [
                'id' => $this->primaryKey(),
                'uid' => $this->uid(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'siteId' => $this->integer()->notNull(),
                'entryId' => $this->integer()->notNull(),
                'entryUrl' => $this->string(512)->notNull()
            ]);

            $this->createIndex(null, $history, ['siteId'], false);
            $this->createIndex(null, $history, ['entryId'], false);
            $this->createIndex(null, $history, ['siteId', 'entryId'], false);

            $this->addForeignKey(null, $history, 'siteId', '{{%sites}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, $history, 'entryId', '{{%entries}}', 'id', 'CASCADE', 'CASCADE');
        }
        
        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%eternal_slug_stash}}');
        $this->dropTableIfExists('{{%eternal_slug_redirects}}');
        $this->dropTableIfExists('{{%eternal_slug_history}}');
        
        return true;
    }
}
