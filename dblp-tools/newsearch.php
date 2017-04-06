<?php

/* Function get parametrs:
 *	$_GET['query'] is a search query
 *	$_GET['searchby'] shows how we search, i.e. by authors ('author') or by tiitles ('title')
 *	$_GET['offset'] shows offset (how many articles already on a page)
 *  $_GET['order']  shows if we must order query ('year' - order by year, title' - order by title)
 *  $_GET['sequence'] shows if we have ascending or descending order
 * Function returns answer in html format as described in values
 */

/*
Standard limit (how many articles script returns) is 15. You can easily change it by changing
$limit value;
 */

//limit value
$limit = 15;

include("dbConnection.php");

session_start();

if ($_SESSION['userRights'] == 'user') {
//Parts of html for answer for ordinary user
    $start = '<tr><td class="mdl-data-table__cell--non-numeric"><p class="title">';
    //$title;
    $after_title = ' </p></td><td class="mdl-data-table__cell--non-numeric"><p class="authors">';
    //$authors;
    $after_authors = '</p></td><td>';
    //$year;
    $after_year = '</td><td>';
    //$link;
    $end = '</td></tr>';
} else if ($_SESSION['userRights'] == 'admin') {
    //Parts of answer for admin
    $start = '<tr onclick="operate(this)"><td class="mdl-data-table__cell--non-numeric"><p id="res-title" class="title">';
    //$title;
    $after_title = ' </p></td><td class="mdl-data-table__cell--non-numeric"><p id="res-authors" class="authors">';
    //$authors;
    $after_authors = '</p></td><td id="res-year">';
    //$year;
    $after_year = '</td><td id="res-link">';
    //Ы$link;
    $preend = '</td><td class="id">';
    $end = '</td></tr>';
} else {
    die("<img src='http://media.giphy.com/media/RX3vhj311HKLe/giphy.gif'>");
}
$answer = array();

$authors_names = array();

if ($_GET['searchby'] == 'title') {
    $answer = getAnswerByTitle();
} elseif ($_GET['searchby'] == 'author') {
    $answer = getAnswerByAuthor();
} elseif ($_GET['searchby'] == 'keyword') {
    $answer = getAnswerByKeyword();
} elseif ($_GET['searchby'] == 'id') {
    $answer = getAnswerById();
} elseif ($_GET['searchby'] == 'year') {
    $answer = getAnswerByYear();
}

if ($answer != false) {
    printArticles($answer);
} else {
    return 0;
}
/**
 *    Function returns array of answers
 *
 * @return array of titles
 */
function getAnswerByTitle()
{

    $return_array = array();

    if (!isset($_GET['order'])) {
        $query = "SELECT id,title,publication_year,link_to_article
		FROM articles
		WHERE LOWER(title) LIKE LOWER('%" . $_GET['query'] . "%')
		LIMIT " .
            $GLOBALS['limit'] .
            " OFFSET " .
            $_GET['offset'] .
            ";";
    } else {
        $query = "SELECT id,title,publication_year,link_to_article
				FROM articles
				WHERE LOWER(title) LIKE LOWER('%" . $_GET['query'] . "%')" .
            " ORDER BY " . $_GET['order'] . " "  . $_GET['sequence'] .
            " LIMIT " . $GLOBALS['limit'] .
            " OFFSET " . $_GET['offset'] . ";";
    }

    // [0] - id; [1] title; [2] - year; [3] - link
    $answer = pg_query($query);

    if (pg_num_rows($answer) == 0) {
        return false;
    }

    for ($i = 0; $i < pg_num_rows($answer); $i++) {
        $current_row = pg_fetch_row($answer);
        $article = $GLOBALS['start'] . prettifyTitle($current_row[1]) . $GLOBALS['after_title'];
        $array_authors = getAuthorsOfArticle($current_row[0]);
        $string_authors = prettifyAuthors($array_authors);
        $article = $article . $string_authors . $GLOBALS['after_authors'] . $current_row[2] . $GLOBALS['after_year'];
        if ($_SESSION['userRights'] == 'user') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['end'];
        } else if ($_SESSION['userRights'] == 'admin') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['preend'] . $current_row[0] . $GLOBALS['end'];
        }
        array_push($return_array, $article);
    }
    return $return_array;
}

/**
 *
 *
 */
