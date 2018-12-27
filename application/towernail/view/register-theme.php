<?php
	namespace towernail;
	use Mirana_Theme;

	#Mirana_Theme::add($themeName, $moduleName);
	Mirana_Theme::registerTheme("towernail", function(){
		Mirana_Theme::addTheme("/", "nailshop");
	});
?>
