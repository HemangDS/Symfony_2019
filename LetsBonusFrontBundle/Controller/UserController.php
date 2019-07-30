<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Application\Sonata\MediaBundle\Entity\Media;
use iFlair\LetsBonusAdminBundle\Entity\AddtoFev;
use iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpSegmentListNewsletter;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpSubscription;
use iFlair\LetsBonusAdminBundle\Entity\MailchimpUserListStatus;
use iFlair\LetsBonusAdminBundle\Entity\UserPaymentDetail;
use iFlair\LetsBonusFrontBundle\Form\LoginType;
use iFlair\LetsBonusFrontBundle\Form\RegistrationType;
use iFlair\LetsBonusFrontBundle\Form\UserEditType;
use iFlair\LetsBonusFrontBundle\Form\UserPaymentDetailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Csrf\CsrfToken;
use ZfrMailChimp\Client\MailChimpClient;

class UserController extends Controller
{
    public function viewUserAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);

        if (false !== $userId) {
            return $this->render('iFlairLetsBonusFrontBundle:User:user.html.twig');
        }

        return $this->redirectToRoute('i_flair_lets_bonus_front_login');
    }

    public function getUserHistoryAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $where = ' AND ct.status = :requestedStatus';
            switch ($request->get('requestedStatus')) {
                case cashbackTransactions::STATUS_TYPE_CONFIRMED:
                case cashbackTransactions::STATUS_TYPE_APPROVED:
                    $requestedStatus = cashbackTransactions::STATUS_TYPE_CONFIRMED;
                    $where .= ' AND ct.amount >= 0';
                    break;
                case cashbackTransactions::STATUS_TYPE_PAYED:
                    $requestedStatus = cashbackTransactions::STATUS_TYPE_PAYED;
                    $where = 'AND ct.status = :requestedStatus AND (ct.amount > 0 OR ct.type="withdrawal")';
                    break;
                case cashbackTransactions::STATUS_TYPE_DENIED:
                case cashbackTransactions::STATUS_TYPE_CANCELLED:
                    $requestedStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $where = 'AND (ct.status = :requestedStatus OR ct.amount < 0) AND ct.type!="withdrawal"';
                    break;
                case cashbackTransactions::STATUS_TYPE_PENDING:
                    $requestedStatus = cashbackTransactions::STATUS_TYPE_PENDING;
                    break;
                default:
                    $requestedStatus = 'ALL';
                    $where = '';
                    break;
            }
            $em = $this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $statement = $connection->prepare(
                'SELECT ct.id AS id, ct.amount AS amountCashback, ct.total_affiliate_amount AS amount,
                ct.created, ct.status, ct.aproval_date, sh.cashbackPercentage, ct.type AS transactionType, 
                vp.program_name, cs.type, ct.sepagenerated_date, ct.parent_transaction_id
                FROM lb_cashback_transactions AS ct
                LEFT JOIN lb_shop_history AS sh ON sh.id = ct.shop_history_id
                LEFT JOIN lb_cashbackSettings AS cs ON cs.id = ct.cashbacksetting_id
                LEFT JOIN lb_shop AS s ON s.id = ct.shop_id
                LEFT JOIN lb_voucher_programs AS vp ON vp.id = s.vprogram_id
                WHERE ct.user_id=:userId
                '.$where.'
                GROUP BY ct.id ORDER BY ct.created DESC, ct.parent_transaction_id'
            );
            $statement->bindValue('userId', $userId);
            if ($where !== '') {
                $statement->bindValue('requestedStatus', $requestedStatus);
            }
            $statement->execute();
            $userHistoryData = $statement->fetchAll();
            $historyData = [];
            foreach ($userHistoryData as $data) {
                if (null === $data['parent_transaction_id']) {
                    $cashback = ($data['amount'] > 0) ? round(
                        $data['amountCashback'] * 100 / $data['amount'],
                        2
                    ) : $data['cashbackPercentage'];
                    switch (strtolower($data['type'])) {
                        case 'triple':
                            $multipicator = 3;
                            $promotionName = 'Triple Cashback';
                            break;
                        case 'double':
                            $multipicator = 2;
                            $promotionName = 'Doble Cashback';
                            break;
                        default:
                            $multipicator = 1;
                            $promotionName = '-';
                            break;
                    }
                    $totalCashback = round($cashback * $multipicator, 2);
                    if ($data['transactionType'] === 'withdrawal') {
                        $status = $data['sepagenerated_date'] ? 'Pagado el '.date(
                                'd/m/Y',
                                strtotime($data['sepagenerated_date'])
                            ) : 'En proceso';
                        $data['program_name'] = 'Transferido';
                    } else {
                        switch ($data['status']) {
                            case cashbackTransactions::STATUS_TYPE_PAYED:
                                $status = 'Pagado el '.date('d/m/Y', strtotime($data['aproval_date']));
                                break;
                            case cashbackTransactions::STATUS_TYPE_PENDING:
                                $status = 'Pendiente';
                                break;
                            case cashbackTransactions::STATUS_TYPE_CONFIRMED:
                                $status = ($data['amount'] >= 0) ? 'Confirmado' : 'Cancelado';
                                $data['status'] = ($data['amount'] >= 0) ?: cashbackTransactions::STATUS_TYPE_DENIED;
                                break;
                            case cashbackTransactions::STATUS_TYPE_CANCELLED:
                            case cashbackTransactions::STATUS_TYPE_DENIED:
                                $status = 'Cancelado';
                                break;
                            default:
                                $status = '';
                                break;
                        }
                    }
                    $historyData[$data['id']] = [
                        'brand' => $data['program_name'],
                        'date' => date('d/m/y', strtotime($data['created'])),
                        'amount' => $data['amount'],
                        'cashback' => $cashback,
                        'promotion' => $promotionName,
                        'totalCashback' => $totalCashback,
                        'amountCashback' => round($data['amountCashback'] * $multipicator, 2),
                        'statusTxt' => $status,
                        'status' => $data['status'],
                    ];
                }
            }

            $resp['html'] = $this->render(
                'iFlairLetsBonusFrontBundle:User:userHistory.html.twig',
                [
                    'userData' => $historyData,
                    'requestedStatus' => $requestedStatus,
                ]
            )->getContent();
        } else {
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return new Response(json_encode($resp));
    }

    /**
     * @param $userId
     *
     * @return array
     * @throws \LogicException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getCalculatedUserAmounts($userId)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare(
            'SELECT SUM(ct.amount) as amount ,ct.type,ct.status 
             FROM lb_cashback_transactions AS ct WHERE ct.user_id=:userId
             GROUP BY ct.type,ct.status'
        );
        $statement->bindValue('userId', $userId);
        $statement->execute();
        $userCashbackData =  $statement->fetchAll();

        $calculatedUserAmounts = [
            'cashbackFinalAvailable' => 0,
            'cashbackFinalOutstanding' => 0,
            'cashbackFinalTransferred' => 0,
        ];
        foreach ($userCashbackData as $userCaschback) {
            if ($userCaschback['status'] === cashbackTransactions::STATUS_TYPE_PAYED && $userCaschback['type'] === 'withdrawal') {
                $calculatedUserAmounts['cashbackFinalTransferred'] += abs($userCaschback['amount']);
            } elseif ($userCaschback['status'] === cashbackTransactions::STATUS_TYPE_CONFIRMED) {
                $calculatedUserAmounts['cashbackFinalAvailable'] += $userCaschback['amount'];
            } elseif ($userCaschback['status'] === cashbackTransactions::STATUS_TYPE_PENDING) {
                $calculatedUserAmounts['cashbackFinalOutstanding'] += $userCaschback['amount'];
            }
        }

        return $calculatedUserAmounts;
    }

    public function getUserCashbackAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);

        if (!empty($userId)) {
            $calculatedUserAmounts = $this->getCalculatedUserAmounts($userId);
            $resp['html'] = $this->render('iFlairLetsBonusFrontBundle:User:userCashback.html.twig', array(
                            'cashbackFinalTransferred' => $calculatedUserAmounts['cashbackFinalTransferred'],
                            'cashbackFinalAvailable' => $calculatedUserAmounts['cashbackFinalAvailable'],
                            'cashbackFinalOutstanding' => $calculatedUserAmounts['cashbackFinalOutstanding']
                        ))->getContent();
        } else {
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return new Response(json_encode($resp));
    }

    public function checkTransferEligibilityAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);
        $calculatedUserAmounts = $this->getCalculatedUserAmounts($userId);
        $minimumTransferAmount = $this->container->getParameter('minimo_transfer_dinero');

        if (!empty($userId)) {
            if($calculatedUserAmounts['cashbackFinalAvailable'] > $minimumTransferAmount) {
                $resp['status'] = 1;
                $resp['transferUrl'] = $this->generateUrl('i_flair_lets_bonus_front_user_getamounttransferform');
            } else {
                $resp['status'] = 0;
            }
        } else {
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return new Response(json_encode($resp));
    }

    public function processAmountTransferAction(Request $request)
    {
        $resp = [];
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $em = $this->getDoctrine()->getManager();
            $saveBankDetail = $request->request->get('save_bank_detail', 0);
            $accountnumber = $request->request->get('accountnumber', '');
            $swiftcodebic = $request->request->get('swiftcodebic', '');
            $ownername = $request->request->get('ownername', '');

            $user = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(['id' => $userId]);
            if ($saveBankDetail) {
                $userPayment = $this->getDoctrine()->getRepository(
                    'iFlairLetsBonusAdminBundle:UserPaymentDetail'
                )->findOneBy(['userid' => $userId]);
                if (null === $userPayment) {
                    $userPayment = new UserPaymentDetail();
                    $userPayment->setUserid($user);
                }
                $userPayment->setOwnername($ownername);
                $userPayment->setAccountnumber($accountnumber);
                $userPayment->setSwiftcodebic($swiftcodebic);
                $em->persist($userPayment);
                $em->flush();
                $this->sendEmailPaymentDataUpdate($user);
            }

            $cashbackTransactions = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findBy(
                [
                    'userId' => $userId,
                    'status' => cashbackTransactions::STATUS_TYPE_CONFIRMED,
                ]
            );
            $amount = 0;
            $child = [];
            foreach ($cashbackTransactions as $cashbackTransaction) {
                $child[] = $cashbackTransaction->getId();
                $amount += $cashbackTransaction->getAmount();
                $cashbackTransaction->setStatus(cashbackTransactions::STATUS_TYPE_PAYED);
                $em->persist($cashbackTransaction);
            }
            $currency = $em->getRepository('iFlairLetsBonusAdminBundle:Currency')->find(1);
            $withDrawTransaction = new Cashbacktransactions();
            $withDrawTransaction->setUserId($userId);
            $withDrawTransaction->setCurrency($currency);
            $withDrawTransaction->setAmount(-1 * $amount);
            $withDrawTransaction->setType(cashbackTransactions::TRANSACTION_TYPE_WITHDRAWAL);
            $withDrawTransaction->setStatus(cashbackTransactions::STATUS_TYPE_PENDING);
            $withDrawTransaction->setDate(new \DateTime());
            $withDrawTransaction->setCreated(new \DateTime());
            $withDrawTransaction->setModified(new \DateTime());
            $withDrawTransaction->setUserName($ownername);
            $withDrawTransaction->setUserBankAccountNumber($accountnumber);
            $withDrawTransaction->setBic($swiftcodebic);
            $withDrawTransaction->setCashbacktransactionsChilds(serialize($child));
            $em->persist($withDrawTransaction);
            $em->flush();
            $this->withdrawalEmail($withDrawTransaction);
            $resp['success'] = 1;
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_user_cashback');
        }

        return new Response(json_encode($resp));

    }

    public function getAmountTransferFormAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $calculatedUserAmounts = $this->getCalculatedUserAmounts($userId);
            $paymentform = $this->getUserPaymentForm($userId);

            $resp['html'] = $this->render('iFlairLetsBonusFrontBundle:User:userAmountTransferForm.html.twig', array(
                                                'user_payment' => $paymentform->createView(),
                                                'amount' => $calculatedUserAmounts['cashbackFinalAvailable']
                                            ))->getContent();
        } else {
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return new Response(json_encode($resp));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \LogicException
     */
    public function getUserProfileAction(Request $request)
    {
        $response = [];
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $this->setUserAreaPath($request);
            /** @var FrontUser $user */
            $user = $request->getSession()->get('user');
            $userImageId = $request->getSession()->get('userimage');
            $userPayment = $this->getDoctrine()->getRepository(
                'iFlairLetsBonusAdminBundle:UserPaymentDetail'
            )->findOneBy(['userid' => $userId]);
            if (null === $userPayment) {
                $userPayment = new UserPaymentDetail();
            }
            $imagePath = $this->getImagePath($userImageId, $user->getUserGender());
            $response['html'] = $this->render(
                'iFlairLetsBonusFrontBundle:User:userProfile.html.twig',
                ['user' => $user, 'userPayment' => $userPayment,'image_path' => $imagePath]
            )->getContent();
            $response['imagepath'] = $imagePath;
        }else{
            $response['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');

        }
        return new Response(json_encode($response));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getUserImageAction(Request $request)
    {
        $imagePath = '';
        if ($this->isUserLoggedIn($request)) {
            /** @var FrontUser $user */
            $user = $request->getSession()->get('user');
            $userImageId = $request->getSession()->get('userimage');
            $imagePath = $this->getImagePath($userImageId, $user->getUserGender());
        }

        return $this->render(
            'iFlairLetsBonusFrontBundle:User:userProfileImageTag.html.twig',
            [
                'image_path' => $imagePath,
            ]
        );
    }
    public function getUserFavouriteAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $connection = $this->get('doctrine.dbal.default_connection');
            $statement = $connection->prepare(
                'SELECT atf.id as fav_id, s.vprogram_id, tgs.name as tagname, s.offers,
                v.exclusive as v_exclusive, v.isnew, s.exclusive, vp.program_name, s.cashbackPercentage
                , COUNT(v.id) as numCupons, vp.image_id
                FROM lb_add_to_fev AS atf
                INNER JOIN lb_shop AS s ON s.id = atf.shop_id
                INNER JOIN lb_shop_history AS sh ON sh.id = atf.shop_history_id
                LEFT JOIN lb_voucher_programs AS vp ON vp.id = s.vprogram_id
                LEFT JOIN lb_tags AS tgs ON sh.tag = tgs.id
                LEFT JOIN lb_shop_voucher as sv ON sv.shop_id = s.id
                LEFT JOIN lb_voucher as v ON v.id = sv.voucher_id
                WHERE atf.user_id=:userId 
                GROUP BY atf.id');
            $statement->bindValue('userId', $userId);
            $statement->execute();
            $userFavoriteData = $statement->fetchAll();
            $finalArray = [];
            $tmpImages = [];
            foreach ($userFavoriteData as $userFavoriteDataKey => $userFavoriteDataValue) {
                $userFavoriteDataValue['scheme'] = '';
                $userFavoriteDataValue['offer_label'] = '';
                if (!empty($userFavoriteDataValue['offers'])) {
                    if ($userFavoriteDataValue['offers'] === 'cashback') {
                        $userFavoriteDataValue['scheme'] = $userFavoriteDataValue['cashbackPercentage'].'% Cashback';
                        $userFavoriteDataValue['offer_label'] = $userFavoriteDataValue['tagname'];
                    } elseif ($userFavoriteDataValue['offers'] === 'voucher') {
                        if ($userFavoriteDataValue['v_exclusive']) {
                            $userFavoriteDataValue['offer_label'] = $userFavoriteDataValue['isnew'] ? ' * Novedad exclusiva' : '* Exclusivo';
                        } elseif (!$userFavoriteDataValue['v_exclusive'] && $userFavoriteDataValue['isnew']) {
                            $userFavoriteDataValue['offer_label'] = '* Nuevo';
                        }
                        $userFavoriteDataValue['scheme'] = $userFavoriteDataValue['numCupons'].' Cupones y Ofertas';
                    }
                }
                if (!array_key_exists($userFavoriteDataValue['vprogram_id'], $tmpImages)) {
                    $media = $this->get('doctrine.orm.default_entity_manager')
                        ->getRepository('ApplicationSonataMediaBundle:Media')
                        ->find($userFavoriteDataValue['image_id']);
                    if (null !== $media) {
                        $mediaManager = $this->get('sonata.media.pool');
                        $provider = $mediaManager->getProvider($media->getProviderName());
                        $format = $provider->getFormatName($media, 'default_big');
                        $tmpImages[$userFavoriteDataValue['vprogram_id']] = $provider->generatePublicUrl(
                            $media,
                            $format
                        );
                    }
                }
                if($tmpImages[$userFavoriteDataValue['vprogram_id']]!== '') {
                    $userFavoriteDataValue['logo_path'] = $tmpImages[$userFavoriteDataValue['vprogram_id']];
                }
                $finalArray[] = $userFavoriteDataValue;
            }

            $resp['html'] = $this->render(
                'iFlairLetsBonusFrontBundle:User:userFavourite.html.twig',
                ['userFavoriteData' => $finalArray]
            )->getContent();
        } else {
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return new Response(json_encode($resp));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function removeUserFavouriteAction(Request $request)
    {
        $favId = (int) $request->get('id');
        $userId = $this->isUserLoggedIn($request);
        if ($favId > 0) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            /** @var AddtoFev $userFav */
            $userFav = $em->getRepository('iFlairLetsBonusAdminBundle:AddtoFev')->findOneById($favId);
            if ($userId === $userFav->getUserId()) {
                $em->remove($userFav);
                $em->flush();
            }
        }

        return new Response('[]');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \LogicException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \InvalidArgumentException
     */
    public function getUserCommentsAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $em = $this->getDoctrine()->getEntityManager();
            $connection = $em->getConnection();
            $statement = $connection->prepare(
                'SELECT r.*,vp.*,r.created,r.id, fu.name, fu.surname FROM lb_review AS r
                JOIN lb_voucher_programs AS vp ON vp.id = r.brand_id
                JOIN lb_front_user AS fu ON fu.id = r.user_id
                WHERE r.user_id=:userId'
            );
            $statement->bindValue('userId', $userId, \PDO::PARAM_INT);
            $statement->execute();
            $userCommentData = $statement->fetchAll();

            $resp['html'] = $this->render(
                'iFlairLetsBonusFrontBundle:User:userComments.html.twig',
                ['userCommentData' => $userCommentData]
            )->getContent();
        } else {
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return new Response(json_encode($resp));
    }

    /**
     * @param $userid
     *
     * @return string|\Symfony\Component\Form\FormInterface
     * @throws \LogicException
     */
    protected function getUserPaymentForm($userid)
    {
        $userPaymentDetails = '';
        if (!empty($userid)) {
            $userPayment = $this->getDoctrine()
                ->getRepository('iFlairLetsBonusAdminBundle:UserPaymentDetail')->findOneBy(['userid' => $userid]);

            $userPaymentDetails = $this->createFormBuilder($userPayment)
                ->add(
                    'ownername',
                    'text',
                    [
                        'attr' => [
                            'class' => 'form-control',
                            'required' => false,
                        ],
                    ]
                )
                ->add(
                    'accountnumber',
                    'text',
                    [
                        'attr' => [
                            'class' => 'form-control',
                            'required' => false,
                        ],
                    ]
                )
                ->add(
                    'swiftcodebic',
                    'text',
                    [
                        'attr' => [
                            'class' => 'form-control',
                            'required' => false,
                        ],
                    ]
                )
                ->getForm();
        }

        return $userPaymentDetails;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function getProfileEditFormAction(Request $request)
    {
        switch ($request->get('targetSection')) {
            case 'user-newsletter-edit':
                return $this->getNewsletterEditAction($request);
            case 'user-insurance-edit';
                return $this->getInsuranceEditAction();
            case 'user-payment-edit':
                return $this->getPaymentsEditAction($request);
            case 'user-data-edit':
                $resp = [];
                $targetSection = $request->get('targetSection');
                $userId = $this->isUserLoggedIn($request);
                if (false !== $userId) {
                    $user = $request->getSession()->get('user');
                    $userImageId = $request->getSession()->get('userimage');
                    $form = $this->createForm(new UserEditType(), $user);

                    $resp['html'] = $this->render(
                        'iFlairLetsBonusFrontBundle:User:userEdit.html.twig',
                        [
                            'form' => $form->createView(),
                            'image_path' => $this->getImagePath($userImageId , $user->getUserGender()),
                            'userData' => $user,
                            'target_section' => $targetSection,
                        ]
                    )->getContent();
                }
                return new Response(json_encode($resp));
            default:
                break;
        }

        return $this->redirectToRoute('i_flair_lets_bonus_front_login');
    }

    /**
     * @todo remove this behaivor, do preview in client side
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function uploadImageAction()
    {
        $request = Request::createFromGlobals();
        $resp = [];
        foreach ($request->files as $uploadedFile) {
            $fileData = file_get_contents($uploadedFile->openFile()->getPathName());
        }

        $enocoded_data = base64_encode($fileData);
        $resp['filename'] = 'data:image/png;base64, '.$enocoded_data;

        return new Response(json_encode($resp));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \LogicException
     */
    public function updateUserDataAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);
        $data = $request->request->all();
        $response = ['success' => 0];
        if (!empty($data) && false !== $userId) {
            /** @var FrontUser $user */
            $csrf = $this->get('security.csrf.token_manager');
            $token = new CsrfToken('user-data-edit-form', $request->get('_csrf_token'));
            if ($csrf->isTokenValid($token)) {
                $repository = $this->get('doctrine.orm.default_entity_manager')->getRepository('iFlairLetsBonusAdminBundle:FrontUser');
                $user = $repository->find($userId);
                $encoder = new MessageDigestPasswordEncoder();
                if (array_key_exists('password', $data)
                    && $data['password']['first'] !== '' && $data['password']['first'] === $data['password']['second']
                    && $encoder->isPasswordValid($user->getPassword(), $data['password']['old'], $user->getSalt())
                ) {
                    $password = $encoder->encodePassword($data['password']['first'], $user->getSalt());
                    $user->setPassword($password);
                }
                $user->setName($data['name']);
                $user->setSurname($data['surname']);
                $user->setAlias($data['alias']);
                $userbirthdate = $data['userBirthDate']['year'].'-'.$data['userBirthDate']['month'].'-'.$data['userBirthDate']['day'];
                $user->setUserBirthDate(new \DateTime($userbirthdate));
                $user->setUserGender($data['userGender']);
                $user->setCity($data['city']);
                $user->setModified(new \Datetime());
                $mediaManager = $this->get('sonata.media.manager.media');
                foreach ($request->files as $uploadedFile) {
                    if (!empty($uploadedFile)) {
                        $media = new Media();
                        $media->setBinaryContent($uploadedFile->getPathname());
                        $media->setContext('default');
                        $media->setProviderName('sonata.media.provider.image');
                        $media->setContext('user');
                        $mediaManager->save($media);
                        $user->setImage($media);
                    }
                }
                $repository->save($user);
                $request->getSession()->set('user',$user);
                $response['success'] = 1;
            }
        }

        return new Response(json_encode($response));
    }

    public function updateUserInsuranceAction(Request $request)
    {
        $resp['success'] = 1;

        return new Response(json_encode($resp));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function updateUserPaymentAction(Request $request)
    {
        $response = ['success' => 0];
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $repository = $this->get('doctrine.orm.default_entity_manager')->getRepository('iFlairLetsBonusAdminBundle:FrontUser');
            $user = $repository->find($userId);
            $userPayment = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:UserPaymentDetail')->findOneBy(['userid' => $userId]);
            if (null === $userPayment) {
                $userPayment = new UserPaymentDetail();
                $userPayment->setUserid($user);
            }
            $form = $this->createForm(new UserPaymentDetailType(), $userPayment);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userPayment);
                $em->flush();
                $this->sendEmailPaymentDataUpdate($user);
                $response['success'] = 1;
            }
        }

        return new Response(json_encode($response));
    }

    public function updateUserSubscriptionAction(Request $request)
    {
        $resp = array();
        $em = $this->getDoctrine()->getEntityManager();
        $request_data = $request->request->all();
        $session = $request->getSession();
        $useremail = $session->get('user_email');
        $apikey = $this->container->getParameter('mailchimp_api');
        $client = new MailChimpClient($apikey);
        $NewsListId = array();
        $i =0;
        //$isRegistered = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('email' =>  $request_data['subscriptionemail']));
        $isRegistered = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('email' =>  $useremail));
        if($isRegistered){$regStatus = "yes";} else{$regStatus = "no";}

          $userId = $this->isUserLoggedIn($request);
        if (!empty($userId)) {
                $user = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => $userId));
            }

         foreach ($request->request->all()['form'] as $news_key => $news_value)
        {
            if(is_array($news_value))
            {
                foreach ($news_value as $key1 => $value1)
                {
                    if (strpos($value1, '-') !== false)
                    {
                        $news_id = explode('-', $value1);
                    }

                    $userEmailReferance = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSubscription')->findOneBy(array('sEmail' =>  $user->getEmail()));

                    $newsletterReferance=$em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findOneBy(array('id'=>$news_id[0]));

                    $segmentExist=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=> $newsletterReferance , 'list' => $newsletterReferance->getList()));

                    $segmentStatus = "";
                    if(is_null($segmentExist)== false){ $segmentStatus = $segmentExist->getSegmentId();}

                    if($news_id[1] == "Yes")
                    {
                       if(empty($segmentStatus))
                        {
                            try
                            {
                                $addSegment = $client->addStaticListSegment(array(
                                    'apikey' => $apikey,
                                    'id' => $newsletterReferance->getList()->getListId(),
                                    'name'=>$newsletterReferance->getNname()."-".$newsletterReferance->getId(),
                                    ));

                                $segmentInfo = new MailchimpSegmentListNewsletter();
                                $segmentInfo->setSegmentId($addSegment["id"]);
                                $segmentInfo->setSegmentName($newsletterReferance->getNname()."-".$newsletterReferance->getId());
                                $segmentInfo->setList($newsletterReferance->getList());
                                $segmentInfo->setNewsletter($newsletterReferance);
                                $em->persist($segmentInfo);
                                $em->flush();
                            }
                            catch(\ZfrMailChimp\Exception\Ls\InvalidOptionException $e)
                            {
                                 $getSegment = $client->getListStaticSegments(array(
                                     'apikey' => $apikey,
                                            'id' => $newsletterReferance->getList()->getListId(),
                                    ));

                                foreach ($getSegment as $getSegmentkey => $getSegmentvalue) {
                                    if($getSegmentvalue["name"] == $newsletterReferance->getNname()."-".$newsletterReferance->getId())
                                    {
                                        $segmentId = $getSegmentvalue["id"];
                                    }
                                }

                                $segmentInfo = new MailchimpSegmentListNewsletter();
                                $segmentInfo->setSegmentId($segmentId);
                                $segmentInfo->setSegmentName($newsletterReferance->getNname()."-".$newsletterReferance->getId());
                                $segmentInfo->setList($newsletterReferance->getList());
                                $segmentInfo->setNewsletter($newsletterReferance);
                                $em->persist($segmentInfo);
                                $em->flush();
                            }

                        }

                        $userStatus = "";
                        if(is_null($userEmailReferance)== false){ $userStatus = $userEmailReferance->getId();}

                        if(empty($userStatus))
                        {
                            $Subscription = new MailchimpSubscription();
                            $Subscription->setSEmail(trim($user->getEmail()));
                            $Subscription->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                            $em->persist($Subscription);
                            $em->flush();
                        }

                        $segmentExist=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('newsletter'=> $newsletterReferance , 'list' => $newsletterReferance->getList()));
                        $SubscriptionController = New SubscriptionController();
                        $mailChimpStatus = $SubscriptionController->mailChimpStatus($client,$newsletterReferance->getList()->getListId(),$user->getEmail());

                        if($mailChimpStatus != 'subscribed')
                        {
                           /* Add subscriber */
                            $subscribe = $client->subscribe(array(
                                'apikey' => $apikey,
                                'id' => $newsletterReferance->getList()->getListId(),
                                'email' => array(
                                    'email' => $user->getEmail(),
                                  ),
                                 'double_optin' => false,
                            ));
                           /* End Add subscriber */
                        }
                         $SubscriptionController = New SubscriptionController();
                        $mailChimpStatus = $SubscriptionController->mailChimpStatus($client,$newsletterReferance->getList()->getListId(),$user->getEmail());

                        $mailchimpUserListStatus = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('user_id' => $userEmailReferance, 'list_id' => $newsletterReferance->getList(), 'segment_id' => $segmentExist));

                        $mailchimpUserListStatusVariable = "";
                        if(is_null($mailchimpUserListStatus)== false){ $mailchimpUserListStatusVariable = $mailchimpUserListStatus->getId();}

                        if(empty($mailchimpUserListStatusVariable))
                        {
                            $userInfo = new MailchimpUserListStatus();
                            $userInfo->setUserId($userEmailReferance);
                            $userInfo->setListId($newsletterReferance->getList());
                            $userInfo->setUserMailchimpStatus($mailChimpStatus);
                            $userInfo->setSegmentId($segmentExist);
                            $userInfo->setUserRegistered($regStatus);
                            $userInfo->setUserSegmentStatus("Yes");
                            $em->persist($userInfo);
                            $em->flush();
                        }

                        $addSubscriber = $client->addStaticSegmentMembers(array(
                        'apikey' => $apikey,
                        'id' => $newsletterReferance->getList()->getListId(),
                        'seg_id'=>$segmentExist->getSegmentId(),
                        'batch'=>array(array('email'=>$user->getEmail()))
                        ));


                        if($addSubscriber["success_count"] == 1)
                        {
                            $userExist=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('list_id'=> $newsletterReferance->getList() , 'user_id' => $userEmailReferance, 'segment_id' => $segmentExist));

                            if($userExist)
                            {
                                $userExist->setUserMailchimpStatus($mailChimpStatus);
                                $userExist->setUserSegmentStatus("Yes");
                                $em->persist($userExist);
                                $em->flush();
                            }
                        }
                    }
                    else if($news_id[1] == "No")
                    {
                        if(!empty($segmentStatus))
                        {
                            $userDelete = $client->deleteStaticSegmentMembers(array(
                                    'apikey' => $apikey,
                                    'id' => $newsletterReferance->getList()->getListId(),
                                    'seg_id'=>$segmentStatus,
                                    'batch'=>array(array('email'=>$user->getEmail()))
                                ));

                            if($userDelete["success_count"] == 1)
                            {
                                $userExist=$em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('list_id'=> $newsletterReferance->getList() , 'user_id' => $userEmailReferance, 'segment_id' => $segmentExist));
                                 $em->remove($userExist);
                                $em->flush();
                            }
                        }
                    }

                }
            }
        }
        $resp['success'] = 1;
        return new Response(json_encode($resp));
    }

    public function getCommentEditFormAction(Request $request, $id)
    {
        $resp = array();

        $review = $this->getDoctrine()->getRepository('iFlairLetsBonusFrontBundle:Review')->findOneBy(array('id' => $id));

        $form = $this->createFormBuilder($review)
            ->add('review', 'textarea', array('attr' => array(
                                      'class' => 'form-control',
                                      'required' => true,
                                  )))
            ->add('rating', 'text', array('attr' => array(
                                      'class' => 'form-control',
                                      'required' => true,
                                  )))
            ->getForm();

        $resp['html'] = $this->render('iFlairLetsBonusFrontBundle:User:userCommentEdit.html.twig', array(
                'form' => $form->createView(),
                'id' => $id,
            ))->getContent();

        return new Response(json_encode($resp));
    }

    public function updateCommentAction(Request $request)
    {
        $previous_url = $this->getRequest()->headers->get('referer');

        if (!empty($request->request->all())) {
            $review = $this->getDoctrine()->getRepository('iFlairLetsBonusFrontBundle:Review')->findOneBy(array('id' => $request->request->all()['review_id']));

            if ($review) {
                $review->setReview($request->request->all()['form']['review']);
                $review->setRating($request->request->all()['form']['rating']);

                $em = $this->getDoctrine()->getManager();
                $em->persist($review);
                $em->flush();
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'You have successfully updated your comment!')
                ;
            }

            return $this->redirect($previous_url);
        }
    }

    public function deleteCommentAction(Request $request, $id)
    {
        $review = $this->getDoctrine()->getRepository('iFlairLetsBonusFrontBundle:Review')->findOneBy(array('id' => $id));

        if (!empty($review)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($review);
            $em->flush();
        }

        $user = '';
        $resp = array();
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!empty($userId)) {
            $em = $this->getDoctrine()->getEntityManager();
            $connection = $em->getConnection();
            $statement = $connection->prepare(' SELECT r.*,vp.*,r.created,r.id from lb_review as r
                                                JOIN lb_voucher_programs AS vp ON vp.id = r.brand_id
                                                JOIN lb_front_user AS fu ON fu.id = r.user_id
                                                WHERE r.user_id=:userId
                                                ');

            $statement->bindValue('userId', $userId);

            $statement->execute();
            $userCommentData = $statement->fetchAll();

            $resp['html'] = $this->render('iFlairLetsBonusFrontBundle:User:userComments.html.twig', array('userCommentData' => $userCommentData))->getContent();
        } else {
            $resp['url'] = $this->generateUrl('i_flair_lets_bonus_front_homepage');
        }

        return new Response(json_encode($resp));
    }

    /**
     * @param Request $request
     *
     * @return bool|int
     */
    public function isUserLoggedIn(Request $request)
    {
        return $request->getSession()->get('user_id') ?: false;
    }

