(function() {
    console.log("ready");
    const AUTH_BG = document.querySelector(".auth-bg");
    const WINDOWS_WIDTH = window.innerWidth;
    const WINDOW_HEIGHT = window.innerHeight;
    const UNSPLASH_API = "https://source.unsplash.com/category/technology/";

    if(WINDOWS_WIDTH > 768) {
        AUTH_BG.setAttribute("src", `${UNSPLASH_API}${WINDOWS_WIDTH}x${WINDOW_HEIGHT}`);

        return;
    }

    AUTH_BG.remove();

    $(".intro-arrow").mouseover(function(){
        console.log("hola");
        $(this).removeClass("animated");
    })

})();
