<?php

namespace tweeterapp\control;
use \tweeterapp\model\Like;
use \tweeterapp\model\Tweet;
use \tweeterapp\model\User;
use \tweeterapp\model\Follow;
use \tweeterapp\view\TweeterView;
use mf\router\Router;
use \mf\auth\Authentification;

/* Classe TweeterController :
 *  
 * Réalise les algorithmes des fonctionnalités suivantes: 
 *
 *  - afficher la liste des Tweets 
 *  - afficher un Tweet
 *  - afficher les tweet d'un utilisateur 
 *  - afficher la le formulaire pour poster un Tweet
 *  - afficher la liste des utilisateurs suivis 
 *  - évaluer un Tweet
 *  - suivre un utilisateur
 *   
 */

class TweeterController extends \mf\control\AbstractController {


    /* Constructeur :
     * 
     * Appelle le constructeur parent
     *
     * c.f. la classe \mf\control\AbstractController
     * 
     */
    
    public function __construct(){
        parent::__construct();
    }


    /* Méthode viewHome : 
     * 
     * Réalise la fonctionnalité : afficher la liste de Tweet
     * 
     */
    
    public function viewHome(){

        /* Algorithme :
         *  
         *  1 Récupérer tout les tweet en utilisant le modèle Tweet
         *  2 Parcourir le résultat 
         *      afficher le text du tweet, l'auteur et la date de création
         *  3 Retourner un block HTML qui met en forme la liste
         * 
         */
        $req = Tweet::select()->orderBy('created_at', 'DESC')->get();

        $vue = new TweeterView($req);

        $vue->render('maison');


    }


    /* Méthode viewTweet : 
     *  
     * Réalise la fonctionnalité afficher un Tweet
     *
     */
    
    public function viewTweet(){

        /* Algorithme : 
         *  
         *  1 L'identifiant du Tweet en question est passé en paramètre (id) 
         *      d'une requête GET 
         *  2 Récupérer le Tweet depuis le modèle Tweet
         *  3 Afficher toutes les informations du tweet 
         *      (text, auteur, date, score)
         *  4 Retourner un block HTML qui met en forme le Tweet
         * 
         *  Erreurs possibles : (*** à implanter ultérieurement ***)
         *    - pas de paramètre dans la requête
         *    - le paramètre passé ne correspond pas a un identifiant existant
         *    - le paramètre passé n'est pas un entier 
         * 
         */
        $id = $_GET['id'];
        $reqP = Tweet::select()->where('id','=',$id)->first();
        $vue = new TweeterView($reqP);

        $vue->render('affTweet');
        
    }


    /* Méthode viewUserTweets :
     *
     * Réalise la fonctionnalité afficher les tweet d'un utilisateur
     *
     */
    
    public function viewUserTweets(){

        /*
         *
         *  1 L'identifiant de l'utilisateur en question est passé en 
         *      paramètre (id) d'une requête GET 
         *  2 Récupérer l'utilisateur et ses Tweets depuis le modèle 
         *      Tweet et User
         *  3 Afficher les informations de l'utilisateur 
         *      (non, login, nombre de suiveurs) 
         *  4 Afficher ses Tweets (text, auteur, date)
         *  5 Retourner un block HTML qui met en forme la liste
         *
         *  Erreurs possibles : (*** à implanter ultérieurement ***)
         *    - pas de paramètre dans la requête
         *    - le paramètre passé ne correspond pas a un identifiant existant
         *    - le paramètre passé n'est pas un entier 
         * 
         */
        $id = $this->request->get;
        $reqP = User::select()->where('id','=', $id)->first();
        $tweets = $reqP->tweets()->get();
        
        $vue = new TweeterView($tweets);
        $vue->render('affUser');

    }

    public function viewPost(){
        $vue = new TweeterView('');
        $vue->render('post');
    }

    public function viewSend(){
        $username=$_SESSION['user_login'];
        $user = User::where('username','=',$username)->first();
        $tweet = new Tweet();
        //print_r($this->request->post);
        $tweet->text = filter_var($this->request->post['value'], FILTER_SANITIZE_SPECIAL_CHARS);
        $tweet->author = $user->id;
        $tweet->score = 0;
        $tweet->save();
        $vue = new Router();
        $vue->executeRoute('maison');
    }

    public function viewLike(){
        $user = User::where('username','=', $_SESSION['user_login'])->first();
        $id = $this->request->get;
        $requete =Tweet::select()->where('id','=',$id);
        $tweet = $requete->first();
        $requeteLike = Like::where('user_id','=',$user->id)->where('tweet_id','=',$tweet->id)->first();
        if(!$requeteLike)
        {
            $like = new Like();
            $like->user_id = $user->id;
            $like->tweet_id = $tweet->id;
            $like->save();
            $likeTweet = Tweet::find($tweet->id);
            $likeTweet->score = (($tweet->score)+1);
            $likeTweet->save();
            $vue = new TweeterView($likeTweet);
        }
        else{
            $like = Like::find($requeteLike->id);
            $like->delete();
            $likeTweet = Tweet::find($tweet->id);
            $likeTweet->score = (($tweet->score)-1);
            $likeTweet->save();
            $vue = new TweeterView($likeTweet);
        }
        $vue->render('affTweet');
    }

    public function viewFollow(){
        $user = User::where('username','=', $_SESSION['user_login'])->first();
        $id = $this->request->get;
        $requete = Tweet::select()->where('id','=',$id);
        $tweet = $requete->first();
        $requeteFollow = Follow::where('follower','=',$user->id)->where('followee','=',$tweet->author)->first();
        if(empty($requeteFollow))
        {
            $follow = new Follow();
            $follow->follower=$user->id;
            $follow->followee=$tweet->author;
            $follow->save();
            $user->followers = $user->followedCount();
            $user->save();
            echo"Vous suivez maintenant cette personne";
        }
        else{
            echo"Vous suivez déja cette personne"; //Exception
        }
        $vue = new TweeterView($tweet);
        $vue->render('affTweet');
    }

    public function viewFollowers(){
        $user = User::where('username','=', $_SESSION['user_login'])->first();
        $follows = $user->follows()->get();
        foreach($follows as $following)
        {
            $tweets[$following->username] = $following->tweets()->orderBy('created_at','DESC')->get();//Contient les tweets d'un utilisateur qu'il suit
        }
        $tweets[1]= $user->followedBy()->get(); // Contient les suiveurs
        $vue = new TweeterView($tweets);
        $vue->render('myPage');
    }
}
