<?php

	require_once (__DIR__ . "/../includes/config.php");

	if($id = storeNewPost($_POST))
	{
		include ('index.php');
	}
	else
	{
		$title = $_POST['title'];
		$poster = $_POST['poster'];
		$message = $_POST['message'];

		if ($title && $poster && $message)
		{
			renderHeader($title);

			displayNewPostForm($title, $poster, $message);

			echo 'Such message already exists :-(';

			renderFooter();
		}
		else
		{
			if ($title == NUll)
				$title = 'FUCKED!!! Do fill in ALL the blanks, Woodpecker :-)))';

			renderHeader($title);
		
			if ($title == 'FUCKED!!! Do fill in ALL the blanks, Woodpecker :-)))')
				$title = NUll;

			displayNewPostForm($title, $poster, $message);

			echo 'Your message is not saved. Check if all the fields are complete, and then try again.';

			renderFooter();
		}

	}


/*PHP-компонент для сохранения
нового сообщения в базу данных*/

?>
