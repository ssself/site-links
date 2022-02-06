<?
# origin: http://php.holtsmark.no -  Rolf
# fix for older versions of PHP.
if (!isset($_GET) && !isset($_POST))
{
        $_GET = @$HTTP_GET_VARS;
        $_POST = @$HTTP_POST_VARS;
        $_COOKIE = @$HTTP_COOKIE_VARS;
        $_SESSION = @$HTTP_SESSION_VARS;
        $_ENV = @$HTTP_ENV_VARS;
        $_SERVER = @$HTTP_SERVER_VARS;
}
$topdir = "./"; // Don't touch this - go to the configuration part!
if (empty($_GET["dir"])) $dir="/"; 
if (!empty($_GET["dir"])) $dir=$_GET["dir"]; 
$debug = false; // Display debug messages?
$ver="1.3";

####################################################
# START OF CONFIGURATION
#
# Uncomment one of the $topdir configurations or create one yourself! Default listing is ./
#
# Most usefull examples:
# $topdir = $_SERVER["DOCUMENT_ROOT"];  // This will display the whole webserver directory.
# $topdir = "./"; // Current directory and below.
#
# Note: 
# 	Listing files outside the webserver is possible, 
# 	but accessing them with this script is not possible.
#
# Example:
# $topdir = "/"; // Linux root directory - Not recomended!
# $topdir = "/home/username/public_html/";  // A spesified directory
# $topdir = "/home/ftp";
# $topdir = "c:/ftp/"; //win32 envariment?
#
#
# Extra configuration - should not be needed.
# $otherlocation = "/path/on/the/web"; //files exist on another location?
# $otherlocation = "http://www.server.com/~username/files/"; //files exist on another location?
#
#
######
# Filter filetypes:
# $filetypes = array("htm","html");
# $filetypes = array("css","js");
#
# $filter = 0; // Only show $filetypes
# $filter = 1; // Ignore $filetypes, show the rest.
#
$sort = true; // Sort the listing: true / false.
$sort_type = SORT_REGULAR;
#
# 		SORT_REGULAR - compare items normally
# 		SORT_NUMERIC - compare items numerically
# 		SORT_STRING - compare items as strings

# END OF CONFIGURATION
####################################################


if (!empty($otherlocation)) if ($otherlocation[strlen($otherlocation)-1] != '/') $otherlocation .= '/';
if (empty($otherlocation)) $otherlocation="";


function f($size="2",$color="#000033",$face="Lucida Console")
{
	echo '<font face="'.$face.'" color="'.$color.'" size="'.$size.'">'."\n";
}
function endf()
{
	echo '</font>'."\n";
}
function br()
{
	return '<br>'."\n";
}
function img($nr,$border=0)
{
	global $_SERVER;
	return  '<img src="'.$_SERVER["PHP_SELF"].'?image='.$nr.'" border="'.$border.'">';
}

function getext($filename) 
{
  $f = strrev($filename);
  $ext = substr($f, 0, strpos($f,"."));
  return strrev($ext);
 }

function sw($i)
{
	if ($i) return 0;
	else return 1;
}

function listdir($dir="./",$toplevel="./",$otherlocation="")
{
	global $_SERVER,$debug,$filetypes, $filter, $sort, $sort_type;
	$dir=str_replace('./','/',$dir);
	$text = "";
	if ($dir[strlen($dir)-1] != '/') $dir .= '/';
	$path=$toplevel.$dir;
	$path=str_replace('//','/',$path);
	$path=str_replace('..','',$path);
	# echo 'You are here: '.$path.'<br><br>';

	if ($debug) {f(1); echo br().'$path = '.$path.br().'$dir = '.$dir.br().br(); endf();}
	$diren=$dir;
	if (!is_dir("$path")) die(f(2,"#FF0000")."<b>Error $path is not a directory?</b>");
	$files = dir($path) or die("Error reading/opening $path");
 	while ($a = $files->read())
	{
		if (!empty($a)) $currentArray[] = $a;
	}
	if ($sort && (!$sort_type)) sort($currentArray);
	if ($sort && $sort_type) sort($currentArray,$sort_type);
	for ($i = 0; $i < count($currentArray); $i++)
	{
		$current = $currentArray[$i];
		if (($current != "..") && ($current != ".")) 
		{
			$dir = $diren;
			# echo "<br>if (is_dir(".$path.$current."))<br>";
			if (is_dir($path.$current)) 
		        if ($debug) echo "\$dir = $dir  - ";
			if (is_dir($path.$current)) 
			# echo 	'* <a href="'.$_SERVER["PHP_SELF"].'?dir='.$dir.''.$current.'">'.'</a> '."\n".'* <a href="'.$_SERVER["PHP_SELF"].'?dir='.$dir.''.$current.'">'.$current.'</a>'.br();
			echo' <a href="'.$current.'/">'.$dir.''.$current.'</a>'.br();
			
			else 
			{
				if ($toplevel == "./") $dir = $path;
	                        if ($debug) echo "\$dir = $dir  - ";
				$match = sw($filter);
				if (!empty($filetypes[0])) 
				{
					$ext=getext($current);
					$match = $filter;
				        for ($i=0;$i<count($filetypes);$i++)
				        {
						if ($ext == $filetypes[$i] ) $match = sw($filter);
					}
				} else $match = 1;
				if ($match == 1) 
					echo '  <a href="'.$otherlocation.$dir.$current.'">'.$current.'</a>'.br();
			}
		}
	};
}


f(2); 
echo '<html><head><title>signalpost links & media</title><link href="images/signal.css" rel="stylesheet" type="text/css"></head>
<body><div id="tablet">';

echo '<a href="../"><span class="signalpost links & media"><font color="#FF0000"><strong>signalpost</strong></font></span></a> links & media'.br();
if (empty($topdir)) $topdir = "./";

if ($debug) { f(1); echo "listdir($dir,$topdir,$otherlocation);".br().br(); endf(); }

f(2); listdir($dir,$topdir,$otherlocation); endf();

echo '</div></body></html>';

?>
