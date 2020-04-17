$(document).on("click", ".container table td.edit .text-group svg", function(){
    $(this).parents("tr").addClass("showInputs");
});

$(document).on("click", ".container table td.edit .input-group svg", function(){
    var _this = $(this).parents("tr");
    ajaxUpdate(_this);
});

$(document).on("keyup", ".container table td .input-group .form-control", function(e){
    if(e.keyCode == 13){
        var _this = $(this).parents("tr");
        ajaxUpdate(_this);
    }
});

$.datepicker.regional['ru'] = {
    closeText: 'Закрыть',
    prevText: 'Предыдущий',
    nextText: 'Следующий',
    currentText: 'Сегодня',
    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
    dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
    dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
    weekHeader: 'Не',
    dateFormat: 'dd.mm.yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['ru']);

$(".datepicker").datepicker({
    dateFormat: "dd.mm.yy",
    maxDate: new Date(),
    minDate: new Date("2017-07-02")
});



function ajaxUpdate(object) {

    var dataAjax = {};

    dataAjax["update"] = {};

    var error = false;

    object.find(".form-control").each(function () {

        if(!$(this).val()){
            error = true;
            $(this).addClass("is-invalid").focus();
        }

        dataAjax["update"] [$(this).attr("name")] = $(this).val();
    });

    if(!error){

        if (confirm("Вы точно хотите перезаписать данные?")) {
            $.ajax({
                type:'GET',
                url:'/'+entity+'/edit/'+parseInt(dataAjax["update"]["id"]),
                success:function (data) {
                    location.href = '/'+entity+'/';
                },
                data:dataAjax
            });

            object.removeClass("showInputs");
        }


    }
}