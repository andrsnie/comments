<?php

	require_once(__DIR__ . "/constants.phpm");

	function displayMes()
	{
		$handle = new PDO("mysql:dbname=" . DATABASE . "; host=" . SERVER, USERNAME, PASSWORD);

		$query = "SELECT count(*) FROM header";
		if($result = $handle -> query($query))
		{
			//Определим количество строк, подходящих под условие выражения SELECT
			$numRows = $result -> fetchColumn();
			if($numRows > 0)
			{
				//Выполняем реальный SELECT и работаем с его результатом
				$query = "SELECT * FROM header";
				$result = $handle -> query($query);
				$row = $result -> fetchAll(PDO::FETCH_ASSOC);
				for ($count = 0; $count < $numRows; $count++)
				{
					echo "<a href = 'http://localhost/showPost.php?postid={$row[$count]['postid']} & title={$row[$count]['title']}'>{$row[$count]['title']} - {$row[$count]['poster']} - {$row[$count]['posted']} <br/><br/></a>";
				}
			}
		}
		else
		{
			return false;
		}
	}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	function getPost($postid)
	{
		// загрузить из базы данных одно сообщение
		// и вернуть его в виде массива.
		if(!$postid)
			return false;

		$handle = new PDO("mysql:dbname=" . DATABASE . "; host=" . SERVER, USERNAME, PASSWORD);

		$query = "SELECT count(*) FROM header WHERE postid = $postid";
		if($result = $handle -> query($query))
		{
			if($result -> fetchColumn() == 1)
			{
				$query = "SELECT * FROM header WHERE postid = $postid";
				$result = $handle -> query($query);
				$post = $result -> fetchAll(PDO::FETCH_ASSOC);
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		// загрузить содержимое сообщения из таблицы body
		// и добавить его в этот же массив.
		$query = "SELECT count(*) FROM body WHERE postid = $postid";
		if($result = $handle -> query($query))
		{
			if($result -> fetchColumn() > 0)
			{
				$query = "SELECT * FROM body WHERE postid = $postid";
				$result = $handle -> query($query);
				$body = $result -> fetchAll(PDO::FETCH_ASSOC);
				if($body)
				{
					$post['message'] = $body[0]['message'];
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		return $post;
	}

//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	function displayPost($post)
	{
		echo "{$post[0]['title']} - {$post[0]['poster']} - {$post[0]['posted']}<br/><br/>{$post['message']}";
	}


/*Функция displayPost() для отображения выбранного сообщения*/

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	function displayNewPostForm($title = 'Write your own new comment', $poster = NULL, $message = NULL)
	{

?>

	<html>
		<body style="text-align: center;">
			<form action="storeNewPost.php" method="post">
				<p>Title: <input name="title" type="text" <?php if ($title == 'Write your own new comment') $title = NULL;?> value = "<?= htmlspecialchars($title) ?>"></p>
				<p>Poster: <input name="poster" type="text" value = "<?= htmlspecialchars($poster) ?>"></p>
				<p>Message: <input name="message" type="text" value = "<?= htmlspecialchars($message) ?>"></p>
				<p><input type="submit" value="Send"></p>
			</form>
		</body>
	</html>
	
<?php

	}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	function storeNewPost($post)
	{
		// проверка, что пользователь заполнил все обязательные поля
		if($post['title'] == NULL || $post['poster'] == NULL || $post['message'] == NULL)
		{
			return false;
		}
		
		$handle = new PDO("mysql:dbname=" . DATABASE . "; host=" . SERVER, USERNAME, PASSWORD);

		// проверка, что добавляемое сообщение не дублирует какое-либо уже существующее
		$query = "SELECT count(*) FROM header, body WHERE
				header.postid = body.postid AND
				header.title = '".$post['title']."' AND
				header.poster = '".$post['poster']."' AND
				body.message = '".$post['message']."'";
		if($result = $handle -> query($query))
		{
			if($result -> fetchColumn() > 0)
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		// сохранение параметров нового сообщения в таблицу header
		$query = "INSERT INTO header VALUES ('".$post['title']."', '".$post['poster']."', NOW(), NULL)";
		$result = $handle -> query($query);
		if (!$result)
		{
			return false;
		}

		// определение идентификатора добавляемого сообщения.
		//У только что добавленного сообщения еще нет содержимого,
		// поэтому его можно найти с помощью объединения таблиц header и body.
		$query = "SELECT count(*) FROM header LEFT JOIN body
				ON header.postid = body.postid
				WHERE title = '".$post['title']."'
				AND poster = '".$post['poster']."'
				AND body.postid is NULL";	
		if($result = $handle -> query($query))
		{
			if($result -> fetchColumn() > 0)
			{
				$query = "SELECT header.postid FROM header LEFT JOIN body
						ON header.postid = body.postid
						WHERE title = '".$post['title']."'
						AND poster = '".$post['poster']."'
						AND body.postid is NULL";
				$result = $handle -> query($query);
				$row = $result -> fetchAll(PDO::FETCH_NUM);
				$id = $row[0][0];
			}
		}
		else
		{
			return false;
		}

		if($id)
		{
		    $query = "INSERT INTO body VALUES ($id, '".$post['message']."')";
		    $result = $handle -> query($query);
			if (!$result)
			{
				return false;
			}
		}
		return $id;
	}


/*Функция storeNewPost() для
сохранения сообщения в базу данных*/

?>
