class Session {
    constructor() {
        this.reset;
        this.endOfSession = false;
        this.time = 0;
        this.sessionId = session_id;
        this.date = new Date;
        this.containerTimer = document.getElementById("timer");
        this.startTimer(20*60);
        this.sendPresence();
        window.addEventListener('mousemove', function() {newSession.restartTimer()} );
        window.addEventListener('touchstart', function() {newSession.restartTimer()} );
        document.addEventListener('visibilitychange', this.changementVisibilite, false);
        this.callPresence = setInterval(function(){ newSession.sendPresence() }, 20000);
    
    }
     addMinutes(date, minutes) {
        return new Date(date.getTime() + minutes*60000);
    }
     changementVisibilite() {
        if(document.hidden){
            clearInterval(newSession.callPresence)
            clearInterval(newSession.reset)
            // console.log("on me vois plus")

        }else{
            let now = new Date;
            // console.log("----------------------- re " + now)
            let long_freeze = newSession.timeDiffCalc(newSession.date, now);
            if(long_freeze)
            {
                newSession.callPresence = setInterval(function(){ newSession.sendPresence() }, 20000);
                // console.log('continue')
                newSession.startTimer(20*60);
                // console.log("-------" + newSession.date)

            }else{
                // console.log("-------" + newSession.date)
                let timeDeco = newSession.addMinutes(newSession.date, 20);
                let dateString = newSession.formatDate(timeDeco);
                // console.log(window.location.origin + "/deco/" +user_id + "/" + dateString + "/"+ newSession.sessionId );
                let url = window.location.origin + "/deco/" +user_id + "/" + dateString + "/"+ newSession.sessionId;
                $.ajax({
                    type: 'POST',
                    url: url,
                    complete: function(resultat){
                        // console.log('deco');
                        window.alert('Vous êtes déconnecté (inactivé trop longue depuis votre dernière session), vous allez commencez une nouvelle session');
                        window.location.reload();
                    }
                  });
               newSession.endOfSession = true;

            }

        }
      }

    launchSession(){
        
      
          if(this.endOfSession == false){
            let date = new Date;
            let dateString = this.formatDate(date);
            // console.log(window.location.origin + "/co/" +user_id + "/" + dateString + "/"+ this.sessionId );
            let url = window.location.origin + "/co/" +user_id + "/" + dateString + "/"+ this.sessionId;
            $.ajax({
                type: 'POST',
                url: url,
                complete: function(resultat){
                    // console.log( "--------------" + resultat.responseText);
                    session_id = resultat.responseText;
                }
                });
          }
    }

    closeSession(event){

        if(this.endOfSession == false){
            let date = new Date;
            let dateString = this.formatDate(date);
            // console.log(window.location.origin + "/deco/" +user_id + "/" + dateString + "/"+ this.sessionId );
            let url = window.location.origin + "/deco/" +user_id + "/" + dateString + "/"+ this.sessionId;
            $.ajax({
                type: 'POST',
                url: url,
                complete: function(resultat){
                    $('.deco').css('display', 'block');
                    // console.log('deco');
                    window.alert('Vous êtes déconnecté (inactivé trop longue depuis votre dernière session), vous allez commencez une nouvelle session');
                    window.location.reload();
                }
              });
           this.endOfSession = true;
        }

    }

    sendPresence(){
        // console.log(this.date);
        if(this.endOfSession ==  false ){
            let date = new Date;
            let dateString = this.formatDate(date);
            let url = window.location.origin + "/presence/" + user_id + "/" + dateString + "/"+ this.sessionId;
            // console.log(url);
            $.ajax({
                    type: 'POST',
                    url: url,
                    complete: function(resultat){
                        // console.log(resultat);
                        if(resultat.responseText == "deco"){
                            newSession.endOfSession = true;
                            window.alert('Vous êtes déconnecté, rechargez la page');
                            window.location.reload(); 
                        }
                    }
                });
        }else{
            this.closeSession();
        }
       
    }

    restartTimer(){
        // relaunch the timer 
        this.stopTimer();
        this.date = new Date;
        // console.log(this.date);
        this.startTimer(20*60);

    }

    startTimer(duration) {
        this.time = duration;
        this.updateTimer();
        this.reset = setInterval(() => this.countdown(), 1000);
    }

    countdown() {
        this.time--; // 1. 1199
        this.updateTimer();
        sessionStorage.setItem('timer' + 'time', this.time)
        if (this.time <= 0) {
            // stop timer and send end of session to API
            this.stopTimer();
            this.closeSession();
        }
    }

    updateTimer() {
        let minutes = Math.floor(this.time / 60); //1.20   2. 19
        let seconds = this.time - minutes * 60; // 1. 0    2.  1199 - 1140 = 59
        if (minutes < 10) minutes = '0' + minutes;
        if (seconds < 10) seconds = '0' + seconds;

        // $("#timer").html(minutes + ':' + seconds);
    //    console.log(minutes + ':' + seconds);
    //    console.log(new Date )
    }

    stopTimer() {
        clearInterval(this.reset);
        sessionStorage.removeItem('timer' + 'time');
    }

    formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear(),
            hour = d.getHours(),
            minute = d.getMinutes();
        
        if (hour.length < 2){
            hour = '0' + hour; 
        } 
        if (minute.length < 2) 
        minute = '0' + minute;

        if (month.length < 2) 
            month = '0' + month;

        if (day.length < 2) 
            day = '0' + day;

        let hours = [String(hour).padStart(2, '0'), String(minute).padStart(2, '0')].join(':');
        let days = [year, month, day].join('-');

        return days + "_"+ hours;
    }

   timeDiffCalc(lastDate, dateNow) {
    let diffInMilliSeconds = Math.abs(lastDate - dateNow) / 1000;

    // calculate days
    const days = Math.floor(diffInMilliSeconds / 86400);
    diffInMilliSeconds -= days * 86400;

    // calculate hours
    const hours = Math.floor(diffInMilliSeconds / 3600) % 24;
    diffInMilliSeconds -= hours * 3600;

    // calculate minutes
    const minutes = Math.floor(diffInMilliSeconds / 60) % 60;
    diffInMilliSeconds -= minutes * 60;

    if (days > 0) {
        return false;
    }
    if( hours > 0)
    {
        let nowMinute = dateNow.getMinutes() + 60;
        // console.log(lastDate.getMinutes() -  nowMinute);
        if((lastDate.getMinutes() -  nowMinute) >= 20 )
        {
            return false;
        }
        return true;
    }
    if(minutes < 20)
    {
        return true;
    }
    // console.log(lastDate)
 
    
  }

}
