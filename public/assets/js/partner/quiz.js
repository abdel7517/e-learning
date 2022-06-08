// add quiz on video 
let quizIndex = 0;
let quizContainers = document.querySelector("#quiz-parent");
let oldQuizContainers = document.querySelector("#old-quiz");
let quizInput = document.querySelector(".quiz-input");

quizInput.value !== "" ? parseQuizs() : null;


function save()
{
    let quizs = quizInput.value ? JSON.parse(quizInput.value) : [];
    // get all quiz added 
    for (let i = 0; i < quizContainers.children.length; i++) {
        // get quiz and seg value for all elmnt added 
        let quiz = quizContainers.children[i].children[0].children[0].value;
        let seg =  quizContainers.children[i].children[1].children[0].value;
              
        // save it on assoc array and add it to quizs 
        let tmp = {};
        tmp.quiz = quiz;
        tmp.seg = seg;
        tmp.id = ++quizIndex;
        quizs.push(tmp);
        quizInput.value = JSON.stringify(quizs);
    }        
}

function create(){
    addQuizsElements(quizContainers);
}

function parseQuizs()
{
    let quizs = JSON.parse(quizInput.value);
    quizIndex = quizs.length;
    // index for new quiz 
    quizContainers.classList.replace( quizContainers.classList[0] , ++quizIndex);
    quizs.forEach((quiz) => {
        addQuizsElements(oldQuizContainers, quiz)
    });

}

// parent represent the element of DOM where you want insert the quizs
function addQuizsElements(parent, quiz)
{
    let div = document.createElement('div');
    let labelQuiz = document.createElement('label')
    let textAreaQuiz = document.createElement('textarea')
    let labelSeg = document.createElement('label')
    let inputSeg = document.createElement('input')
    let buttonDelete = document.createElement('div')

    labelQuiz.innerHTML = "Script du quiz"
    textAreaQuiz.value = quiz == undefined  ? "" : quiz.quiz
    labelSeg.innerHTML = "segonde de déclenchement"
    inputSeg.value = quiz == undefined  ? "" :  quiz.seg 
    
    div.classList.add( "quiz-" + ( quiz == undefined  ? "" :  quiz.id) )
    div.classList.add('d-flex')
    div.classList.add('flex-column')

    buttonDelete.textContent = "Supprimer"
    buttonDelete.classList.add("btn-primary") 
    
    buttonDelete.setAttribute("onclick","supp('" + ( quiz == undefined  ? "" :  quiz.id) +"')");

    labelQuiz.appendChild(textAreaQuiz)
    labelSeg.appendChild(inputSeg)

    div.appendChild(labelQuiz)
    div.appendChild(labelSeg)
    div.appendChild(buttonDelete)

    parent.appendChild(div)
    quizIndex++;
    // create and add update button with id of quiz 
}


function supp(index)
{
    // get the index of quiz and delete on dom and on input (json)
    let divToSup = document.querySelector('.quiz-' + index);
    divToSup.remove();

    let quizs = JSON.parse(quizInput.value);
    quizs.forEach((quiz, i) => {
        console.log(quiz.id + '==' + index)
        if(quiz.id == index) {
            console.log("ok --- " + (quizs[i]).id)
             quizs.splice(i)
        }
    })
    console.log(quizs + " --  " + quizs.length)
    quizInput.innerHTML =  quizs.length == 0 ? "" :  JSON.stringify(quizs);

}
