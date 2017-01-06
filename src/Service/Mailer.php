<?php

namespace Service;

use \PHPMailer;

class Mailer {
    public static $server = 'smtp';

    protected static $mailer = false;

    public static function getInstance() {
        if(self::$mailer === false) {
            self::$mailer = new PHPMailer();
        }

        return self::$mailer;
    }

    public static function send($to = [], $subject = '', $message = '', $attach = []) {
        $config = autoload_config();

        if($charset = $config['mailer']['charset']) {
            self::getInstance()->CharSet = $charset;
        }

        if(self::$server === 'smtp') {
            self::getInstance()->isSMTP();
            self::getInstance()->SMTPSecure  = $config['mailer'][self::$server]['secure'];
            self::getInstance()->SMTPDebug   = $config['mailer'][self::$server]['debug'];
            self::getInstance()->SMTPAuth    = $config['mailer'][self::$server]['auth'];
            self::getInstance()->SMTPOptions = $config['mailer']['options'];
        }

        self::getInstance()->Host     = $config['mailer'][self::$server]['host'];
        self::getInstance()->Port     = $config['mailer'][self::$server]['port'];
        self::getInstance()->Username = $config['mailer'][self::$server]['username'];
        self::getInstance()->Password = $config['mailer'][self::$server]['password'];

        self::getInstance()->SetFrom($config['mailer'][self::$server]['email'], $config['mailer'][self::$server]['name']);
        self::getInstance()->AddReplyTo($config['mailer'][self::$server]['email'], $config['mailer'][self::$server]['name']);
        self::getInstance()->isHTML(true);

        self::getInstance()->Subject = $subject;

        self::getInstance()->Body = $message;
        self::getInstance()->AltBody = $config['mailer'][Session::get('s_locale')]['app']['mailer.altbody'] . '\r\n\n\n' . $message;


        foreach($to as $address) {
            self::getInstance()->AddAddress($address);
        }

        foreach($attach as $file) {
            self::getInstance()->AddAttachment($file);
        }

        if(!self::getInstance()->Send()) {
            return self::getInstance()->ErrorInfo;
        }

        return false;
    }
}
