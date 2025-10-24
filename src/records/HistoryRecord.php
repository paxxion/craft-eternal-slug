<?php

namespace paxxion\crafteternalslug\records;

use craft\db\ActiveRecord;

class HistoryRecord extends ActiveRecord
{
    public static function tableName() {
        return '{{%eternal_slug_history}}';
    }
}
