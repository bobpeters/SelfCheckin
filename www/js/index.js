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
	console.log('back button');
        var activePage = $.mobile.activePage[0].id;
        if(activePage != 'page-login'){
            $.mobile.changePage('#page-menu');
        }
}


$(document).ready(init);