<?php 
$set = array (
  'cache' => 0,
  'language' => 'russian',
  'cache_querys' => 0,
  'cookie_time' => '30',
  'start_mod' => '',
  'open_reg' => '1',
  'debug_mode' => 1,
  'max_file_size' => '15000000',
  'min_password_lenght' => '5',
  'admin_email' => 'test@email.cms',
  'redirect' => '',
  'redirect_delay' => '1',
  'time_on_line' => '10',
  'template' => 'default',
  'use_additional_fields' => 0,
  'hlu' => 1,
  'hlu_extention' => '.htm',
  'hlu_understanding' => 0,
  'allow_html' => 0,
  'autotags_active' => 1,
  'autotags_exception' => 'для,его,при,При,свои,как,все,так,что,это',
  'autotags_priority' => 'fapos cms,fapos,free cms',
  'img_preview_size' => '200',
  'date_format' => 'Y-m-d/D H:i:s',
  'use_watermarks' => 1,
  'watermark_img' => 'watermark.png',
  'quality_jpeg' => '75',
  'quality_png' => '9',
  'watermark_hpos' => '3',
  'watermark_vpos' => '2',
  'watermark_alpha' => '50',
  'watermark_type' => '1',
  'watermark_text' => 'fapos.net',
  'watermark_text_angle' => '90',
  'watermark_text_size' => '10',
  'watermark_text_color' => 'FFFFFF',
  'watermark_text_border' => '0000FF',
  'watermark_text_font' => 'Scada-Bold.ttf',
  'redirect_active' => 1,
  'use_noindex' => 1,
  'blacklist_sites' => '*.ucoz.ru,*.ucoz.net',
  'whitelist_sites' => 'fapos.net,www.fapos.net',
  'url_delay' => '5',
  'use_pdo' => 1,
  'latest_on_home' => 
  array (
    0 => 'news',
    1 => 'stat',
    'news' => 'news',
    'stat' => 'stat',
  ),
  'cnt_latest_on_home' => '5',
  'announce_lenght' => '1000',
  'forum' => 
  array (
    'title' => 'Форум',
    'description' => 'CMS форум',
    'not_reg_user' => 'Гостелло',
    'max_post_lenght' => '3000',
    'themes_per_page' => '5',
    'posts_per_page' => '10',
    'active' => 1,
  ),
  'news' => 
  array (
    'title' => 'Новости',
    'description' => 'Самые свежие новости',
    'max_lenght' => '15000',
    'announce_lenght' => '700',
    'per_page' => 10,
    'active' => 1,
    'comment_active' => 1,
    'comment_per_page' => '50',
    'comment_lenght' => '500',
    'max_attaches' => '10',
    'max_attaches_size' => 5000000,
    'img_size_x' => '150',
    'img_size_y' => '350',
    'fields' => 
    array (
    ),
    'comments_order' => 0,
  ),
  'stat' => 
  array (
    'title' => 'Каталог статей',
    'description' => 'Только интересные статьи',
    'max_lenght' => '10000',
    'per_page' => '7',
    'announce_lenght' => '1000',
    'active' => '1',
    'comment_active' => 1,
    'comment_per_page' => '30',
    'comment_lenght' => '500',
    'max_attaches' => 10,
    'max_attaches_size' => 5000000,
    'img_size_x' => 150,
    'img_size_y' => 200,
    'fields' => 
    array (
    ),
  ),
  'pages' => 
  array (
    'active' => 1,
  ),
  'loads' => 
  array (
    'title' => 'Каталог файлов',
    'description' => 'Каталог файлов. Все файлы тут.',
    'min_lenght' => '200',
    'max_lenght' => '4500',
    'announce_lenght' => '300',
    'per_page' => '30',
    'max_file_size' => '15000000',
    'active' => 1,
    'comment_active' => 1,
    'comment_per_page' => 0,
    'comment_lenght' => '500',
    'max_attaches' => '10',
    'max_attaches_size' => 5000000,
    'img_size_x' => '150',
    'img_size_y' => '200',
    'fields' => 
    array (
      'attach_file' => 'attach_file',
    ),
    'Ограничения' => '',
    'Изображения' => '',
    'Обязательные поля' => '',
    'Комментарии' => '',
    'comments_order' => 0,
    'Прочее' => '',
  ),
  'secure' => 
  array (
    'antisql' => 0,
    'anti_ddos' => 0,
    'request_per_second' => '7',
    'system_log' => 1,
    'max_log_size' => '99999744',
    'autorization_protected_key' => 0,
    'session_time' => '116013',
  ),
  'users' => 
  array (
    'max_avatar_size' => '200000',
    'users_per_page' => '30',
    'max_message_lenght' => '2000',
    'max_count_mess' => '100',
    'title' => 'Пользователи',
    'description' => 'Юзвери',
    'max_mail_lenght' => '20000',
    'rating_comment_lenght' => '200',
    'active' => 1,
    'warnings_by_ban' => '5',
    'autoban_interval' => '2000000',
    'fields' => 
    array (
      'keystring' => 'keystring',
    ),
  ),
  'chat' => 
  array (
    'title' => 'Чат',
    'max_lenght' => '200',
    'active' => 1,
    'description' => '',
  ),
  'foto' => 
  array (
    'description_lenght' => '300',
    'description_requred' => 0,
    'active' => 1,
    'title' => 'Фото',
    'description' => 'Каталог Фотографий',
    'per_page' => '20',
    'max_file_size' => '5000000',
    'Ограничения' => '',
    'Поля обязательные для заполнения' => '',
    'fields' => 
    array (
      'description' => 'description',
    ),
    'Прочее' => '',
  ),
  'statistics' => 
  array (
    'active' => '1',
  ),
  'db' => 
  array (
    'host' => 'localhost',
    'name' => 'fapos',
    'user' => 'root',
    'pass' => '',
    'prefix' => '',
  ),
  'news_on_home' => 1,
  'site_title' => 'CMS Fapos',
  'meta_keywords' => 'создание сайта, шаблоны',
  'meta_description' => 'Что такое и как пользоваться Fapos CMS. Документация и шаблоны',
  'title' => 'Fapos.net - простота и легкость создания сайтов',
  'search' => 
  array (
    'title' => 'Поиск',
    'active' => '1',
    'description' => 'Лучший  поиск от Fapos',
    'min_lenght' => '2',
    'per_page' => '20',
    'index_interval' => 2,
  ),
  'email_activate' => 1,
  'common' => 
  array (
    'rss_news' => 1,
    'rss_stat' => 1,
    'rss_loads' => 1,
    'rss_lenght' => 300,
    'rss_cnt' => 10,
  ),
  'auto_sitemap' => 1,
  'allow_smiles' => 1,
  'smiles_set' => 'fapos',
  'Какие из последних материалов выводить на главной' => '',
  'Прочее' => '',
)
?>