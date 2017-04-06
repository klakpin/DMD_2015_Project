
// Here we store opened row in
var openedElement;

/**
 *  Add editing article menu
 */
function operate(element) {

    if (openedElement == element) {
        openedElement = null;
        $('.control-menu').remove();
    } else {

        $('.control-menu').remove();
        openedElement = element;

        var id = element.lastChild.textContent;


        element.insertAdjacentHTML("afterEnd", "" +
            "<div class='control-menu'><input class='form-control' type='text' id='new-title' placeholder='Title'>" +
            "<input class='form-control' type='text' id='new-authors' placeholder='Authors'>" +
            "<input class='form-control' type='text' id='new-year' placeholder='Year'>" +
            "<input class='form-control' type='text' id='new-link' placeholder='Link'>" +
            "<button id='upd-btn' class='btn btn-primary'>Save changes</button>" +
            "<button id='delete-btn' class='btn btn-danger'>Delete article</button></div>");

        document.getElementById('delete-btn').onclick = function () {
            remove(id);
            $(element).remove();
            $('.control-menu').remove();
        };

        document.getElementById('upd-btn').onclick = function () {
            update(id, element);
            $('.control-menu').remove();

        }
    }
}
/**
 * Send delete request
 */
function remove(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "dblp-tools/operate.php?do=delete&id=" + id);
    xhttp.onreadystatechange = function () { // (3)
        if (xhttp.status != 200) {
            alert("xhttp.status != 200 " + xhttp.status + " statusText=" + xhttp.statusText);
        }
    }
    xhttp.send();
}


/**
 *  Send update request
 */
function update(id, element) {
    var title = document.getElementById("new-title").value;
    var authors = document.getElementById("new-authors").value;
    var year = document.getElementById("new-year").value;
    var link = document.getElementById("new-link").value;

    var xhttp = new XMLHttpRequest();

    xhttp.open("GET", "dblp-tools/operate.php?do=update&id=" + id +
        "&title=" + title +
        "&authors=" + authors +
        "&year=" + year +
        "&link=" + link);
    xhttp.onreadystatechange = function () { // (3)
        if (xhttp.status != 200) {
            alert("xhttp.status != 200 " + xhttp.status + " statusText=" + xhttp.statusText);
        }
        reloadElement(id, element);
    }
    xhttp.send();
}


/**
 *  Search article by id and replace element with it
 */
function reloadElement(id, element) {

    var xhttp = new XMLHttpRequest();

    xhttp.open("GET", "dblp-tools/newsearch.php?searchby=id&id=" + id, true);

    xhttp.onreadystatechange = function () { // (3)
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            element.innerHTML = xhttp.responseText;
        }
    }
    xhttp.send();
}

/**
 * Shows popup box for adding article
 */
function showAddMenu() {
    $("#popup").bPopup({
        speed: 650,
        transition: 'slideIn',
        transitionClose: 'slideBack'
    });
}

/**
 * Sends query to operate.php to add article
 */
function addArticle() {
    if (!checkAddInput()) {
        return;
    }

    $.get("dblp-tools/operate.php", {
        title: $("#add-title").val(), authors: $("#add-authors").val(),
        pyear: $("#add-year").val(), plink: $("#add-link").val(),
        keywords: $("#add-keywords").val(), do: "insert"
    }, function (data) {
        if (data == '0') {
            $("#messages").text("Ok!");
            $("#messages").show(1000);
            setTimeout(function () {
                $("#close-button").trigger("click");
            }, 1000);

        }

        $("#messages").hide(1);
        $("#add-title").val("");
        $("#add-authors").val("");
        $("#add-year").val("");
        $("#add-link").val("");
        $("#add-keywords").val("");
    });


}

/**
 * Check input values for correctness
 * @return Boolean if input is right function returns 'true' otherwise returns 'false'
 */
function checkAddInput() {
    if ($("#add-title").val() != "" &&
        $("#add-authors").val() != "" &&
        $("#add-year").val() != "" &&
        $("#add-link").val() != "" &&
        $("#add-keywords").val() != "") {
        return true;
    } else {
        $("#messages").text("Please, check your input.");
        $("#messages").show(300);
        return false;
    }
}