function getAnswerByAuthor()
{

    $return_array = array();

    if (!isset($_GET['order'])) {
        $query = "SELECT DISTINCT article_id,title,publication_year,link_to_article
		FROM authors,write,articles
		WHERE authors.id = write.author_id and write.article_id = articles.id
		and LOWER(authors.name) LIKE LOWER('%" . $_GET['query'] . "%')" .
            " LIMIT " . $GLOBALS['limit'] .
            " OFFSET " . $_GET['offset'] . ";";
    } else {
        $query = "SELECT DISTINCT article_id,title,publication_year,link_to_article
		FROM authors,write,articles
		WHERE authors.id = write.author_id and write.article_id = articles.id
		and LOWER(authors.name) LIKE LOWER('%" . $_GET['query'] . "%')" .
            " ORDER BY " . $_GET['order'] . " "  . $_GET['sequence'] .
            " LIMIT " . $GLOBALS['limit'] .
            " OFFSET " . $_GET['offset'] . ";";
    }
    // [0] - id; [1] title; [2] - year; [3] - link
    $answer = pg_query($query);

    if (pg_num_rows($answer) == 0) {
        return false;
    }

    for ($i = 0; $i < pg_num_rows($answer); $i++) {
        $current_row = pg_fetch_row($answer);
        $article = $GLOBALS['start'] . prettifyTitle($current_row[1]) . $GLOBALS['after_title'];
        $array_authors = getAuthorsOfArticle($current_row[0]);
        $string_authors = prettifyAuthors($array_authors);
        $article = $article . $string_authors . $GLOBALS['after_authors'] . $current_row[2] . $GLOBALS['after_year'];
        if ($_SESSION['userRights'] == 'user') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['end'];
        } else if ($_SESSION['userRights'] == 'admin') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['preend'] . $current_row[0] . $GLOBALS['end'];
        }
        array_push($return_array, $article);
    }
    return $return_array;
}

function getAnswerByYear()
{

    $return_array = array();
    if (!isset($_GET['order'])) {
        $query = "SELECT id,title,publication_year,link_to_article FROM articles
                  WHERE publication_year ='" . $_GET['query'] . "' LIMIT " . $GLOBALS['limit'] .
            " OFFSET " . $_GET['offset'] . ";";
    } else {
        $query = "SELECT id,title,publication_year,link_to_article FROM articles " .
            " WHERE publication_year ='" . $_GET['query'] . "' ORDER BY " . $_GET['order'] . " " . $_GET['sequence'] .
            " LIMIT " . $GLOBALS['limit'] .
            " OFFSET " . $_GET['offset'] . ";";
    }
    // [0] - id; [1] title; [2] - year; [3] - link
    $answer = pg_query($query);
    if (pg_num_rows($answer) == 0) {
        return false;
    }
    for ($i = 0; $i < pg_num_rows($answer); $i++) {
        $current_row = pg_fetch_row($answer);
        $article = $GLOBALS['start'] . prettifyTitle($current_row[1]) . $GLOBALS['after_title'];
        $array_authors = getAuthorsOfArticle($current_row[0]);
        $string_authors = prettifyAuthors($array_authors);
        $article = $article . $string_authors . $GLOBALS['after_authors'] . $current_row[2] . $GLOBALS['after_year'];
        if ($_SESSION['userRights'] == 'user') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['end'];
        } else if ($_SESSION['userRights'] == 'admin') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['preend'] . $current_row[0] . $GLOBALS['end'];
        }
        array_push($return_array, $article);
    }
    return $return_array;

}

function getAnswerByKeyword()
{
    $return_array = array();

    if ($_GET['order'] != 1) {
        $query = "SELECT DISTINCT article_id,title,publication_year,link_to_article
		FROM keywords,attached,articles
		WHERE keywords.id = attached.keyword_id and attached.article_id = articles.id
		and LOWER(keywords.word) LIKE LOWER('%" . $_GET['query'] . "%')" .
            " LIMIT " . $GLOBALS['limit'] .
            " OFFSET " . $_GET['offset'] . ";";
    } else {
        $query = "SELECT DISTINCT article_id,title,publication_year,link_to_article
		FROM keywords,attached,articles
		WHERE keywords.id = attached.keyword_id and attached.article_id = articles.id
		and LOWER(keywords.word) LIKE LOWER('%" . $_GET['query'] . "%')" .
            " ORDER BY title " . $_GET['sequence'] .
            " LIMIT " . $GLOBALS['limit'] .
            " OFFSET " . $_GET['offset'] . ";";
    }
    // [0] - id; [1] title; [2] - year; [3] - link

    $answer = pg_query($query);

    if (pg_num_rows($answer) == 0) {
        return false;
    }

    for ($i = 0; $i < pg_num_rows($answer); $i++) {
        $current_row = pg_fetch_row($answer);
        $article = $GLOBALS['start'] . prettifyTitle($current_row[1]) . $GLOBALS['after_title'];
        $array_authors = getAuthorsOfArticle($current_row[0]);
        $string_authors = prettifyAuthors($array_authors);
        $article = $article . $string_authors . $GLOBALS['after_authors'] . $current_row[2] . $GLOBALS['after_year'];
        if ($_SESSION['userRights'] == 'user') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['end'];
        } else if ($_SESSION['userRights'] == 'admin') {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['preend'] . $current_row[0] . $GLOBALS['end'];
        }
        array_push($return_array, $article);
    }
    return $return_array;
}

