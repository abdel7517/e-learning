

fetch("/api/headers", {
  method: "POST",
  body: filename
}).then(response => {
  return response.text();
})
  .then(data => {
    let headers = JSON.parse(data);
    let select = document.querySelectorAll('.leads_options');
    let btn = document.getElementById('btn_send');
    let btn_api = document.getElementById('btn_api');

    select.forEach(element => {
      for (const [key, value] of Object.entries(headers)) {
        let opt = document.createElement('option');
        opt.value = value;
        opt.innerHTML = value;
        element.appendChild(opt);
      }
      let select = document.querySelector(".choice_leads");
      select.classList.remove('none');
      btn.classList.add("none")
      btn_api.classList.remove("none")
    });
  });

// get all select option and label and send to api 
function send(){

  let all_select = document.querySelectorAll(".leads_options")
  let headers = [];
  let body = []

  // console.log(all_select.length);
  all_select.forEach(select => {
    headers[select.labels[0].textContent] = select.value;
  });

  json_headers = Object.assign({}, headers);
  body.push(json_headers, filename)
  console.log(body); 

  fetch("/api/add", {
    method: "POST",
    body: JSON.stringify(body)
  }).then(response => {
    return response.text();
  })
    .then(data => {
      if(data == "ok"){
          alert(data);
        }else{
          console.log(data)
          alert("Erreur lors de l'ajout " . data);
        }
    });

}


