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

function remove_character(str, char_pos) {
    part1 = str.substring(0, char_pos);
    part2 = str.substring(char_pos + 1, str.length);
    return part2;
}

function remove_character2(str, char_pos) {
    part1 = str.substring(0, char_pos);
    part2 = str.substring(char_pos + 1, str.length);
    return part1;
}

var url = window.location.href;
var dateChoose = remove_character(url, (url).lastIndexOf("/"));

let myCalendar = new VanillaCalendar({
    selector: "#myCalendar",
    months: ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'],
    shortWeekday: ['Lun', 'Mar', 'Mer', 'Jeu', 'Vend', 'Sam', 'Dim'],
    onSelect: (data, elem) => {
        let date = formatDate(data.date);
        // window.location.href = window.location.href + '/'+ date;
        //  Mettre la date 
        var url = window.location.href;
        var newURL;
        let length = (url.match(/\//g)).length;
        if (length > 6) {

            while (length > 6) {
                var url = remove_character2(url, (url).lastIndexOf("/"));
                var newURL = url;
                length = (url.match(/\//g)).length;
            }
            console.log(newURL + "/" + date);

            window.location.href = newURL + "/0/" + date;

        }
        else {
            window.location.href = window.location.href + "/0/" + date;

        }
    }
})

function hasNumbers(t) {
    var regex = /\d/g;
    return regex.test(t);
}

// if(hasNumbers(dateChoose)){
//     let date = new Date(dateChoose);
//     myCalendar.set({'todaysDate': date});
//     let dateForData = JSON.stringify(date);
//     $(`[data-calendar-date*="${date.toDateString()}"]`).css('background', '#e7e9ed');
// }