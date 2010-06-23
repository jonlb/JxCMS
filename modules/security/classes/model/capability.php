<?php


class Model_Capability extends Jelly_Model {


    public static function initialize(Jelly_Meta $meta)
	{
        $meta->table('capabilities')
            ->fields(array(
                'id' => new Field_Primary,
                'capability' => new Field_String(array(
                    'unique' => TRUE
                )),
                'description' => new Field_Text,
                'module' => new Field_BelongsTo
            ));
    }
}
