<?php 

use Firebase\JWT\JWT;

class Controller_Songs extends Controller_Rest
{
    private $key = '53jDgdTf5efGH54efef978';

    private function authorization($token)
    {

        $decoded = JWT::decode($token, $this->key, array('HS256'));

        $userId = $decoded->id;

        $users = Model_users::find('all', array(
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
    
    public function post_createSong()
    {
         try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;
                $user = Model_Users::find($id);

                if($user->id_rol == 1){

                    if ( !isset($_POST['titulo']) or
                      !isset($_POST['url']) or
                      !isset($_POST['artista']) or
                     $_POST['titulo'] == "" or
                     $_POST['url'] == "" or
                     $_POST['artista'] == "")
                    {

                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                        'data' => null
                    ));

                    return $json;
                    }

                    //Validar titulo no existe
                    $songName = Model_Songs::find('all', array(
                        'where' => array(
                            array('titulo', $_POST['titulo']),
                        ),
                    ));

                    if (! empty($songName)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'Ya existe una cancion con este titulo',
                            'data' => null
                        ));
                       return $json;
                    }

                    //Validar titulo no existe
                    $songUrl = Model_Songs::find('all', array(
                        'where' => array(
                            array('url', $_POST['url']),
                        ),
                    ));

                    if (! empty($songUrl)) {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'Ya existe una cancion con esta url',
                            'data' => null
                        ));
                       return $json;
                    }

                    $song = new Model_Songs();
                    $song->titulo = $_POST['titulo'];
                    $song->url = $_POST['url'];
                    $song->artista = $_POST['artista'];
                    $song->reproducciones = 0;
                    $song->save();
                    $json = $this->response(array(
                       'code' => 202,
                       'message' => 'cancion creada',
                        'data' => null
                    ));

                    return $json;
                } 
                else
                {
                    $json = $this->response(array(
                    'code' => 400,
                    'message' => 'No tienes permiso para añadir canciones, necesitas ser administrador',
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

    public function post_deleteSong()
    {
         try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;
                $user = Model_Users::find($id);


                if($user->id_rol == 1)
                {
                     if ( !isset($_POST['id']) or
                     $_POST['id'] == "")
                    {

                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'Introduce id de la canción',
                        'data' => null
                    ));

                    return $json;
                    }

                    $song = Model_Songs::find('first', array(
                        'where' => array(
                            array('id', $_POST['id']),
                        ),
                    ));
                    if (empty($song)) {
                       $json = $this->response(array(
                            'code' => 403,
                            'message' => 'No existe ninguna cancion con ese id',
                            'data' => null
                        ));
                        return $json;
                    } else {
                        $song->delete();
                        $json = $this->response(array(
                            'code' => 201,
                            'message' => 'cancion borrada',
                            'data' => null
                        ));
                        return $json;
                    }
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'No tienes permiso para borrar canciones, accede con un usuario adminitrador',
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

    public function get_getSongs()
    {
        try {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               

                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;

                $songs = Model_Songs::find('all');

                $json = $this->response(array(
                    'code' => 200,
                    'message' => 'Canciones mostradas',
                    'data' => $songs
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

    public function post_playSong()
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
                        'message' => 'Introduce id de la canción',
                        'data' => null
                    ));

                    return $json;
                    }

                    $song = Model_Songs::find('first', array(
                        'where' => array(
                            array('id', $_POST['id']),
                        ),
                    ));
                    if (empty($song)) {
                       $json = $this->response(array(
                            'code' => 403,
                            'message' => 'No existe ninguna cancion con ese id',
                            'data' => null
                        ));
                        return $json;
                    } else {
                        $song->reproducciones = $song->reproducciones + 1;
                        $song->save();
                        $json = $this->response(array(
                            'code' => 201,
                            'message' => 'cancion reproducida',
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
                'code' => 500,
                'message' => $e->getMessage(),
            ));

            return $json;
        }
    }
}