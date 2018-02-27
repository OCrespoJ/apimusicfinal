<?php 

class Model_Songs extends Orm\Model
{
    protected static $_table_name = 'canciones';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'titulo' => array(
            'data_type' => 'varchar'   
        ),
        'url' => array(
            'data_type' => 'varchar'   
        ),
        'artista' => array(
            'data_type' => 'varchar'   
        ),
        'reproducciones' => array(
            'data_type' => 'int'   
        )
    );
}
