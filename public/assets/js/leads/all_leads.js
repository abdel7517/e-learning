// launch when user click on inscription
function interested(id) {
    Swal.fire({
        title: "1 ère étape - lien de connexion",
        html: "<button type='button' onclick='infoMail(" + id + ")' class='btn btn-primary'>Envoyer lien de connexion</button>",
        showCloseButton: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        },
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText:
            'Etape suivante ! <i class="fa fa-thumbs-up"></i>',
        confirmButtonAriaLabel: 'Thumbs up, great!',
        cancelButtonText:
            '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: 'Thumbs down'
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            Swal.fire({
                title: "2 ème étape - lien de la formation",
                html:
                    "<div class='input-group mb-3'>" +
                    "<input type='texte' class='form-control'  id='link' placeholder='Lien de la formation'>" +
                    "<button onclick='linkFormationMail(" + id + ")' id='button-addon1' class='btn btn-outline-secondary' type='button' id='button-addon1'>Envoyer</button>" +
                    "<div class='' " +
                    "</div>"
                ,
                showCloseButton: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText:
                    "<i class='fa fa-thumbs-up'></i> Etape suivante ! ",
                confirmButtonAriaLabel: 'Thumbs up, great!',
                cancelButtonText:
                    '<i class="fa fa-thumbs-down"></i>',
                cancelButtonAriaLabel: 'Thumbs down'
            }).then((result) => {
                if (result.isConfirmed) {
                    registerLead(id)
                }
            })
        }
    })

}

function infoMail(mail) {
    fetch("/api/mailInfo", {
        method: "POST",
        body: JSON.stringify(mail)
    }).then(response => {
        return response.text();
    }).then(data => {
        console.log(data)
    });
}

function linkFormationMail(mail) {
    let link = document.getElementById("link").value;
    let body = { "mail": mail, "link": link }
    fetch("/api/linkFormationMail", {
        method: "POST",
        body: JSON.stringify(body)
    }).then(response => {
        return response.text();
    }).then(data => {
        console.log(data)
    });
}

function registerLead(id) {
    Swal.fire({
        title: "3 ème étape - Date de formation",
        html: "<div id='foo'>" +
            " <input type='text' name='start'>" +
            "<span> au </span>" +
            "<input type='text' name='end'> " +
            "</div>",
        showCloseButton: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        },
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText:
            "<i class='fa fa-thumbs-up'></i> Etape suivante !",
        confirmButtonAriaLabel: 'Thumbs up, great!',
        cancelButtonText:
            '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: 'Thumbs down'
    }).then((result) => {
        if (result.isConfirmed) {
            registerFormation(id)
        }
    })
    let firstDay = new Date();
    firstDay.setDate(firstDay.getDate()+ 16);
    const elem = document.getElementById('foo');
    const datepicker = new DateRangePicker(elem, {
        defaultViewDate: firstDay,
        minDate : firstDay,
        format: 'dd-mm-yyyy',
        clearBtn : true,
        orientation : 'left',
        language :'fr'
    });
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function registerDate(id) {
    let start = document.querySelector('input[name="start"]').value
    let end = document.querySelector('input[name="end"]').value
    fetch("/api/" + id + "/" + start + "/" + end, {
        method: "POST",
    }).then(response => {
        return response.text();
    }).then(data => {
        console.log(data)
    });
}


function registerFormation(id) {
    registerDate(id)
    alert('Ce lead à bien fait une demande de formation sur CPF ?', "formation", id)
}
// launch when user click to button nrp
function nrp(id) {
    alert('Connvertir le lead en NRP', "nrp", id)
}
// launch when user click to button pas intérssez
function nointerested(id) {
    alert('Ce leads n\'est pas interessez ?', "ni", id)
}

function alert(message, newState, id) {

    Swal.fire({
        title: message,
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: 'Confirmez',
        denyButtonText: `Annulez`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) changeState(newState, id);
    })
}

function changeState(newState, id) {
    fetch("/api/register/" + newState + "/" + id, {
        method: "POST",
    }).then(response => {
        return response.text();
    }).then(data => {
            if (data == "ok") hideLeads(id)
        });
}

function hideLeads(id) {
    let elmnt = document.getElementById(id);
    elmnt.classList.add("none");
}

function saveChange(key, id) {
    // api request to save change 
    let value = document.getElementById(key + "-" + id).textContent;
    console.log(value)
    let body = { "key": key, "id": id, "value": value }
    fetch("/api/updateField", {
        method: "POST",
        body: JSON.stringify(body)
    }).then(response => {
        return response.text();
    }).then(data => {
        console.log(data)
    });
}

function getLeads(type){
    fetch("/api/get/leads/" + type , {
        method: "POST",
    }).then(response => {
        return response.text();
    })
        .then(data => {
            console.log(data)
        });
}
getLeads("formation");