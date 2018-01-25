<?php 

namespace Fuel\Migrations;

class Usuarios
{

    function up()
    {
        \DBUtil::create_table('usuarios', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'username' => array('type' => 'varchar', 'constraint' => 100),
            'password' => array('type' => 'varchar', 'constraint' => 200),
            'email' => array('type' => 'varchar', 'constraint' => 100),
            'id_device' => array('type' => 'varchar', 'constraint' => 100, 'null' => true),
            'foto_perfil' => array('type' => 'varchar', 'constraint' => 100, 'null' => true),
            'descripcion' => array('type' => 'varchar', 'constraint' => 400, 'null' => true),
            'cumple' => array('type' => 'varchar', 'constraint' => 20, 'null' => true),
            'coordenada_x' => array('type' => 'decimal', 'constraint' => 30, 'null' => true),
            'coordenada_y' => array('type' => 'decimal', 'constraint' => 30, 'null' => true),
            'ciudad' => array('type' => 'varchar', 'constraint' => 100, 'null' => true),
            'id_rol' => array('type' => 'int', 'constraint' => 11),
        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'claveAjenaUsuariosARoles',
		            'key' => 'id_rol',
		            'reference' => array(
		                'table' => 'roles',
		                'column' => 'id'
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'RESTRICT'
		        )
		    )
		);

        \DB::query("ALTER TABLE `usuarios` ADD UNIQUE (`username`)")->execute();
        \DB::query("ALTER TABLE `usuarios` ADD UNIQUE (`email`)")->execute();
    }

    function down()
    {
       \DBUtil::drop_table('usuarios');
    }
}