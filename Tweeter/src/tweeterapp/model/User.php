<?php
namespace tweeterapp\model;

class User extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'user';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */
       public function tweets() {
              return $this->hasMany('\tweeterapp\model\Tweet', 'author');
       }
       public function followedBy() {
              return $this->belongsToMany('\tweeterapp\model\User', 'Follow', 'followee', 'follower');
       }
       public function follows(){
              return $this->belongsToMany('tweeterapp\model\User','Follow','follower','followee');
       }
       public function liked(){
              return $this->belongsToMany('tweeterapp\model\Tweet','Like','user_id','tweet_id');
       }

       public function followedCount(){
              return $this->belongsToMany('tweeterapp\model\User','follow','followee','follower')->count();
          }
}
