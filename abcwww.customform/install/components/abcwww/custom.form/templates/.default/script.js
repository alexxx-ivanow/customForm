document.addEventListener('DOMContentLoaded', function(){
    document.addEventListener("cf_success", function(event) {
        console.log(event.detail);
    });
})