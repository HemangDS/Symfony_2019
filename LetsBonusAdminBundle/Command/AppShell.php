<?php

    class AppShell
    {
        public function sendAdminAlert($alert, $subject, $containerEmailObject, $email = 'cristina.batarseh@shoppiday.com')
        {
            //TO-DO :: Commented to test command timebeing.
            /*echo "\n $subject\n";
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($email)
                ->setTo($email)
                ->setCharset('UTF-8')
                ->setContentType('text/html')
                ->setBody($alert);

            $res = $containerEmailObject->send($message);

            if ($res) {
                echo "\n Alert sent\n";
            }*/
        }
        public function sendTransactionAlert($body, $param, $subject, $containerEmailObject, $email = 'cristina.batarseh@shoppiday.com')
        {
            /*$message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($email)
                ->setTo($email)
                ->setCharset('UTF-8')
                ->setContentType('text/html')
                ->setBody($this->getContainer()->get('templating')->render($body, $param)); //close

            //ignore below comments

                /*->setBody($this->renderView(
                        $body, $param
                    ));*/

            //ignore upper comments

            /*$res = $containerEmailObject->send($message);

            if ($res) {
                echo "\n Alert sent\n";
            }*/
        }
    }
