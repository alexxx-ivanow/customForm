if (typeof custom_form === "undefined") {
    let custom_form = {
        prefix: "CF_",
        form_selector: ".jsCustomForm",
        result_selector: ".jsCustomResult",
        getForms() {
            return document.querySelectorAll(this.form_selector);
        },
        getPrefix() {
            return this.prefix;
        },
        getFormField(key, form) {
            return form.querySelector('.' + this.prefix + key + '_error');
        },
        getFormInput(key, form) {
            return form.querySelector('[name=' + this.prefix + key);
        },
        getResultWrap(form) {
            return form.querySelector(this.result_selector);
        },
        setSuccessNotification(response, result) {
            let successHtml = '';
            response.forEach(function (item, i) {
                successHtml += '<p>' + item + '</p>';
            });
            result.innerHTML = '<span class="custom_result_success">' + successHtml + '</span>';
        },
        setFailureNotification(response, result) {
            let failureHtml = '';
            response.forEach(function (item, i) {
                failureHtml += '<p>' + item + '</p>';
            });
            result.innerHTML = '<span class="custom_result_error">' + failureHtml + '</span>';
        },
        clearSpanErrors(form) {
            let span_errors = form.querySelectorAll('.' + this.prefix + 'form_error');
            span_errors.forEach(function (item) {
                item.innerHTML = "";
            });
        },
        clearInputErrors(form) {
            let form_inputs = form.querySelectorAll('input, textarea');
            form_inputs.forEach(function (item) {
                item.classList.remove('error');
            });
        },
        clearForm(form) {
            let inputs = form.querySelectorAll('input:not([type=hidden]):not([type=checkbox]), textarea');
            inputs.forEach(function (current) {
                current.value = '';
            });

            let checkbox = form.querySelectorAll('input[type=checkbox]');
            checkbox.forEach(function (current) {
                current.checked = false;
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function(){

        let forms = custom_form.getForms();
        forms.forEach(function (currentValue, currentIndex, listObj) {

            // добавляем антиспам
            let regAttribute = currentValue.getAttribute('data-register');
            if(regAttribute !== null) {
                let inputs = currentValue.querySelectorAll('input');
                inputs.forEach(function (input) {
                    input.addEventListener('focus', function() {
                        let nodeInput = document.createElement("input");
                        nodeInput.setAttribute('type', 'hidden');
                        nodeInput.setAttribute('name', custom_form.getPrefix() + 'B_FIELD');
                        nodeInput.setAttribute('value', regAttribute);
                        currentValue.prepend(nodeInput);
                        currentValue.removeAttribute('data-register');
                    });
                });
            }

            // отправка формы
            currentValue.onsubmit = async (e) => {
                e.preventDefault();

                let formData = new FormData(currentValue);
                let result = custom_form.getResultWrap(currentValue);
                let success = false;

                var httpRequest = new XMLHttpRequest();
                httpRequest.responseType = "json";

                httpRequest.open('POST', currentValue.getAttribute('action'));
                httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // без него bitrix не принимает через isAjaxRequest
                httpRequest.responseType = "json";

                httpRequest.onreadystatechange = function(){
                    if ( this.readyState == 4 && this.status == 200 ) {

                        // сбрасываем поля валидации и оповещение
                        if(result !== null) {
                            result.innerHTML = "";
                        }
                        custom_form.clearSpanErrors(currentValue);
                        custom_form.clearInputErrors(currentValue);

                        // если есть ошибки, выдаем предупреждения полям
                        if (Object.keys(this.response.ERRORS).length !== 0) {
                            let error = this.response.ERRORS;
                            for (var key in error) {
                                let field = custom_form.getFormField(key, currentValue);
                                let input = custom_form.getFormInput(key, currentValue);
                                if(field || input) {
                                    if(field) {
                                        field.innerHTML = error[key];
                                    }
                                    if(input) {
                                        input.classList.add('error');
                                    }
                                } else {
                                    console.log('Unknown key: ' + key);
                                }
                            }
                            if(result !== null) {
                                custom_form.setFailureNotification(this.response.MESSAGE, result);
                            }
                        } else { // успешная отправка
                            custom_form.clearForm(currentValue);
                            if(result !== null) {
                                custom_form.setSuccessNotification(this.response.MESSAGE, result);
                            }
                            success = true;
                        }

                        // генерируем событие на отправку формы
                        const cf_event = new CustomEvent("cf_complete", {
                            detail: {
                                cancelable: true,
                                message: this.response.MESSAGE,
                                success: success,
                                form: currentValue,
                                errors: this.response.ERRORS
                            }
                        });
                        document.dispatchEvent(cf_event);
                    }
                };
                httpRequest.send(formData);
            };
        });
    });
}