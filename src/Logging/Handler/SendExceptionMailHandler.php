<?php
namespace XanUtility\Logging\Handler;

use Concrete\Core\Support\Facade\Facade;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\MailHandler;
use Monolog\Logger;
use User;

class SendExceptionMailHandler extends MailHandler
{
    /**
     * Email Address where to send Exceptions.
     *
     * @var String
     */
    protected $reportEmail;

    /**
     * SendExceptionMailHandler constructor.
     *
     * @param string $reportMailAdr Email Address where to send Exceptions
     */
    public function __construct($reportMailAdr)
    {
        parent::__construct(Logger::EMERGENCY, true);
        $this->setFormatter(new HtmlFormatter());
        $this->reportEmail = $reportMailAdr;
    }

    protected function send($content, array $records)
    {
        $app = Facade::getFacadeApplication();

        $u = $app->make(User::class);
        $user = t('User: %s', $u->isRegistered() ? $u->getUserName() : t('Guest'));

        $url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $mh = $app->make('mail');
        $mh->setTesting(true);
        $mh->setSubject($_SERVER['SERVER_NAME'] . ': Exception occurred');
        $mh->setBodyHTML($user . '<br>' . $url . '<br>' . $content);
        $mh->to($this->reportEmail);
        try {
            $mh->sendMail();
        } catch (\Exception $e) {
        }
    }
}
