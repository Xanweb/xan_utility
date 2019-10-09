<?php
namespace XanUtility\Logging\Handler;

use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Facade;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\MailHandler;
use Monolog\Logger;
use Config;

class SendExceptionMailHandler extends MailHandler
{
    const MAX_EMAILS_PER_HOUR = 10;

    /**
     * Email Address where to send Exceptions.
     *
     * @var string
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
        if (!$this->canSend()) {
            return;
        }

        $app = Facade::getFacadeApplication();
        $u = $app->make(User::class);
        $user = t('User: %s', $u->isRegistered() ? $u->getUserName() : t('Guest'));

        $refererURL = t('Referer URL: %s', $_SERVER['HTTP_REFERER']);
        $url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $mh = $app->make('mail');
        $mh->setTesting(true);
        $mh->setSubject($_SERVER['SERVER_NAME'] . ': Exception occurred');
        $mh->setBodyHTML($user . '<br>' . $url . '<br>' . $refererURL . '<br>' . $content);
        $mh->to($this->reportEmail);
        try {
            $mh->sendMail();
        } catch (\Exception $e) {
        }
    }

    /**
     * Check if limit is reached per hour.
     *
     * @return bool
     */
    protected function canSend()
    {
        $hourStamp = (int) Config::get('concrete.xanweb.email_logging.hour_stamp', 0);
        $diff = time() - $hourStamp;
        if ($diff > 3600) {
            Config::save('concrete.xanweb.email_logging.hour_stamp', time());
            Config::save('concrete.xanweb.email_logging.count', 1);

            return true;
        } else {
            $sentLogCount = Config::get('concrete.xanweb.email_logging.count', 0);
            if ($sentLogCount < static::MAX_EMAILS_PER_HOUR) {
                Config::save('concrete.xanweb.email_logging.count', ++$sentLogCount);

                return true;
            }
        }

        return false;
    }
}
