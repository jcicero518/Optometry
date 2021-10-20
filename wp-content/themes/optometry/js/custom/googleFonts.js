WebFontConfig = {
    google: { families: [ 'Roboto+Slab:400,700:latin', 'Raleway:400,500,600:latin', 'Lato:400,300,700:latin' ] }
};
(function() {
    var wf = document.createElement('script');
    wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
})();