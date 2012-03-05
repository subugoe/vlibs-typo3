<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Email service
 *
 * Contains quick-use emailing functions.
 *
 * @package Fed
 * @subpackage Service
 */
class Tx_Fed_Service_Email implements t3lib_Singleton {

	/**
	 * Send an email. Supports any to-string-convertible parameter types
	 *
	 * @param mixed $subject
	 * @param mixed $body
	 * @param mixed $recipientEmail
	 * @param mixed $recipientName
	 * @param mixed $fromEmail
	 * @param mixed $fromName
	 * @return integer the number of recipients who were accepted for delivery
	 * @api
	 */
	public function mail($subject, $body, $recipientEmail, $recipientName=NULL, $fromEmail=NULL, $fromName=NULL) {
		$mail = new t3lib_mail_Message();
		if ($recipientName == NULL) {
			$recipientName = $recipientEmail;
		}
		if ($fromEmail) {
			if ($fromName == NULL) {
				$fromName = $fromEmail;
			}
			$mail->setFrom(array($fromEmail => $fromName));
		}
		$mail->setTo(array($recipientEmail => $recipientName));
		$mail->setSubject($subject);
		$mail->setBody($body);
		return $mail->send();
	}

	/**
	 * Get a mailer (SwiftMailer) object instance
	 *
	 * @return t3lib_mail_Message;
	 * @api
	 */
	public function getMailer() {
		$mail = new t3lib_mail_Message();
		return $mail;
	}

}
?>
