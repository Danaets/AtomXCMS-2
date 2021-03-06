<?php
/*-----------------------------------------------\
| 												 |
|  @Author:       Andrey Brykin (Drunya)         |
|  @Version:      1.2.9                          |
|  @Project:      CMS                            |
|  @package       CMS AtomX                      |
|  @subpackege    Entry dot                      |
|  @copyright     ©Andrey Brykin 2010-2012       |
|  @last mod.     2012/04/29                     |
\-----------------------------------------------*/

/*-----------------------------------------------\
| 												 |
|  any partial or not partial extension          |
|  CMS AtomX,without the consent of the          |
|  author, is illegal                            |
|------------------------------------------------|
|  Любое распространение                         |
|  CMS AtomX или ее частей,                      |
|  без согласия автора, является не законным     |
\-----------------------------------------------*/




header('Content-Type: text/html; charset=utf-8');




if (file_exists('install')) {
	include_once ('sys/settings/config.php');
	if (!empty($set) &&
        !empty($set['db']['name']) &&
        (
            !empty($set['db']['user']) ||
            !empty($set['db']['pass'])
        )
    ) {
		die('Before use your site, delete INSTALL dir! <br />Перед использованием удалите папку INSTALL');	
	}
	header('Location: install'); die();
}




include_once 'sys/boot.php';

Plugins::intercept('before_pather', array());

/**
 * Parser URL
 * Get params from URL and launch needed module and action
 */
new Pather($Register);
Plugins::intercept('after_pather', array());
//pr($Register);
