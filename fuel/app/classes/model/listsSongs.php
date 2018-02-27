<?php 

class Model_Contiene extends Orm\Model
{
    protected static $_table_name = 'listas_contienen_canciones';
    protected static $_primary_key = array('id_lista', 'id_cancion');
    protected static $_properties = array('id_lista', 'id_cancion');
}
