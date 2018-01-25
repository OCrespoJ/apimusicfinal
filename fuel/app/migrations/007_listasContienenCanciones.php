<?php 

namespace Fuel\Migrations;

class listasContienenCanciones
{

    function up()
    {
        \DBUtil::create_table('listas_contienen_canciones', array(
            'id_lista' => array('type' => 'int', 'constraint' => 11),
            'id_cancion' => array('type' => 'int', 'constraint' => 11),
        ), array('id_lista', 'id_cancion'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'claveAjenaListasContienenCancionesAListas',
		            'key' => 'id_lista',
		            'reference' => array(
		                'table' => 'listas',
		                'column' => 'id'
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'RESTRICT'
		        ),
		        array(
		            'constraint' => 'claveAjenaListasContienenCancionesACanciones',
		            'key' => 'id_cancion',
		            'reference' => array(
		                'table' => 'canciones',
		                'column' => 'id'
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'RESTRICT'
		        )
		    )
		);
    }

    function down()
    {
       \DBUtil::drop_table('listas_contienen_canciones');
    }
}