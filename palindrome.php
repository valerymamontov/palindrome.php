<?php
	header('Content-Type: text/html; charset=utf-8');
	$src_text = $_POST['user_text'];//сохраняем исходное значение переменной
	$user_text = $src_text;

	//если данные через форму ещё не отправлялись,
	//то предлагаем выполнить проверку на палиндромность
	if (empty($user_text)) 
	{
echo <<<HTML
	<br><br><br><br><br><br><br>
	<hr>
	<p>Давайте проверим, является ли строка палиндромом!</p>
	<form method="post" action="palindrome.php">
	<p><b>Введите текст для проверки:</b><br>
	<input name="user_text" type="text" size="60"></p>
	<p><input type="submit" value="Проверить">
	<input type="reset" value="Очистить"></p>
 	</form>
 	<br><br><br><br>
HTML;
	//Внимание: конструкция HTML обязательно пишется без пробелов, без отступов, без комментариев
	}
	
	else
	{
	//:::::::::::::::::: начало проверки на палиндромность ::::::::::::::::::

	//1. на первом шаге приводим к одному регистру (нижнему)
	$user_text = mb_strtolower($user_text,'UTF-8');
	
	//2. на втором шаге удаляем пробелы
	$user_text = str_replace(" ", "", $user_text);
	
	//3. на третьем шаге переворачиваем строку. Так как стандартная функция "strrev" 
	//плохо работает с кодировкой UTF-8, то дорабатываем функцию: меняем кодировку, 
	//переворачиваем строку и меняем кодировку обратно 
	function strrev_enc($str)//функция
	{
	    $str = iconv('utf-8', 'windows-1251', $str);
	    $str = strrev($str);
	    $str = iconv('windows-1251', 'utf-8', $str);
	    return $str;
	}
	$user_text_palindr = strrev_enc($user_text);
	
	//4. на четвёртом шаге стравниваем две строки, чтобы
	//определить является ли введённая строка палиндромом.
	//Если они идентичны, то выводим ТЕКСТ (HTML)
	if ($user_text == $user_text_palindr)
	{
echo <<<HTML
		<br><br><br><br><br><br><br>
		<hr>
		<p>Введённая строка <b>"$src_text"</b> является палиндромом!</p>
		<p><a href='palindrome.php'>Вернуться назад</a></p>
HTML;
	}

	else
	{
	//ДАЛЕЕ: если строка не является палиндромом, то 
	//5. ищем самый длинный подпалиндром или 6. выводим первый символ строки (если подпалиндромов нет) 

	//5.1. длина строки вычисляется в байтах и для разных кодировок 
	//результат будет разным. Поэтому пишем условие. 
	//Если текст введён на русском, то длина делится пополам.
	if (mb_detect_encoding($user_text) == 'ASCII')
		{
			$len_user_text = strlen($user_text);
		}
	else
		{
			$len_user_text = (strlen($user_text))/2;
		}
		
	//5.2. пишем цикл для перебора всех возможныех вариантов слов, 
	//которые можно получить из введённой строки. 
	//Используется функция subsrt, точнее её аналог для многобайтовых кодировок (mb_sbstr)/
	//Полученные значения передаются в массив $elem.
	for ($i=0; $i<$len_user_text;$i++)
		{
			for ($c=0; $c < $len_user_text-$i; $c++)
				{
					$elem[] = mb_substr($user_text, $i,$c+1,'UTF-8');
				}
		}

	//****************************************************************************
	//код для отображения массива элементов, которые были сформированы из строки
				/*
				echo "<br><pre>";
				print_r($elem);
				echo "</pre><br>";
				*/
	//*****************************************************************************
		
	//5.3. отбираем элементы массива, исключая элементы из одного символа.
	//Выбранное значение отражается (переворачивается) и проверяется на идентичность.
	//Для реверса (переворота) снова используется пользовательская функция strrev_enc (написанная выше).
	//Она необходима для решения проблем с кодировокой.

	$len_arr = count($elem);//количество элементов массива
	for ($a=0; $a < $len_arr; $a++)
		{
			if (strlen($elem[$a])/2>1) //исключение всех значений из одного символа
				{
					if ($elem[$a] == strrev_enc($elem[$a]))//проверка на идентичность
						{
							$palindrome_array[] = $elem[$a];//занесение идентичных строк в новый массив
						}
				}
		}


	//5.4. если массив с подпалиндромами не пустой: пишем цикл, который перебирает элементы массива,
	//сравнивает их длину и определяет самый длинный подпалиндром. Затем выводим ТЕКСТ (HTML)
	if (!empty($palindrome_array))
		{
			$max_value = mb_strlen($palindrome_array[0])/2;//берём длину первого элемента
			$index = 0;
			foreach($palindrome_array as $key => $value)
				{
					if(mb_strlen($value)/2 > $max_value)
						{
							$max_value = mb_strlen($value)/2;
							$index = $key;
						}
				}
echo <<<HTML
		<br><br><br><br><br><br><br>
		<hr>
		<p>Введённая строка <b>"$src_text"</b> не является палиндромом!</p>
		<p>Но содержит подпалиндромы, самый длинный из которых <b>"$palindrome_array[$index]"</b>.</p>
		<p><a href='palindrome.php'>Вернуться назад</a></p>
HTML;
		}
	
	//6. если строка не палиндром и нет подпалиндромов,
	//то выводим ТЕКСТ (HTML) и первый символ строки
	else
		{
			$first_symbol = mb_substr($user_text, 0, 1,'UTF-8');

echo <<<HTML
		<br><br><br><br><br><br><br>
		<hr>
		<p>Введённая строка <b>"$src_text"</b> не является палиндромом!</p>
		<p>Она не содержит подпалиндромы. Первый символ строки: <b>"$first_symbol"</b>.</p>
		<p><a href='palindrome.php'>Вернуться назад</a></p>
HTML;

		}

	}
	//:::::::::::::::::: конец проверки на палиндромность ::::::::::::::::::
	}
?>