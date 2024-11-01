<?php
/*
Plugin Name: SymfoPress Lite
Plugin URI: http://tokarchuk.ru
Description: Symfony 2 integration to WordPress
Version: 1.0
Author: Andrey Tokarchuk
Author URI: http://tokarchuk.ru
License: GPL2
*/

//Catch anyone trying to directly acess the plugin - which isn't allowed
if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

//Check if the the class already exists
if (!class_exists("NetandreusSymfopressLitePlugin")) {
    class NetandreusSymfopressLitePlugin {

        // Environment settings
        public static $env = array(
            'env'   => 'dev', // dev/prod/test
            'debug' => 'true'
        );

        protected     $_VendorsLoaded = false;
        protected     $_Options = array();


        /**
         * Basic constructor for the LoadsysSimplePlugin Class
         */
        public function __construct() {
            global $SYMFOPRESS;
            $sf_root = plugin_dir_path(__FILE__);
            if(file_exists($sf_root.'/vendor/autoload.php')) {
                $this->_VendorsLoaded = true;
            }
            if(file_exists($sf_root.'/options.json')) {
                $this->_Options = json_decode(file_get_contents($sf_root.'/options.json'), true);
            // Default values
            } else {
                $this->initDefaultOptions();
            }
            $SYMFOPRESS = $this->_Options;

            // Настройка
        }

        private function initDefaultOptions()
        {
            $this->_Options = array(
                'slug' => basename(__FILE__, '.php')
            );
        }

        public function install()
        {
            if ( ! current_user_can( 'activate_plugins' ) )
                return;

            // Is PHP supports namespaces?
            if(!version_compare(phpversion(), '5.3.3', '>=')) {
            //if(true) {
                echo '<div class="error">'
                    . '<h3>' . __( 'Error activating the plugin', WPLANG ). '</h3>'
                    . '<p>' . __( 'You need PHP >= 5.3.3 to activate this plugin.', WPLANG ).'&nbsp;'
                    . __( 'Your version', WPLANG ).' is <b>'.phpversion().'</b></p>'
                    .'</div>'
                ;
                deactivate_plugins(__FILE__); // deactivate plugin
                unset($_GET['activate']);     // disble activation message
            }
        }

        /**
         * Returns a string to add to the WordPress Footer
         * @return string short string added to the WP footer
         */
        public function init_action() {
            // For debug
            /*
            error_reporting(E_ALL);
            ini_set('display_errors', true);
            date_default_timezone_set('Europe/Moscow');
            */
            
            // If Plugin is not properly install just return to WordPress dispatching
            if(!array_key_exists('dispatching', $this->_Options) OR $this->_Options['dispatching'] == false)
                return;
            // Check if plugin is activate and vendors downloaded
            $sf_root = plugin_dir_path(__FILE__);
            if(!$this->_VendorsLoaded) {
                wp_die('<h1>Setup stage 2/2</h1><br/>Please run <pre>cd ./wp-content/plugins/'.$this->_Options['slug'].'<br/>php composer.phar install</pre> to complete plugin install.', 'Symfony 2 vendors not installed');
            }

            // Load Sf
            if(!$_SERVER['REMOTE_ADDR'])
                $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            //require_once $sf_root.'web/app_dev.php';
            require_once $sf_root.'/app/bootstrap.php.cache';
            require_once $sf_root.'/app/AppKernel.php';
            try {
                $kernel = new AppKernel(self::$env['env'], self::$env['debug']);
                $kernel->loadClassCache();
                $kernel->handle(Symfony\Component\HttpFoundation\Request::createFromGlobals())->send();
            } catch (\Exception $e) {
                if($e->getMessage() != 'SF_NOT_FOUND_EXCEPTION') {
                    throw $e;
                }
            }
        }

        function admin()
        {
            if ( function_exists('add_options_page') )
            {
                add_options_page('SymfoPress Options',
                    'SymfoPress Lite', 8, basename(__FILE__),
                    array (&$this, 'admin_form') );
            }
        }

        function admin_form()
        {
            $mode = array_key_exists('mode', $_GET)? $_GET['mode'] : 'home';
            $optionsFile = plugin_dir_path(__FILE__).'/options.json';
            require_once plugin_dir_path(__FILE__).'/src/Netandreus/SymfopressBundle/ServerValidator.php';
            $checker = new Netandreus\SymfopressBundle\ServerValidator();
            $errors = $checker->check();

            // Update settings
            if($mode == 'update') {
                if(array_key_exists('dispatching', $_POST) && $_POST['dispatching'] == 'on') {
                    $options = array(
                        'slug' => basename(__FILE__, '.php'),
                        'dispatching' => true
                    );
                    file_put_contents($optionsFile, json_encode($options));
                    $this->_Options = $options;
                } else {
                    if(file_exists($optionsFile))
                        unlink($optionsFile);
                    $this->initDefaultOptions();
                }
            }
            // Install vendors info subpage
            if($mode == 'install-vendors') {
                $currentDir = getcwd();
                chdir(plugin_dir_path(__FILE__));
                shell_exec('(php composer.phar install > composer.log; echo finished >> composer.log)&');
                $log = file_get_contents('./composer.log');
                chdir($currentDir);
                print '<div class="wrap">
                <?php screen_icon(); ?>
                <h2>Installation vendors</h2>';
                if(strpos($log, 'Fatal error') != FALSE) {
                    print '<h3 style="color: orangered;">Error installing vendors!</h3>';
                } elseif(strpos($log, 'finished')) {
                    print '<h3 style="color: green;">Vendors installation complete!</h3>';
                    print 'Now you can <a href="/wp-admin/options-general.php?page='.$this->_Options['slug'].'.php">activate dispatching</a>.<br/>';
                }
                print '<h4>Composer log file:</h4>
                <textarea style="width: 100%; height: 300px;">'.$log.'</textarea>
                <br/>';
                if(!strpos($log, 'finished')) {
                    print 'If you see some errors you can manually eun commands in console:<br/>
                <pre>cd ./wp-content/plugins/'.$this->_Options['slug'].'<br/>php composer.phar install</pre>
                to download Symfony 2 and complete SymfoPress activation.<br/>';
                }
                return;
            // Display form
            } elseif(in_array($mode, array('update', 'home'))) {
                if($mode == 'update' && array_key_exists('dispatching', $_POST) && $_POST['dispatching'] == true) {
                    $currentDir = getcwd();
                    chdir(plugin_dir_path(__FILE__));
                    shell_exec('(php app/console cache:clear)&');
                    chdir($currentDir);
                }
                print '<div class="wrap">
                <?php screen_icon(); ?>
                <h2>SymfoPress install Wizard!</h2>';
                if(count($errors) > 0) {
                    print '<div style="color: orangered;">You should fix folowwing issues to continue the installation.</div>';
                    $errorsTable = $checker->getCss().$checker->getTable($errors);
                    print $errorsTable;
                    return;
                }
                print '<form method="post" action="/wp-admin/options-general.php?page='.$this->_Options['slug'].'.php&mode=update">';
                wp_nonce_field('update-options');
                print '<table class="form-table">
                <tr valign="top">
                <th scope="row">1. Plugin activated</th>
                <td><input type="checkbox" id="checkbox_example" name="" checked disabled/></td>
                </tr>';
                print '<th scope="row">2. Install Symfony 2 vendors (additional libs)</th>';
                if($this->_VendorsLoaded) {
                    print '<td><input type="checkbox" id="checkbox_example" name="" disabled checked/></td></tr>';
                } else {
                    print '<td><input type="checkbox" id="checkbox_example" name="" disabled/>&nbsp; <a href="options-general.php?page='.$this->_Options['slug'].'.php&mode=install-vendors"/>Automatic install</a> *</td></tr>';
                }
                print '
                <th scope="row">3. Activate dispatching</th>';
                $isDispatchingEnable = (array_key_exists('dispatching', $this->_Options) && $this->_Options['dispatching'] == true);
                print '<td><input type="checkbox" id="checkbox_example" name="dispatching" '.(($isDispatchingEnable)? 'checked' : '').' '.((!$this->_VendorsLoaded)? 'disabled' : '').'/></td></tr>';
                print '</table>';
                if($this->_VendorsLoaded && $isDispatchingEnable) {
                    print '<p style="color: green;">SymfoPress Lite installation complete! Now you can <a href="/symfopress-demo/" target="new">view demo</a></p>';
                }
                if(!$this->_VendorsLoaded) {
                    print '&nbsp;&nbsp;&nbsp;<i>* - You can install vendors manually by this commands: cd ./wp-content/plugins/symfopress; php composer.phar install</i>';
                }
                submit_button();
                print '</form></div>';
            }
        }

    } // end of class
}

if (class_exists("NetandreusSymfopressLitePlugin")) {
    $NetandreusSymfopressLitePlugin = new NetandreusSymfopressLitePlugin();
}

if (isset($NetandreusSymfopressLitePlugin)) {
    add_action('plugins_loaded', array(&$NetandreusSymfopressLitePlugin, 'init_action'), 100);
    add_action('admin_menu',  array (&$NetandreusSymfopressLitePlugin, 'admin') );
    add_action('admin_head',  array (&$NetandreusSymfopressLitePlugin, 'install') );
}
?>
