let set = setInterval(() => {
    let logo = document.querySelector(".genially-view-logo");
    let icon = document.querySelector(".genially-view-navigation-actions-toggle-button");
    if(logo !== null)
    {
        logo.remove();
        icon.remove();
        clearInterval(set);    
    }
}, 0,1);

let quizsTexte = document.querySelector('.q')
let quizs = JSON.parse(quizsTexte.textContent);


    //add question 
    let video = document.getElementsByTagName('video')[0];
    let quiz = document.querySelector('.container-wrapper-genially ');
    let index = 0;
    let lastTime = 0;
    let onQuiz = false;
    let clickAfterMoove = true;
    let closeButton = document.getElementById('close');

    let content = document.querySelector('.example-wrapper');
    let breadcrumb = document.querySelector('.breadcrumb');
    let cardLesson = document.querySelector('.card-lesson');


    let first = false

    console.log((quizs[index]).quiz);
    addQuizToDOM(((quizs[index]).quiz));


    video.ontimeupdate = function(){if(onQuiz == false)timeUpdate()};

    function timeUpdate()
    {
        console.log(quizs[index] + " --- " + index);
        console.log(video.currentTime);

            if(video.currentTime >= (quizs[index]).seg){
                onQuiz = true;
                video.pause();
                lastTime = video.currentTime;
                addQuizToDOM((quizs[index]).quiz);
                // launch quiz 
               hidAndDisplay()
                ableClick();
                index++;
                first = true;

            }
    }

    document.addEventListener("mousemove", () => {
       if(clickAfterMoove && onQuiz) 
            {
                ableClick();
            }
        clickAfterMoove = false;
    })
    

    function ableClick(){
        let divs = document.querySelectorAll('.genially-animated-wrapper');
        divs.forEach((div)=>
        {
                div.style.PointerEvent = "inherit !important";
                div.addEventListener('click', function()
                {
                    let divs = document.getElementsByTagName('div');
                    divs.forEach((div)=>
                    {
                        console.log(div.textContent)
                        if(div.textContent == "BRAVO") relaunchVideo();
                        
                    })
                    clickAfterMoove = true;
                })
            })

    }

    function relaunchVideo(){
        // launch quiz 
        quiz.classList.add("hide");
        video.style.display = "block";
        cardLesson.style.display = "block";
        video.classList.remove("hidden")
        breadcrumb.classList.remove("hidden")
        content.classList.remove("hidden")
        video.play();
        closeButton.style.display = "none";
        onQuiz = false;
        // smoot scroll to video                            todo
    }

    function hidAndDisplay(){

        video.webkitExitFullscreen()
            content.classList.add('hidden');
            breadcrumb.classList.add('hidden');
            video.classList.add('hidden');
            setTimeout( () => {
                cardLesson.style.display = "none";
                video.style.display = "none";
                // quiz.style.position = "inherit";
                quiz.style.top = "0px";
                quiz.classList.remove('hide')
                quiz.classList.add('come');
                setTimeout(() => {
                    document.getElementById('close').style.display = "block";
                    document.getElementById('close').addEventListener("click", () => relaunchVideo())
                }, 1500);
              
            },1000 )
    }

    
    function addQuizToDOM(quiz){
        
        let quizElmnt = document.querySelector('.genially-embed');
        console.log(quiz)
        // let containerQuiz = document.querySelector(".media");
        quizElmnt.id = quiz;

        let s = document.querySelector('.s')
        if(first == true) get(document);
        console.log()

     
    }

function get(d){
    var js, id = "genially-embed-js", ref = d.getElementsByTagName("script")[0]; 
    let lastS = d.getElementById(id);
    if (d.getElementById(id)) { lastS.remove(); }
    js = d.createElement("script"); 
    js.id = id; 
    js.async = true; 
    js.src = "https://view.genial.ly/static/embed/embed.js"; 
    ref.remove();
    ref = d.getElementsByTagName("script")[0];
    document.getElementById('https://statics-view.genial.ly/view/static/js/runtime.f84e5838.js').remove();
    document.getElementById('https://statics-view.genial.ly/view/static/js/main.6881dd1a.js').remove()
    document.getElementById('https://statics-view.genial.ly/view/static/css/main.7b9c95e0.css').remove()
    
    for(let i = 0; i < 6; i++) document.getElementsByTagName('body')[0].lastChild.remove() 

    ref.parentNode.insertBefore(js, ref); 
    
}



