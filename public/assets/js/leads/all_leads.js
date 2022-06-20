let lastType = "new";
displayLeads();
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
    firstDay.setDate(firstDay.getDate() + 16);
    const elem = document.getElementById('foo');
    const datepicker = new DateRangePicker(elem, {
        defaultViewDate: firstDay,
        minDate: firstDay,
        format: 'dd-mm-yyyy',
        clearBtn: true,
        orientation: 'left',
        language: 'fr'
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
function rdv(id) {
    alert('Connvertir le lead en RDV ?', "rdv", id)
}
function signe(id) {
    alert('Connvertir le lead en signé ?', "signe", id)
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

function displayLeads(type) {
    getLeads("new", "");
}

function getLeads(type, page) {
    lastType = type ? type : lastType;
    console.log(lastType + "---------")

    fetch("/api/get/leads/" + ( type !== null ? type : lastType ) + "/null/" + ( !isNaN(page) ? page : "null"), {
        method: "POST",
    }).then(response => {
        return response.text();
    })
        .then(data => {
            addToDOM(data)
        });
}

function addToDOM(lead) {
    let leads = JSON.parse(lead)
    let table = document.getElementsByTagName("tbody")
    let buttons = document.querySelectorAll('.buttons')
    table[0].textContent = ''
    let leadsLength = 0;


    // for all lead 
    for (const [key, value] of Object.entries(leads)) {
        let tr = document.createElement('tr')
        let dataObj = JSON.parse(value)
        // for all data of lead
        for (const [i, v] of Object.entries(dataObj)) {
            let th = document.createElement('th')
            if (i == "id") {
                let btns = addButtons(v, buttons[0])
                btns.forEach(btn => {
                    th.appendChild(btn)
                });
                tr.appendChild(th)
                th.setAttribute('class', 'buttons')
                break
            }
            th.setAttribute('contenteditable', true)
            th.setAttribute('id', i + "-" + dataObj.id)
            th.setAttribute('onblur', "saveChange('" + i + "', '" + dataObj.id + "' )")
            tr.setAttribute('id', dataObj.id)
            th.setAttribute('class', i)

            th.textContent = v
            tr.appendChild(th)
        }
        leadsLength++;
        table[0].appendChild(tr);
    }
    handlePagination(leadsLength);
}


function addButtons(id, buttons) {
    let btn = buttons.querySelectorAll(':scope > button')
    let buttonsnew = btn[0].cloneNode(true)
    let buttonsnew2 = btn[1].cloneNode(true)
    let buttonsnew3 = btn[2].cloneNode(true)
    let buttonsnew4 = btn[3].cloneNode(true)
    let buttonsnew5 = btn[4].cloneNode(true)


    let btns = []

    buttonsnew.setAttribute('onclick', 'interested(' + id + ')')
    buttonsnew2.setAttribute('onclick', 'nrp(' + id + ')')
    buttonsnew3.setAttribute('onclick', 'nointerested(' + id + ')')
    buttonsnew4.setAttribute('onclick', 'rdv(' + id + ')')
    buttonsnew5.setAttribute('onclick', 'signe(' + id + ')')


    btns.push(buttonsnew, buttonsnew2, buttonsnew3, buttonsnew4, buttonsnew5)

    return btns

}

function search(search) {
    // let fields = document.querySelectorAll('.'+ search)
    let searchValue = document.querySelector('.search' + search)
    // let response;
    // fields.forEach(field => {
    //     console.log(searchValue.value +" - "+ field.textContent )
    //     if(searchValue.value.trim() == field.textContent.trim()){
    //         let id = field.id.split('-')
    //         let row = document.getElementById(id[1])
    //         response = row
    //         // pop up aucune rep
    //     }
    // });
    // let table = document.getElementsByTagName("tbody")
    // table[0].textContent = ''
    // console.log(response)
    // table[0].appendChild(response)
    getLeadsWith(search, searchValue.value)
}


function getLeadsWith(typeSearch, data) {
    fetch("/api/get/leads/with/" + typeSearch, {
        method: "POST",
        body: JSON.stringify(data)
    }).then(response => {
        return response.text();
    })
        .then(data => {
            console.log(JSON.parse(data))
            addToDOMLeadsObject(data)
        });
}

function addToDOMLeadsObject(lead) {
    let leads = JSON.parse(lead)
    let table = document.getElementsByTagName("tbody")
    let buttons = document.querySelectorAll('.buttons')
    table[0].textContent = ''

    // for all lead 
    for (const [key, value] of Object.entries(leads)) {
        let tr = document.createElement('tr')
        let dataObj = JSON.parse(value.data)

        // for all data of lead
        for (const [i, v] of Object.entries(dataObj)) {
            let th = document.createElement('th')

            if (i == "id") {

                break
            }
            th.setAttribute('contenteditable', true)
            th.setAttribute('id', i + "-" + value.id)
            th.setAttribute('onblur', "saveChange('" + i + "', '" + value.id + "' )")
            tr.setAttribute('id', value.id)
            tr.setAttribute('class', i)

            th.textContent = v
            tr.appendChild(th)
        }
        let th = document.createElement('th')
        let btns = addButtons(value.id, buttons[0])
        btns.forEach(btn => {
            th.appendChild(btn)
        });
        tr.appendChild(th)
        th.setAttribute('class', 'buttons')

        table[0].appendChild(tr);
    }
}

function handlePagination(page) {
    let pagination = document.querySelector('.pagination')
    let paginations = [];
    console.log(page)
    page = (page / 10) >= 1 ? (page/10)+1 : page/10;
    console.log(page)
    if(page > 0){
        for (let i = 0; i < page; i++) {
            // console.log( (((pagination.children)[0]).children[0]) )
            let a =  ( (((pagination.children)[0]).children[0])).cloneNode(true);
            a.setAttribute('onclick', "getLeads('" + lastType +"', " + i + ")")
            a.innerHTML = i+1
            let li = document.createElement('li');
            li.append(a);
            paginations.push(li);
        }
    }else{
        let a =  ( (((pagination.children)[0]).children[0])).cloneNode(true);
            a.setAttribute('onclick', "getLeads('" + lastType +"', " + (1) + ")")
            a.innerHTML = 1
            let li = document.createElement('li');
            li.append(a);
            paginations.push(li);
    }
    // loop on pagination and delete all children
    pagination.innerHTML = '';
    paginations.forEach(li => {
        pagination.appendChild(li);
    })

}
// For add new field 
// function addField() {
//     // api request to save change 
//     // let value = document.getElementById(key + "-" + id).textContent;
//     let body = { "key": "commentaire","value": "" }
//     fetch("/api/addField", {
//         method: "POST",
//         body: JSON.stringify(body)
//     }).then(response => {
//         return response.text();
//     }).then(data => {
//         console.log(data)
//     });
// }

// addField();