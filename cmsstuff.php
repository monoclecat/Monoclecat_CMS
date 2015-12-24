<?php
/*
Author: Andrew Delay
Last updated: 27.Dec 2015
Licensed under: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
				http://creativecommons.org/licenses/by-nc-sa/4.0/
*/


/*

In this cms, the html content is seperated from function calls by ' | ' symbols. 
When you want to call a html-building function, it looks like this "|+img,221,0|"

*/

/*
------------ FUNCTIONS ------------
*/
function cms_table_exists($create) {
	global $con;
	if (!$result = mysqli_query($con,"SELECT * FROM cms")) {   //if table doesn't exist
		if ($create == true) {
			if (!$query = mysqli_query($con,"CREATE TABLE cms (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) COLLATE latin1_bin, 
												tag VARCHAR(100) COLLATE latin1_bin, primaryimage VARCHAR(10) COLLATE latin1_bin, 
												visibleto VARCHAR(100) COLLATE latin1_bin,
												created VARCHAR(100) COLLATE latin1_bin, showname VARCHAR(50) COLLATE latin1_bin, 
												featured VARCHAR(10) COLLATE latin1_bin, 
												title VARCHAR(100) COLLATE latin1_bin, headline VARCHAR(200) COLLATE latin1_bin, 
												abstract VARCHAR(200) COLLATE latin1_bin, includes TEXT COLLATE latin1_bin, 
												head TEXT COLLATE latin1_bin, main TEXT COLLATE latin1_bin, additionalimages VARCHAR(100) COLLATE latin1_bin, 
												misc TEXT COLLATE latin1_bin) 
												COLLATE latin1_bin")) {
				errorreport(mysqli_error($con)."\r\nCreating table cms");
				return false;
			}
			else {
				return true; //database created
			}
		}
	}
	else {
		return true; //database exists
	}
	return false;
}


function parse_content(&$toparse) {
	$toparse = htmlspecialchars_decode($toparse);
	$explo = explode('|', $toparse);
	foreach ($explo as $index => $part) {
		$part = trim($part);
		if ($part[0] == '+') {
			$func = explode(',', $part, 4);
			switch ($func[0]) {
				case "+img":
					$explo[$index] = imgtag($func[1], $func[2]);
					break;
				case "+spacer":
					$explo[$index] = spacertag($func[1]);
					break;
				case "+atvgall":
					$explo[$index] = build_atv_gallery();
					break;
				case "+imagegall":
					$explo[$index] = '<h1>Image Gallery</h1>'.imageupload().'<p><br><br></p>'.delimage().'<p><br><br></p>'.buildgallery();
					break;
				case "+csv":
					$explo[$index] = csvfileupload();
					break;
				case "+viparea":
					$explo[$index] = viparea();
					break;
				case "+dbviewer":
					$explo[$index] = dbcontrol().printusers().printfiles().printnotes().printcms();
					break;
				case "+filedeposit":
					$explo[$index] = filedeposit();
					break;
				case "+newsfeed":
					$explo[$index] = build_newsfeed();
					break;
				case "+projfeed":
					$explo[$index] = build_projectfeed($func[1]);
					break;
				case "+login":
					$explo[$index] = login_page();
					break;
				case "+contactprofile":
					$explo[$index] = profilepage();
					break;
			}
		} 
	}
	$toparse = implode($explo);
}

/*
------------ REQUESTHANDLER ------------
*/

function requestedpage($strict) {
	$l = htmlspecialchars($_GET["l"]);
	if (empty($l)) {
		if ($strict == true) {
			return FALSE;
		}
		else {
			return "home";
		}
	}
	else {
		return $l;
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST["savepage"]) {
		$savepage;
		$savepage["id"] = htmlspecialchars($_POST["id"]);
		$savepage["name"] = htmlspecialchars($_POST["name"]);
		$savepage["tag"] = htmlspecialchars( str_replace("'", "\'", $_POST["tag"]) );
		$savepage["primaryimage"] = htmlspecialchars($_POST["primaryimage"]);
		$savepage["visibleto"] = htmlspecialchars($_POST["visibleto"]);
		$savepage["created"] = htmlspecialchars($_POST["created"]);
		$savepage["showname"] = htmlspecialchars( str_replace("'", "\'", $_POST["showname"]) );
		$savepage["featured"] = htmlspecialchars($_POST["featured"]);
		$savepage["title"] = htmlspecialchars( str_replace("'", "\'", $_POST["title"]) );
		$savepage["includes"] = htmlspecialchars( str_replace("'", "\'", $_POST["includes"]) );
		$savepage["headline"] = htmlspecialchars( str_replace("'", "\'", $_POST["headline"]) );
		$savepage["abstract"] = htmlspecialchars( str_replace("'", "\'", $_POST["abstract"]) );
		$savepage["head"] = htmlspecialchars( str_replace("'", "\'", $_POST["head"]) );
		$savepage["main"] = htmlspecialchars( str_replace("'", "\'", $_POST["main"]) );
		$savepage["additionalimages"] = htmlspecialchars( str_replace("'", "\'", $_POST["additionalimages"]) );
		$savepage["misc"] = htmlspecialchars( str_replace("'", "\'", $_POST["misc"]) );
		
		if ($savepage["featured"] != "yes") {
			$savepage["featured"] = "no";
		}
		
		if ($savepage["id"] == "") {
			//New page
			if (!$query = mysqli_query($con, "INSERT INTO cms 
						(name, tag, primaryimage, visibleto, created, showname, featured, title, headline, abstract, includes, head, main, additionalimages, misc) 
			
												VALUES ('{$savepage['name']}', 
														'{$savepage['tag']}', 
														'{$savepage['primaryimage']}', 
														'{$savepage['visibleto']}', 
														'{$savepage['created']}', 
														'{$savepage['showname']}',
														'{$savepage['featured']}',
														'{$savepage['title']}', 
														'{$savepage['headline']}',
														'{$savepage['abstract']}', 
														'{$savepage['includes']}', 
														'{$savepage['head']}', 
														'{$savepage['main']}', 
														'{$savepage['additionalimages']}', 
														'{$savepage['misc']}')")) {
				errorreport(mysqli_error($con)."\r\nAdding page in cmsstuff.php");
				throw new RuntimeException( "Failed to add page: ".mysqli_error($con) );											
			}
		}
		else {
			//Modify page
			if (!$query = mysqli_query($con, "UPDATE cms SET name = '{$savepage['name']}', tag = '{$savepage['tag']}', 
														primaryimage = '{$savepage['primaryimage']}', 
														visibleto = '{$savepage['visibleto']}', 
														created = '{$savepage['created']}', 
														showname = '{$savepage['showname']}',
														featured = '{$savepage['featured']}',
														title = '{$savepage['title']}',
														headline = '{$savepage['headline']}', 
														abstract = '{$savepage['abstract']}', 
														includes = '{$savepage['includes']}', 
														head = '{$savepage['head']}', 
														main = '{$savepage['main']}', 
														additionalimages = '{$savepage['additionalimages']}',
														misc = '{$savepage['misc']}'
														WHERE id = '{$savepage['id']}'")) {
				errorreport(mysqli_error($con)."\r\nSaving page in cmsstuff.php");
				throw new RuntimeException( "Failed to save page: ".mysqli_error($con) );											
			}
		}
	header("Location: http://monoclecat.de/?l=".$savepage['name'], true, 303);
	die();
	}
}


/*
------------ HTML BUILDERS ------------
*/


?>