if (typeof custom_form === "undefined") {
    let custom_form = {
        prefix: "CF_",
        getForms() {
            return document.querySelectorAll('.jsCustomForm');
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
            return form.querySelector('.jsCustomResult');
        },
        setSuccessNotification(response, result) {
            let success = response;
            let successHtml = '';
            success.forEach(function (item, i) {
                successHtml += '<p>' + item + '</p>';
            });
            result.innerHTML = '<div class="custom_result_success">' + successHtml + '</div>';
        },
        clearSpanErrors(form) {
            let span_errors = form.querySelectorAll('.' + this.prefix + 'form_error');
            span_errors.forEach(function (item) {
                item.innerHTML = "";
            });
        },
        clearInputErrors(form) {
            let form_inputs = form.querySelectorAll('.jsCustomForm input, .jsCustomForm textarea');
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

        // добавляем антиспам
        let inputs = document.querySelectorAll('input');
        inputs.forEach(function (input) {
            input.addEventListener('focus', function() {
                let objForm = input.closest('form');
                if(objForm != null) {
                    let regAttribute = objForm.getAttribute('data-register');
                    if(regAttribute !== null) {
                        let nodeInput = document.createElement("input");
                        nodeInput.setAttribute('type', 'hidden');
                        nodeInput.setAttribute('name', custom_form.getPrefix() + 'B_FIELD');
                        nodeInput.setAttribute('value', objForm.getAttribute('data-register'));
                        objForm.prepend(nodeInput);
                        objForm.removeAttribute('data-register');
                    }
                }
            });
        });

        let forms = custom_form.getForms();
        forms.forEach(function (currentValue, currentIndex, listObj) {
            currentValue.onsubmit = async (e) => {
                e.preventDefault();

                let formData = new FormData(currentValue);
                let result = custom_form.getResultWrap(currentValue);

                var httpRequest = new XMLHttpRequest();
                httpRequest.responseType = "json";

                httpRequest.open('POST', currentValue.getAttribute('action'));
                httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // без него bitrix не принимает через isAjaxRequest
                httpRequest.responseType = "json";

                httpRequest.onreadystatechange = function(){
                    if ( this.readyState == 4 && this.status == 200 ) {
                        result.innerHTML = "";

                        custom_form.clearSpanErrors(currentValue);
                        custom_form.clearInputErrors(currentValue);

                        //console.log(this.response);

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
                                    console.log('Неизвестный ключ: ' + key);
                                }
                            }

                            const cf_event = new CustomEvent("cf_success", {
                                detail: {
                                    //bubbles: true,
                                    //cancelable: true,
                                    message: this.response.MESSAGE,
                                    success: false,
                                    errors: this.response.ERRORS
                                }
                            });
                            document.dispatchEvent(cf_event);

                        } else {
                            custom_form.clearForm(currentValue);
                            custom_form.setSuccessNotification(this.response.MESSAGE, result);

                            const cf_event = new CustomEvent("cf_success", {
                                detail: {
                                    //bubbles: true,
                                    //cancelable: true,
                                    message: this.response.MESSAGE,
                                    success: true,
                                    errors: []
                                }
                            });
                            document.dispatchEvent(cf_event);

                        }
                    }
                };
                httpRequest.send(formData);
            };
        });
    });
}