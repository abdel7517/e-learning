let shown = false;

document.getElementById('profiledrop').addEventListener("click", function () {
    if (shown === false) {
        document.getElementById('reveal').classList.remove('gone');
        document.getElementById('screen').classList.remove('gone');
    }
    if (shown === true) {
        document.getElementById('reveal').classList.add('gone');
        document.getElementById('screen').classList.add('gone');
    }
    shown = shown === false;
});

document.getElementById('screen').addEventListener("click", function () {

    document.getElementById('reveal').classList.add('gone');
    document.getElementById('screen').classList.add('gone');

    shown = false;
});


/* DRAGGABLE UI */
if (document.getElementById('js-sortable-chapters')) {

    /**
     * Dragable Chapters
     */
    // To prevent the user from changing before the ajax call could finish, we will block the interface using a modal Div.
    var blockUI = document.getElementById('js-block-ui');
    var el = document.getElementById('js-sortable-chapters');
    var sortable = Sortable.create(el, {
        animation: 150,
        ghostClass: "ghost",
        chosenClass: "chosen",
        // Element dragging ended
        onEnd: function (evt) {
            blockUI.classList.remove('hidden');

            let endPosition = evt.newIndex;  // element's new index within new parent
            let entityId = evt.item.getAttribute('data-id');
            console.log("Entity id = ", entityId)
            console.log("endPosition = ", endPosition)
            // do the ajax call to update the database
            fetch('/partner/dashboard/chapter/sort/' + entityId + '/' + endPosition)
                .then(function (res) {

                    // hide the blockUI
                    blockUI.classList.add('hidden');
                }).catch(function () {
                    alert("An error occurred while sorting. Please refresh the page and try again.")
                });
        },

    });

}

if (document.getElementById('js-sortable-pages')) {

    /**
     * Dragable Chapters
     *
     * Documentation: https://github.com/SortableJS/Sortable
     */
    var el = document.getElementById('js-sortable-pages');

    // To prevent the user from changing before the ajax call could finish, we will block the interface using a modal Div.
    var blockUI = document.getElementById('js-block-ui');
    var sortable = Sortable.create(el, {
        animation: 150,
        ghostClass: "ghost",
        chosenClass: "chosen",
        // Element dragging ended
        onEnd: function (evt) {

            blockUI.classList.remove('hidden');
            let endPosition = evt.newIndex;  // element's new index within new parent
            let entityId = evt.item.getAttribute('data-id');
            let moduleId = evt.item.getAttribute('data-module')
            console.log("Entity id = ", entityId)
            console.log("endPosition = ", endPosition)
            // do the ajax call to update the database
            fetch('/partner/page/sort/' + entityId + '/' + endPosition)
                .then(function (res) {
                    // hide the blockUI
                    blockUI.classList.add('hidden');

                }).catch(function () {
                    alert("An error occurred while sorting. Please refresh the page and try again.")
                });
        },

    });

}

// Links to open on new window
document.querySelectorAll(".ck-content a:not(.prev):not(.next)").forEach(
    function (el) {
        el.target = "_blank";
    });

  function check()
  {
    if (!confirm("Voulez-vous annuler cette formation ?"))
         event.preventDefault();
}

/* launch animation */
    document.getElementsByClassName("card-lesson")[0].classList.add('anime')
    console.log('----------------------')
  
/* zoom */
/* let more = document.getElementById("more");
let less = document.getElementById("less");

 less.addEventListener("click", less());
 
more.addEventListener("click", add());

function add()
{
    let p = document.getElementsByTagName("p");
    console.log(p);

    for (let elmnt of p) {
        let size = elmnt.style.fontSize;
        console.log(size);
        elmnt.style.fontSize = size + 2 + "em";
        }

} */
$('.question').on("click", function() {
    $(".registration-form").css("display", "block");
    console.log("heloooooo")
});
$('.close').on("click", function() {
    $(".registration-form").css("display", "none");
});

$('.sujet').on("change keyup paste",
  function(){
    if($(this).val()){
      $('.icon-paper-plane').addClass("next");
    } else {
      $('.icon-paper-plane').removeClass("next");
    }
  }
);

$('.next-button').hover(
  function(){
    $(this).css('cursor', 'pointer');
  }
);

$('.next-button.sujet').click(
  function(){
    console.log("Something");
    $('.sujet-section').addClass("fold-up");
    $('.question-section').removeClass("folded");
  }
);

$('.question').on("change keyup paste",
  function(){
    if($(this).val()){
      $('.icon-lock').addClass("next");
    } else {
      $('.icon-lock').removeClass("next");
    }
  }
);

$('.next-button').hover(
  function(){
    $(this).css('cursor', 'pointer');
  }
);

$('.next-button.question').click(
  function(){
    console.log("Something");
    $('.repeat-question-section').addClass("fold-up");
    $('.success').css("marginTop", 0);
  }
);


