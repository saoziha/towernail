Controller:
- Controller will do the routing
- Controller will help you load a package: $this->loadPackage("package/name/xyz");
- Controller will handle web-service for you
- Controller will handle session for you
- Controller will let you do some security and access control

Model:
- Model will allow you to declare datasource
- Model will allow you to use DAO (named as arrow)-- virtual table, free lookup, ...
- Model will enable every public function as a web-service

View:
- View will allow you to use template engine (starfall)
- View will allow you to render a package (a folder) as a single function: $this->render("package/name/xyz/view1")
(same as v1, view file must be identical with folder name, but in case only 1 php/html file, it will load)
(no more template, template now is just a bigger star with multiple stars inside, so that we call it "starfall")
