<?php

    namespace tweeterapp\control;
    use \tweeterapp\model\Tweet;
    use \tweeterapp\model\User;
    use \tweeterapp\view\TweeterView;
    use mf\router\Router;
    use tweeterapp\auth\TweeterAuthentification;
    use mf\auth\Authentification;

    class TweeterAdminController extends \mf\control\AbstractController {
        
        public function __construct(){
            parent::__construct();
        }

        public function viewLogin(){
            $vue = new TweeterView('');
            $vue->render('logUser');
        }

        public function checkLogin(){
            $r = new Router();
            $post = $this->request->post;
            $user = new TweeterAuthentification();
            $user->loginUser($post['user_login'], $post['user_pass']);
            $r->executeRoute('myPage');
        }

        public function viewLogout(){
            $r = new Router();
            $auth = new Authentification();
            $auth->logout();
            $r->executeRoute('maison');
        }

        public function viewSignup(){
            $vue = new TweeterView('');
            $vue->render('addUser');
        }

        public function checkSignup()
    {
        $auth = new TweeterAuthentification();
        $vue = new TweeterView('');

        if(isset($this->request->post['user_name'], $this->request->post['user_pass'],$this->request->post['fullname']))
        {
            $auth->createUser($this->request->post['user_name'], $this->request->post['user_pass'],$this->request->post['fullname']);
            Router::executeRoute('logUser');
        }
        else
        {
            $vue->render('addUser');
        }
    }
    }