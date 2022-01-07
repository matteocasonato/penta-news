<?php

require_once('php/db.php');

session_start();
use DB\DBAccess;

// prendere il risultato dal DB
$db = new DBAccess();

$connection = $db->openDBConnection();
$user_output = "";
$slides = array();

if($connection){
    if(isset($_GET['tag']) || isset($_GET['src_text'])){
        if(isset($_GET['src_text'])){
            $articles = $db->getSearchRelatedArticles($_GET['src_text']);
        }
        else if(isset($_GET['tag'])){
            $articles = $db->getSearchRelatedArticles($_GET['tag']);
        }
        $db->closeDBConnection();   //ho finito di usare il db quindi chiudo la connessione
        if($articles!=null){        
            foreach($articles as $art){
                $user_output .= 
                    '<a class="articleLink" href="article.php?id='.$art['id'].'">
                    <article>
                        <div class="article_image">
                            <img src="images/article_covers/'.$art['cover_img'].'"/>
                        </div>
                        <div class="article_info">
                            <h3>'.$art['title'].'</h3>
                            <h4>'.$art['subtitle'].'</h4>
                            <p>'.$art['publication_date'].'</p>';
                $intro=true;
                $connection = $db->openDBConnection();
                $tags = $db->getSearchedArticlesTags($art['id']);
                $db->closeDBConnection();
                foreach($tags as $tag){
                    if($tag['article_id']==$art['id']){
                        if($intro){
                            $user_output .= '<ul id="article-tags-home" class="tag-list">';
                            $intro=false;
                        }
                        $user_output .= '<li class="tag"><a href="search.php?tag='.$tag['name'].'">'.$tag['name'].'</a></li>';
                    }
                }
                if(!$intro)            
                    $user_output .= '</ul>';
                $user_output .= 
                        '</div>
                    </article>
                </a>';
            }
        }else{
            $user_output .= "<p>Your search doesn't correspond to any article in our database, try changing your search request!</p>";
        }
    }else{
        $user_output .= "<p>You didn't search for anything or the URL is incorrect, write something in the search box to look up for something!</p>";
    }
} else {
    $user_output = "<p>Something went wrong while connecting to the database, try again or contact us.</p>";
}

$htmlPage = file_get_contents("html/search.html");

//header footer and dynamic navbar all at once (^^^ sostituisce il commento qua sopra ^^^)
require_once('php/full_sec_loader.php');

//str_replace finale col conenuto specifico della pagina
$htmlPage = str_replace("<search_results/>", $user_output, $htmlPage);


echo $htmlPage;

?>