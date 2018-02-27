<?php 

use Firebase\JWT\JWT;

class Controller_Lists extends Controller_Rest
{
    private $key = '53jDgdTf5efGH54efef978';

    private function authorization($token)
    {

        $decoded = JWT::decode($token, $this->key, array('HS256'));

        $userId = $decoded->id;

        $users = Model_Users::find('all', array(
                'where' => array(
                    array('id', $userId)
                ),
        ));

        if ($users != null) {
            return true;
        }
        else 
        {
           return false; 
        }
    }
    
    public function post_createList()
    {
         try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;
                $user = Model_Users::find($id);

                if($user->id_rol != 1){

                    if ( !isset($_POST['titulo']) or
                     $_POST['titulo'] == "")
                    {

                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'Introduce el titulo de la lista',
                        'data' => null
                    ));

                    return $json;
                    }

                    //Validar titulo no existe
                    $listName = Model_Lists::find('all', array(
                        'where' => array(
                            array('titulo', $_POST['titulo']),
                        ),
                    ));

                    if (! empty($listName)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'Ya existe una lista con este titulo',
                            'data' => null
                        ));
                       return $json;
                    }

                    $list = new Model_Lists();
                    $list->titulo = $_POST['titulo'];
                    $list->editable = 1;
                    $list->id_usuario = $id;
                    $list->save();
                    $json = $this->response(array(
                       'code' => 202,
                       'message' => 'lista creada',
                        'data' => null
                    ));

                    return $json;
                } 
                else
                {
                    $json = $this->response(array(
                    'code' => 400,
                    'message' => 'El usuario adminstrador no tiene acceso a esta funcionalidad',
                    'data' => null
                    ));

                    return $json;
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 501,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }

    public function post_deleteList()
    {
         try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;
                $user = Model_Users::find($id);


                if($user->id_rol != 1)
                {
                     if ( !isset($_POST['id']) or
                     $_POST['id'] == "")
                    {

                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'Introduce id de la lista',
                        'data' => null
                    ));

                    return $json;
                    }

                    $list = Model_Lists::find('first', array(
                        'where' => array(
                            array('id', $_POST['id']),
                        ),
                    ));

                    if (empty($list)) {
                       $json = $this->response(array(
                            'code' => 403,
                            'message' => 'No existe ninguna lista con ese id',
                            'data' => null
                        ));
                        return $json;
                    } else {
                        if($list->id_usuario == $id)
                        {
                            $list->delete();
                            $json = $this->response(array(
                                'code' => 201,
                                'message' => 'lista borrada',
                                'data' => null
                            ));
                            return $json;
                        }
                        else
                        {
                            $json = $this->response(array(
                                'code' => 400,
                                'message' => 'No puedes borrar listas de otros usuarios',
                                'data' => null
                            ));
                            return $json;
                        }
                    }
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'El usuario adminstrador no tiene acceso a esta funcionalidad',
                        'data' => null
                    ));
                    return $json;
                }
                
            
            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 501,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }

    public function get_getLists()
    {
        try {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               

                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;

                $lists = Model_Lists::find('first', array(
                        'where' => array(
                            array('id_usuario', $id),
                        ),
                    ));

                $json = $this->response(array(
                    'code' => 200,
                    'message' => 'Listas mostradas',
                    'data' => $lists
                ));

                return $json;

            }
            else
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Token incorrecto, no tienes permiso'
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 500,
                'message' => $e->getMessage(),
            ));

            return $json;
        }
    }

    public function post_addSongToList()
    {
         try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;
                $user = Model_Users::find($id);


                if($user->id_rol != 1)
                {
                     
                    if ( !isset($_POST['lista']) or
                      !isset($_POST['cancion']) or
                     $_POST['lista'] == "" or
                     $_POST['cancion'] == "")
                    {

                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                        'data' => null
                    ));

                    return $json;
                    }

                    //Validar lista existe
                    $list = Model_Lists::find('first', array(
                        'where' => array(
                            array('titulo', $_POST['lista']),
                        ),
                    ));

                    if (empty($list)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'No existe una lista con ese titulo',
                            'data' => null
                        ));
                       return $json;
                    } 
                    else if($list->id_usuario != $id)
                    {
                        $json = $this->response(array(
                            'code' => 400,
                            'message' => 'No puedes añadir canciones a listas de otros usuarios',
                            'data' => null
                        ));
                       return $json;
                    }

                    //Validar cancion existe
                    $song = Model_Songs::find('first', array(
                        'where' => array(
                            array('titulo', $_POST['cancion']),
                        ),
                    ));

                    if (empty($song)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'No existe una cancion con ese titulo',
                            'data' => null
                        ));
                       return $json;
                    }

                    $contiene = Model_Contiene::find('first', array(
                        'where' => array(
                            array('id_lista', $list->id),
                            array('id_cancion', $song->id)
                        ),
                    ));

                    if (!empty($contiene)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'La cancion ya existe en esta lista',
                            'data' => null
                        ));
                       return $json;
                    }
                    else
                    {
                        $add = new Model_Contiene();
                        $add->id_lista = $list->id;
                        $add->id_cancion = $song->id;
                        $add->save();
                        $json = $this->response(array(
                           'code' => 202,
                           'message' => 'cancion añadida',
                            'data' => null
                        ));

                        return $json;
                    }
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'El usuario adminstrador no tiene acceso a esta funcionalidad',
                        'data' => null
                    ));
                    return $json;
                }
            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Token incorrecto, no tienes permiso',
                    'data' => null
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 501,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }

     public function get_getSongsOfList()
    {
        try {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               

                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;
                $user = Model_Users::find($id);

                if($user->id_rol != 1)
                {
                     
                    if ( !isset($_GET['lista']) or
                    $_GET['lista'] == "")
                    {

                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'Introduce el titulo de la lista',
                        'data' => null
                    ));

                    return $json;
                    }

                    //Validar lista existe
                    $list = Model_Lists::find('first', array(
                        'where' => array(
                            array('titulo', $_GET['lista']),
                        ),
                    ));

                    if (empty($list)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'No existe una lista con ese titulo',
                            'data' => null
                        ));
                       return $json;
                    } 

                    $contiene = Model_Contiene::find('all', array(
                        'where' => array(
                            array('id_lista', $list->id),
                        ),
                    ));

                     $songs = Model_Songs::find('all', array(
                        'where' => array(
                            array('id', $contiene->id_cancion),
                        ),
                    ));

                    if (empty($contiene)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'La lista no tiene canciones',
                            'data' => null
                        ));
                       return $json;
                    }
                    else
                    {
                        $json = $this->response(array(
                           'code' => 200,
                           'message' => 'canciones mostradas',
                            'data' => $songs
                        ));

                        return $json;
                    }
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'El usuario adminstrador no tiene acceso a esta funcionalidad',
                        'data' => null
                    ));
                    return $json;
                }

            }
            else
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Token incorrecto, no tienes permiso'
                ));

                return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 500,
                'message' => $e->getMessage(),
            ));

            return $json;
        }
    }
}