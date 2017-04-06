// Counter for elements.
var elements_on_page = 0;

// Check if we can load more elements
var isMore = true;

// User's query
var query;


window.onscroll = function () {
    if (isMore == true && document.body.scrollHeight - window.innerHeight == document.body.scrollTop) {
        elements_on_page = document.getElementById("output").childNodes.length;
        loadMore();
    }
}


/**
 *  Search articles and put them to the 'output' section
 */
function search() {
    if ($(".search-menu").css("display") != "none") {
        $(".search-menu").hide();
        $("#search-menu-button").blur();
        $("#search-menu-button").text("Search settings");
    }
    isMore = true;
    var elements_on_page = 0;
    document.getElementById("progress-bar").style.visibility = "visible";
    query = document.getElementById("input").value;
    var searchBy = getSearchSettings();
    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "dblp-tools/newsearch.php?query=" + query +
        "&offset=" + elements_on_page +
        searchBy, true);

    xhttp.onreadystatechange = function () { // (3)
        document.getElementById("output").innerHTML = xhttp.status;
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            document.getElementById("output").innerHTML = xhttp.responseText;
        }
        document.getElementById("progress-bar").style.visibility = "hidden";
        document.getElementById("output-table").style.visibility = "visible";
    }
    xhttp.send();
}

/**
 *  Add new articles while scrolling
 */
function loadMore() {

    document.getElementById("spinner").style.visibility = "visible";
    document.getElementById("progress-bar").style.visibility = "visible";
    var searchBy = getSearchSettings();
    var xhttp = new XMLHttpRequest();
    xhttp.open('GET', "dblp-tools/newsearch.php?query=" + query +
        "&offset=" + elements_on_page +
        searchBy, true);
    xhttp.onreadystatechange = function () { // (3)
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            document.getElementById("output").insertAdjacentHTML("beforeend", xhttp.responseText);
            document.getElementById("progress-bar").style.visibility = "hidden";
            document.getElementById("spinner").style.visibility = "hidden";
        }
    }
    xhttp.send();
}

/**
 *  Returns XmlHttp object of a user's browser
 *  @return XMLHttpRequest object
 */
function getXmlHttp() {
    var xmlhttp;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

/**
 *  Function returns search settings for newsearch.php. Return string looks like:"&searchby=a&order=b"
 *  @return String value for 'searchby' part of sql query for newsearch.php
 */
function getSearchSettings() {
    if ($("#searchby-title").prop("checked")) {
        var searchby = "title";
    } else if ($("#searchby-authors").prop("checked")) {
        var searchby = "author";
    } else if ($("#searchby-year").prop("checked")) {
        var searchby = "year";
    } else if ($("#searchby-keywords").prop("checked")) {
        var searchby = "keyword";
    } else {
        var searchby = "title";
    }
    if ($("#orderby-title").prop("checked")) {
        var orderby = "&order=title";
    } else if ($("#orderby-year").prop("checked")) {
        var orderby = "&order=publication_year";
    } else {
        var orderby = "&order=title";
    }

    if ($("#order-asc").prop("checked")) {
        var order ="&sequence=ASC";
    } else if  ($("#order-desc").prop("checked")) {
        var order = "&sequence=DESC";
    } else {
        var order ="&sequence=ASC";
    }
    return ("&searchby=" + searchby + orderby + order);
}


/**
 * Shows popup menu for search settings
 */
function showSearchMenu() {
    if ($(".search-menu").css("display") == "none") {
        $(".search-menu").show();
        $("#search-menu-button").blur();
        $("#search-menu-button").text("Hide settings");
    } else {
        $(".search-menu").hide();
        $("#search-menu-button").blur();
        $("#search-menu-button").text("Search settings");
    }
}

function showAccountMenu() {
    if ($(".account-settings").css("display") == 'none') {
        $(".account-settings").show();
    } else {
        $(".account-settings").hide();
    }
}

function saveNewPassword() {
    $.post("changePassword.php", {password: $("#new-password").val()}, function (data) {
        if (data == 0) {
            $("#save-pswrd-button").text("Success!");
            $("#save-pswrd-button").animate({width: "100%"}, 1000);
            $("#close-account-settings-button").hide(800);

            setTimeout(function () {
                $("#close-account-settings-button").trigger("click");
                $("#close-account-settings-button").show();
                $("#save-pswrd-button").animate({width: "45%"}, 1);
                $("#save-pswrd-button").text("Save new password");
            }, 1000);

        }
    });
}