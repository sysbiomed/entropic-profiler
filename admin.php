<html>
<head>
<title>
Entropic Profiler Storage Administration Screen
</title>
<style type="text/css">
   body { background-color: whitesmoke; text-align: center; font-family: sans-serif; }
   table { border: 1px solid black; background-color: white; text-align: center; }
   th { background-color: deepskyblue; }
   tr { background-color: palegreen; }
   tr.dirname { background-color: lightskyblue; text-align: left; }
   tr.expfile { background-color: lightpink; }
   tr.examplefile { background-color: plum; }
   tr.totalsize { background-color: lightcyan; text-align: right; }
</style>
</head>
<body>
<center>

<table>
<tr><th>Name</th><th>Timestamp</th><th>Age</th><th>Files</th><th>Size</th></tr>
<?php
$filespath="./examples/";
print("<tr class='dirname'><td colspan=5>".$filespath."</td></tr>");
$foundfiles=glob($filespath."*.seq");
foreach($foundfiles as $file){
  $fullfilename=realpath($file);
  $filename=basename($fullfilename,".seq");
  $filesize=filesize($fullfilename);
  $filedate=filemtime($fullfilename);
  $fileage=(time()-$filedate)/(24*3600);
  if(file_exists($fullfilename.".tree")) $filesize+=filesize($fullfilename.".tree");
  if(file_exists($fullfilename.".data")) $filesize+=filesize($fullfilename.".data");
  if(file_exists($fullfilename.".desc")) $filesize+=filesize($fullfilename.".desc");
  $totalsize+=$filesize;
  $rowclass=" class='examplefile'";
  print("<tr".$rowclass."><td>".$filename."</td><td>".date("d-m-Y H:i:s",$filedate)."</td><td>".round($fileage,2)."</td><td>4</td><td>".number_format($filesize,0,",",".")."</td></tr>\n");
}
$dir1="./output/";
$dir2="./uploads/";
$totalsize=0;
print("<tr class='dirname'><td colspan=5>".$dir2."</td></tr>");
$subdirs=glob($dir1."*",GLOB_ONLYDIR);
$expiredate=time()-5*24*3600; // 5 days lifetime
foreach($subdirs as $subdir){
	$subdirname=basename($subdir);
	$subdirdate=filemtime($subdir);
	$subdirage=(time()-$subdirdate)/(24*3600);
	if( isset($_POST['submit']) && $subdirdate<$expiredate ){
		$files=glob($subdir."/*");
		foreach($files as $file) { unlink($file); }
		rmdir($subdir);
		$subdir2=$dir2.$subdirname;
		$files=glob($subdir2."/*");
		foreach($files as $file) { unlink($file); }
		rmdir($subdir2);
	} else {
		$filescount=0;
		$subdirsize=0;
		$files=glob($subdir."/*");
		foreach($files as $file) { $subdirsize+=filesize($file); $filescount++; }
		$subdir2=$dir2.$subdirname;
		$files=glob($subdir2."/*");
		foreach($files as $file) { $subdirsize+=filesize($file); $filescount++; }
		$totalsize+=$subdirsize;
		$rowclass="";
		if($subdirdate<$expiredate) $rowclass=" class='expfile'";
		print("<tr".$rowclass."><td>".htmlentities($subdirname)."</td><td>".date("d-m-Y H:i:s",$subdirdate)."</td><td>".round($subdirage,2)."</td><td>".$filescount."</td><td>".number_format($subdirsize,0,",",".")."</td></tr>\n");
	}
}
print("<tr class='totalsize'><td colspan=4></td><td>".number_format($totalsize,0,",",".")."</td></tr>\n");
?>
</table>

<br>
<form method='post' action='<?php print($PHP_SELF); ?>'>
<?php if(!isset($_POST['submit'])) print("<input type='submit' name='submit' value='Clean'>&nbsp"); ?>
<input type='button' value='Done' onclick="window.open('index.php','_self');">
</form>

</center>
</body>
</html>
