<?php
    namespace mf\auth;

    use \tweeterapp\view\TweeterView;
    
    class Authentification extends AbstractAuthentification{

        public function __construct(){
            // Constructeur de la classe Authentification reprenant les éléments de AbstractAuthentification
            if(isset($_SESSION['user_login'])){
                $this->user_login = $_SESSION['user_login'];
                $this->access_level = $_SESSION['access_level'];

                $this->logged_in = true;
            }
            else{
                $this->user_login = null;
                $this->access_level = self::ACCESS_LEVEL_NONE;

                $this->logged_in = false;
            }
        }

        protected function updateSession($username, $level){
            // Méthode pour enregistrer la connexion d'un utilisateur dans la session 
            $this->user_login = $username;
            $this->access_level = $level;

            $_SESSION['user_login'] = $username;
            $_SESSION['access_level'] = $level;

            $this->logged_in = true;
        }

        public function logout(){
            // Méthode pour effectuer la déconnexion
            unset($_SESSION['user_login']);
            unset($_SESSION['access_right']);
            $this->user_login = null;
            $this->access_level = self::ACCESS_LEVEL_NONE;
            $this->logged_in = false;
        }

        public function checkAccessRight($requested){
            // Méthode pour verifier le niveau d'accès de l'utilisateur
            if($requested > $this->access_level){
                return false;
            }
            else{
                return true;
            }
        }

        public function login($username, $db_pass, $given_pass, $level){
            // Méthode qui réalise la connexion d'un utilisateur
            if(!$this->verifyPassword($given_pass, $db_pass)){
                echo "Mot de passe invalide. Veuillez réessayer";
                $vue = new TweeterView('');
                $vue->render('logUser');
                exit;
            }
            else{
                $this->updateSession($username, $level);
            }
        }

        protected function hashPassword($password){
            // Méthode pour hacher un mot de passe
            return password_hash($password, PASSWORD_DEFAULT);
        }

        protected function verifyPassword($password, $hash){
            // Méthode pour vérifier si un mot de passe est égale a un hache
            return password_verify($password, $hash);
        } 

    }