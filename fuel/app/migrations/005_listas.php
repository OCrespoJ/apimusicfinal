<?php 

namespace Fuel\Migrations;

class Listas
{

    function up()
    {
        \DBUtil::create_table('listas', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'titulo' => array('type' => 'varchar', 'constraint' => 100),
            'editable' => array('type' => 'int', 'constraint' => 1),
            'id_usuario' => array('type' => 'int', 'constraint' => 11),
        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'claveAjenaListasAUsuarios',
		            'key' => 'id_usuario',
		            'reference' => array(
		                'table' => 'usuarios',
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
       \DBUtil::drop_table('listas');
    }
}