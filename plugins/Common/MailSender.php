<?php
/**
 * CommonPlugin for phplist.
 *
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 *
 * @author    Duncan Cameron
 * @copyright 2011-2018 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

namespace phpList\plugin\Common;

use JMathai\PhpMultiCurl\MultiCurl;

/**
 * This class handles the sending of an email using either curl or multi-curl.
 */
class MailSender
{
    /** @var IMailClient client instance */
    private $client;
    /** @var array the outstanding multi-curl calls */
    private $calls = [];
    /** @var MultiCurl instance */
    private $mc = null;
    /** @var bool whether to use multi-curl */
    private $useMulti;
    /** @var int the maximum number of concurrent curl calls */
    private $multiLimit;
    /** @var bool whether to create a log of multi-curl usage */
    private $multiLog;
    /** @var bool whether to generate verbose curl output */
    private $curlVerbose;
    /** @var bool whether to validate the ssl certificate */
    private $verifyCert;
    /** @var int total of multi-curl calls that were successful */
    private $totalSuccess = 0;
    /** @var int total of multi-curl calls that failed */
    private $totalFailure = 0;
    /** @var phpList\plugin\Common\Logger */
    private $logger;

    /**
     * Constructor.
     */
    public function __construct(IMailClient $client, $useMulti, $multiLimit, $multiLog, $curlVerbose, $verifyCert)
    {
        $this->client = $client;
        $this->useMulti = $useMulti;
        $this->multiLimit = $multiLimit;
        $this->multiLog = $multiLog;
        $this->curlVerbose = $curlVerbose;
        $this->verifyCert = $verifyCert;
        $this->logger = Logger::instance();
    }

    /**
     * Complete any outstanding multi-curl calls.
     * Any emails sent after this point will use single send.
     */
    public function shutdown()
    {
        if ($this->mc !== null) {
            $this->completeCalls();
            $this->mc = null;
            $this->useMulti = false;
        }
    }

    /**
     * This method redirects to send single or multiple emails.
     *
     * @see
     *
     * @param PHPlistMailer $phplistmailer mailer instance
     * @param string        $messageheader the message http headers
     * @param string        $messagebody   the message body
     *
     * @return bool success/failure
     */
    public function send(\PHPlistMailer $phplistmailer, $messageheader, $messagebody)
    {
        try {
            return $this->useMulti
                ? $this->multiSend($phplistmailer, $messageheader, $messagebody)
                : $this->singleSend($phplistmailer, $messageheader, $messagebody);
        } catch (Exception $e) {
            logEvent($e->getMessage());

            return false;
        }
    }

    private function initialiseCurl()
    {
        global $tmpdir;

        if (($curl = curl_init()) === false) {
            throw new Exception('Unable to create curl handle');
        }
        curl_setopt($curl, CURLOPT_URL, $this->client->endpoint());
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->verifyCert);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, true);
        curl_setopt($curl, CURLOPT_USERAGENT, NAME . ' (phpList version ' . VERSION . ', http://www.phplist.com/)');
        curl_setopt($curl, CURLOPT_POST, true);

        if ($this->curlVerbose) {
            curl_setopt($curl, CURLOPT_VERBOSE, true);
            $log = fopen(sprintf('%s/curl_%s.log', $tmpdir, date('Y-m-d')), 'a+');
            curl_setopt($curl, CURLOPT_STDERR, $log);
        }

        return $curl;
    }

    /**
     * Waits for a call to complete.
     *
     * @param array $call
     */
    private function waitForCallToComplete(array $call)
    {
        $manager = $call['manager'];
        $httpCode = $manager->code;

        if ($httpCode == 200 && $this->client->verifyResponse($manager->response)) {
            ++$this->totalSuccess;
        } else {
            ++$this->totalFailure;
            logEvent(sprintf('Multi-curl http code %s result %s email %s', $httpCode, $manager->response, $call['email']));
        }
    }

    /**
     * Waits for each outstanding call to complete.
     * Writes the sequence of calls to a log file.
     * Writes to the event log except when only one email has been sent.
     */
    private function completeCalls()
    {
        global $tmpdir;

        while (count($this->calls) > 0) {
            $this->waitForCallToComplete(array_shift($this->calls));
        }

        if ($this->multiLog) {
            file_put_contents("$tmpdir/multicurl.log", $this->mc->getSequence()->renderAscii());
        }

        if (!($this->totalSuccess == 1 && $this->totalFailure == 0)) {
            logEvent(sprintf('Multi-curl successes: %d, failures: %d', $this->totalSuccess, $this->totalFailure));
        }
    }

    /**
     * Send an email using curl multi to send multiple emails concurrently.
     *
     * @param PHPlistMailer $phplistmailer mailer instance
     * @param string        $messageheader the message http headers
     * @param string        $messagebody   the message body
     *
     * @return bool success/failure
     */
    private function multiSend($phplistmailer, $messageheader, $messagebody)
    {
        if ($this->mc === null) {
            $this->mc = MultiCurl::getInstance();
            register_shutdown_function([$this, 'shutdown']);
        }

        /*
         * if the limit has been reached then wait for the oldest call
         * to complete
         */
        if (count($this->calls) == $this->multiLimit) {
            $this->waitForCallToComplete(array_shift($this->calls));
        }
        $curl = $this->initialiseCurl();
        $body = $this->client->requestBody($phplistmailer, $messageheader, $messagebody);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->client->httpHeaders($messageheader, $body));

        $this->calls[] = [
            'manager' => $this->mc->addCurl($curl),
            'email' => $phplistmailer->destinationemail,
        ];

        return true;
    }

    /**
     * This method uses curl directly with an optimisation of re-using
     * the curl handle.
     *
     * @param PHPlistMailer $phplistmailer mailer instance
     * @param string        $messageheader the message http headers
     * @param string        $messagebody   the message body
     *
     * @return bool success/failure
     */
    private function singleSend($phplistmailer, $messageheader, $messagebody)
    {
        static $curl = null;

        if ($curl === null) {
            $curl = $this->initialiseCurl();
        }
        $body = $this->client->requestBody($phplistmailer, $messageheader, $messagebody);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->client->httpHeaders($messageheader, $body));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false || preg_match('/^2\d\d$/', $httpCode) !== 1 || !$this->client->verifyResponse($response)) {
            $error = curl_error($curl);
            logEvent(sprintf('MailSender http code: %s, result: %s, curl error: %s', $httpCode, strip_tags($response), $error));
            curl_close($curl);
            $curl = null;

            return false;
        }

        return true;
    }
}
