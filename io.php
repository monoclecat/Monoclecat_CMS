<?php
/*
Author: Andrew Delay
Last updated: 27.Dec 2015
Licensed under: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License
				http://creativecommons.org/licenses/by-nc-sa/4.0/
*/

include realpath($_SERVER["DOCUMENT_ROOT"]).'/phpinclude/includes.php';


$_SESSION["reqpage"] = requestedpage(false);
if (!isset($_SESSION["visitedpages"])) {
	$_SESSION["visitedpages"] = array();
}

if (cms_table_exists(true)) {	
	if ($result = mysqli_query($con, "SELECT * FROM cms WHERE name = '{$_SESSION['reqpage']}'")) {
		$page = mysqli_fetch_assoc($result);
		if (!empty($page)) {
			if ($page["visibleto"] == "" || strpos($page["visibleto"], $_SESSION["status"]) !== FALSE) {
				$thumb = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM images WHERE groupid = '{$page['primaryimage']}' 
																AND height = '350' AND original = '0' ORDER BY id ASC"));
				$pagesnottomention = array("home","default","404","login");
				if (!in_array($page["name"],$pagesnottomention) && !in_array($page["showname"],$_SESSION["visitedpages"])) {
					$_SESSION["visitedpages"][] = $page["showname"];
					if (count($_SESSION["visitedpages"]) > 3) {
						array_shift($_SESSION["visitedpages"]);
					}
				}
				if ($_SESSION["status"] == "admin") {
					$origpage = $page;
				}
			}
			else {
				$page = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM cms WHERE name = 'home'"));
			}
		}
		else {
			if ($_SESSION["status"] == "admin" && $_SESSION["editmode"] == 1) {
				$page = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM cms WHERE name = 'default'"));
				unset($page["id"]);
				unset($page["name"]);
			}
			else {
				$page = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM cms WHERE name = 'home'"));
			}
		}
		
		parse_content($page["includes"]);
		parse_content($page["head"]);
		parse_content($page["main"]);
	}
}
$filestoinclude = explode(",",$page["includes"]);
for ($i = 0; $i < count($filestoinclude); $i++) {
	if (!empty($filestoinclude[$i])) {
		include_once $root.'/phpinclude/'.$filestoinclude[$i];
	}
}


?> 
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
<title><?php echo $page["title"];?></title>
<?php 
	analytics();
	echo css_js();
