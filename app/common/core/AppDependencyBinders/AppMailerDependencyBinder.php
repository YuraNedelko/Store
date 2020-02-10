<?php


namespace app\common\core\AppDependencyBinders;


use app\common\core\App;
use PHPMailer\PHPMailer\PHPMailer;

class AppMailerDependencyBinder
{
    /**
     * Register bindings in the container.
     * @param App $app
     * @return void
     * @throws \Exception
     */
    public function register(App $app)
    {
        $app->instance('Mail',
            function () use ($app) {

                $mail = new PHPMailer(true);
                $config = $app->getConfig()->mail;
                $mail->isSMTP();
                $mail->Host = $config['SMTP'];
                $mail->SMTPAuth = true;
                $mail->Username = $config['username'];
                $mail->Password = $config['password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $config['port'];

                return $mail;
            });
    }
}