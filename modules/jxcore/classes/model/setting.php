<?php

/**
 * Jelly-based model for settings table
 */
 class Model_Setting extends Jelly_Model {

     public static function initialize(Jelly_Meta $meta){

         $meta->table('settings')
                ->fields(array(
                    'id' => new Field_Primary,
                    /**
                     * Key should be in the form of
                     * module.key
                     */
                    'setting' => new Field_String,
                    'value' => new Field_Text,
                    'description' => new Field_text
                ));
     }

 }
