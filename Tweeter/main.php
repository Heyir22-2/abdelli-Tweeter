<?php
    session_start();
    /* pour le chargement automatique des classes d'Eloquent (dans le répertoire vendor) */
    require_once 'vendor/autoload.php';
    require_once "src/mf/utils/AbstractClassLoader.php";
    require_once "src/mf/utils/ClassLoader.php";
        
    $loader = new \mf\utils\ClassLoader('src');
    $loader->register();
    
    $config = parse_ini_file("conf/config.ini");
    //var_dump($config);
    //echo "<br>";
    
    /* une instance de connexion  */
    $db = new Illuminate\Database\Capsule\Manager();
    
    $db->addConnection( $config ); /* configuration avec nos paramètres */
    $db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
    $db->bootEloquent();           /* établir la connexion */

    use mf\router\Router;
    use tweeterapp\view\TweeterView;
    use mf\auth\TweeterAuthentification;

    

    $router = new Router();

    $router->addRoute('maison', '/home/', '\tweeterapp\control\TweeterController', 'viewHome', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);

    $router->addRoute('affTweet', '/view/', '\tweeterapp\control\TweeterController', 'viewTweet', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
    $router->addRoute('affUser', '/user/', '\tweeterapp\control\TweeterController', 'viewUserTweets', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
    $router->addRoute('post', '/post/', '\tweeterapp\control\TweeterController', 'viewPost', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
    $router->addRoute('send', '/send/', '\tweeterapp\control\TweeterController', 'viewSend', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

    $router->addRoute('logUser', '/login/', '\tweeterapp\control\TweeterAdminController', 'viewLogin', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
    $router->addRoute('checkLog', '/check_login/', '\tweeterapp\control\TweeterAdminController', 'checkLogin', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
    $router->addRoute('logoutUser', '/logout/', '\tweeterapp\control\TweeterAdminController', 'viewLogout', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);
    $router->addRoute('addUser', '/signup/', '\tweeterapp\control\TweeterAdminController', 'viewSignup', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
    $router->addRoute('checkAdd', '/check_signup/', '\tweeterapp\control\TweeterAdminController', 'checkSignup', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_NONE);
    $router->addRoute('myPage', '/following/', '\tweeterapp\control\TweeterController', 'viewFollowers', \tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

    $router->addRoute('like','/like/','\tweeterapp\control\TweeterController','viewLike',\tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

    $router->addRoute('follow','/follow/','\tweeterapp\control\TweeterController','viewFollow',\tweeterapp\auth\TweeterAuthentification::ACCESS_LEVEL_USER);

    $router->setDefaultRoute('/home/');

    Tweeterview::addStyleSheet("html/style.css");
    
    $router->run();

    //echo $router->urlFor('affTweet', [['id', 72]]);