# Monoclecat CMS
A small content managing system for my homepage at monoclecat.de written only in PHP

### The following is the content from the [project page on my homepage](http://www.monoclecat.de/?l=cms) ###


In this article I want to present to you the content managing system of this site. As everything here is a product of my coding, I have the urge to show what I have created. The thing with web development is that the magic happens behind the scenes. The only thing the website visitor sees is the output.
For that reason I have created this article; to show & tell my code. The entire content managing system is written in PHP only.

### Overview on how the cms works ###


Compared to other free cms systems you can install on your server, this cms is tiny. But it's all I need for my homepage and it was fun programming it.
When you request a page by typing in the url (for example monoclecat.de/?l=etching), then you are performing a GET-request with the variable l set to the value "etching". Then the code searches the database of pages for the one with the name "etching" and loads its content into a skeleton which contains the basic structure all pages have (banner on top, horizontal menu bar, sidebar and footer). The result is what you see, for example on this page right here.
The database


The table "cms" in the website's database contains all the script need to build a fully functional page. Here is an example with two pages where you can see the stucture: Scroll horizontal to see the entire table
id 	name 	tag 	primaryimg 	created 	showname 	featured 	title 	headline 	abstract 	main
10 	4-bit-adder 	project 	48 	2014-06 	4-bit Adder 	no 	4-Bit-Adder Monoclecat.de 	4-Bit-Adder made only with logic gates 	A 4-bit adder I built in 10th grade. 	In 10th grade computer-science...
4 	about 	about 		2015-12 	About me 	no 	About Me Monoclecat.de 	About me 	About me and the history of this site 	Hi, my name is Andrew Delay and...


As you can see, there is much more to a page than just a title (seen on the label of the tag in your browser), a name (which is what the l-variable must be set to) and the content. As an example, the links on the sidebar are also built with a php script. To have a nice name to display, the script needs the "showname".
On the welcome page of the site, the newsfeed is also built via a script. Here it need even more additional information. The database supplies it with the primary image of the page, a headline and an abstract, which is a short summary of the page content.


###Adding a new page / modifying an existing one###

To create or edit a page, one must log in first so the website knows who the visitor is and can give him the necessary permissions. After logging in, when visiting a page, it builds it as if you were a normal visitor. Under the content though, at the bottom of the page, the php script will add a form and fill it with the content from the database. Then you can edit it, press "Save" and the script updates the database content at that place.
Here is an example of the editing mode:
Click to enlarge
The code

The code for the cms is made up of two php files, io.php and cmsstuff.php. io.php is the "page skeleton" mentioned above. It is a body which is filled with content every time you load a page. You are actually on it right now, it is just not necessarily visible in the url. Instead of "monoclecat.de/?l=cms" you could also write "monoclecat.de/io.php?l=cms". cmsstuff.php represents the backend with the crucial scripts, handling database updates and parsing database content. 
