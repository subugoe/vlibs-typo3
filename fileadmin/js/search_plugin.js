function addEngine()
{
    if ((typeof window.sidebar == "object") && (typeof window.sidebar.addSearchEngine == "function"))
    {
        window.sidebar.addSearchEngine(
            "http://www.vifamath.de/fileadmin/search_plugin/vifamath.src",  /* engine URL */
            "http://www.vifamath.de/fileadmin/search_plugin/vifamath.png",  /* icon URL */
            "ViFaMATH",                                         /* engine name */
            "Web" );                                               /* category name */
    }
    else
    {
        alert ("Benutzen Sie Mozilla M15 oder h&ouml;her f&uuml;r dieses Plugin");
    }
}

