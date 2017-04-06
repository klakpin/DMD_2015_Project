<?php
session_start();
if (!isset($_SESSION['userRights'])) {
	header("Location: /login.php");
	exit;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Article's search engine</title>
    <link rel="shortcut icon" href="pics/index-icon.ico">
    <link rel="stylesheet" href="css/material.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Lily+Script+One' rel='stylesheet' type='
    display: block;text/css'>
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/material.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popUp.js"></script>
    <script src="js/indexJs.js"></script>
    <?php
if ($_SESSION['userRights'] == 'admin') {
	echo '<script src="js/adminJs.js"></script>';
}
?>
</head>
<body>
<!-- top line -->
<div class="col-md-12 top-line">
    <!-- Left title-->
    <div class="col-md-2 left-part">
        <h4 class="main-title">Easy search</h4>
    </div>
    <!-- End of Left title -->
    <!-- Search field -->
    <div class="col-md-6">
        <div class="input-group input-group-lg main-search">
            <input type="text" class="form-control" id="input" placeholder="Search for...">
                <span class="input-group-btn">
                <button class="btn btn-default" onclick="search()" type="button">Go!</button>
                    </span>
        </div>
    </div>
    <!-- End of search field -->
    <!-- Search setting buttons -->
    <div>
        <button id="search-menu-button" class="btn btn-info btn-lg" onclick="showSearchMenu()">Search settings</button>
        <!-- Logout button -->
        <a class="logout-button btn btn-primary btn-sm" href="logout.php">Logout</a>
        <!--  Account settings button -->
        <button class="account-button btn btn-info btn-sm" onclick="showAccountMenu()">Account</button>
        <?php
if ($_SESSION['userRights'] == 'admin') {
	echo '<button type="button" onclick="showAddMenu()" class="add-article-button btn btn-success btn-sm">
            Add article</button>';

	// Popup window with add article menu
	echo '<div id="popup">
            <div id="popup-header" class="header"><h3>Add article</h3></div>
            <div id="popup-content">
            <input id="add-title" type="text" placeholder="Title" class="form-control">
            <input id="add-authors" type="text" placeholder="Authors (separate by comma \',\')" class="form-control">
            <input id="add-year" type="text" placeholder="Year" class="form-control">
            <input id="add-link" type="text" placeholder="Link to article" class="form-control">
            <input id="add-keywords" type="text" placeholder="Keywords (separate by comma \',\')" class="form-control">
            <button id="add-button" class="btn btn-success" onclick="addArticle()">Add article</button>
            <button id="close-button" class="btn btn-info b-close">Close</button>
            <p id="messages">Kek</p>
            </div>
</div>';
}
?>
    </div>
    <!-- End of search settings -->
</div>
<!-- End of top line -->

<!-- Menu for serch settings -->
<div class="search-menu">
    <div class="search-settings">
        <h5>Search by:</h5>

        <div class="radio-buttons">
            <label id="searchby-radios" class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="searchby-title">
                <input type="radio" id="searchby-title" class="mdl-radio__button" name="searchby-radios" value="1">
                <span class="mdl-radio__label">Titles</span>
            </label>
            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="searchby-authors">
                <input type="radio" id="searchby-authors" class="mdl-radio__button" name="searchby-radios" value="2">
                <span class="mdl-radio__label">Author</span>
            </label>
            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="searchby-year">
                <input type="radio" id="searchby-year" class="mdl-radio__button" name="searchby-radios" value="3">
                <span class="mdl-radio__label">Year</span>
            </label>
            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="searchby-keywords">
                <input type="radio" id="searchby-keywords" class="mdl-radio__button" name="searchby-radios"
                       value="4<span class="
                       mdl-radio__label">Keyword</span>
            </label>
        </div>
    </div>
    <div class="search-settings-2">
        <h5>Order by:</h5>

        <div class="radio-buttons">
            <label id="orderby-radios" class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="orderby-title">
                <input type="radio" id="orderby-title" class="mdl-radio__button" name="orderby-radios" value="1"
                       checked>
                <span class="mdl-radio__label">Title</span>
            </label>

            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="orderby-year">
                <input type="radio" id="orderby-year" class="mdl-radio__button" name="orderby-radios" value="3">
                <span class="mdl-radio__label">Year</span>
            </label>
            <label id="order-radios" class=" order-radios mdl-radio mdl-js-radio mdl-js-ripple-effect" for="order-desc">
                <input type="radio" id="order-desc" class="mdl-radio__button" name="order-radios" value="1"
                       >
                <span class="mdl-radio__label">Desc</span>
            </label>

            <label id="order-radios" class="order-radios mdl-radio mdl-js-radio mdl-js-ripple-effect" for="order-asc">
                <input type="radio" id="order-asc" class="mdl-radio__button" name="order-radios" value="2" checked>
                <span class="mdl-radio__label">Asc</span>
            </label>
        </div>
    </div>
</div>
<!-- End of search settings -->
<!-- Account settings -->
<div class="account-settings">
    <h4>Your email is: <?php echo $_SESSION['email']; ?></h4>
    <input type="password" class="form-control" placeholder="Enter new password" id="new-password">
    <button id="save-pswrd-button" class="btn btn-info" onclick="saveNewPassword()">Save new password</button>
    <button id="close-account-settings-button" class="btn btn-primary" onclick="showAccountMenu()">Close</button>
</div>
<!-- end of account settings -->
<!-- Progress bar -->
<div class="col-md-12">
    <div id="progress-bar" class="mdl-progress mdl-js-progress mdl-progress__indeterminate">
    </div>
</div>
<!-- End of progress bar -->
<!-- Output table -->
<div class="col-md-12" id="output-table">
    <br/>
    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
        <thead>
        <tr>
            <th class="mdl-data-table__cell--non-numeric">Title</th>
            <th class="mdl-data-table__cell--non-numeric">Authors</th>
            <th>Year</th>
            <th>Link</th>
        </tr>
        </thead>
        <tbody id="output">
        <!-- Here will be output -->
        </tbody>
    </table>
</div>
<!-- MDL Spinner Component -->
<div id="spinner" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
</body>
</html>
