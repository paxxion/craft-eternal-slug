<?php

namespace paxxion\crafteternalslug\records;

use craft\db\ActiveRecord;

class StashRecord extends ActiveRecord
{
    public static function tableName() {
        return '{{%eternal_slug_stash}}';
    }
}
