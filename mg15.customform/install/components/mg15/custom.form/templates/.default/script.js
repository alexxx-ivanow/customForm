document.addEventListener('DOMContentLoaded', function(){
    let forms = document.querySelectorAll('.jsCustomFrom');

    forms.forEach(function (currentValue, currentIndex, listObj) {              
        currentValue.onsubmit = async (e) => {
            e.preventDefault();
            
            let formData = new FormData(currentValue);
            let result = currentValue.querySelector('.jsCustomResult');            

            var httpRequest = new XMLHttpRequest();
            httpRequest.responseType = "json";
            
            httpRequest.open('POST', currentValue.getAttribute('action'));
            httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // без него bitrix не принимает через isAjaxRequest
            httpRequest.responseType = "json";

            httpRequest.onreadystatechange = function(){
                if ( this.readyState == 4 && this.status == 200 ) {
                    //console.log(this.response);
                    result.innerHTML = "";
                    if(this.response.ERRORS.length){
                        let error = this.response.ERRORS;
                        let errorHtml = '';
                        error.forEach(function (item, i){
                            errorHtml += '<p>' + item + '</p>';
                        });
                        result.innerHTML = '<div class="custom_result_error">' + errorHtml + '</div>';
                    } else {
                        clearForm(currentValue);                                     
                        let success = this.response.MESSAGE;
                        let successHtml = '';
                        success.forEach(function (item, i){
                            successHtml += '<p>' + item + '</p>';
                        });
                        result.innerHTML = '<div class="custom_result_success">' + successHtml + '</div>';
                    }            
                }
            };
            httpRequest.send(formData);
        };        
    });
});

function clearForm(form) { 
    let inputs = form.querySelectorAll('input:not([type=checkbox]), textarea');
    inputs.forEach(function (current) {
        current.value = '';
    });

    let checkbox = form.querySelectorAll('input[type=checkbox]');
    checkbox.forEach(function (current) {
        current.checked = false;
    });
}