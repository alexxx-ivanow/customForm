if (AbcwwwMaskPhone === undefined) {
    var AbcwwwMaskPhone = new function () {
        this.isNum = function (value) {
            return /\d/.test(value);
        };
        this.maskEditValue = function (input, pos) {
            let posReturn = pos;
            let posReturnChange = true;
            if (pos > 0) {
                pos--;
            }

            let valueNew = '';
            for (let posValue = 0; posValue < pos; posValue++) {
                valueNew += input.value.charAt(posValue);
            }

            let posMask = pos;
            for (let posValue = pos; posValue < input.value.length; posValue++) {
                if (posMask < input.mask.length) {
                    let simbolValue = input.value.charAt(posValue);
                    let simbolMask = input.mask.charAt(posMask);

                    if (
                        simbolMask == simbolValue
                        || (simbolMask == '9' && AbcwwwMaskPhone.isNum(simbolValue))
                    ) {
                        posReturnChange = false;
                        valueNew += simbolValue;
                        posMask++;
                    } else if (
                        simbolMask == '9'
                        && !AbcwwwMaskPhone.isNum(simbolValue)
                    ) {
                        posReturnChange = false;
                    } else if (
                        simbolMask != '9'
                        && AbcwwwMaskPhone.isNum(simbolValue)
                    ) {
                        valueNew += simbolMask;
                        posMask++;
                        posValue--;
                        if (posReturnChange) {
                            posReturn++;
                        }
                        if (
                            input.valueOld.length > posReturn
                            && input.valueOld.charAt(posReturn - 1) == simbolMask
                        ) {
                            while (posReturn > 1 && !AbcwwwMaskPhone.isNum(input.valueOld.charAt(posReturn - 1))) {
                                posReturn--;
                            }
                        }
                    } else if (
                        simbolMask != '9'
                        && !AbcwwwMaskPhone.isNum(simbolValue)
                    ) {
                        valueNew += simbolMask;
                        posMask++;
                    }
                }
            }
            input.value = valueNew;
            input.valueOld = valueNew;

            let valueNotFull = '';
            for (let posMask = 0; posMask < input.mask.length; posMask++) {
                let simbolMask = input.mask.charAt(posMask);
                if (simbolMask == '9') {
                    if (input.value.length <= posMask) {
                        input.value = valueNotFull;
                        input.valueOld = valueNotFull;
                        posReturn = input.value.length;
                    }
                    break;
                }
                valueNotFull += simbolMask;
            }

            return posReturn;
        };
        this.cursorPosition = function (input) {
            let posMinimum = 0;
            for (let posMask = 0; posMask < input.mask.length; posMask++) {
                let simbolMask = input.mask.charAt(posMask);
                if (
                    simbolMask == '9'
                    || input.value.length <= posMask
                ) {
                    posMinimum = posMask;
                    break;
                }
            }
            if (
                input.selectionStart < posMinimum
                || input.selectionEnd < posMinimum
            ) {
                input.selectionStart = posMinimum;
                input.selectionEnd = posMinimum;
            }
        }
        this.maskDefault = '+7 (999) 999-99-99';

        this.init = function (input, mask, placeholder) {
            placeholder = placeholder === undefined || placeholder === true ? true : false;
            if (!input) {
                return false;
            }
            if (!input.classList.contains('is-masked')) {
                input.mask = mask || AbcwwwMaskPhone.maskDefault;
                input.valueOld = input.value;
                AbcwwwMaskPhone.maskEditValue(input, 0);
                if (input.value.length != input.mask.length) {
                    input.value = '';
                }
                if (placeholder) {
                    //input.setAttribute('placeholder', input.mask);
                }

                input.classList.add('is-masked');

                input.addEventListener('input', function () {
                    let posReturn = AbcwwwMaskPhone.maskEditValue(this, this.selectionStart);
                    this.selectionStart = posReturn;
                    this.selectionEnd = posReturn;
                });
                input.addEventListener('focus', function () {
                    if (this.value.length <= 0) {
                        this.value = this.valueOld;
                        this.selectionStart = this.value.length;
                        this.selectionEnd = this.value.length;
                    }
                });
                input.addEventListener('blur', function () {
                    if (this.value.length != this.mask.length) {
                        this.value = '';
                    }
                });
                input.addEventListener('keydown', function () {
                    let obInput = this;
                    setTimeout(function () {
                        AbcwwwMaskPhone.cursorPosition(obInput);
                    }, 10);
                });
                input.addEventListener('keyup', function () {
                    AbcwwwMaskPhone.cursorPosition(this);
                });
                input.addEventListener('click', function () {
                    AbcwwwMaskPhone.cursorPosition(this);
                });
            }
        };
    };
}

document.addEventListener('DOMContentLoaded', function(){
    let jsTels = document.querySelectorAll('.jsCFTel');
    console.log(jsTels)
    jsTels.forEach(function (value) {
        AbcwwwMaskPhone.init(value, '+7 (999) 999-99-99', false);
    });
});