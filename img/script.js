var checkbox = document.getElementById('send');

checkbox.onchange = function(e){
    if (checkbox.checked){
        document.querySelector(`.form-email`).style.display = `flex`;
    }else 
        document.querySelector(`.form-email`).style.display = `none`;
}
