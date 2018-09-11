<?php
//TODO
//UPDATEをつける
//各記事にインデックスをつける。
$blogname="ふくゆきブログ(再)";
date_default_timezone_set('Asia/Tokyo');

function newpost(){
    file_put_contents( "./posts/".date("YmdHi").".txt" , "TITLE\nBODYBODYBODYBODYBODYBODYBODY");
}

function make_blog(){
    global $blogname;
    $posts_list = read_post_entry();
    $index="";
    $title_list="";
    ( false===($b=file_get_contents( "template_index.html")))? die("can not read template") : 1;
    foreach( $posts_list as $l ){
        $list.="<li class=\"list-group-item\"><a href=".$l["filename"].">".$l["title"]."</a><br>"
        ."".mb_substr( $l["body"] , 0 , 128 , "utf-8")."...</li>";
        $title_list.="<p><a href=".$l["filename"].">".$l["title"]."</a><br>";
    }
    $b=mb_ereg_replace("{{list}}" , $list , $b );
    $b=mb_ereg_replace("{{blogname}}" , $blogname , $b );
    $b=mb_ereg_replace("{{title}}" , $blogname , $b );
    file_put_contents("index.html" , trim( $b ));

    foreach( $posts_list as $l ){
        ( false===($b=file_get_contents(  $l["filename"])))? die("can not read template") : 1;
        $b=mb_ereg_replace("{{list}}" , $title_list , $b );
        file_put_contents( $l["filename"] , str_replace(array("\t" , "\r" , "\n" ," ") ,"" ,  $b )  );
    }

}

function read_post_entry(){
    $d = dir("./posts");
    $post_list=array();
    while (false !== ($entry = $d->read())) {
        $fn = $entry;
        if ( false!==strpos( $fn , ".txt")){
            if ( false!==( $r=makehtml( $fn ))){
                $post_list[ count( $post_list )] = $r;
            }
        }
    }
    $d->close();
    //post_list を日付でソート。
    return $post_list;
}

function makehtml( $fn ){
    global $blogname;

    if ( false===($f=fopen( $fn2="./posts/".$fn , "rt"))){
        die("can not open ".$fn.".");
    }
    $title=trim(fgets( $f ));
    if ( 7 > mb_strlen( $title , "utf-8" )){
        echo("too short title:".$fn.":".$title );
        return false;
    }
    $body="";
    while( $b=fgets( $f  , 1024 )){
        $body.=$b;
    }
    $html_fn=str_replace( ".txt" , ".html" , $fn );
    if ( false===($b=file_get_contents( "template_posts.html"))){
        die("can not read template");
        return false;
    }

    $b=mb_ereg_replace("{{title}}" , $title , $b );
    $b=mb_ereg_replace("{{date}}" , date("Y/m/d H:i" , filemtime( $fn2 )) , $b );
    $b=mb_ereg_replace("{{body}}" , $body , $b );
    $b=mb_ereg_replace("{{blogname}}" , $blogname , $b );
    file_put_contents( $html_fn , $b );
    return array(
        "filename"=>$html_fn,
        "title"=>$title,
        "body"=>$body
        );
}

//newpost();

function main(){
    //echo @$_SERVER['argv'][0]."\n";
    if ( "new" ==@$_SERVER['argv'][1] ){
        newpost();
        return;
    }
    make_blog();
    system("./d.sh");
}

main();


