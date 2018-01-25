<?php 

namespace Fuel\Migrations;

class Amigos
{

    function up()
    {
        \DBUtil::create_table('amigos', array(
            'id_usuario_seguidor' => array('type' => 'int', 'constraint' => 11),
            'id_usuario_seguido' => array('type' => 'int', 'constraint' => 11),
        ), array('id_usuario_seguidor', 'id_usuario_seguido'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'claveAjenaAmigosAUsuariosSeguidor',
		            'key' => 'id_usuario_seguidor',
		            'reference' => array(
		                'table' => 'usuarios',
		                'column' => 'id'
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'RESTRICT'
		        ),
		        array(
		            'constraint' => 'claveAjenaAmigosAUsuariosSeguido',
		            'key' => 'id_usuario_seguido',
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
       \DBUtil::drop_table('amigos');
    }
}