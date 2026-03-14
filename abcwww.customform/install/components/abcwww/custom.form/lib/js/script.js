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
            return form.querySelector("." + this.prefix + key + "_error");
        },

        getFormInput(key, form) {
            return form.querySelector("[name=" + this.prefix + key + "]");
        },

        getResultWrap(form) {
            return form.querySelector(this.result_selector);
        },

        setSuccessNotification(response, result) {
            let successHtml = "";
            response.forEach(function (item) {
                successHtml += "<div class='customForm__success'>" + item + "</div>";
            });
            result.innerHTML = successHtml;
        },

        setFailureNotification(response, result) {
            let failureHtml = "";
            response.forEach(function (item) {
                failureHtml += "<div class='customForm__error'>" + item + "</div>";
            });
            result.innerHTML = failureHtml;
        },

        clearSpanErrors(form) {
            let span_errors = form.querySelectorAll("." + this.prefix + "form_error");
            span_errors.forEach(function (item) {
                item.innerHTML = "";
            });
        },

        clearInputErrors(form) {
            let form_inputs = form.querySelectorAll("input, textarea, select");
            form_inputs.forEach(function (item) {
                item.classList.remove("error");
            });
        },

        clearForm(form) {
            let inputs = form.querySelectorAll("input:not([type=hidden]):not([type=checkbox]), textarea");
            inputs.forEach(function (current) {
                current.value = "";
            });

            let checkbox = form.querySelectorAll("input[type=checkbox]");
            checkbox.forEach(function (current) {
                current.checked = false;
            });
        },

        ensureAntispamField(form, regAttribute) {
            if (!regAttribute) return;
            if (form.querySelector('[name="' + this.getPrefix() + 'B_FIELD"]')) return;

            let nodeInput = document.createElement("input");
            nodeInput.setAttribute("type", "hidden");
            nodeInput.setAttribute("name", this.getPrefix() + "B_FIELD");
            nodeInput.setAttribute("value", regAttribute);
            form.prepend(nodeInput);
        },

        bindAntispam(form) {
            if (form.dataset.cfAntispamInit === "Y") return;
            form.dataset.cfAntispamInit = "Y";

            let regAttribute = form.getAttribute("data-register");
            if (regAttribute === null) return;

            let controls = form.querySelectorAll("input, textarea, select");
            let self = this;

            controls.forEach(function (control) {
                control.addEventListener(
                    "focus",
                    function () {
                        self.ensureAntispamField(form, regAttribute);
                        form.removeAttribute("data-register");
                    },
                    { once: true }
                );
            });
        },

        bindSubmit(form) {
            if (form.dataset.cfSubmitInit === "Y") return;
            form.dataset.cfSubmitInit = "Y";

            let self = this;

            form.onsubmit = async function (e) {
                e.preventDefault();

                let formData = new FormData(form);
                let result = self.getResultWrap(form);
                let success = false;

                let httpRequest = new XMLHttpRequest();
                httpRequest.responseType = "json";
                httpRequest.open("POST", form.getAttribute("action"));
                httpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");

                httpRequest.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        if (result !== null) {
                            result.innerHTML = "";
                        }

                        self.clearSpanErrors(form);
                        self.clearInputErrors(form);

                        if (Object.keys(this.response.ERRORS).length !== 0) {
                            let error = this.response.ERRORS;

                            for (let key in error) {
                                let field = self.getFormField(key, form);
                                let input = self.getFormInput(key, form);

                                if (field || input) {
                                    if (field) {
                                        field.innerHTML = error[key];
                                    }

                                    if (input) {
                                        input.classList.add("error");
                                    }
                                } else {
                                    console.log("Unknown key: " + key);
                                }
                            }

                            if (result !== null) {
                                self.setFailureNotification(this.response.MESSAGE, result);
                            }
                        } else {
                            self.clearForm(form);

                            if (result !== null) {
                                self.setSuccessNotification(this.response.MESSAGE, result);
                            }

                            success = true;
                        }

                        const cf_event = new CustomEvent("cf_complete", {
                            detail: {
                                cancelable: true,
                                message: this.response.MESSAGE,
                                success: success,
                                form: form,
                                errors: this.response.ERRORS,
                            },
                        });

                        document.dispatchEvent(cf_event);
                    }
                };

                httpRequest.send(formData);
            };
        },

        init() {
            let self = this;
            let forms = self.getForms();

            forms.forEach(function (form) {
                self.bindAntispam(form);
                self.bindSubmit(form);
            });
        },
    };

    function initCustomForm() {
        custom_form.init();
    }

    if (window.frameCacheVars !== undefined && window.BX) {
        BX.addCustomEvent("onFrameDataReceived", function () {
            initCustomForm();
        });
    } else if (window.BX) {
        BX.ready(function () {
            initCustomForm();
        });
    } else {
        document.addEventListener("DOMContentLoaded", function () {
            initCustomForm();
        });
    }
}