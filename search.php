<?php

require_once('php/db.php');

session_start();
use DB\DBAccess;

// prendere il risultato dal DB
$db = new DBAccess();

$connection = $db->openDBConnection();
$user_output = "";
$slides = array();
$dynamicBreadcrumb="";

if($connection){
    if(isset($_GET['tag']) || isset($_GET['src_text']) || isset($_GET['game'])){
        if(isset($_GET['src_text'])){
            $articles = $db->getSearchRelatedArticles($_GET['src_text']);
            $dynamicBreadcrumb=': articles containing "'.$_GET['src_text'].'"';
        }
        else if(isset($_GET['tag'])){
            $articles = $db->getSelectedTagArticles($_GET['tag']);
            $dynamicBreadcrumb=': articles with tag "'.$_GET['tag'].'"';
        }
        else if(isset($_GET['game'])){
            $articles = $db->getSelectedGameArticles($_GET['game']);
            $dynamicBreadcrumb=': articles about "'.$_GET['game'].'"';
        }
        $db->closeDBConnection();   //ho finito di usare il db quindi chiudo la connessione
        if($articles){
            foreach($articles as $art){
                $user_output .= 
                    '<a class="card-article-link" href="article.php?id='.$art['id'].'">
                    <article>
                        <div class="card-article-image">
                            <img src="images/article_covers/'.$art['cover_img'].'"/>
                        </div>
                        <div class="card-article-info">
                            <h3>'.$art['title'].'</h3>
                            <h4>'.$art['subtitle'].'</h4>
                            <p>'.$art['publication_date'].'</p>';
                if($tags){
                    $user_output .= '<ul id="card-article-tags" class="tag-list">';
                    foreach($tags as $tag){
                        if($tag['article_id']==$art['id']){
                            $user_output .= '<li class="tag">'.$tag['name'].'</li>';
                        }
                    }
                    $user_output .= '</ul>';
                }   
                $user_output .= '</div>
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

$htmlPage = str_replace("<DynamicBreadCrumb/>", $dynamicBreadcrumb, $htmlPage);
$htmlPage = str_replace("<search_results/>", $user_output, $htmlPage);


echo $htmlPage;

?>