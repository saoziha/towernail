<?php
	namespace towernail;
	use Mirana_Theme;

	#Mirana_Routing::add(appname, url, layout, pagename);
	Mirana_Theme::registerPage("towernail", function(){
		Mirana_Theme::addPage("/", "landing", "homepage");
		Mirana_Theme::addPage("/home", "landing", "homepage");
		Mirana_Theme::addPage("/service", "landing", "service");
		Mirana_Theme::addPage("/gallery", "landing", "gallery");
		Mirana_Theme::addPage("/contact", "landing", "contact");

		Mirana_Theme::addFunctionFolder("viewfunction");
	});
?>
