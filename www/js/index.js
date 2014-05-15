var permStorage = window.localStorage;
var tempStorage = window.sessionStorage;
var loggedIn = false;
var apiURL = "http://pokerrun.org/api/";
var debugging = true;
var scanner = {};

var init = function(){
    console.log('Init App...');
    document.addEventListener("backbutton", backKeyDown, true);
    document.addEventListener("deviceready", onDeviceReady, false);
    
    
};

function onDeviceReady(){
    scans.init();
    dbi.init();
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
	console.log('back button');
        var activePage = $.mobile.activePage[0].id;
        if(activePage != 'page-login'){
            $.mobile.changePage('#page-menu');
        }
}


/*  objects */



// local database object
var dbi = {
    init: function(){
       console.log('DBI init'); 
    },
    login: function(){
        //participantlogin
        var userEmail = $('#user-email').val();
        var userPass = $('#user-pass').val();
        if(userEmail != '' && userPass != ''){
            var userData = {
                email:userEmail,
                password:userPass,
            };
            $.ajax({
                type: "POST",
                url: apiURL+'participantlogin.json',
                data: userData
                })
                .done(function( msg ) {
                     
                    if(typeof msg.data.error == 'undefined' || msg.data.error == '' ){
                         $.mobile.changePage($('#page-menu'));
                    }else{
                        // handle the error
                        alert(msg.data.error);
                        $('#user-pass').val('');
                    }
                    
                    
                  
                });
        }
    },
    getLogin: function(){
        
        var linfo = permStorage.getItem('logininfo');
        if(!linfo){
            console.log('getLogin returned false');
            return false;
        }else{
            console.log('getLogin returned true');
            return true;
        }
        
        
    },
    getLoginInfo: function(){
      return JSON.parse(permStorage.getItem('logininfo'));  
    },
    getLocationInfo: function(){
      return JSON.parse(permStorage.getItem('currentLocation'));  
    },
    setLogin: function(login){
        console.log(login);
        if(login !== ''){
            permStorage.setItem('logininfo',JSON.stringify(login));
        }
    },
    logout: function(){
        permStorage.clear();
        $.mobile.changePage($('#page-login'));
    }
};

//scanner object 
var scans = {
    init: function(){
        console.log('Initializing Scanner app');
        scanner = cordova.require("cordova/plugin/BarcodeScanner");
        
        console.log('Scanner is now Initialized');
        
        var isLoggedIn = dbi.getLogin();
        console.log(isLoggedIn);
        
        try{
            if(!isLoggedIn){
                console.log('getLogin returned false');
                $.mobile.changePage('#page-login');
            }else{
                console.log('getLogin returned true');
                dbi.initLoc();
                $.mobile.changePage('#page-menu');
            }
        }catch(err){
            console.log('Scans init error:'+err);
            alert('Scans init error: "'+err+'"\n '+isLoggedIn);
        }    
        return true;
    },
    scan: function() {
       // console.log('scanning');
       
        scanner.scan( function (result,permStorage) { 
           // is a QR code
           if(result.format === "QR_CODE") {
               
            var ulocation = dbi.getLocationInfo();
            var ulogin = JSON.parse(dbi.getLoginInfo());
            
            if(typeof(ulocation) != "object"){
                console.log("not an object");
            }
            //add to our object
            
            
            var userData = JSON.parse(result.text);
            userData["stop_id"] = ulocation["locId"];
            userData["api"] = ulogin["api"];
            
            for(x in userData){
                console.log("userData."+x+": "+userData[x]+"\n");
            }
            
            $.ajax({
                type: "POST",
                url: apiURL+'checkin.json',
                data: userData
                })
                .done(function( msg ) {
                     /*for(x in msg){
                            console.log(x+" Says "+msg[x]+" \n");
                            var t = msg[x];
                            for(l in t){
                                console.log(l+" in "+t[l]+"\n")
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
            
            /* console.log("Scanner result: \n" +
                 "text: " + result.text + "\n" +
                 "in  " + result.format + " format:\n" +
                 "cancelled: " + result.cancelled + "\n"); */
             
            
            }

        }, function (error) { 
            console.log("Scanning failed: "+error); 
        } );
    }
};
$(document).ready(init);