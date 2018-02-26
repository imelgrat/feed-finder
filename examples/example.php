<?php

	require_once ('../src/feed-finder.php');
	$url = 'https://imelgrat.me';
	$useragent = 'GoogleBot';
	$obey = false;

	if($_POST['submit'] != '')
	{
		if($_POST['url'] != '')
		{
			$url =  trim($_POST['url']) ;
		}
		
		if($_POST['useragent'] != '')
		{
			$useragent =  trim($_POST['useragent']) ;
		}
		
		$obey = $_POST['obey']?true:false;
		
		$find_links = new FeedFinder();
		$links = $find_links->setURL($url)->setUserAgent($useragent)->setObeyRobots($obey)->getFeeds();		
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>FeedFinder Example</title>
</head>
<body>
	<h1>Feed Finder Tester</h1>
	<form action="" method="post">
	<div><label for="url"><strong>URL: </strong><input name="url" type="text" value="<?php echo $url;?>" size="100" ></label></div>
	<div><label for="useragent"><strong>User Agent: </strong><input name="useragent" type="text" value="<?php echo $useragent;?>" size="100" ></label></div>
	<div><label for="obey"><strong>Obey robots.txt? </strong><input name="obey" type="checkbox" value="1" <?php if($obey){echo 'checked="checked"';}?> ></label></div>
	<div><input type="submit" name="submit" value="Find Feeds"></div>
	</form>
	
	<?php  if($_POST['submit']!=''): ?>
		<h2>Feeds found on URL:</h2>
		<?php
			foreach($links as $row)
			{
				echo "<p>{$row}</p>";
			}
		?>
	<?php  endif; ?>
</body>
</html>