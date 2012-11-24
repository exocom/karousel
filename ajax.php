<?php 
	$items = Array(
		0=>'<li><a href="img/t28.jpg"><img src="img/t28.jpg" alt="T28 Turbo" title="T28 Turbo" /></a></li>',
		1=>'<li><a href="img/big_air.jpg"><img src="img/big_air.jpg" alt="Snowboarding into foam pit" title="Snowboarding into foam pit" /></a></li>',
		2=>'<li><a href="img/lib.png"><img src="img/lib.png" alt="LIB Tech Skateboard" title="LIB Tech Skateboard" /></a></li>',
		3=>'<li><a href="img/Pyro-GX.jpg"><img src="img/Pyro-GX.jpg" alt="Pyro GX" title="Pyro GX" /></a></li>',
		4=>'<li><a href="img/forum_grudge_f.jpg"><img src="img/forum_grudge_f.jpg" alt="Forum Snowboard" title="Forum Snowboard" /></a></li>'
	);
	
	if(isset($_GET['numbers'])){
		$items = Array(
			0=>'<li><a href="img/1.png"><img src="img/1.png" alt="1" title="1" /></a></li>',
			1=>'<li><a href="img/2.png"><img src="img/2.png" alt="2" title="2" /></a></li>',
			2=>'<li><a href="img/3.png"><img src="img/3.png" alt="3" title="3" /></a></li>',
			3=>'<li><a href="img/4.png"><img src="img/4.png" alt="4" title="4" /></a></li>',
			4=>'<li><a href="img/5.png"><img src="img/5.png" alt="5" title="5" /></a></li>'
		);
	}
	
	$selectedItems = Array();
	
	if(isset($_GET['from']) && is_numeric($_GET['from'])) {
	
		if(isset($_GET['to']) && is_numeric($_GET['to'])) {
			$selectedItems = array_slice($items,$_GET['from'],abs($_GET['from'] - $_GET['to']) + 1);
		
		} else {
			$selectedItems = array_slice($items,0,$_GET['from']);
		}
	}
	foreach($selectedItems as $k => $v) {
		echo $v;
	}
?>