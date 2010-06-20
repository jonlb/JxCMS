<?php

/**
 * Jelly-based model for modules table
 */
 class Model_Module extends Jelly_Model {

     public static function initialize(Jelly_Meta $meta){

         $meta->table('modules')
                ->fields(array(
                    'id' => new Field_Primary(),
                    'name' => new Field_String(),
                    'activated' => new Field_Boolean(),
                    'permanent' => new Field_Boolean()
                ));
     }

 }