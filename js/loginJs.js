
$("#registration-button").click (function () {
    showRegistrationMenu();
});

$("#messages").click(function () {
    $("#messages").hide(1000);
    $("#messages").show(1000);
});


function showRegistrationMenu() {
    var regButton = $("#registration-button");
    $("#login-button").hide(800);
    regButton.animate({width: "100%"}, 1000);
    regButton.blur();
    regButton.text("Register me!");
    regButton.off("click");
    regButton.click (function () {
        sendRegistrationRequest();
    });
}

function sendRegistrationRequest() {

    if ($("#email").val() != "" && $("#password").val().length >= 5) {

        $.post("registration.php", {email: $("#email").val(), password: $("#password").val()},
            function (data) {
                if (data == '0') {
                    onSuccessRegistration();
                } else {
                    $("#messages").text(data);
                    $("#messages").show(300);
                }
            });
    } else if ($("#password").val().length < 5) {
        $("#messages").text("Please, use longer password.");
        $("#messages").show(300);

    } else {
        $("#messages").text("Please, check your input.");
        $("#messages").show(300);
    }
}

function onSuccessRegistration() {
    $("#login-button").css({"margin-top": "3.66%", "margin-right": "0%", "width": "100%"});
    $("#messages").hide(200);
    $("#registration-button").hide(800);
    $("#login-button").show(1000);
    $("#login-button").text("Enter now");
    $("#email").val("");
    $("#password").val("");


}