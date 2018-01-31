/* 
 * rewriting float ads to make them more mobile friendly
 * R. Peters <rob@highoctanebrands.com>
 * 
 */
var isMobile = false; //initiate as false
var containerStyle = "  #fadeinbox{ position:absolute; width: 308px; left: 0; top: -400px; border: 2px solid black; background-color: #000000; padding: 0px; z-index: 100; visibility:hidden; font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; } ";
var iframeDim = "width=\"308\" height=\"286\"";
var frameWidth = "308", frameHeight ="286";
var fibSrc = "//adserver.juicyads.com/js/fadeinbox.js";
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))){ 
    containerStyle = "#fadeinbox{height: 116px; position: fixed;  bottom:116px; width:100%; background-color: #FFF; opacity: 1; margin-left: 0; right: 0; left:0; padding:8px; text-align:center; z-index:10001; box-shadow: -5px -5px 5px #444;}";
    iframeDim = "width=\"300\" height=\"100\"";
    frameHeight = "100";
    frameWidth = "300";
    fibSrc = "//adserver.juicyads.com/js/fib.js";
    isMobile = true;
    }

function strip_alpha_chars(source_string) {  
	var string_out = new String(source_string); 
    string_out = string_out.replace(/[^0-9]/g, '');
	return string_out; 
}

!function(e,t){typeof module!="undefined"?module.exports=t():typeof define=="function"&&typeof define.amd=="object"?define(t):this[e]=t()}("domready",function(){var e=[],t,n=typeof document=="object"&&document,r=n&&n.documentElement.doScroll,i="DOMContentLoaded",s=n&&(r?/^loaded|^c/:/^loaded|^i|^c/).test(n.readyState);return!s&&n&&n.addEventListener(i,t=function(){n.removeEventListener(i,t),s=1;while(t=e.shift())t()}),function(t){s?setTimeout(t,0):e.push(t)}})





domready( function(){
    //create style element
    var head = document.head || document.getElementsByTagName('head')[0],
    style = document.createElement('style');
    style.type = 'text/css';
    if (style.styleSheet){
      style.styleSheet.cssText = containerStyle;
    } else {
      style.appendChild(document.createTextNode(containerStyle));
    }
    head.appendChild(style);
    // create js element
    var script = document.createElement('script');
    script.src = fibSrc;
    var sId = document.createAttribute("id");
    sId.value = "jaFib";
    script.setAttributeNode(sId);
    document.body.appendChild(script);
    //add fade in box
    var fibC = document.createElement('div');
    var fibCId = document.createAttribute("id");
    fibCId.value = "fadeinbox";
    fibC.setAttributeNode(fibCId);
    // close handle
    var fibCL = document.createElement('div');
    var fibCLId = document.createAttribute("id");
    fibCLId.value = "fibCloser";
    fibCL.setAttributeNode(fibCLId);
    //apppend close handle to fib c
    fibC.appendChild(fibCL);
    // anchor to closer
    var fibCLA  = document.createElement("a");
    var fibCLAStyle = document.createAttribute("style");
    fibCLAStyle.value = "color:#000; margin-right:0px; padding: 2px 10px 0px 10px; background-color: #FFF; border:1px solid #000; border-bottom: none; text-align: right; position: absolute; right: 0; top:-32px; font-size: 22px; text-decoration:none;";
    fibCLA.setAttributeNode(fibCLAStyle);
    var fibCLAOC = document.createAttribute("onclick");
    fibCLAOC.value = "hidefadebox();return false;";
    fibCLA.setAttributeNode(fibCLAOC);
    var fibCLATxt = document.createTextNode("X");
    fibCLA.appendChild(fibCLATxt);
    var fibCLAH = document.createAttribute('href');
    fibCLAH.value = "#";
    fibCLA.setAttributeNode(fibCLAH);
    // append anchor to closer
    fibCL.appendChild(fibCLA);
    //  create iframe for ad
    var fibI = document.createElement("iframe");
    var fibIB = document.createAttribute("border");
    fibIB.value = "0";
    fibI.setAttributeNode(fibIB);
    var fibIFB = document.createAttribute("frameborder");
    fibIFB.value = "0";
    fibI.setAttributeNode(fibIFB);
    var fibIMH = document.createAttribute("marginheight");
    fibIMH.value = "0";
    fibI.setAttributeNode(fibIMH);
    var fibIMW = document.createAttribute("marginwidth");
    fibIMW.value = "0";
    fibI.setAttributeNode(fibIMW);
    var fibIW = document.createAttribute("width");
    fibIW.value = frameWidth;
    fibI.setAttributeNode(fibIW);
    var fibIH = document.createAttribute("height");
    fibIH.value = frameHeight;
    fibI.setAttributeNode(fibIH);
    var fibIS = document.createAttribute("scrolling");
    fibIS.value = "no";
    fibI.setAttributeNode(fibIS);
    var fibIAT = document.createAttribute("allowtransparency");
    fibIAT.value = "true";
    fibI.setAttributeNode(fibIAT);
    fibI.src = "//adserver.juicyads.com/adshow.php?adzone=" + strip_alpha_chars(juicy_adzone);
    fibC.appendChild(fibI);
    document.body.appendChild(fibC);
});
