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

/**
 * This interface must be implemented in order to send emails using the generic MailSender class and the instance
 * provided to the constructor of MailSender.
 *
 * @see phpList\plugin\Common\MailSender
 */
interface IMailClient
{
    /**
     * Builds the request body.
     *
     * @param $phplistmailer PHPlistMailer
     * @param $messageheader string
     * @param $messagebody   string
     *
     * @return string the body of the request
     */
    public function requestBody(\PHPlistMailer $phplistmailer, $messageheader, $messagebody);

    /**
     * Provides additional http headers to be added to the request.
     * For backwards compatibility the parameters are not included in the method signature.
     *
     * @param $messageheader string
     * @param $messagebody   string
     *
     * @return array headers
     */
    public function httpHeaders();

    /**
     * Provides the endpoint for the request.
     *
     * @return string
     */
    public function endpoint();

    /**
     * Verifies the response.
     *
     * @param $response string the response body
     *
     * @return bool whether the response indicates success
     */
    public function verifyResponse($response);
}