function getAnswerById()
{
    $return_array = array();
    $query = "SELECT id,title,publication_year,link_to_article
		FROM articles
		WHERE id = " . $_GET['id'] . ";";
    // [0] - id; [1] title; [2] - year; [3] - link
    $answer = pg_query($query);
    if (pg_num_rows($answer) == 0) {
        return false;
    }
    for ($i = 0; $i < pg_num_rows($answer); $i++) {
        $current_row = pg_fetch_row($answer);
        $article = $GLOBALS['start'] . prettifyTitle($current_row[1]) . $GLOBALS['after_title'];
        $array_authors = getAuthorsOfArticle($current_row[0]);
        $string_authors = prettifyAuthors($array_authors);
        $article = $article . $string_authors . $GLOBALS['after_authors'] . $current_row[2] . $GLOBALS['after_year'];
        if ($GLOBALS['user'] == 1) {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['end'];
        } else if ($GLOBALS['user'] == 0) {
            $article = $article . prettifyLink($current_row[3]) . $GLOBALS['preend'] . $current_row[0] . $GLOBALS['end'];
        }
        array_push($return_array, $article);
    }
    return $return_array;
}

/**
 *    Print all elements from array
 *
 * @param $articles - array of strings to print
 * @return int
 */
function printArticles($articles)
{

    for ($i = 0; $i < count($articles); $i++) {
        echo $articles[$i];
    }
    return 0;
}

/**
 * Checks if string is more than 80 symbols and add tags <br> in spaces
 * to make all pieces no more than 80 sybmolsю
 *
 * @param $title string title without <br> tags
 * @return string title decomposed with <br> tags
 */
function prettifyTitle($title)
{
    $return_string = '';
    $current_row_length = 0;
    //no more than 80 symbols in a row
    if (strlen($title) > 80) {
        $words = explode(" ", $title);

        for ($i = 0; $i < count($words); $i++) {
            if ($current_row_length + strlen($words[$i]) > 79) {
                $return_string = $return_string . "<br>" . $words[$i];
                $current_row_length = 0;
            } else {
                $return_string = $return_string . " " . $words[$i];
                $current_row_length = $current_row_length + strlen($words[$i]) + 1;
            }
        }
        return trim($return_string);
    } else {
        return $title;
    }
}

/**
 * Returns prettified string of authors.
 * @param string authors string
 * @return string prettified by <br> tags
 */
function prettifyAuthors($authors)
{

    if (count($authors) == 0) {
        return "NaN";
    }
    $current_row_length = 0;
    $return_string = $authors[0];
    for ($i = 1; $i < count($authors); $i++) {
        if ($current_row_length + strlen($authors[$i]) + 1 > 40) {
            $return_string = $return_string . "<br>" . $authors[$i];
            $current_row_length = 0;
        } else {
            $return_string = $return_string . ", " . $authors[$i];
            $current_row_length = $current_row_length + strlen($authors[$i]) + 1;
        }
    }
    return trim($return_string);
}

/**
 *    Make from $link good link in HTML code
 *
 * @return string <a> tag with link
 */
function prettifyLink($link)
{
    if (substr($link, 0, 3) != 'db/') {
        return "<a href='$link' target='_blank'>Link</a>";
    } else {
        return "<a href='http://dblp.uni-trier.de/$link' target='_blank'>Link</a>";
    }

}

/**
 *    Function returns array of authors of article with id $id
 *
 * @param $id - id of article
 * @return array of author's names
 */
function getAuthorsOfArticle($id)
{
    // Array to output
    $authors_names = array();
    $query = "SELECT author_id FROM write WHERE article_id='" . $id . "';";
    $answer = pg_query($query);
    if ($answer == 0) {
        die('Cannot get authors ids');
    }
    for ($i = 0; $i < pg_num_rows($answer); $i++) {
        $authors_ids = pg_fetch_row($answer);
        $query = "SELECT name FROM authors WHERE id = '" . $authors_ids[0] . "';";
        $name_answer = pg_query($query);
        $name = pg_fetch_row($name_answer);
        array_push($authors_names, $name[0]);
    }
    return $authors_names;
}

?>