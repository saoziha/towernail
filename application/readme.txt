Application architecture:
    each app will be in exactly one folder, for example: application/app1

    inside app-folder (now is app1), we may create as many folder as needed, for example:
    - app1/test
    - app1/test/inside_test
    - ...

    In order to communicate withn Mirana, each php file in the folder must be strictly followed a defined format, as an example below:
    - file a.php in folder app1/test must begin with: " namespace app1\test; "
    - after namespace, the whole mirana must be import by: use Mirana;
    - now load everything in Mirana: Mirana->load();

    At this stage, each namespace is considered as a package. It's time to go "active";
    Mirana will understand your sub-folder as either a sub-package or just a container. (the container case will be used to store view file(s))
    In short, everything in a folder is in a package with its namespace is the folder name, or at least its parent(s).

    For example, now we have package: app1.test , and inside this package we have a file a.php

    It's easy to see that the biggest package is our application, app1. Now Mirana will ask you to provide "mirana_main.php"
    What's inside mirana_main.php? it is simply define a life-cycle of your application using Mirana features (DAO, template engine, routing,...).
    By doing so, create a class with classname is exactly your application name (e.g folder name)
    Define a public static method call "main"
    Mirana will check for this. In this example, Mirana will check for app1->main();
    If not existed, Mirana will stop caring about your app but redirect all requests to your app.

    For old-style applications, please gently use "Mirana->run_v1();" in this mirana_main.php, follow the old structure, everything will be perfectly fine.

    Further than that, enjoy the new MVC structure, with your own routing methods

Database registration:
    Mirana will look for the file "mirana_datasource.php" inside the biggest package, e.g your application.
    In this file, you're required to create an object name datasource extends "DataSource".
    Inside that class, it must be defined a list of data source(s) that project using.

Package registration:
    For every using package, you must define in "mirana_package.php".
    If you try to load an unregistered package, Mirana will out put an error.
