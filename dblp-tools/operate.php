<?php

/*
 *	Script for operations with DB
 *	$_GET['do'] - can be 'delete' or 'insert' or 'update'
 */

/*
 *	Delete operation uses variables
 *	$_GET['id'] - delete article with this id
 */

/*
 *	Insertion operation uses variables
 *	$_GET['title'] - title of article
 *	$_GET['pyear'] - publication year
 *	$_GET['link'] - link to article
 *	$_GET['authors'] - authors divided by "," (one comma)
 *  $_GET['keywords'] - keywords divided by "," (one comma)
 */

/*
 *	Updating articles uses variables
 *	$_GET['id'] - apply changes to article with this ID
 *	$_GET['title'] - new title
 *	$_GET['pyear'] - new year
 *	$_GET['plink'] - new link
 *	$_GET['authors'] - new authors divided by "," (one comma)
 */

session_start();

if ($_SESSION['userRights'] != 'admin') {
    die('You have no power here!');
}

include("dbConnection.php");

if ($_GET['do'] == 'delete') {
    $status = deleteArticle($_GET['id']);
    if ($status != true) {
        echo $status;
    }
} else if ($_GET['do'] == 'insert') {
    $status = insertArticle();
    if ($status != true) {
        echo $status;
    }
    echo '0';
} else if ($_GET['do'] == 'update') {
    $status = updateArticle();

} else {
    echo 'Cannot understand what do you want.';
}

pg_close();

/**
 * Deletes article from database
 * @param $id - id of article to delete
 * @return bool|string returns 'true' if operation was successful otherwise return 'pg_last_error()' value
 */
function deleteArticle($id)
{
    $answer = pg_query("DELETE FROM write WHERE article_id = " . $id . ";");
    if ($answer == false) {
        return pg_last_error();
    }

    $answer = pg_query("DELETE FROM attached WHERE article_id = " . $id . ";");
    if ($answer == false) {
        return pg_last_error();
    }

    $answer = pg_query("DELETE FROM articles WHERE id = " . $id . " ;");
    if ($answer == false) {
        return pg_last_error();
    } else {
        return true;
    }
}

/**
 * Function insert article to db by given values in '$_GET'
 * @return bool|string returns 'true' if operation was successful otherwise return 'pg_last_error()' value
 */
function insertArticle()
{
    $query = "INSERT INTO articles(title,publication_year,link_to_article)
			VALUES('" .
        str_replace("'", "''", $_GET['title']) . "','" .
        $_GET['pyear'] . "','" .
        $_GET['plink'] . "');";

    $answer = pg_query($query);

    if ($answer == false) {
        return pg_last_error();
    }

    $query = "SELECT id FROM articles WHERE link_to_article = '" . $_GET['plink'] . "';";

    $answer = pg_query($query);

    $row = pg_fetch_row($answer);

    $article_id = $row[0];

    $authors = explode(",", $_GET['authors']);

    for ($i = 0; $i < count($authors); $i++) {

        $author_id = getAuthorId(trim($authors[$i]));
        $query = "INSERT INTO write VALUES( '" . $author_id . "','" . $article_id . "');";
        $answer = pg_query($query);

        if ($answer == false) {
            return pg_last_error();
        }
    }

    $keywords = explode(",", $_GET['keywords']);

    for ($i = 0; $i < count($keywords); $i++) {
        $answer = pg_query("INSERT INTO attached VALUES('$article_id','" . getKeywordId(trim($keywords[$i])) . "');");
    }

    return true;
}

/**
 * Update article by '$_GET' values. Function don't take empty values
 * @return bool|string returns 'true' if operation was successful otherwise return 'pg_last_error()' value
 */
function updateArticle()
{
    $query = "UPDATE articles SET ";
    $query_end = "WHERE id=" . $_GET['id'];

    $ischanged = 0;
    if (!empty($_GET['title'])) {
        $query = $query . "title='" . str_replace("'", "''", $_GET['title']) . "' ";
        $ischanged = 1;
    }
    if (!empty($_GET['year'])) {
        $query = $query . "publication_year='" . $_GET['year'] . "' ";
        $ischanged = 1;
    }
    if (!empty($_GET['link'])) {
        $query = $query . "link_to_article='" . $_GET['link'] . "' ";
        $ischanged = 1;
    }
    if ($ischanged == 1) {
        $query = $query . $query_end;

        $answer = pg_query($query);

        if ($answer == false) {
            return pg_last_error();
        }
    }

    if (!empty($_GET['authors'])) {
        $authors_array = explode(",", $_GET['authors']);

        $query = "DELETE FROM write WHERE article_id=" . $_GET['id'];

        $answer = pg_query($query);

        if ($answer == false) {
            return pg_last_error();
        }

        for ($i = 0; $i < count($authors_array); $i++) {
            $query = "INSERT INTO write VALUES('" . getAuthorId(trim($authors_array[$i])) . "','" . $_GET['id'] . "');";

            $answer = pg_query($query);

            if ($answer == false) {
                return pg_last_error();
            }
        }
    }
    return true;
}

/**
 * Function search authors id by his name
 * @param $name author's name
 * @return string author's id
 */
function getAuthorId($name)
{
    $query = "SELECT id FROM authors WHERE name = '" . $name . "'";

    $answer = pg_query($query);

    //Found such author
    if (pg_num_rows($answer) != 0) {
        $row = pg_fetch_row($answer);
        return $row[0];
    } else {
        $query = "INSERT INTO authors(name) VALUES('" . $name . "');";

        $answer = pg_query($query);

        if ($answer == false) {
            return pg_last_error();
        }

        $query = "SELECT id FROM authors WHERE name = '" . $name . "';";
        $answer = pg_query($query);
        $row = pg_fetch_row($answer);
        return $row[0];
    }
}

/**
 * Searches keyword in db otherwise put it into DB
 * @param string keyword
 * @return int keyword's id
 */
function getKeywordId($keyword)
{
    $answer = pg_query("SELECT id FROM keywords WHERE word='$keyword';");
    if (pg_num_rows($answer) != 0) {
        return pg_fetch_row($answer)[0];
    } else {
        pg_query("INSERT INTO keywords(word) VALUES('$keyword');");
        $answer = pg_query("SELECT id FROM keywords WHERE word='$keyword';");
        return pg_fetch_row($answer)[0];
    }
}