$(document).ready(function() {
    $('#submit').css('background-color', 'gray');

    $('#agree').on('click', function() {
        if ($(this).is(':checked')) {
            $('#submit').prop('disabled',false);
            $('#submit').css('background-color', '#3179BC');
        }
        else {
            $('#submit').prop('disabled',true);
            $('#submit').css('background-color', 'gray');
        }
    });

    var $spec = $('#specialist').selectize();
    var specControl = $spec[0].selectize;

    specs.forEach(element => {
        specControl.addOption({value: element, text: element});
    });

    var $center = $('#center').selectize({
        /*onChange: function(value) {
            specControl.clear();
            specControl.clearOptions();
            if (value == '') {
                specControl.disable();
            }
            else {
                specControl.enable();
                specs[value].forEach(element => {
                    specControl.addOption({value: element, text: element});
                });
            }
        }*/
    });

    var centerControl = $center[0].selectize;
    centerControl.clear();
    cities['Москва'].forEach(element => {
        centerControl.addOption({value: element, text: element});
    });

    centerControl.refreshOptions(false);
    var $city = $('#city').selectize({
        onChange: function(value) {
            centerControl.clearOptions();
            if (value == '') {
                centerControl.disable();
            }
            else {
                cities[value].forEach(element => {
                    centerControl.addOption({value: element, text: element});
                });
                centerControl.refreshOptions(false);
                centerControl.enable();
            }
        }
    });
    var cityControl = $city[0].selectize;
    cityControl.clear();
    document.querySelector('#city').closest('div').querySelector('.selectize-dropdown-content').setAttribute('style', 'display:flex;flex-direction: column-reverse;');

    var phone = document.getElementById('phoneMasked');
    var mask = IMask(phone, {
        mask: [
          {
            mask: '{+7}(000)000-00-00',
            length: '11',
          },
          {
            mask: '(000)00000',
            length: '8',
          },
          {
            mask: '00000',
            length: '5',
          }
        ],
        dispatch: function (appended, dynamicMasked) {
            var number = (dynamicMasked.value + appended).replace(/\D/g,'');

            if (number.length <= 5) {
                return dynamicMasked.compiledMasks[2];
            }
            else if (number.length > 5 && number.length <= 8) {
                return dynamicMasked.compiledMasks[1];
            }
            else {
                return dynamicMasked.compiledMasks[0];
            }
        }
    });

    $('#phoneMasked').on('input', function(){
        $("#phone_error").hide();
        $("#phone").val(this.value);
    });

    $('#center').on('change', function(){
        $("#center_error").hide();
    });

    $('#specialist').on('change', function(){
        $("#specialist_error").hide();
    });

    function validate() {
        let phone = false;
        let center = false;
        let city = false;
        let spec = false;
        let flag = false;

        if ($('#phoneMasked').val().length == 5 ||
            $('#phoneMasked').val().length == 10 ||
            $('#phoneMasked').val().length == 16)
        {
            phone = true;
            $("#phone_error").hide();
        }  else {
            $("#phone_error").show();
        }

        if ($('#center option:selected').val() != '') {
            center = true;
            $("#center_error").hide();
        }  else {
            $("#center_error").show();
        }

        if ($('#city option:selected').val() != '') {
            city = true;
            $("#city_error").hide();
        }  else {
            $("#city_error").show();
        }

        if ($('#specialist option:selected').val() != '') {
            spec = true;
            $("#specialist_error").hide();
        }  else {
            $("#specialist_error").show();
        }

        if (phone && center && spec && city) {
            flag = true;
        }

        return flag;
    }

    $('#submit').on(
        'click',
        function (event) {
            event.stopPropagation();

            if (validate()) {
                $('form').preventDoubleSubmission();
            }
            else {
                return false;
            }
        }
    );


    //Stops double click send
    jQuery.fn.preventDoubleSubmission = function() {
        $(this).on('submit',function(e){
            var $form = $(this);
            if ($form.data('submitted') === true) {
                // Previously submitted - don't submit again
                e.preventDefault();
            } else {
                // Mark it so that the next submit can be ignored
                $form.data('submitted', true);
                alert('Ваша заявка направлена специалисту ОКДЦ. С Вами свяжутся в течение одного рабочего дня')
            }
        });

        // Keep chainability
        return this;
    };
});
