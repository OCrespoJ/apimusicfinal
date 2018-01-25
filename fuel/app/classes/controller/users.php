<?php 

use Firebase\JWT\JWT;

class Controller_Users extends Controller_Rest
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

    public function post_configAdmin()
    {
        try {
            //Validar si hay usuarios
            $users = Model_Users::find('all');

            if (! empty($users)) {
                $input = $_POST;
                $user = new Model_Users();
                $user->username = 'admin';
                $user->email = 'admin@admin.com';
                $user->password = '1234';
                $user->id_device = $input['id_device'];
                $user->coordenada_x = $input['coordenada_x'];
                $user->coordenada_y = $input['coordenada_y'];
                $user->id_rol = 1;
                $user->save();
                $json = $this->response(array(
                   'code' => 202,
                   'message' => 'usuario creado',
                    'data' => null
                ));

            return $json;
            }
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }

    public function get_login()
    {
        try {
            if ( ! isset($_GET['username']) or
                 ! isset($_GET['password']) or
                 $_GET['username'] == "" or
                 $_GET['password'] == "") 
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));

                return $json;
            }

            $users = Model_users::find('first', array(
                'where' => array(
                    array('username', $_GET['username']),
                    array('password', $_GET['password'])
                ),
            ));
            
            //Validación usuario
            if (!empty($users)) {
               //Generar token
                $token = array(
                    'id'  => $users['id'],
                    'username' => $_GET['username'],
                    'password' => $_GET['password'],
                    'id_device' => $_GET['id_device'],
                    'coordenada_x' => $_GET['coordenada_x'],
                    'coordenada_y' => $_GET['coordenada_y']
                );
            
            $jwt = JWT::encode($token, $this->key);

            $json = $this->response(array(
                    'code' => 201,
                    'message' => 'usuario logeado',
                    'data' => array(
                        'token' => $jwt,
                        'username' => $token['username']   
                    )
                ));
            return $json;
            }
            else
            {
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'El usuario no existe o contraseña incorrecta',
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
    
    public function post_create()
    {
        try {
            //Validar campos rellenos y nombre correcto
            if ( ! isset($_POST['username']) or
                 ! isset($_POST['email']) or
                 ! isset($_POST['password']) or
                 ! isset($_POST['repeatPassword']) or
                 $_POST['username'] == "" or
                 $_POST['email'] == "" or
                 $_POST['password'] == "" or
                 $_POST['repeatPassword'] == "") 
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));

                return $json;
            }

            //Validar si hay usuarios
            $users = Model_Users::find('all');

            //Validar usuario no existe
            $userName = Model_users::find('all', array(
                'where' => array(
                    array('username', $_POST['username']),
                ),
            ));

            if (! empty($userName)) {
               $json = $this->response(array(
                    'code' => 403,
                    'message' => 'Ya existe un usuario con este username',
                    'data' => null
                ));
               return $json;
            }

            //Validar email no existe
            $userEmail = Model_users::find('all', array(
                'where' => array(
                    array('email', $_POST['email']),
                ),
            ));

            if (! empty($userEmail)) {
               $json = $this->response(array(
                    'code' => 404,
                    'message' => 'Ya existe un usuario con este email',
                    'data' => null
                ));
               return $json;
            }

            if ($_POST['password'] == $_POST['repeatPassword']) {
                
                $input = $_POST;
                $user = new Model_Users();
                $user->username = $input['username'];
                $user->email = $input['email'];
                $user->password = $input['password'];
                $user->id_device = $input['id_device'];
                $user->coordenada_x = $input['coordenada_x'];
                $user->coordenada_y = $input['coordenada_y'];
                $user->id_rol = 2;
                $user->save();
                $json = $this->response(array(
                   'code' => 202,
                   'message' => 'usuario creado',
                    'data' => null
                ));

            return $json;
            }
            else
            {
                $json = $this->response(array(
                    'code' => 405,
                    'message' => 'Las contraseñas no coinciden',
                    'data' => null
                ));
               return $json;
            }

            

        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
                'data' => null
            ));

            return $json;
        }
    }

    public function post_delete()
    {
        try
        {
            $token = apache_request_headers()['Authorization'];

            if ($this->authorization($token) == true){
               
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $id = $decoded->id;
                $user = Model_Users::find($id);

                $user->delete();
                $json = $this->response(array(
                    'code' => 201,
                    'message' => 'usuario borrado',
                    'data' => null
                ));
                return $json;
            
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

    public function get_checkEmail()
    {
        if (! isset($_GET['email']) or $_GET['email'] == "")
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));
                return $json;
            }

            //Validar usuario no existe
            $user = Model_users::find('all', array(
                'where' => array(
                    array('email', $_GET['email']),
                ),
            ));

            if (empty($user)) {
               $json = $this->response(array(
                    'code' => 403,
                    'message' => 'No existe un usuario con este correo',
                    'data' => null
                ));
                return $json;
            } else {
                $json = $this->response(array(
                    'code' => 200,
                    'message' => 'El correo existe',
                    'data' => $user
                ));
                return $json;
            }
        }

    public function post_recoverPassword()
    {
        try {
            //Validar campos rellenos y nombre correcto
            if (! isset($_POST['email']) or $_POST['email'] == "" or 
                ! isset($_POST['password']) or $_POST['password'] == "" or
                ! isset($_POST['repeatPassword']) or $_POST['repeatPassword'] == "")
            {
                $json = $this->response(array(
                    'code' => 402,
                    'message' => 'parametros incorrectos/Los campos no pueden estar vacios',
                    'data' => null
                ));
                return $json;
            }

            //Validar usuario no existe
            $user = Model_users::find('first', array(
                'where' => array(
                    array('email', $_POST['email']),
                ),
            ));

            if (empty($user)) {
               $json = $this->response(array(
                    'code' => 403,
                    'message' => 'No existe un usuario con este correo',
                    'data' => null
                ));
               return $json;
            }

            if ($_POST['password'] == $_POST['repeatPassword']) {
                $user->password = $_POST['password'];
                $user->save();
                $json = $this->response(array(
                    'code' => 201,
                    'message' => 'Contraseña cambiada',
                    'data' => null
                ));

                return $json;
            }
            else
            {
                $json = $this->response(array(
                    'code' => 404,
                    'message' => 'Las contraseñas no coinciden',
                    'data' => null
                ));
               return $json;
            }

        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 502,
                'message' => $e->getMessage(),
                'data' => $user
            ));

            return $json;
        }
    }
}