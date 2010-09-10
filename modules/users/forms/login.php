<?php

return array(
    'attributes' => array(
        'action' => ''
    ),
    'populate_defaults' => true,
    'messages' => 'forms/login',
    'label_file' => 'forms/login.label',
    'use_model_validation' => false,
    'result_type' => 'model',
    'fields' => array(
        array(
            'model' => 'user',
            'field' => 'username',
            'label_key' => 'username',
            'validation' => array(
                'rules' => array(
                    'not_empty' => NULL
                )
            )
        ),
        array(
            'model' => 'user',
            'field' => 'password',
            'label' => 'Enter Password: ',
            'validation' => array(
                'rules' => array(
                    'not_empty' => NULL
                )
            )
        ),
        array(
            'name' => 'login',
            'type' => 'submit',
            'value'=> 'Ok'
        )
    )
);