//    public function withdrawalRequestAction(Request $request)
//    {
//        $resp = array();
//        $isError = true;
//        $cashbackId = $request->get('cashbackId');
//        $havingPaymentInformation = true;
//        if ($cashbackId) {
//            $cashback = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => $cashbackId));
//            if ($cashback) {
//                $withdrawuserid = $cashback->getUserId();
//                $userPayment = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:UserPaymentDetail')->findOneBy(array('userid' => $withdrawuserid));
//                if ($userPayment) {
//                    $em = $this->getDoctrine()->getManager();
//                    $cashback->setStatus(cashbackTransactions::STATUS_TYPE_PENDING);
//                    $cashback->setType(cashbackTransactions::TRANSACTION_TYPE_WITHDRAWAL);
//                    $cashback->setUserName($userPayment->getOwnername());
//                    $cashback->setUserBankAccountNumber(trim($userPayment->getAccountnumber()));
//                    $cashback->setBic($userPayment->getSwiftcodebic());
//                    $em->persist($cashback);
//                    $em->flush();
//                    /* Once User Ask For Transfering Money */
//                    $this->withdrawalEmail($cashbackId, $withdrawuserid, $em);
//                    $isError = false;
//                    $resp['updatedStatus'] = cashbackTransactions::STATUS_TYPE_PENDING;
//                } else {
//                    $havingPaymentInformation = false;
//                }
//            }
//        }
//        if ($isError) {
//            if (!$havingPaymentInformation) {
//                $resp['msg'] = 'Please fill up your bank information in My Account first.';
//            } else {
//                $resp['msg'] = 'Sorry! something went wrong.';
//            }
//        } else {
//            $resp['msg'] = 'Thank you! We will get back to you!';
//        }
//        $resp['error'] = $isError;
//
//        return new Response(json_encode($resp));
//    }

    public function withdrawalEmail(cashbackTransactions $withDrawTransaction)
    {
        $userRecord = $this->getDoctrine()->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->find($withDrawTransaction->getUserId());
        $currency_symbol = $withDrawTransaction->getCurrency()->getSymbol() ?: '€';
        $name = $userRecord->getName();
        $email = $userRecord->getEmail();
        $amount = $withDrawTransaction->getAmount();
        $message = \Swift_Message::newInstance()
            ->setSubject('Hemos transferido tu Cashback')
            ->setFrom($this->container->getParameter('from_send_email_id'))
            ->setTo($email)
            ->setBody($this->renderView(
                'iFlairLetsBonusFrontBundle:Email:Transactional_Email_Dinero_Transferido.html.twig',
                array(
                        'name' => $name,
                        'email' => $email,
                        'amount' => $amount,
                        'currency' => $currency_symbol,
                    )
            ), 'text/html');
        $this->get('mailer')->send($message);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUserEarningsAction(Request $request)
    {
        $earnings = 0;
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $amounts = $this->getCalculatedUserAmounts($userId);
            $earnings = array_sum($amounts);
        }

        return $this->render(
            'iFlairLetsBonusFrontBundle:User:userEarnings.html.twig',
            [
                'earnings' => $earnings,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUserBlockAction(Request $request)
    {
        $session = $request->getSession();
        if (null !== $session->get('user_id') && null !== $session->get('user')) {
            /** @var FrontUser $user */
            $user = $session->get('user');
            $userImageId = $session->get('userimage');
            $imagePath = $this->getImagePath($userImageId , $user->getUserGender());

            return $this->render('::loginblock.html.twig', ['image_path' => $imagePath]);
        }
        $registerForm = $this->createForm(new RegistrationType());
        $loginForm = $this->createForm(new LoginType());

        return $this->render(
            '::loginblock.html.twig',
            ['form' => $registerForm->createView(), 'login_form' => $loginForm->createView()]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function getNewsletterEditAction(Request $request)
    {
        $resp = ['html' => ''];
        $userId = $this->isUserLoggedIn($request);
        if (false !== $userId) {
            $user = $request->getSession()->get('user');
            /* SUBSCRIPTION  */
            $subscribed_email = trim($user->getEmail());
            $em = $this->getDoctrine()->getEntityManager();
            $subscription_id = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSubscription')->findBySEmail($subscribed_email);
            $sub_id = 0;
            foreach ($subscription_id as $id) {
                $sub_id = $id->getId();
            }

            /* Update Status*/
            $apikey = $this->container->getParameter('mailchimp_api');
            $client = new MailChimpClient($apikey);

            $SubscriptionController = new SubscriptionController();
            $list_id = $SubscriptionController->getLists($em, $client, $apikey);

            foreach ($list_id as $list_key => $list_value)
            {
                $listId = $list_value['id'];
                //$mailChimpStatus=$SubscriptionController->mailChimpStatus($client,$listId,$subscribed_email);
                $info = $client->getListMembersInfo(array(
                    'id' => $listId,
                    'emails' => array(
                        array(
                            'email' => $subscribed_email,
                        ),
                    )
                ));

                if(!empty($info['data']))
                {
                    $count = count($info['data'])-1;
                    $staticSegments = $info['data'][$count]["static_segments"];
                    $status =  $info['data'][$count]["status"];
                }

                if(!empty($staticSegments))
                {
                    foreach ($staticSegments as $segment_key => $segment_value)
                    {
                        $segmentInfo = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('segment_id' => $segment_value['id'] ));

                        $statusUpdate = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus')->findOneBy(array('user_id' => $sub_id, 'segment_id' => $segmentInfo ));

                        if($statusUpdate)
                        {
                            $statusUpdate->setUserMailchimpStatus($status);
                            $em->persist($statusUpdate);
                            $em->flush();
                        }
                    }
                }
            }
            /*ENd Update status*/

            $newsletters = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus');
            $nwsletter_sub = $newsletters->findBy(array('user_id' => $sub_id));
            $newsletter_subscription_data = array();
            $count = 0;
            foreach ($nwsletter_sub as $nws) {

                $group = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpSegmentListNewsletter')->findOneBy(array('id'=>$nws->getSegmentId()));

                $Newsletter = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter')->findBy(array('id'=>$group->getNewsletter()));
                foreach ($Newsletter as $news_letter) {
                    if ($nws->getUserSegmentStatus() == 'Yes')
                    {
                        $newsletter_subscription_data[$count]['nw_sub_id'] = $nws->getId();
                        $newsletter_subscription_data[$count]['id'] = $news_letter->getId();
                        $newsletter_subscription_data[$count]['name'] = $news_letter->getNname();
                        $newsletter_subscription_data[$count]['checked'] = 'checked';
                    }
                    else{
                        $newsletter_subscription_data[$count]['nw_sub_id'] = $nws->getId();
                        $newsletter_subscription_data[$count]['id'] = $news_letter->getId();
                        $newsletter_subscription_data[$count]['name'] = $news_letter->getNname();
                        $newsletter_subscription_data[$count]['checked'] = '';
                    }
                    ++$count;
                }
            }


            $Newsletter_Entity = $em->getRepository('iFlairLetsBonusAdminBundle:Newsletter');
            $NEwsletters = $Newsletter_Entity->findBy(array('status' => 'ready'));
            $all_newsletter_data = array();
            $counts = 0;
            foreach ($NEwsletters as $NEntity) {
                $all_newsletter_data[$counts]['id'] = $NEntity->getId();
                $all_newsletter_data[$counts]['name'] = $NEntity->getNname();
                $all_newsletter_data[$counts]['checked'] = '';
                ++$counts;
            }
            foreach ($all_newsletter_data as $index => $all_data) {
                foreach ($newsletter_subscription_data as $n_data) {
                    if ($all_data['id'] == $n_data['id']) {
                        unset($all_newsletter_data[$index]);
                    }
                }
            }
            $all_newsletter_subscriptions = array_merge($newsletter_subscription_data, $all_newsletter_data);

            $formBuilderQuestionnaire = $this->createFormBuilder();
            $i = 0;
            $formBuilder = '';
            foreach ($all_newsletter_subscriptions as $choice)
            {
                if ($choice['checked'] == 'checked')
                {

                    if (!empty($choice['nw_sub_id']))
                    {

                        $nw_sub1 = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus');
                        $nwsletter_sub = $nw_sub1->findById($choice['nw_sub_id']);
                        $formBuilder = $this->get('form.factory')->createNamedBuilder($i, 'form', $nwsletter_sub);
                        $label = str_replace(' ', '-', $choice['name']);
                        $formBuilder->add($label, 'choice',
                            array('attr' => array('class' => 'gender'),
                                'choices' => array(trim($choice['id']).'-No' => 'No Suscrito', trim($choice['id']).'-Yes' => 'Suscrito'),
                                'data' => trim($choice['id']).'-Yes',
                                'multiple' => false,
                                'expanded' => true,
                                'required' => true,
                            ));
                        $formBuilderQuestionnaire->add($formBuilder);
                        ++$i;
                    }
                }
                else
                {

                    if (!empty($choice['nw_sub_id']))
                    {

                        $nw_sub1 = $em->getRepository('iFlairLetsBonusAdminBundle:MailchimpUserListStatus');
                        $nwsletter_sub = $nw_sub1->findById($choice['nw_sub_id']);
                        $formBuilder = $this->get('form.factory')->createNamedBuilder($i, 'form', $nwsletter_sub);
                        $label = str_replace(' ', '-', $choice['name']);
                        $formBuilder->add($label, 'choice',
                            array('attr' => array('class' => 'gender'),
                                'choices' => array(trim($choice['id']).'-No' => 'No Suscrito', trim($choice['id']).'-Yes' => 'Suscrito'),
                                'data' => trim($choice['id']).'-No',
                                'multiple' => false,
                                'expanded' => true,
                                'required' => true,
                            ));
                        $formBuilderQuestionnaire->add($formBuilder);
                        ++$i;
                    }
                    else{

                        $label = str_replace(' ', '-', $choice['name']);
                        $formBuilder = $this->get('form.factory')->createNamedBuilder($i, 'form');
                        $formBuilder->add($label, 'choice',
                            array('attr' => array('class' => 'gender'),
                                'choices' => array(trim($choice['id']).'-No' => 'No Suscrito', trim($choice['id']).'-Yes' => 'Suscrito'),
                                'data' => trim($choice['id']).'-No',
                                'multiple' => false,
                                'expanded' => true,
                                'required' => true,
                            ));
                        $formBuilderQuestionnaire->add($formBuilder);
                        ++$i;
                    }
                }
                //exit();
            }
            $form1 = $formBuilderQuestionnaire->getForm()->createView();
            /* SUBSCRIPTION  */
            $resp['html'] = $this->render(
                'iFlairLetsBonusFrontBundle:User:userNewsletterEdit.html.twig',
                [
                    'nw_subscription' => $form1,
                ]
            )->getContent();
        }

        return new Response(json_encode($resp));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function getPaymentsEditAction(Request $request)
    {
        $userId = $this->isUserLoggedIn($request);
        $resp = [];
        if (false !== $userId) {
            $repository = $this->get('doctrine.orm.default_entity_manager')->getRepository(
                'iFlairLetsBonusAdminBundle:FrontUser'
            );
            /** @var FrontUser $user */
            $user = $repository->find($userId);
            $userPayment = $user->getUserPaymentDetail();
            if (false === $userPayment) {
                $userPayment = new UserPaymentDetail();
            }

            $resp['html'] = $this->render(
                'iFlairLetsBonusFrontBundle:User:userPaymentEdit.html.twig',
                [
                    'form' => $this->createForm(new UserPaymentDetailType(), $userPayment)->createView(),
                ]
            )->getContent();
        }


        return new Response(json_encode($resp));
    }

    /**
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function getInsuranceEditAction()
    {
        $resp['html'] = '';
        return new Response(json_encode($resp));
    }

    /**
     * @param int $userImageId
     *
     * @param     $userGender
     *
     * @return string
     */
    protected function getImagePath($userImageId, $userGender)
    {
        if (null !== $userImageId) {
            $media = $this->get('sonata.media.manager.media')->find($userImageId);
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, 'big');
            $image_path = $provider->generatePublicUrl($media, $format);
        } else {
            $image_path = $this->get('templating.helper.assets')->getUrl(
                'bundles/iflairletsbonusfront/images/chico_alta.png'
            );
            if ($userGender === 1) {
                $image_path = $this->get('templating.helper.assets')->getUrl(
                    'bundles/iflairletsbonusfront/images/chica_alta.png'
                );
            }
        }

        return $image_path;
    }

    /**
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     */
    protected function setUserAreaPath(Request $request)
    {
        $path = $request->attributes->get('_route');
        $session = $request->getSession();
        $session->set('user_path', $path);
    }

    /**
     * @param FrontUser $user
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function sendEmailPaymentDataUpdate($user)
    {
        if (null !== $user && $user->getEmail()) {
            $message = \Swift_Message::newInstance()
                ->setSubject('Shoppiday - Cambio de datos bancarios')
                ->setFrom($this->container->getParameter('from_send_email_id'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'iFlairLetsBonusFrontBundle:Email:userpayment_update_success.html.twig',
                        [
                            'name' => $user->getName(),
                            'email' => $user->getEmail(),
                            'url' => $this->generateUrl(
                                'i_flair_lets_bonus_front_login',
                                [],
                                UrlGeneratorInterface::ABSOLUTE_URL
                            ),
                        ]
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
        }
    }
}
