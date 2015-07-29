$(document).ready(function () {
    //отправка на авторизацию
    $('#login_button').click(function () {
        $.ajax({
            type: "POST",
            url: "/admin/loginCheck/",
            data: {
                form: $("#login_form").serialize()
            },
            success: function (data) {
                if (data) window.location = "/admin/";
                else $("#error").html("Ошибка авторизации");
            }
        });

        return false;
    });


});