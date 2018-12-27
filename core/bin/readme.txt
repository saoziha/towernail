For every request to index.php (in which the routing handles the rest of url), Mirana must know exactly which class to declare.
For example: app-name/something/something-else

Let's talk about app-name. This is the folder that you put the app inside.
Now it's time to go "active". Once you call loadPackage() function, just input the folder location and enjoy.

For view, it's something to do with CSS/JS files. What you may expect from v1.6 is that everything in same folder with view file will be added.
It is, exactly, as a heavy loader: loadClient("app-name/something/view").
With the implementation of Starfall, each component have its own CSS/JS files.

So what about common app CSS/JS, common module CSS/JS like the old days? What about everything is just heavy and you do not need all at once?
Here come the answer in v2. Firstly, a view is a set of Star, and is independent with page (now we do not have page anymore).
It will solve the replication of code. Reuse view, reuse CSS/JS, pretty much easy.

Separated folder? Put location inside loadCSS(), loadJS(); more specific: put a file url instead of the folder.
Remember should not register those as package, Mirana will understand, but it might not find any PHP file inside.
Ideally, put them in "publics", and use the loader to hide the real location.
