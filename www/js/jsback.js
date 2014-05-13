/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
var apiURL = "http://pokerrun.org/api/";
var debugging = true;
var scanner = {};
var permStorage = localStorage;

//scans object
var logger = {
    init:function(){
      this.log('Logger Ready');  
    },
    log: function(msg){
        if(debugging){
            console.log(msg);
        }
    }
};

var dbi = {
    init: function(){
       logger.log('DBI init'); 
    },
    getLogin: function(){
        var loginInfo = JSON.parse(permStorage.getItem('logininfo'));
        logger.log(loginInfo);
        if(loginInfo != null && loginInfo != false){
            logger.log('getLogin returned true');
            return loginInfo;
        }else{
            logger.log('getLogin returned false');
            return false;
        }
    },
    setLogin: function(login){
        logger.log(login);
        if(login !== ''){
            permStorage.setItem('logininfo',login);
        }
    },
    
    storeLocations: function(locations){
        permStorage.setItem('locations',locations);
        //create our select
        //$('#select-location').empty();
        $.each(locations, function(i, val){
            $.each(val, function(o, v){
                try{
                var locserv = JSON.stringify({locId:v.Location[0].id, name:v.Location[0].name});
                logger.log(locserv);
                $('#select-location').append("<option value='"+locserv+"'>"+v.Location[0].name+"<option>");
                }catch(err){
                    logger.log(err);
                }
            });
            
        });
    },
    setLocation: function(){
        var loc = JSON.parse($('#select-location').val());
        logger.log('Setting location to: '+loc);
        permStorage.setItem('currentLocation',loc);
        var curLoc = loc.name;
        logger.log('Current location is: '+loc);
        $('#current-loc').empty();
        $('#current-loc').html('<span>You are currently checking in participants at: <br><strong>'+curLoc+'</strong> </span>');
        $.mobile.changePage('#page-menu');
    },
    initLoc:function(){
        var ob = permStorage.getItem('currentLocation');
        var loc = JSON.parse(ob);
        logger.log('in Location init: '+loc);
        if (loc !== null){
            var curLoc = loc.name;
            $('#current-loc').empty();
            $('#current-loc').html('<span>You are currently checking in participants at: <br><strong>'+curLoc+'</strong> </span>');
            var locsa = permStorage.getItem('locations');
            var locs = JSON.parse(locsa);
            this.storeLocations();
        }
    }
};

var scans = {
    init: function(){
        try{
        scanner = cordova.require("cordova/plugin/BarcodeScanner");
        logger.log('Scanner Initialized');
    }catch(err){
            logger.log('Scans init error:'+err);
            alert('Scans init error:'+err);
    }
    },
    scan: function() {
       // logger.log('scanning');
       
        scanner.scan( function (result) { 

           /* alert("We got a barcode\n" + 
            "Result: " + result.text + "\n" + 
            "Format: " + result.format + "\n" + 
            "Cancelled: " + result.cancelled);  */

           logger.log("Scanner result: \n" +
                "text: " + result.text + "\n" +
                "format: " + result.format + "\n" +
                "cancelled: " + result.cancelled + "\n");
            $("#info").innerHTML = result.text;
            logger.log(result);
            

        }, function (error) { 
            logger.log("Scanning failed: "+error); 
        } );
    },

    loginScan: function (){
        logger.log('Starting Login Scan');
        
        scanner.scan( function (result) {
            //check for QR Code only
            if(result.format === "QR_CODE") {
                logger.log('Result: '+result.text);
                var json = JSON.parse(result.text);
                logger.log('JSON: '+json);
                $.ajax({
                type: "POST",
                url: apiURL+'login.json',
                data: json
                })
                .done(function( msg ) {
                    logger.log('API said: '+msg);
                    if(msg.data.error != 'Invalid Login!'){
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
            logger.log("Scanning failed: "+error); 
        });
    }
};



//  main app object
var app = {
    
    // Application Constructor
    init: function() {
        try{
        logger.log('Binding Events');
        //scans.initialize();
        this.bindEvents();
       //alert('scans should be initialized');
        }catch(err){
            //alert('App init error:'+err);
            logger.log('App init error:'+err);
        }
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // `load`, `deviceready`, `offline`, and `online`.
    bindEvents: function() {
        $(document).on('deviceready', this.onDeviceReady);
        
        
    },

    // deviceready Event Handler
    //
    // The scope of `this` is the event. In order to call the `receivedEvent`
    // function, we must explicity call `app.receivedEvent(...);`
    onDeviceReady: function() {
        //delete this.initialize;
        logger.log('Device is ready');
        
        scans.init();
        
        
        app.receivedEvent('deviceready');
    },

    // Update DOM on a Received Event
    receivedEvent: function(id) {
        
        logger.log('Device Connected');
        var isLoggedIn = dbi.getLogin();
        logger.log("Is logged: "+isLoggedIn);
        if(isLoggedIn != false && isLoggedIn != null){
            var dbini = dbi.initLoc();
            $.mobile.changePage($('#page-menu'));
        }
        
    },
    logout: function(){
        localStorage.clear();
        $.mobile.changePage($('#page-login'));
    }
};


/*logger.init();
dbi.init();
app.init();*/


 