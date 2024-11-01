<?php
// Get WordPress DB settings

// For app/console mode
if(php_sapi_name() === 'cli') {
   $file = file('../../../wp-config.php');
   $constants = \Netandreus\SymfopressBundle\NetandreusSymfopressBundle::getWpConstants('../../../wp-config.php');
   $container->setParameter('database_name', $constants['DB_NAME']);
   $container->setParameter('database_user', $constants['DB_USER']);
   $container->setParameter('database_password', $constants['DB_PASSWORD']);
   $container->setParameter('database_host', $constants['DB_HOST']);
   if(file_exists('./options.json')) {
        $SYMFOPRESS = json_decode(file_get_contents('options.json'), true);
   } else {
       $SYMFOPRESS['slug'] = NULL;
   }
// For web mode
} else {
    $container->setParameter('database_name', DB_NAME);
    $container->setParameter('database_user', DB_USER);
    $container->setParameter('database_password', DB_PASSWORD);
    $container->setParameter('database_host', DB_HOST);
    global $SYMFOPRESS;

}
$container->setParameter('plugin_path', '/wp-content/plugins/'.$SYMFOPRESS['slug']);