?>
<script type="application/ld+json">
{
	"@context": "http://schema.org",
	"@type": "WebPage",
	"breadcrumb": "http://monoclecat.de/ > http://monoclecat.de/?l=all-projects > http://monoclecat.de/?l=about",
	"relatedLinks": "https://github.com/monoclecat > https://www.youtube.com/c/TheMonoclecat > http://www.instructables.com/member/Candymanproductions",
	"reviewedBy": <?php seo_monoclecat();?>,
	"author": <?php seo_monoclecat();?>,
	"creator": <?php seo_monoclecat();?>,
	"primaryImageOfPage": <?php echo "http://monoclecat.de".$thumb["directory"].$thumb["filename"];?>,
	"dateCreated": <?php echo $page["created"];?>,
	"headline": <?php echo $page["title"];?>,
	"about": "Electronics and Programming Projects",
	"significantLinks": "http://monoclecat.de/?l=all-terrain-vehicle > http://monoclecat.de/?l=bldc-driver > http://monoclecat.de/?l=cellphone-mod > 
						http://monoclecat.de/?l=flyback-transformer-driver > http://monoclecat.de/?l=nintendo-ds-mod > http://monoclecat.de/?l=csv-to-png-converter",
	"specialty": "Electrical Engineering, Programming",
	},
}
</script>
</head>
<body>
        <?php echo head();?>
        <div id="maincontainer">
				<div id="side" itemscope itemtype="http://schema.org/WPSideBar" itemprop="breadcrumb">
					<?php
						$sidebar;
						$alreadyadded = array();
						if (count($_SESSION["visitedpages"]) > 0) {
							$sidebar .= "<p>Recently visited</p><ul>";
							for ($i = 0; $i < count($_SESSION["visitedpages"]); $i++) {
								$sidebar .= '<li><a href="/?l='.$_SESSION["visitedpages"][$i].'">'.$_SESSION["visitedpages"][$i].'</a></li>';
							}
							$sidebar .= "</ul>";
						}
						
						$sidebar .= "<p>Featured Projects</p><ul>";
						$sidebarprojects = mysqli_query($con,"SELECT name, showname FROM cms WHERE tag LIKE '%project%' AND featured = 'yes' ORDER BY created ASC");
						while ($sidebarproj = mysqli_fetch_assoc($sidebarprojects)) {
							if (!in_array($sidebarproj["name"],$alreadyadded)) {
								$sidebar .= '<li><a href="/?l='.$sidebarproj["name"].'">'.$sidebarproj["showname"].'</a></li>';
								$alreadyadded[] = $sidebarproj["name"];
							}
						}
						$sidebar .= "</ul>";
						
						$sidebar .= "<p>Web Applications</p><ul>";
						$sidebarprojects = mysqli_query($con,"SELECT name, showname FROM cms WHERE tag LIKE '%webapp%' ORDER BY created DESC");
						while ($sidebarproj = mysqli_fetch_assoc($sidebarprojects)) {
							if (!in_array($sidebarproj["name"],$alreadyadded)) {
								$sidebar .= '<li><a href="/?l='.$sidebarproj["name"].'">'.$sidebarproj["showname"].'</a></li>';
								$alreadyadded[] = $sidebarproj["name"];
							}	
						}
						$sidebar .= "</ul>";
						
						$sidebar .='<p>Links</p>
									<ul>
										<li><a href="https://github.com/monoclecat">My Github</a></li>
										<li><a href="https://www.youtube.com/c/TheMonoclecat">My Youtube Channel</a></li>
										<li><a href="http://www.instructables.com/member/Candymanproductions">My Instructables</a></li>
										<li><a href="https://plus.google.com/+TheMonoclecat/">My Google+ page</a></li>
									</ul>';
									
						$sidebar .= "<p>Other Projects</p><ul>";
						$sidebarprojects = mysqli_query($con,"SELECT name, showname FROM cms WHERE tag LIKE '%project%' AND featured = 'no' ORDER BY created DESC");
						while ($sidebarproj = mysqli_fetch_assoc($sidebarprojects)) {
							if (!in_array($sidebarproj["name"],$alreadyadded)) {
								$sidebar .= '<li><a href="/?l='.$sidebarproj["name"].'">'.$sidebarproj["showname"].'</a></li>';
								$alreadyadded[] = $sidebarproj["name"];
							}
						}
						$sidebar .= "</ul>";
						$sidebar .= "<p>Meta</p><ul>";
						switch ($_SESSION["status"]) {
							case "admin":
								$sidebar .= '<li><a href="/?l=login">Logout</a></li>
											<li><a href="/?l=viparea">VIP area</a></li>
											<li><a href="/?l=dbviewer">DB viewer</a></li>
											<li><a href="/?l=filedeposit">Filedeposit</a></li>
											<li><a href="/?l=imagegallery">Image gallery</a></li>';
								break;
							case "uploader":
								$sidebar .= '<li><a href="/?l=login">Logout</a></li>
											<li><a href="/?l=viparea">VIP area</a></li>
											<li><a href="/?l=filedeposit">Filedeposit</a></li>';
								break;
							case "user":
								$sidebar .= '<li><a href="/?l=login">Logout</a></li>
											<li><a href="/?l=viparea">VIP area</a></li>';
								break;
							default:
								$sidebar .= '<li><a href="/?l=login">Login</a>';
						}
						$sidebar .= "</ul>";	
						unset($sidebarprojects);
						unset($sidebarproj);
						unset($alreadyadded);
						
						echo $sidebar;
					?>
				</div>
                <div id="main" itemprop="mainEntityOfPage" itemscope itemtype="http://schema.org/CreativeWork" itemprop="text">
				<meta itemprop="headline" content="<?php echo $page["headline"];?>">
				<meta itemprop="creator" content="<?php echo seo_monoclecat();?>">
				<meta itemprop="keywords" content="electronics, hobby, circuits, monoclecat, robots, etching, esp8266, ".$page["tag"]."">
				<meta itemprop="potentialAction" content="learn">
				<meta itemprop="url" content="http://monoclecat.de/?l=<?php echo $page["name"];?>">
				<meta itemprop="name" content="http://monoclecat.de/?l=<?php echo $page["title"];?>">
				<meta itemprop="relatedLink" content="http://monoclecat.de/?l=<?php echo $page["title"];?>">
					<?php
					if (strpos($page["tag"], "system") === FALSE) {
						if (strlen($page["headline"]) < 50) {
							echo "<h1>".$page["headline"]."</h1>";
						}
						else {
							echo "<h2>".$page["headline"]."</h2>";
						}
						if (strpos($page["tag"], "project") !== FALSE) {
							echo '<i class="thin">'.date_converter($page["created"]).'</i>'; //Function is in general.php
						}
					}
					
					echo $page["main"];
					
					if ($_SESSION["status"] == "admin" && $_SESSION["editmode"] == 1) {
						echo spacertag(true);
						
						if (!isset($page["id"])) {
							//New page
							echo '
								<h3>Create a new page!</h3>
								<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">
									<input type="hidden" name="id" value="">
									<input type="hidden" name="created" value="'.date("o-m").'">
									<input type="submit" name="savepage" class="subm" value="Save">
									<p>Name: <input type="text" name="name" class="inp" value="'.$_SESSION["reqpage"].'"></p>
									<p>Showname: <input type="text" name="showname" class="inp" value="'.$page["showname"].'"></p>
									<p>Feature Page <input type="checkbox" name="featured" value="yes"></p>
									<p>Title: <input type="text" name="title" class="inp" value="'.$page["title"].'"></p>
									<p>Tag: <input type="text" name="tag" class="inp" value="'.$page["tag"].'"></p>
									<p>VisibleTo: <input type="text" name="visibleto" class="inp" value="'.$page["visibleto"].'"></p>
									<p>Includes: <br><textarea name="includes" class="cms" rows="2" cols="87">'.$page["includes"].'</textarea></p>
									<p>Head: <br><textarea name="head" class="cms" rows="10" cols="87">'.$page["head"].'</textarea></p>
									<p>Abstract: <br><textarea name="main" class="cms" rows="3" cols="87" maxlength="199">'.$origpage["abstract"].'</textarea></p>
									<p>Headline: <br><input type="text" name="headline" class="inp" size="100" value="'.$page["headline"].'"></p>
									<p>Main: <br><textarea name="main" class="cms" rows="200" cols="87">'.$page["main"].'</textarea></p>
									<p>Additional Images (group ids): <input type="text" name="additionalimages" class="inp" value="'.$page["additionalimages"].'"></p>
								</form>';
						}
						else {
							if ($page["featured"] == "yes") {
								$featuredchecked = "checked";
							}
							else {
								$featuredchecked = "";
							}
							echo '
								<h3>Modify existing page</h3>
								<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">
									<input type="hidden" name="id" value="'.$page["id"].'">
									<input type="submit" name="savepage" class="subm" value="Save">
									<p>Name: <input type="text" name="name" class="inp" value="'.$origpage["name"].'"></p>
									<p>Showname: <input type="text" name="showname" class="inp" value="'.$page["showname"].'"></p>
									<p>Feature Page <input type="checkbox" name="featured" value="yes" '.$featuredchecked.'></p>
									<p>Created: <input type="text" name="created" class="inp" value="'.$page["created"].'"></p>
									<p>Primary Image (Group ID): <input type="text" name="primaryimage" class="inp" value="'.$origpage["primaryimage"].'"></p>
									<p>Title: <input type="text" name="title" class="inp" value="'.$page["title"].'"></p>
									<p>Tag: <input type="text" name="tag" class="inp" value="'.$page["tag"].'"></p>
									<p>VisibleTo: <input type="text" name="visibleto" class="inp" value="'.$page["visibleto"].'"></p>
									<p>Includes: <br><textarea name="includes" class="cms" rows="2" cols="87">'.$origpage["includes"].'</textarea></p>
									<p>Head: <br><textarea name="head" class="cms" rows="10" cols="87">'.$origpage["head"].'</textarea></p>
									<p>Abstract: <br><textarea name="abstract" class="cms" rows="3" cols="87" maxlength="199">'.$origpage["abstract"].'</textarea></p>
									<p>Headline: <br><input type="text" name="headline" class="inp" size="100" value="'.$page["headline"].'"></p>
									<p>Main: <br><textarea name="main" class="cms" rows="200" cols="87">'.$origpage["main"].'</textarea></p>
									<p>Additional Images (group ids): <input type="text" name="additionalimages" class="inp" value="'.$page["additionalimages"].'"></p>
								</form>';
						}
						
					}
						
					?>
                </div>
        </div>
        <?php echo foot();?>
</body>
</html>