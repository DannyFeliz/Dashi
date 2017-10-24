(function() {

    // Start Smooth Scroll Initialize
    var scroll = new SmoothScroll('a[href*="#"]', {
    	speed: 1000,
    });
    // End Smooth Scroll Initialize


    // Start Auth BG api call/initialize
    const AUTH_BG = document.querySelector(".auth-bg");
    if (!AUTH_BG) return;
    
    const WINDOWS_WIDTH = window.innerWidth;
    const WINDOW_HEIGHT = window.innerHeight;
    const UNSPLASH_API = "https://source.unsplash.com/category/technology/";
    const MOBILE_BREAKPOINT = 768;

    if(WINDOWS_WIDTH > MOBILE_BREAKPOINT) {
        AUTH_BG.setAttribute("src", `${UNSPLASH_API}${WINDOWS_WIDTH}x${WINDOW_HEIGHT}`);

        return;
    }

    AUTH_BG.remove();
    // End Auth BG api call/initialize

})();
