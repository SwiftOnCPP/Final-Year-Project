<footer>

<div class="copyright-container">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 bottom-menu">
                <ul class="list-inline"><li class=" "><a class="" href="https://pahe.plus/pages/privacy"><span>Privacy Policy</span></a></li><li class=" "><a class="" href="https://pahe.plus/pages/terms"><span>Terms of Use</span></a></li></ul>                </div>
            <div class="col-sm-4 social-links">
            </div>
            <div class="col-sm-4 copyright">
                <div>Copyright &copy; pahe+ 2024</div>

            </div>
        </div>
    </div>
</div>
</footer>



<footer>
        <br>
        <center>
            <div id="pages" style="width:90%;">
                <center>
                    <div class="card" style="width:90%;">
                        <div class="card-header" style="border:0px;">
                            <h6 class="mb-0 text-center">
                                <strong>
                                    <a href="#">
                                        <i class="fa fa-info-circle text-light"></i> About US
                                    </a> |
                                    <a href="#">
                                        <i class="fa fa-bullhorn text-light"></i> Privacy & Policy
                                    </a> |
                                    <a href="#">
                                        <i class="fa fa-book text-light"></i> Terms & Conditions
                                    </a> |
                                    <a href="#">
                                        <i class="fa fa-copyright text-light"></i> Copyright Policy
                                    </a> |
                                    <a href="#">
                                        <i class="fa fa-envelope text-light"></i> DMCA & Contact Us
                                    </a>
                                    |
                                    <a href="#" target="_blank">
                                        <i class="fab fa-telegram-plane text-light"></i> 
                                        Join Telegram
                                        Copyright © LinkSpy
                                    </a>
                                </strong>
                            </h6>
                        </div>
                    </div>
                </center>
            </div>
        </center>
    </footer>

    
    function toggleDropdown() {
    document.querySelector('#dropdown').classList.toggle('hidden');
}
(function() {
    new LazyLoad({
        elements_selector: ".lazyload",
        load_delay: 300,
        threshold: 0
    });
})();
tocbot.init({
    tocSelector: '#mytoc',
    contentSelector: '.ct',
    headingSelector: 'h1,h2,h3,b,strong',
    hasInnerContainers: true,
    scrollSmoothOffset: -100,
    extraLinkClasses: 'font-bold text-primary hover:text-copy mx-auto'
});
function scrollToBottom() {
    // Smooth scroll to the bottom of the document
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}