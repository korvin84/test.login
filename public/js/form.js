$(document).ready(function () {
    //датапикер
    $("#dob").datepicker({
        dateFormat: "dd-mm-yy",
        defaultDate: '-25y',
        changeMonth: true,
        changeYear: true,
        yearRange: '1915:1999',
        monthNamesShort: ["Январь", "Февраль", "Март", "Апрель",
            "Май", "Июнь", "Июль", "Август", "Сентябрь",
            "Октябрь", "Ноябрь", "Декабрь"],
        dayNamesMin: ["Пн", "Вт", "Ср", "Чт",
            "Пт", "Сб", "Вс"]
    });

    //проверка на уникальность ника
    $(document).on("change", "#nick", function () {
        //alert("f");
        $.ajax({
            url: "/registration/uniqueNick/",
            method: "POST",
            data: {
                value: $("#nick").val()
            },
            success: function (data) {
                if (!data)
                    $("#nick").after("<p class=\"error\">Этот ник занят</p>");
            }
        });

    });

    //удаляем сообщения об ошибке
    $('#nick').on("propertychange input", function (ev) {
        $('#nick').next('p').remove();
    });

    $('#register_form :input').focus(function () {
        $(this).next('p').remove();
        $(this).css("border", "0");
    });



    //отправка на регистраицю
    $('#register_button').click(function () {

        //очищаем поля от плейсхолдеров
        $("input[placeholder]").each(function () {
            if (this.value === $(this).attr("placeholder"))
                this.value = "";
        });



        $('p.error').remove();
        $.ajax({
            type: "POST",
            url: "/registration/adduser/",
            data: {
                form: $("#register_form").serialize()
            },
            success: function (data) {
                if ($.isNumeric(data))
                {
                    //registration complete
                    $('div.form').remove();
                    $('article').html("Данные отправлены. ID: " + data);
                }
                else
                {
                    var receiveData = $.parseJSON(data);

                    //выводим ошибки                
                    $.each(receiveData, function (key, value) {
                        //alert(key + '->' + value);
                        $("#" + key).after("<p class=\"error\">" + value + "</p>");
                        if ($("#" + key).is("input"))
                            $("#" + key).css("border", "1px solid red");
                    });
                    //alert(receiveData.dob);
                }
            }
        });

        return false;
    });


    //выбор страны региона города
    var countryId, regionName;

    $("#country").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "http://api.vk.com/method/database.getCountries?v=5.5&need_all=1&count=1000",
                dataType: "jsonp",
                success: function (data) {
                    if (data.error) return false;
                    var pattern = new RegExp("^" + request.term, "i");
                    var results = $.map(data.response.items, function (item) {
                        if (pattern.test(item.title)) {
                            return item;
                        }
                    });

                    response($.map(results, function (item) {
                        return{
                            countryid: item.id,
                            value: item.title
                        };
                    }));
                }
            });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
            countryId = ui.item.countryid;
        }
    });

    $("#region").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "http://api.vk.com/method/database.getRegions?v=5.5&need_all=1&offset=0&count=1000",
                dataType: "jsonp",
                data: {
                    q: request.term,
                    country_id: countryId
                },
                success: function (data) {
                    if (data.error) return false;
                    var pattern = new RegExp(request.term, "i");
                    var results = $.map(data.response.items, function (item) {
                        if (pattern.test(item.title)) {
                            return item;
                        }
                    });

                    response($.map(results, function (item) {
                        return{
                            value: item.title
                        };
                    }));
                }
            });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
            regionName = ui.item.title;
        }
    });

    $("#city").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "http://api.vk.com/method/database.getCities?count=1000",
                dataType: "jsonp",
                data: {
                    q: request.term,
                    country_id: countryId,
                    need_all: 1
                },
                success: function (data) {
                    if (data.error) return false;
                    var pattern = new RegExp(request.term, "i");
                    var results = $.map(data.response, function (item) {
                        if (pattern.test(item.title) &&
                                (item.region === $("#region").val() || !item.region)) {
                            return item;
                        }
                    });

                    response($.map(results, function (item) {
                        return{
                            value: item.title
                        };
                    }));
                }
            });
        },
        minLength: 1,
        delay: 500
    });

    //выбор файлов
    $('#files').uploadify({
        method: 'post',
        height: 50,
        swf: '/data/uploadify.swf',
        uploader: '/registration/upload/',
        buttonText: 'Загрузить фото',
        buttonClass: 'upload-button',
        fileSizeLimit: '1.5MB',
        fileTypeDesc: 'Изображения',
        fileTypeExts: '*.gif; *.jpg; *.jpeg; *.png',
        itemTemplate: '<div id="${fileID}" class="upload"></div>',
        //queueSizeLimit: 5,
        //uploadLimit: 5,
        //removeTimeout: 99999,
        removeCompleted: false,
        debug: false,
        onUploadStart: function (file) {
            if ($("#file_upload-queue > div").length > 5)
            {
                $('#files').uploadify('cancel');
                $("#error").html("<p>Превышен лимит файлов. Некоторые файлы не были загружены</p>");
                return false;
            }
        },
        onCancel: function (file) {
            $('#files').uploadify('cancel', file.id);
            $("#" + file.id).remove();
        },
        onUploadSuccess: function (file, data, response) {
            var dataArray = $.parseJSON(data);

            if (!response)
            {
                $("#error").html("Ошибка загрузки");
            }
            else if (dataArray.error)
            {
                $("#error").html(dataArray.error);
            }
            else {
                $("#" + file.id).append('<img src="' + dataArray.preview + '" /><br />')
                        .append('<p class="left"><a href="javascript:$(\'#files\').uploadify(\'cancel\', \'' + file.id + '\')">X</a></p>')
                        .append('<p class="right"><a class="ava" id="#' + file.id + '" href="javascript:">На аву</a></p>')
                        .append('<input name="files[]" type="hidden" value="' + dataArray.path + '" />');
            }
        },
        onDialogOpen : function() {
            $('p.error').remove();
        }
    });

    //выбор аватара
    $(document).on("click", "a.ava", function () {
        //удаляем границы у всех изображений
        $(this).parent().parent().parent().find("img").css("border", "5px solid #271604");
        //ставим границы у выбранного
        $(this).parent().parent().children("img").css("border", "5px solid red");
        //получаем значение скрытого поля с путем файла
        var choosen_ava = $(this).parent().parent().find("input[type=hidden]").val();
        $("#avatar").html('<input name="avatar" type="hidden" value="' + choosen_ava + '" />');
    });

    //убираем сообщ об ошибке
    $(document).click(function () {
        $("#error").html("");
    });

});




function dump(obj, withn) {
    var out = '';
    for (var i in obj) {
        out += obj[i];
        if (withn) out += "\n";
    }


    var pre = document.createElement('pre');
    pre.innerHTML = out;
    document.body.appendChild(pre);
}

