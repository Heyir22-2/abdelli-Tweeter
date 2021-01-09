<?php

    namespace tweeterapp\view;
    use \tweeterapp\model\User;
    use \tweeterapp\model\Tweet;
    use \mf\router\Router;
    use \mf\utils\HttpRequest;
    use \mf\auth\Authentification;

    class TweeterView extends \mf\view\AbstractView {
    
        /* Constructeur 
        *
        * Appelle le constructeur de la classe parent
        */
        public function __construct($data){
            parent::__construct($data);
        }

        /* Méthode renderHeader
        *
        *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
        */ 
        private function renderHeader(){
return <<<EOT
<h1>MiniTweeTR</h1>
EOT;
        }
        
        /* Méthode renderFooter
        *
        * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
        */
        private function renderFooter(){
            return "La super app créée en Licence Pro &copy;2020";
        }

        /* Méthode renderHome
        *
        * Vue de la fonctionalité afficher tous les Tweets. 
        *  
        */
        
        protected function renderHome(){

            /*
            * Retourne le fragment HTML qui affiche tous les Tweets. 
            *  
            * L'attribut $this->data contient un tableau d'objets tweet.
            * 
            */
            $r=new Router();

            $chaine = "<h2> Latest Tweets </h2>";

            foreach($this->data as $tweet){

                $hrefTweet=$r->urlFor('affTweet', [['id', $tweet->id]]);
                $hrefUser=$r->urlFor('affUser', [['id', $tweet->author]]);
                $user = $tweet->author()->first()->username;
                $chaine .= "<div class = \"tweet\"><a href=".$hrefTweet.">".$tweet->text."</a>"."<br><a class=\"tweet-author\" href=".$hrefUser.">".$user."</a><br>".$tweet->created_at."</div>";
            }
            return $chaine;
        }
    
        /* Méthode renderUeserTweets
        *
        * Vue de la fonctionalité afficher tout les Tweets d'un utilisateur donné. 
        * 
        */
        
        private function renderUserTweets(){

            /* 
            * Retourne le fragment HTML pour afficher
            * tous les Tweets d'un utilisateur donné. 
            *  
            * L'attribut $this->data contient un objet User.
            *
            */
            $r=new Router();
            $chaine = "<h2>Tweets from ".$this->data[0]->author()->first()->fullname."</h3><br>";
            
            foreach($this->data as $tweet){

                $hrefTweet=$r->urlFor('affTweet', [['id', $tweet->id]]);
                $chaine .= "<div class = \"tweet\"><a href=".$hrefTweet.">".$tweet->text."</a>"."<div class=\"tweet-footer\">".$tweet->created_at."</div></div>";
            }
            return $chaine;
        }
    
        /* Méthode renderViewTweet 
        * 
        * Rréalise la vue de la fonctionnalité affichage d'un tweet
        *
        */
        
        private function renderViewTweet(){

            /* 
            * Retourne le fragment HTML qui réalise l'affichage d'un tweet 
            * en particulié 
            * 
            * L'attribut $this->data contient un objet Tweet
            *
            */
            $r=new Router();
            $chaine = "<div class=\"tweet\">";
            $hrefTweet = $r->urlFor("user",array(["id",$this->data->author]));
            $urlLike = $r->urlFor('like',array(["id",$this->data->id]));
            $urlFollow = $r->urlFor('follow',array(["id",$this->data->id]));
            $appRoot = (new HttpRequest())->root;
            $chaine.= "
            <div class=\"tweet-text\">"
                .$this->data->text.
            "</div>
            <div class=\"tweet-footer\">
                <span class=\"tweet-timestamp\">"
                    .$this->data->created_at.
                "</span>
                <span class=\"tweet-author\">
                    <a href=\"$hrefTweet\">".$this->data->author()->first()->fullname."</a>
                </span>
            </div></hr>
            <div class=\"tweet-footer\"><hr>
                <span class=\"tweet-score tweet-control\">".$this->data->score."</span>";
            $auth = new Authentification();
            if($auth->logged_in)
            {
                $chaine.="<a class=\"tweet-control\" href=\"${urlLike}\">
                    <img alt=\"Like\" src=\"$appRoot/html/like.png\">
                </a>
                <a class=\"tweet-control\" href=\"${urlFollow}\">
                    <img alt=\"Like\" src=\"$appRoot/html/follow.png\">
                </a>
            </div>";
            }
            else{
                $chaine.= "</div>";
            }
            return $chaine."</div>";
        }



        /* Méthode renderPostTweet
        *
        * Realise la vue de régider un Tweet
        *
        */
        protected function renderPostTweet(){
            
            /* Méthode renderPostTweet
            *
            * Retourne la framgment HTML qui dessine un formulaire pour la rédaction 
            * d'un tweet, l'action (bouton de validation) du formulaire est la route "/send/"
            *
            */
            $r = new Router();
            $tweet = $r->urlFor('send',null);
return <<<EOT
<form action="${tweet}" method="post">
    <textarea id="tweet-form" maxlength='160' name="value"></textarea>
    <br>
    <input type="submit" value="Envoyer"/>
</form>
EOT;
        }

        /* Méthode renderLogin
        *
        * Realise la vue qui permet de se connecter
        *
        */
        private function renderLogin(){
            /* Méthode renderPostTweet
            *
            * Retourne la framgment HTML qui dessine un formulaire pour la création 
            * d'un compte, l'action (bouton de validation) du formulaire est la route "/send/"
            *
            */
            $r = new Router();
            $log = $r->urlFor('checkLog',null);
return <<<EOT
<form action="${log}" method="post">
    <label for="user_login">Username:</label>
    <input type="text" name="user_login"/>
    <br>
    <label for="user_pass">Password:</label>
    <input type="password" name="user_pass" required/>
    <br>
    <input type="submit" value="Envoyer"/>
</form>
EOT;
        }

        private function renderFollowers(){
            $r = new Router();
            $result=0;
            foreach($this->data as $key=>$tweets)
            {
                if(empty($key))
                {
                    $result=1;
                }
            }
            if($result){
                $chaine ="<br><h3>Vous ne suivez personne et aucune personne vous suit</h3>";
            }
            else
            {
                $chaine ="<h3>Vos Suivis :</h3>";
                $nb=0;
                foreach ($this->data as $key=>$tweets)
                {
                    if(gettype($key)=="string")
                    {
                        $chaine .= "<h5>$key</h5>"; 
                        foreach($tweets as $tweet)
                        {
                            $hrefTweet = $r->urlFor("affTweet",array(["id",$tweet->id]));
                            $chaine = $chaine."<div class=\"tweet\"><a href=\"$hrefTweet\">$tweet->text</a><div class=\"tweet-footer\"><span class=\"tweet-timestamp\">".$tweet->created_at."</span><span class=\"tweet-author\">".$tweet->author()->first()->fullname."</span></div></div>";
                        }
                    }
                    else if(gettype($key)=="integer")
                    {
                        foreach($tweets as $value)
                        {
                            $nb++;
                        }
                        $chaine .= "<h3>Vous êtes suivi par $nb Follower(s) </h3>";
                        foreach ($tweets as $follower){
                            $hrefUser = $r->urlFor("affUser",array(["id",$follower->id]));
                            if(!empty($follower))
                            {
                                $chaine .= "<ul class=\"suivi\"><a href=\"$hrefUser\">".$follower->username."</a></ul>";
                            }
                        }
                    }            
                }
            }
            return $chaine;
        }

        public function renderSignup(){
            $r = new Router();
            $url = $r->urlFor("checkAdd",null);
            return <<<EOT
<form method="post" class="forms" action="${url}">
    <input class="forms-text" type="text" name="fullname" placeholder="Fullname"><br>
    <input class="forms-text" type="text" name="user_name" placeholder="Username"><br>
    <input class="forms-text" type="password" name="user_pass" placeholder="Password"><br>
    <input class="forms-text" type="password" name="password_verify" placeholder="Retaper Password"><br>
    <button class="forms-button" type="submit">Create</button>
</form>
EOT;
        }

        private function renderTopMenu(){
            $auth = new Authentification();
            $r = new Router();
            $urlHome = $r->urlFor('maison',null);
            $urlLog = $r->urlFor('logUser',null);
            $urlSignup = $r->urlFor('addUser',null);
            $urlPage = $r->urlFor('myPage',null);
            $urlLogout = $r->urlFor('logoutUser',null);
            $appRoot = (new HttpRequest())->root;
            if($auth->logged_in)
            {
$top=<<<EOT
<div class="tweet-control">
    <a href="${urlHome}">
        <img alt="home" src="$appRoot/html/home.png">
    </a>
</div>
<div class="tweet-control">
    <a href="${urlPage}">
        <img alt="follow" src="$appRoot/html/followees.png">
    </a>
</div>
<div class="tweet-control">
    <a href="${urlLogout}">
        <img alt="logout" src="$appRoot/html/logout.png">
    </a>
</div>
EOT;
            }
            else{
$top=<<<EOT
<div class="tweet-control">
    <a href="${urlHome}">
        <img alt="home" src="$appRoot/html/home.png">
    </a>
</div>
<div class="tweet-control">
    <a href="${urlLog}">
        <img alt="login" src="$appRoot/html/login.png">
    </a>
</div>
<div class="tweet-control">
    <a href="${urlSignup}">
        <img alt="signup" src="$appRoot/html/signup.png">
    </a>
</div>
EOT;
            }
            return $top;
        }

        private function renderBottomMenu(){
            $auth = new Authentification();
            $chaine = "";
            if($auth->logged_in)
            {
                $r = new Router();
                $hrefPost = $r->urlFor("post",null);
$chaine.=<<<EOT
<nav id="menu" class="theme-backcolor1">
    <div id="nav-menu">
        <div class="button theme-backcolor2">
            <a href="${hrefPost}">New</a>
        </div>
    </div>
</nav>
EOT;
            }
            return $chaine;
        }

        /* Méthode renderBody
        *
        * Retourne la framgment HTML de la balise <body> elle est appelée
        * par la méthode héritée render.
        *
        */
        
        protected function renderBody($selector){

            /*
            * voire la classe AbstractView
            * 
            */

            $header=$this->renderHeader();
            $top=$this->renderTopMenu();
            $footer=$this->renderFooter();
            $bottom=$this->renderBottomMenu();
            switch($selector){
                case 'maison':
                    $main = $this->renderHome();
                break;
                case 'affUser':
                    $main = $this->renderUserTweets();
                break;

                case 'affTweet':
                    $main = $this->renderViewTweet();
                break;

                case 'post':
                    $main=$this->renderPostTweet();
                break;

                case 'logUser':
                    $main=$this->renderLogin();
                break;

                case 'myPage':
                    $main=$this->renderFollowers();
                break;

                case 'addUser':
                    $main=$this->renderSignup();
                break;

                default: $main = $this->renderHome();
            break;

            }
$html=<<<EOT
<header class="theme-backcolor1">
    ${header}
    ${top}
</header>
<br>
<section>
    <article class="theme-backcolor2">${main}</article>
</section>
<br>
<footer class="theme-backcolor1 tweet-footer">
    ${bottom}
    ${footer}
</footer>
EOT;
            return $html;
        }
    }
?>