var permStorage = window.localStorage;
var tempStorage = window.sessionStorage;
var loggedIn = false;
var apiURL = "http://pokerrun.org/api/";
var debugging = true;
var scanner = {};
var dbi = {};
var logger = {};

var init = function(){
    document.addEventListener("backbutton", backKeyDown, true);
    document.addEventListener("deviceready", onDeviceReady, false);
    
    
};

function onDeviceReady(){
    scans.init();
    
}

function getLogin(){
    try{
        var linfo = permStorage.getItem('logininfo');
    }catch(er){
       console.log(er);
    }
        if(!linfo){
            console.log('getLogin returned false');
            return false;
        }else{
            //var loginInfo = JSON.parse(linfo);
            console.log(loginInfo);
            console.log('getLogin returned true');
            //dbi.initLoc();
            $.mobile.changePage('#page-menu');
            return true;
        }
    
}


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
    
    storeLocations: function(locations){
        permStorage.setItem('locations',JSON.stringify(locations));
        //create our select
        //$('#select-location').empty();
        $.each(locations, function(i, val){
            $.each(val, function(o, v){
                var locserv = JSON.stringify({locId:v.Stop.id, name:v.Location[0].name.replace(/'/g, "&apos;"), event_id:v.Event.id});
                console.log(locserv);
                $('#select-location').append("<option value='"+locserv+"'>"+v.Location[0].name+"<option>");
                
            });
            
        });
    },
    setLocation: function(){
        var loc = JSON.parse($('#select-location').val());
        var curLoc = loc.name;
        console.log('Setting location to: '+curLoc);
        permStorage.setItem('currentLocation',JSON.stringify(loc));
        
        $('#current-loc').empty();
        $('#current-loc').html('<span>You are currently checking in participants at: <br><strong>'+curLoc+'</strong> </span>');
        $.mobile.changePage('#page-menu');
    },
    initLoc:function(){
        var loc = JSON.parse(permStorage.getItem('currentLocation'));
        //var loc = JSON.parse(ob);
        
        console.log('in Location init: '+loc.name);
        if (loc !== null){
            var curLoc = loc.name;
            $('#current-loc').empty();
            $('#current-loc').html('<span>You are currently checking in participants at: <br><strong>'+curLoc+'</strong> </span>');
            var locsa = JSON.parse(permStorage.getItem('locations'));
            //var locs = JSON.parse(locsa);
            //dbi.storeLocations(locsa);
            $.each(locsa, function(i, val){
                $.each(val, function(o, v){
                    try{
                    var locserv = JSON.stringify({locId:v.Stop.id, name:v.Location[0].name.replace(/'/g, "&apos;"), event_id:v.Event.id});
                    console.log(locserv);
                    $('#select-location').append("<option value='"+locserv+"'>"+v.Location[0].name+"<option>");
                    }catch(err){
                        console.log(err);
                    }
                });

            });
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
        
        scanner = cordova.require("cordova/plugin/BarcodeScanner");
        
        console.log('Scanner Initialized');
        
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
    },
    manualEntry: function(){
       // console.log('scanning');
       var userEmail = $('#user-email').val();
       if(userEmail != ''){
            var ulocation = dbi.getLocationInfo();
            var ulogin = JSON.parse(dbi.getLoginInfo());
            var userData = {email:userEmail,
                stop_id:ulocation["locId"],
                api:ulogin["api"],
                event_id:ulocation["event_id"]}
            
            $.ajax({
                type: "POST",
                url: apiURL+'emailcheckin.json',
                data: userData
                })
                .done(function( msg ) {
                     
                    if(typeof msg.data.error == 'undefined' || msg.data.error == '' ){
                        $('#status-message').html(msg.data.status+"! <br> "+msg.data.name+" has Successfuly Checked-In!");
                    }else{
                        // handle the error
                        $('#status-message').html("Check-In Error! <br>"+msg.data.error);
                        
                    }
                    
                    $.mobile.changePage('#page-status');
                  
                });
       }else{
           alert('Email Can Not Be Blank!!');
       }
        
    },
    loginScan: function (){
        console.log('Starting Login Scan');
        
        scanner.scan( function (result) {
            //check for QR Code only
            if(result.format === "QR_CODE") {
                console.log('Result: '+result.text);
                var json = JSON.parse(result.text);
                console.log('JSON: '+json);
                $.ajax({
                type: "POST",
                url: apiURL+'login.json',
                data: json
                })
                .done(function( msg ) {
                    console.log('API said: '+msg);
                    if(typeof msg.data.error == 'undefined' || msg.data.error == '' ){
                        //before we exit to main menu, build some data
                        dbi.setLogin(result.text);
                        dbi.storeLocations(msg);
                        $.mobile.changePage($('#page-location'));
                    }else{
                        // handle the error
                        alert(msg);
                    }
                });
                
                
            }
        }, function (error) {
            alert('scan went bad');
            console.log("Scanning failed: "+error); 
        });
    }
};
$(document).ready(init);