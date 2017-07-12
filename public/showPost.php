<?php

	require_once (__DIR__ . "/../includes/config.php");

	$postid = $_GET['postid'];

	// получить детальную информацию о выбранном сообщении
	$post = getPost($postid);

	renderHeader($post[0]['title']);

	// отобразить выбранное сообщение
	displayPost($post);

	renderFooter();


/*PHP-компонент для просмотра сообщения*/

?>
