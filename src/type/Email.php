<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 16-03-18
 * Time: 13:56
 */

namespace edwrodrig\contento\type;


class Email
{
    private $mail;

    /**
     * Email constructor.
     * @param string $mail
     * @throws exception\InvalidMailException
     */
    public function __construct(string $mail)
    {
        if ( $mail = filter_var($mail, FILTER_VALIDATE_EMAIL) === FALSE )
            throw new exception\InvalidMailException($mail);

        $this->mail = $mail;

    }

    public function get_domain() : string {
        return explode('@', $this->mail)[1];
    }

    public function __toString() : string {
        return $this->mail;
    }
}