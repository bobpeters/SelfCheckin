var permStorage = window.localStorage;
var tempStorage = window.sessionStorage;
var loggedIn = false;
var apiURL = "http://pokerrun.org/api/";
var debugging = true;
var scanner = {};

var logger = function(msg){
    if(debugging){
        logger(msg);
    }
}

var init = function(){
    logger('Init App...');
    document.addEventListener("backbutton", backKeyDown, true);
    document.addEventListener("deviceready", onDeviceReady, false);
    
    
};

function onDeviceReady(){
    scans.init();
    //dbi.init();
}
/*
 * function: backKeyDown
 * @returns null
 * 
 * Sends user back to the menu page 
 * when back key is touched
 * 
 */
function backKeyDown(){
	logger('back button');
        var activePage = $.mobile.activePage[0].id;
        if(activePage != 'page-login'){
            $.mobile.changePage('#page-menu');
        }
}

//scanner object 
var scans = {
    init: function(){
        logger('Initializing Scanner app');
        scanner = cordova.require("cordova/plugin/BarcodeScanner");
        
        logger('Scanner is now Initialized');
        
        var isLoggedIn = dbi.getLogin();
        logger(isLoggedIn);
        
        try{
            if(!isLoggedIn){
                logger('getLogin returned false');
                $.mobile.changePage('#page-login');
            }else{
                logger('getLogin returned true');
                dbi.initLoc();
                $.mobile.changePage('#page-menu');
            }
        }catch(err){
            logger('Scans init error:'+err);
            alert('Scans init error: "'+err+'"\n '+isLoggedIn);
        }    
        return true;
    },
    scan: function() {
       // logger('scanning');
       
        scanner.scan( function (result,permStorage) { 
           // is a QR code
           if(result.format === "QR_CODE") {
               
            
            
            var stopData = JSON.parse(result.text);
            stopData["user_id"] = userData["user_id"];
            //var event_id = userData["event_id"];
            
            for(x in userData){
                logger("userData."+x+": "+userData[x]+"\n");
            }
            
            $.ajax({
                type: "POST",
                url: apiURL+'participantcheckin.json',
                data: userData
                })
                .done(function( msg ) {
                     /*for(x in msg){
                            logger(x+" Says "+msg[x]+" \n");
                            var t = msg[x];
                            for(l in t){
                                logger(l+" in "+t[l]+"\n")
                            }
                        }*/
                    if(typeof msg.data.error == 'undefined' || msg.data.error == '' ){
                        $('#status-message').html(msg.data.status+"! <br> "+msg.data.name+" has Successfuly Checked-In!");
                    }else{
                        // handle the error
                        $('#status-message').html("Check-In Error! <br>"+msg.data.error);
                        
                    }
                    
                    $.mobile.changePage('#page-status');
                  
                });
            
            /* logger("Scanner result: \n" +
                 "text: " + result.text + "\n" +
                 "in  " + result.format + " format:\n" +
                 "cancelled: " + result.cancelled + "\n"); */
             
            
            }

        }, function (error) { 
            logger("Scanning failed: "+error); 
        } );
    }
};
$(document).ready(init);