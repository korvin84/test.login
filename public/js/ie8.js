$(document).ready(function () {
    //полизаполнитель для плейсхолдеров
    if (!("placeholder" in document.createElement("input"))) {
        $("input[placeholder]").each(function () {
            //значение аттрибура placeholder 
            var val = $(this).attr("placeholder");

            //в пустое поле возвращаем placeholder
            if (!this.value) this.value = val;

            $(this).focus(function () {
                if (this.value === val) this.value = "";
            }).blur(function () {
                if (!$.trim(this.value)) this.value = val;
            });
        });        
    }
});