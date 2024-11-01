<?php
/**
 * SymfoPress server checker
 * Checks if server ready to install SymfoPress
 */
namespace Netandreus\SymfopressBundle;

class ServerValidator
{
    private $validations = array();

    public function __construct() {
        $this->validations = array(
            'PHP Version' => array(
                'isValid' => version_compare(phpversion(), '5.3.3', '>='),
                'message' => 'You should install php >= 5.3.3'
            ),
            'Phar php extension' => array(
                'isValid' => extension_loaded('Phar'),
                'message' => 'You should enable "Phar" php extension'
            ),
            'Intl php extension' => array(
                'isValid' => extension_loaded('intl'),
                'message' => 'You should enable "Intl" php extension'
            ),
            'Json php extension' => array(
                'isValid' => extension_loaded('json'),
                'message' => 'You should enable "Json" php extension'
            ),
            'Ctype php extension' => array(
                'isValid' => extension_loaded('ctype'),
                'message' => 'You should enable "Ctype" php extension'
            )
        );
    }

    public function check()
    {
        $errors = array();

        // Check
        if(count($this->validations) > 0) {
            foreach($this->validations as $name => $data) {
                if(!$data['isValid']) {
                    $errors[$name] = $data['message'];
                }
            }
        }
        return $errors;
    }

    public function getCss()
    {
        return '
        <style>
        body{
            margin:0;
            padding:0;
            background:#f1f1f1;
            font:70% Arial, Helvetica, sans-serif;
            color:#555;
            line-height:150%;
            text-align:left;
        }
        a{
            text-decoration:none;
            color:#057fac;
        }
        a:hover{
            text-decoration:none;
            color:#999;
        }
        h1{
            font-size:140%;
            margin:0 20px;
            line-height:80px;
        }
        h2{
            font-size:120%;
        }
        #container{
            margin:0 auto;
            width:680px;
            background:#fff;
            padding-bottom:20px;
        }
        #content{margin:0 20px;}
        p.sig{
            margin:0 auto;
            width:680px;
            padding:1em 0;
        }
        form{
            margin:1em 0;
            padding:.2em 20px;
            background:#eee;
        }
        table, td{
            font:100% Arial, Helvetica, sans-serif;
        }
        table{width:100%;border-collapse:collapse;margin:1em 0;}
        th, td{text-align:left;padding:.5em;border:1px solid #fff;}
        th{background:#328aa4 url(tr_back.gif) repeat-x;color:#fff;}
        td{background:#e5f1f4;}

        /* tablecloth styles */

        tr.even td{background:#e5f1f4;}
        tr.odd td{background:#f8fbfc;}

        th.over, tr.even th.over, tr.odd th.over{background:#4a98af;}
        th.down, tr.even th.down, tr.odd th.down{background:#bce774;}
        th.selected, tr.even th.selected, tr.odd th.selected{}

        td.over, tr.even td.over, tr.odd td.over{background:#ecfbd4;}
        td.down, tr.even td.down, tr.odd td.down{background:#bce774;color:#fff;}
        td.selected, tr.even td.selected, tr.odd td.selected{background:#bce774;color:#555;}

        /* use this if you want to apply different styleing to empty table cells*/
        td.empty, tr.odd td.empty, tr.even td.empty{background:#fff;}
        </style>
        ';
    }

    public function getTable($errors = array())
    {
        if(count($errors) == 0) return;
        $out = '
        <table class="data">
          <tr>
            <th>Requirement</th>
            <th>Message</th>
          </tr>';
        foreach($errors as $name => $message) {
            $out .= '
              <tr>
                <td>'.$name.'</td>
                <td>'.$message.'</td>
              </tr>
            ';
        }
        $out .= '</table>';
        return $out;
    }

    public function getPage()
    {
        $out = '<html><head><title>SymfoPress checker</title></head><body>';
        $out .= $this->getCss();
        $out .= '<div id="container">
            <h1>SymfoPress server validation</h1>
            <div id="content">';
        $errors = $this->check();
        if(count($errors) == 0) {
            $out .= '<div style="color: green; font-weight: bold;">Your server is compatable with SymfoPress</div>Now you can to start to install it from WordPress Admin -> Settigns -> SymfoPress Lite';
        } else {
            $out .= '<div style="color: darkorange; font-weight: bold;">Your should fix som issues with your server</div>';
        }
        $out .= $this->getTable($errors);
        $out .= '</div></div></body></html>';
        return $out;
    }
}