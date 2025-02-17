<?php
include_once "stationary/markdown.php";

$editting=true;
$backup=true;
$wikiName="courses note corpora";
//
$exceptions=array(
"stationary",
"temp",

"BASIC",
"COMPLEX",
"INTERMED",
"plant psychologist",
"Ryujin 3.5 Course complete",
"very very complex",
"A Very Short Introduction",
"Culture History etc",
"Short Stories",

"computing pixels",
"cT",
"dotisblack",
"glsl-rootlevel",
"glslXp5 by A Ferris",
"GolanLevin",
"inconvergent",
"LewisLepton",
"necessary-disorder",
"PaulBourke",
"RuneMadeson",
"php-5-recipes-code",
"picking up the tabb",
"tutorial-code",
"slides",
"transcripts",
"contents",
"imgs",
"common",
"solving-slides",
"dataset"
);
$css=array("/styles/home.css");
#$favicon="webroot/path/to/favicon.jpg"; //find an icon dum
#$hackerImage="webroot/path/to/image/hackers/see.jpg"; //for index
$user_nav=array(
/*
place more header entries here to come after HOME and EDIT
"link"=>"url"
*/
"Markdown Manual"=>"http://daringfireball.net/projects/markdown/syntax",
);
	$nav=array(
		"HOME"=>"./notes.php",
	);
if(isset($_GET['entry'])){
	if(!isset($_GET['edit'])&&$editting){
		$nav+=array("EDIT"=>curPageURL()."&edit=true");
	}
	else if(!isset($_GET['edit'])&&!$editting){
		$nav+=array("SOURCE"=>curPageURL()."&edit=true");
	}
}
$nav=array_merge($nav,$user_nav);
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function readDirectory($dir,$exceptions,$level=0){
	$notDirs=array();
	echo "<ul class='category' id='$level'>\n";
	if ($handle = opendir($dir)) {
	    while (false !== ($entry = readdir($handle))) {
		if(!preg_match('/^\..*/',$entry)){
			if (is_dir("$dir/$entry") and !in_array($entry,$exceptions)){
				$level+=1;
				echo "<details><summary>";
				echo "<h>$entry</h></summary>\n";
				readDirectory("$dir/$entry",$exceptions,$level);
				echo "</details>";
			}
		}
		if (preg_match('/^.*\.(md|MD|markdown|MarkDown|text|Text|TEXT|txt|TXT)$/',$entry)) {
			$entryURL='entry='.urlencode($dir).'/'.urlencode($entry);
		    array_push($notDirs, "<li><h><a href=$url?".htmlentities($entryURL).">$entry</a><h></li>\n");
		}
	    }
		sort($notDirs);
		$notDirs=array_reverse($notDirs);
		while($entryExt=array_pop($notDirs)){
			echo $entryExt;
		}
	    closedir($handle);
	}
	echo "</ul>\n";
}

function readDirFile($dir,$exceptions){

	$validEntry = $_GET['entry'];
	if (strpos($validEntry, '..') !== false) {
	// entry is invalid, let's show them the main page
		readDirectory('./notes.php',$exceptions);
	} else {
	// entry is valid, carry on
		$file = $dir."/".$validEntry;
		$mark = fopen($file,"r");
		$markContents = fread($mark,filesize("$file"));
		echo Markdown($markContents);
		fclose($mark);
	}
}

function readDirFileRAW($dir){
	$file = "$dir"."/".$_GET['entry'];
	$mark = fopen($file,"r");
	$markContents = fread($mark,filesize("$file"));
	echo $markContents;
	fclose($mark);
}

function updateArticle($article,$update){
	if($backup){
	$file = fopen($article,"r");
	$fileContents = fread($file,filesize("$article"));
	$new_file = fopen("$article".date("dmYHi"),"w");
	if(is_writeable("$article".date("dmYHi"))){
		if(fwrite($new_file,$fileContents) === FALSE){
			echo "Could not write $article backup.<br>";
			exit;
		}
		else{echo "$article has been backed up.<br>";}
	}
	else{echo "Could not write to $article".date("dmYHi").".<br>";}
	fclose($file);
	fclose($new_file);
	}
	$file = fopen($article,"w");
	if(is_writeable("$article")){
		if(fwrite($file,$update) === FALSE){
			echo "Could not update $article.<br>";
			exit;
		}
		else{echo "$article has been updated.<br>";}
	}
	else{echo "Could not write to $article.<br>";}
	fclose($file);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
<?php
echo "<title>$wikiName</title>";
if(isset($css)){
  if(is_array($css)){
    foreach($css as $css_sheet){
    	print "<link rel='stylesheet' href='$css_sheet'>\n";
    }
	} else {
  	print "<link rel='stylesheet' href='$css'>";
	}
} else {
?>
<style type="text/css">
p{font-family:"Times New Roman", Times, serif;}
body{
  color:#2e3436;
  background-color:#729fcf;
}
a {
  color: #555753;
}
.navbar ul {
  margin: 0;
  padding: 0;
  list-style-type: none;
  text-align: center;
}
.navbar ul li {
  display: inline;
  padding: 10px;
}
</style>
<?php
}
if(isset($favicon)){
	echo "<link rel='icon' href='$favicon' type='image/x-icon' />";
	echo "<link rel='shortcut icon' href='$favicon' type='image/x-icon' />";
}
?>
</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <header><h1><a class="brand" href="index.php"><?php echo $wikiName; ?></a></h1></header>
      <ul class="nav">
<?php
foreach($nav as $show=>$html){
	echo "<li><a href=$html>$show</a></li>";
}
?>
      </ul>
    </div>
    <hr>
  </div>
</div>
<div class="container">
<?php
$url = curPageURL();

if($_POST['article']){
	if($_POST['update']){
		$article=$_POST['article'];
		$update=$_POST['update'];
		updateArticle($article,$update);
	}
}

if (!$_GET['edit']){
	if (empty($_GET['entry'])){
		readDirectory('.',$exceptions);
	}
	if ( isset($_GET['entry'])){
		if ( strpos($_GET['entry'], 'index.php') === false ){
			readDirFile('.',$exceptions);
		}
		else{
			echo "<img src='$hackerImage'>";
		}
	}
}
if ($_GET['edit']){
	$validEntry = $_GET['entry'];
	if (strpos($validEntry, '..') !== false) {
	// entry is invalid, let's show them the main page
		readDirectory('.',$exceptions);
	} else {
?>
	<form action="./notes.php" method="post">
		<input type="hidden" name="article" value="<?php echo $_GET['entry']; ?>" >
		<textarea name="update" rows="25" cols="100" class="field span12" <?php if(!$editting) echo "disabled";?>><?php readDirFileRAW('.');?></textarea>
		<br/>
		<?php if($editting){?>
		<input type="submit" value="Submit">
		<?php } ?>
	</form>
<?php
	}
}
?>
</div>
<footer><a href="./index.html"> ↑ coUrse direc </a></footer>
</body>
</html>
