<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getContactAction()
    {
        return $this->render('iFlairLetsBonusFrontBundle:Contact:Contactus.html.twig', []);
    }

    public function getContactEnviadoAction(Request $request)
    {
        $session = $request->getSession();
        $result = $session->get('contactus_result');

        $session->remove('contactus_result');

        if ($result === 'success') {
            return $this->render('iFlairLetsBonusFrontBundle:Contact:ContactusEnviado.html.twig', []);
        } else {
            return $this->redirectToRoute('i_flair_lets_bonus_front_contact');
        }
    }

    public function sendContactAction(Request $request)
    {

        $keys = $request->request->keys();
        $arr = [];

        foreach ($keys as $key) {
            if (preg_match('/^z_/i', $key)) {
                $value = $request->get($key);
                $arr[strip_tags($key)] = strip_tags($value);
            }
        }
        $userArray = [
            'z_name' => $arr['z_28882389'],
            'z_email' => $arr['z_29025325'],
            'z_role' => 'end-user',
            'z_verified' => 'yes',
        ];
        $user = json_encode(
            [
                'user' => [
                    'name' => $userArray['z_name'],
                    'email' => $userArray['z_email'],
                    'role' => $userArray['z_role'],
                ],
            ],
            JSON_FORCE_OBJECT
        );
        $data = $this->curlWrap('/users.json', $user, 'POST');

        if (($data['result']['http_code'] === 201 || $data['result']['http_code'] === 200) && !empty($data['response']->user->id)) {
            $ticketContents = [
                'subject' => $arr['z_29024865'],
                'comment' => [
                    'value' => $arr['z_28882549'],
                    'requester' => [
                        'name' => $arr['z_28882389'],
                        'email' => $arr['z_29025325'],
                    ],
                ],
                'requester_id' => $data['response']->user->id,
                'custom_fields' => [
                    '29024005' => $arr['z_29024005'],
                    '29024865' => $arr['z_29024865'],// Reason
                    '29024885' => $arr['z_29024885'],
                    '28882389' => $arr['z_28882389'],// Name
                    '29025325' => $arr['z_29025325'],// Email
                    '29025345' => $arr['z_29025345'],
                    '28882549' => $arr['z_28882549'] // Description
                ],
            ];

        } elseif ($data['response']->details->email[0]->error === 'DuplicateValue') {

            $userData = $this->searchUser('/users/search.json?query=type:user+email:'.$arr['z_29025325'].'');
            $ticketContents = [
                'subject' => $arr['z_29024865'],
                'comment' => [
                    'value' => $arr['z_28882549'],
                    'requester' => [
                        'name' => $arr['z_28882389'],
                        'email' => $arr['z_29025325'],
                    ],
                ],
                'requester_id' => $userData['response']->users[0]->id,
                'custom_fields' => [
                    '29024005' => $arr['z_29024005'],
                    '29024865' => $arr['z_29024865'],// Reason
                    '29024885' => $arr['z_29024885'],
                    '28882389' => $arr['z_28882389'],// Name
                    '29025325' => $arr['z_29025325'],// Email
                    '29025345' => $arr['z_29025345'],
                    '28882549' => $arr['z_28882549'] // Description
                ],
            ];

        }

        if (!empty($arr)) {
            $ticket = json_encode(
                ['ticket' => $ticketContents]
            );


            $data = $this->curlWrap('/tickets.json', $ticket);
        }

        if (isset($data['result']['http_code'])) {
            if ($data['result']['http_code'] === 201 || $data['result']['http_code'] === 200) {
                $session = $request->getSession();
                $session->start();
                $session->set('contactus_result', 'success');

                return $this->redirectToRoute('i_flair_lets_bonus_front_contact_enviado', []);
            }
        }

        return $this->redirectToRoute('i_flair_lets_bonus_front_contact');
    }

    public function curlWrap($url, $json)
    {
        $zdapikey = $this->container->getParameter('zdapikey');
        $zduser = $this->container->getParameter('zduser');
        $zdurl = $this->container->getParameter('zdurl');
        $data = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_URL, $zdurl.$url);
        curl_setopt($ch, CURLOPT_USERPWD, $zduser.'/token:'.$zdapikey);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MozillaXYZ/1.0');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        $result = curl_getinfo($ch);
        curl_close($ch);
        $response = json_decode($output);
        $data['result'] = $result;
        $data['response'] = $response;

        return $data;
    }

    public function searchUser($url)
    {
        $zdapikey = $this->container->getParameter('zdapikey');
        $zduser = $this->container->getParameter('zduser');
        $zdurl = $this->container->getParameter('zdurl');
        $data = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_URL, $zdurl.$url);
        curl_setopt($ch, CURLOPT_USERPWD, $zduser.'/token:'.$zdapikey);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MozillaXYZ/1.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        $result = curl_getinfo($ch);
        curl_close($ch);
        $response = json_decode($output);
        $data['result'] = $result;
        $data['response'] = $response;

        return $data;
    }
}
