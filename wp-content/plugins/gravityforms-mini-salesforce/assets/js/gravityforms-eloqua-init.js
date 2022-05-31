/**
 * @file
 * Initialize Eloqua tracking.
 */

var _elqQ = _elqQ || [];
// console.log(gravityforms_eloqua_init_strings.gfEloquaSettings.elqSiteId);
var siteId = gravityforms_eloqua_init_strings.gfEloquaSettings.elqSiteId;
var gfEloquaGuidSelector = gravityforms_eloqua_init_strings.gfEloquaGuidSelector;
var gfEloquaGoogleClientSelector = gravityforms_eloqua_init_strings.gfEloquaGoogleClientSelector;
var gfEloquaGoogleTransactionSelector = gravityforms_eloqua_init_strings.gfEloquaGoogleTransactionSelector;

_elqQ.push(['elqSetSiteId', siteId]);
//_elqQ.push(['elqUseFirstPartyCookie', 'tracking.mysite.com']);
_elqQ.push(['elqTrackPageView']);
_elqQ.push(['elqGetCustomerGUID']);

(function() {
  function async_load() {
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = '//img.en25.com/i/elqCfg.min.js';
    var x = document.getElementsByTagName('script')[0];
    x.parentNode.insertBefore(s, x);

    // var s = document.createElement('script');
    // s.type = 'text/javascript';
    // s.async = true;
    // s.src = '//d12ue6f2329cfl.cloudfront.net/resources/utm_form-1.0.3.min.js';
    // var x = document.getElementsByTagName('script')[0];
    // x.parentNode.insertBefore(s, x);
  }
  if (window.addEventListener) window.addEventListener('DOMContentLoaded', async_load, false);
  else if (window.attachEvent) window.attachEvent('onload', async_load);
})();


var timerGUID = null, timeoutGUID = 5;

//Get Eloqua
function WaitUntilCustomerGUIDIsRetrieved() {
  if(timeoutGUID <= 0){
    clearInterval(timerGUID);
    return;
  }
  if (typeof GetElqCustomerGUID == 'function') {
    var customerGuid = GetElqCustomerGUID();
    var elements = document.querySelectorAll(gfEloquaGuidSelector);
    for (var i=0;i<elements.length;i++) {
      elements[i].value = customerGuid;
    }
    _elqQ.push(['elqGetCustomerGUID',customerGuid]);
    clearInterval(timeoutGUID);
    timeoutGUID = 0;
  } else {
    timeoutGUID -= 1;
  }
  timerGUID = setTimeout("WaitUntilCustomerGUIDIsRetrieved()", 500);
  return;
}
if (window.addEventListener) window.addEventListener('DOMContentLoaded', WaitUntilCustomerGUIDIsRetrieved, false);
else if (window.attachEvent) window.attachEvent('onload', WaitUntilCustomerGUIDIsRetrieved);

/// Get google anal...
var timerGA = null, timeout = 5;
function WaitUntilGaGetAllRetrieved() {
  if(timeout <= 0){
    clearInterval(timerGA);
    return;
  }
  if (typeof ga != 'undefined' && typeof ga.getAll != 'undefined') {
    var client = ga.getAll()[0].get('clientId');
    var elements = document.querySelectorAll(gfEloquaGoogleClientSelector);
    for (var i=0;i<elements.length;i++) {
      elements[i].value = String(client);
    }
    var gatrid = String((new Date).getTime());
    var elements = document.querySelectorAll(gfEloquaGoogleTransactionSelector);
    for (var i=0;i<elements.length;i++) {
      elements[i].value = gatrid;
    }
    clearInterval(timerGA);
    timeout = 0;
  }else{
    timeout -= 1;
  }

  timerGA = setTimeout("WaitUntilGaGetAllRetrieved()", 500);
  return;
}

if (window.addEventListener) window.addEventListener('DOMContentLoaded', WaitUntilGaGetAllRetrieved, false);
else if (window.attachEvent) window.attachEvent('onload', WaitUntilGaGetAllRetrieved);
