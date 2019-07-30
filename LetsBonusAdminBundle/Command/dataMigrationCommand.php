<?php
namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Companies;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Language;
use iFlair\LetsBonusAdminBundle\Entity\Network;
use iFlair\LetsBonusAdminBundle\Entity\Voucher;
use iFlair\LetsBonusAdminBundle\Entity\Tags;
use iFlair\LetsBonusAdminBundle\Entity\Groups;
use iFlair\LetsBonusAdminBundle\Entity\Administrator;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;
use iFlair\LetsBonusAdminBundle\Entity\Variation;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusAdminBundle\Entity\parentCategory;
use iFlair\LetsBonusAdminBundle\Entity\Collection;
use iFlair\LetsBonusAdminBundle\Entity\Clicks;
use iFlair\LetsBonusAdminBundle\Entity\Searchlogs;
use iFlair\LetsBonusAdminBundle\Entity\cashbackSettings;
use iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions;
use Application\Sonata\MediaBundle\Entity\Media;
use iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\shopVariation;
use iFlair\LetsBonusAdminBundle\Entity\MigrationLog;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;

class dataMigrationCommand extends ContainerAwareCommand
{
    private $limit = 500;
    private $progress;
    private $em;
    private $queryBuilder;    

    protected function configure()
    {
        $this->setName('data:migration')->setDescription('Data Migrations')->addArgument(
                'migrationType',
                InputArgument::OPTIONAL,
                'Type of migration'
            );
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        gc_disable();

        $migrationType = $input->getArgument('migrationType');

        $this->em = $this->getContainer()->get('doctrine')->getManager('default');
        $this->queryBuilder = $this->em->createQueryBuilder();
        
        //key is target entity
        $entity_array  = array( 
                            'Network'               => array(
                                                        'source_entity' => 'Networks', 
                                                        'function' => 'migrateNetworks'
                                                        ),
                            /*'Groups'                => array(
                                                        'source_entity' => 'Groups', 
                                                        'function' => 'migrateGroups'
                                                        ),*/
                            /*'Administrator'         => array(
                                                        'source_entity' => 'Administrators', 
                                                        'function' => 'migrateAdministrators'
                                                        ),*/
                            'Shop'                  => array(
                                                        'source_entity' => 'Shops',
                                                        'function' => 'migrateShops'
                                                        ),
                            /*'Tags'                  => array(
                                                        'source_entity' => 'Labels',
                                                        'function' => 'migrateTags'
                                                        ),*/
                            'shopHistory'           => array(
                                                        'source_entity' => 'Shopshistories',
                                                        'function' => 'migrateShopHistories'
                                                        ),
                            'Variation'             => array(
                                                        'source_entity' => 'Variations',
                                                        'function' => 'migrateVariations'
                                                        ),
                            'FrontUser'             => array(
                                                        'source_entity' => 'Users',
                                                        'function' => 'migrateFrontendUsers'
                                                        ),
                            'parentCategory'        => array(
                                                        'source_entity' => 'Categories',
                                                        'function' => 'migrateFirstLevelCatgories'
                                                        ),
                            /*'Collection'            => array(
                                                        'source_entity' => 'Collections',
                                                        'function' => 'migrateCollections'
                                                        ),*/
                            'Clicks'                => array(
                                                        'source_entity' => 'Clicks',
                                                        'function' => 'migrateClicks'
                                                        ),
                            /*'Searchlogs'            => array(
                                                        'source_entity' => 'Searchlogs',
                                                        'function' => 'migrateSearchlogs'
                                                        ),*/
                            'cashbackSettings'      => array(
                                                        'source_entity' => 'Cashbacksettings',
                                                        'function' => 'migrateCashbackSettings'
                                                        ),
                            'cashbackSettingsShop'  => array(
                                                        'source_entity' => 'CashbacksettingsShops',
                                                        'function' => 'migrateCashbackSettingsShops'
                                                        ),
                            'LetsBonusTransactions' => array(
                                                        'source_entity' => 'Affiliatetransactions',
                                                        'function' => 'migrateAffiliatetransactions'
                                                        ),
                            'cashbackTransactions'  => array(
                                                        'source_entity' => 'Cashbacktransactions',
                                                        'function' => 'migrateCashbackTransactions'
                                                        )
                            
                        );


        /* new added */
        $migrationLogs = $this->em
            ->getRepository('iFlairLetsBonusAdminBundle:MigrationLog')
            ->findAll();

        if(!empty($migrationLogs)) {
            $migrationLogs = $this->em->getRepository('iFlairLetsBonusAdminBundle:MigrationLog')->findOneBy(array('status' => 0));
            if(empty($migrationLogs)) {
                if($migrationType != "automatic") {
                    $migrationLogsToDelete = $this->em->getRepository('iFlairLetsBonusAdminBundle:MigrationLog')->findAll();
                    if($migrationLogsToDelete) {
                        foreach ($migrationLogsToDelete as $migrationLog) {
                          $this->em->remove($migrationLog);
                          $this->em->flush();
                        }
                        $output->writeln('<info>Flushed migration log table run this command again to setup migration...</info>');   
                        exit;
                    } 
                } else {
                    $this->progress->clear();
                    $output->writeln('<info>Migration Completed Successfully...</info>');
                    $this->progress->display();
                    $this->progress->finish();
                    exit;
                }
            } else {
                if($migrationType != "automatic") {
                    $this->migrateCurrancy();
                    $this->migrateLanguage();
                    $this->migrateCompanies();
                    $output->writeln('<info>Migration Process Started Successfully...</info>');
                }
                foreach ($entity_array as $targetEntity => $migrationInfo) {
                    $currentMigration = $this->em->getRepository('iFlairLetsBonusAdminBundle:MigrationLog')->findOneBy(array('tablename' => $targetEntity, 'status' => 0));
                    if($currentMigration){
                        if ($currentMigration->getLastProcessedId() == 0) {
                            $output->writeln("");
                            $output->writeln("<info> => Migration of ".$targetEntity." started...</info>");
                            $totalRecords = 0;
                            $entityName = $migrationInfo['source_entity'];
                            
                            $this->queryBuilder->resetDQLParts();
                            $this->queryBuilder->select($this->queryBuilder->expr()->count('at_total.id'));
                            $this->queryBuilder->from('iFlairLetsBonusMigrationBundle:'.$entityName,'at_total');
                            $totalRecords = $this->queryBuilder->getQuery()->getSingleScalarResult();
                           
                            $this->progress = new ProgressBar($output, $totalRecords);
                            $this->progress->start();
                            $this->progress->setFormat(' %current%/%max% [%bar%] %percent:3s%%');
                        } elseif($migrationType != "automatic") {
                            $output->writeln("");
                            $output->writeln("<info> => Migration of ".$targetEntity." started...</info>");


                            $totalRecords = 0;
                            $entityName = $migrationInfo['source_entity'];
                            $this->queryBuilder->resetDQLParts();
                            $this->queryBuilder->select($this->queryBuilder->expr()->count('at_total.id'));
                            $this->queryBuilder->from('iFlairLetsBonusMigrationBundle:'.$entityName,'at_total');
                            $totalRecords = $this->queryBuilder->getQuery()->getSingleScalarResult();                            

                            $totalRecordsDone = 0;
                            $currentMigration = $this->em->getRepository('iFlairLetsBonusAdminBundle:MigrationLog')->findOneBy(array('tablename' => $targetEntity));
                            $entityName = $migrationInfo['source_entity'];
                            
                            $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
                            $this->queryBuilder->resetDQLParts();
                            $this->queryBuilder->select($this->queryBuilder->expr()->count('at.id'));
                            $this->queryBuilder->from('iFlairLetsBonusMigrationBundle:'.$entityName,'at');
                            $this->queryBuilder->where('at.id <= :nid');
                            $this->queryBuilder->setParameter('nid', $lastProcessedID);
                            $totalRecordsDone = $this->queryBuilder->getQuery()->getSingleScalarResult();
                            
                            $this->progress = new ProgressBar($output, $totalRecords);
                            $this->progress->start();
                            $this->progress->advance($totalRecordsDone);
                            $this->progress->setFormat(' %current%/%max% [%bar%] %percent:3s%%');
                        } elseif($migrationType == "automatic") {
                            $totalRecords = 0;
                            $entityName = $migrationInfo['source_entity'];
                                                        
                            $this->queryBuilder->resetDQLParts();
                            $this->queryBuilder->select($this->queryBuilder->expr()->count('at_total.id'));
                            $this->queryBuilder->from('iFlairLetsBonusMigrationBundle:'.$entityName,'at_total');
                            $totalRecords = $this->queryBuilder->getQuery()->getSingleScalarResult();

                            $totalRecordsDone = 0;
                            $currentMigration = $this->em->getRepository('iFlairLetsBonusAdminBundle:MigrationLog')->findOneBy(array('tablename' => $targetEntity));
                            $entityName = $migrationInfo['source_entity'];
                            
                            $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
                            $this->queryBuilder->resetDQLParts();
                            $this->queryBuilder->select($this->queryBuilder->expr()->count('at.id'));
                            $this->queryBuilder->from('iFlairLetsBonusMigrationBundle:'.$entityName,'at');
                            $this->queryBuilder->where('at.id <= :nid');
                            $this->queryBuilder->setParameter('nid', $lastProcessedID);
                            $totalRecordsDone = $this->queryBuilder->getQuery()->getSingleScalarResult();
                        }

                        $done = $this->$migrationInfo['function']($currentMigration);

                        if($done) {
                            $this->progress->clear();
                            $output->writeln('<info> Migration of '.$targetEntity.' done</info>');
                            $this->progress->display();
                            $output->writeln('');
                            $currentMigration->setStatus(1);
                            $this->em->persist($currentMigration);
                            $this->em->flush();
                        } else {
                            //gc_collect_cycles();
                            $command = $this->getApplication()->find('data:migration');
                            $arguments = array(
                                'command' => 'data:migration',
                                'migrationType'    => 'automatic',
                            );

                            $migrationInput = new ArrayInput($arguments);
                            $returnCode = $command->run($migrationInput, $output);
                        }

                        $this->em->getConnection()->close();                        
                    }
                }
                
                $output->writeln('<info>Migration Completed Successfully...</info>');
                $this->progress->finish();
                exit; 
            }
        } else {
            $totalTables = count($entity_array);
            $emptyTablesCounter = 0;
            
            foreach ($entity_array as $targetEntity => $migrationInfo) {
                $entityName = $migrationInfo['source_entity'];
                $entity = $this->em->getRepository('iFlairLetsBonusMigrationBundle:'.$entityName,'letbonus')->findOneBy(
                    array(),
                    array('id' => 'DESC'),
                    0,
                    0
                );

                if(empty($entity)){
                    $emptyTablesCounter++;
                }
            }
            if($emptyTablesCounter == $totalTables) {
                $output->writeln('<info>Please load fixtures using "php app/console doctrine:fixtures:load --append" then try again....</info>');
                exit();
            } else {
                foreach ($entity_array as $targetEntity => $migrationInfo) {
                    $entityName = $migrationInfo['source_entity'];
                    $entity = $this->em->getRepository('iFlairLetsBonusMigrationBundle:'.$entityName,'letbonus')->findOneBy(
                        array(),
                        array('id' => 'DESC'),
                        0,
                        0
                    );

                    if(empty($entity)){
                        $emptyTablesCounter++;
                    } else {
                        $migrationLogs = new MigrationLog();
                        $migrationLogs->setTablename($targetEntity);
                        $lastId = $entity->getId();
                        $migrationLogs->setlastId($lastId);
                        $migrationLogs->setLastProcessedId(0);
                        $migrationLogs->setStatus(0);
                        $this->em->persist($migrationLogs);
                        $this->em->flush();
                    }
                        $this->em->getConnection()->close();
                }
            }

            $output->writeln('<info>Migration setup successfully, run this command - "php app/console data:migration" again to start migration....</info>');
        }
        gc_enable();
        /* new added */
    }
    protected function migrateAffiliatetransactions($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusAffiliatetransactions = $this->queryBuilder
                                ->select('at')
                                ->from('iFlairLetsBonusMigrationBundle:Affiliatetransactions',  'at')
                                ->where('at.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery()
                                ->getResult();

        $migrationComleted = 0;
        foreach ($letsbonusAffiliatetransactions as $letsbonusAffiliatetransaction) {
            if (!$this->checkIfTransactionExist($letsbonusAffiliatetransaction)) {
                $id = $this->migrateAffiliatetransaction($letsbonusAffiliatetransaction);
                if($lastId == $id) {
                    $migrationComleted = 1;
                }
                $currentMigration->setLastProcessedId($id);
            }
        }

        $this->em->persist($currentMigration);
        $this->em->flush();
        $this->em->clear();

        return $migrationComleted;        
    }

    protected function checkIfTransactionExist($letsbonusAffiliatetransaction)
    {
        return $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findOneBy(array('transactionId' => $letsbonusAffiliatetransaction->getTransactionid(), 'param0' => $letsbonusAffiliatetransaction->getParam0(),'id' => $letsbonusAffiliatetransaction->getId()));
    }

    protected function migrateAffiliatetransaction($letsbonusAffiliatetransaction)
    {
        $affiliateTransactionExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findOneBy(array('id' => $letsbonusAffiliatetransaction->getId()));

        if ($affiliateTransactionExist) {
            $affiliateTransaction = $affiliateTransactionExist;
        } else {
            $affiliateTransaction = new LetsBonusTransactions();
            $affiliateTransaction->setId($letsbonusAffiliatetransaction->getId());
        }

        $affiliateTransaction->setTransactionId($letsbonusAffiliatetransaction->getTransactionid());
        $affiliateTransaction->setReferenceId($letsbonusAffiliatetransaction->getReferenceId());
        $affiliateTransaction->setAmount($letsbonusAffiliatetransaction->getAmount());
        $affiliateTransaction->setCommission($letsbonusAffiliatetransaction->getCommission());
        $affiliateTransaction->setStatus($letsbonusAffiliatetransaction->getStatus());
        $affiliateTransaction->setStatusName($letsbonusAffiliatetransaction->getStatusName());
        $affiliateTransaction->setStatusState($letsbonusAffiliatetransaction->getStatusState());
        $affiliateTransaction->setStatusMessage($letsbonusAffiliatetransaction->getStatusMessage());
        $affiliateTransaction->setLeadNumber($letsbonusAffiliatetransaction->getLeadnumber());
        $affiliateTransaction->setProcessed($letsbonusAffiliatetransaction->getProcessed());
        $affiliateTransaction->setProcessedDate($letsbonusAffiliatetransaction->getProcessDate());
        $affiliateTransaction->setDaystoautoapprove($letsbonusAffiliatetransaction->getDaystoautoapprove());

        if (!empty($letsbonusAffiliatetransaction->getParam0())) {
            $affiliateTransaction->setParam0($letsbonusAffiliatetransaction->getParam0());
        } else {
            $affiliateTransaction->setParam0('');
        }

        if (!empty($letsbonusAffiliatetransaction->getParam1())) {
            $affiliateTransaction->setParam1($letsbonusAffiliatetransaction->getParam1());
        } else {
            $affiliateTransaction->setParam1('');
        }

        if (!empty($letsbonusAffiliatetransaction->getParam2())) {
            $affiliateTransaction->setParam2($letsbonusAffiliatetransaction->getParam2());
        } else {
            $affiliateTransaction->setParam2('');
        }

        $network = $this->getNetworkById($letsbonusAffiliatetransaction->getNetworkId(), 1);
        $affiliateTransaction->setNetwork($network);

        $currency = $this->getcurrencyByCode($letsbonusAffiliatetransaction->getCurrency(), 1);
        $affiliateTransaction->setCurrency($currency);

        $affiliateTransaction->setCreated(new \DateTime(date('Y-m-d H:i:s', $letsbonusAffiliatetransaction->getCreated()->getTimestamp())));
        $affiliateTransaction->setModified(new \DateTime(date('Y-m-d H:i:s', $letsbonusAffiliatetransaction->getModified()->getTimestamp())));
        $affiliateTransaction->setClickDate($letsbonusAffiliatetransaction->getClickdate());
        $affiliateTransaction->setClickId($letsbonusAffiliatetransaction->getClickid());
        $affiliateTransaction->setClickInId($letsbonusAffiliatetransaction->getClickinid());
        $affiliateTransaction->setTrackingDate($letsbonusAffiliatetransaction->getTrackingdate());
        $affiliateTransaction->setTrackingUrl($letsbonusAffiliatetransaction->getTrackingurl());
        $affiliateTransaction->setOrderNumber($letsbonusAffiliatetransaction->getOrdernumber());
        $affiliateTransaction->setOrderValue($letsbonusAffiliatetransaction->getOrdervalue());
        $affiliateTransaction->setProgramId($letsbonusAffiliatetransaction->getProgramId());
        $affiliateTransaction->setProgramName($letsbonusAffiliatetransaction->getProgramName());

        $shopHistory = $this->getShopHistoryById($letsbonusAffiliatetransaction->getShopshistoryId(), 1);
        $affiliateTransaction->setShopHistory($shopHistory);

        if (!empty($letsbonusAffiliatetransaction->getModifieddate())) {
            $affiliateTransaction->setModifiedDate($letsbonusAffiliatetransaction->getModifieddate());
        }

        $affiliateTransaction->setProductName($letsbonusAffiliatetransaction->getProductname());

        $this->em->persist($affiliateTransaction);
        $metadata = $this->em->getClassMetaData(get_class($affiliateTransaction));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $affiliateTransaction->getId();
    }

    protected function flushTableData($class)
    {
        $cmd = $this->em->getClassMetaData(get_class($class));
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
        $connection->close();
    }

    protected function migrateCurrancy($cur = null)
    {
        if (!empty($cur)) {
            $currency = $this->checkIFCurrencyExists($cur);
            if (!$currency) {
                $currency = new Currency();
                $currency->setCode($cur);
                $currency->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                $currency->setModified(new \DateTime(date('Y-m-d H:i:s')));
                $this->em->persist($currency);
                $this->em->flush();

                return $currency;
            }
        } else {
            $currency = $this->checkIFCurrencyEuroExists();
            if (!$currency) {
                $currency = new Currency();
                $currency->setCode('EUR');
                $currency->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                $currency->setModified(new \DateTime(date('Y-m-d H:i:s')));
                $this->em->persist($currency);
                $this->em->flush();
            }
        }
    }

    protected function getcurrencyByCode($cur, $anyRecord = null)
    {
        $currency = $this->em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => $cur));

        if (empty($currency) && $anyRecord) {
            $currency = $this->em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
        }

        return $currency;
    }
    protected function checkIFCurrencyExists($cur)
    {
        return $currency = $this->em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => $cur));
    }
    protected function checkIFCurrencyEuroExists()
    {
        return $currency = $this->em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => 'EUR'));
    }
    protected function migrateLanguage($lang = null)
    {
        if (!empty($lang)) {
            $language = $this->checkIFLanguageExists($lang);
            if (!$language) {
                $language = new Language();
                $language->setCode($lang);
                $language->setName('English');
                $language->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                $language->setModified(new \DateTime(date('Y-m-d H:i:s')));
                $this->em->persist($language);
                $this->em->flush();
            }
        } else {
            $language = $this->checkIFLanguageENExists();
            if (!$language) {
                $language = new Language();
                $language->setCode('EN');
                $language->setName('English');
                $language->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                $language->setModified(new \DateTime(date('Y-m-d H:i:s')));
                $this->em->persist($language);
                $this->em->flush();
            }
        }
    }
    protected function checkIFLanguageExists($lang)
    {
        return $language = $this->em->getRepository('iFlairLetsBonusAdminBundle:Language')->findOneBy(array('code' => $lang));
    }
    protected function checkIFLanguageENExists()
    {
        return $language = $this->em->getRepository('iFlairLetsBonusAdminBundle:Language')->findOneBy(array('code' => 'EN'));
    }

    protected function migrateCompanies()
    {
        $letsbonusCompanies = $this->em
            ->getRepository('iFlairLetsBonusMigrationBundle:Companies', 'letbonus')
            ->findAll()
        ;
        foreach ($letsbonusCompanies as $letsbonusCompany) {
            if (!$this->checkIFCompaniesByName($letsbonusCompany)) {
                $this->migrateCompany($letsbonusCompany);
            } /*else {
                $this->removeCompanyAndUpdateId($letsbonusCompany);
            }*/
        }
    }

    protected function migrateCompany($letsbonusCompany)
    {
        /*$companyExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('id' => $letsbonusCompany->getId()));

        if ($companyExist) {
            $companies = $companyExist;
        } else {
            $companies = new Companies();
            $companies->setId($letsbonusCompany->getId());
        }*/
        $companies = new Companies();

        $companies->setName($letsbonusCompany->getName());
        $companies->setIsoCode($letsbonusCompany->getIsoCode());
        $companies->setCommonConditions($letsbonusCompany->getCommonConditions());
        $companies->setHoursOffset($letsbonusCompany->getHoursOffset());
        $companies->setTimezone($letsbonusCompany->getTimezone());
        $companies->setCreated(new \DateTime(date('Y-m-d H:i:s', $letsbonusCompany->getCreated()->getTimestamp())));
        $companies->setModified(new \DateTime(date('Y-m-d H:i:s', $letsbonusCompany->getModified()->getTimestamp())));
        $currency = $this->checkIFCurrencyExists($letsbonusCompany->getCurrency());
        if (!$currency) {
            $currency = $this->migrateCurrancy($letsbonusCompany->getCurrency());
            $companies->setCurrency($currency);
        } else {
            $companies->setCurrency($currency);
        }
        $language = $this->checkIFLanguageExists($letsbonusCompany->getLang());
        if (!$language) {
            $language = $this->migrateLanguage($letsbonusCompany->getLang());
            $companies->setLang($language);
        } else {
            $companies->setLang($language);
        }
        $this->em->persist($companies);
        $metadata = $this->em->getClassMetaData(get_class($companies));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();
    }

    protected function removeCompanyAndUpdateId($letsbonusCompany)
    {
        $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('name' => $letsbonusCompany->getName()));

        if ($companies->getId() != $letsbonusCompany->getId()) {
            $this->em->remove($companies);
            $this->em->flush();

            $this->migrateCompany($letsbonusCompany);
        } else {
            $this->migrateCompany($letsbonusCompany);
        }
    }

    protected function checkIFCompaniesByName($letsbonusCompany)
    {
        return $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('name' => $letsbonusCompany->getName()));
    }
    protected function migrateNetworks($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusNetworks = $this->queryBuilder
                                ->select('n')
                                ->from('iFlairLetsBonusMigrationBundle:Networks',  'n')
                                ->where('n.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();                                

        
        $migrationComleted = 0;
        $iterableResultOfNetworks = $letsbonusNetworks->iterate();
        while (($letsbonusNetwork = $iterableResultOfNetworks->next()) !== false) {
            if (!$this->checkIFNetworkExistsByName($letsbonusNetwork[0])) {
                $id = $this->migrateNetwork($letsbonusNetwork[0]);
            } /*else {
                $id = $this->removeNetworkAndUpdateId($letsbonusNetwork[0]);
            }*/

            $this->progress->advance();
            if(isset($id) && !empty($id)) {
                if($lastId == $id) {
                    $migrationComleted = 1;
                } 
            } else {
                $id = $letsbonusNetwork[0]->getId();
                if($lastId == $id) {
                    $migrationComleted = 1;
                } 
            }
            $currentMigration->setLastProcessedId($id);
            unset($id);
            $this->em->detach($letsbonusNetwork[0]);
        }

        $this->em->persist($currentMigration);
        $this->em->flush();

        return $migrationComleted;
    }
    protected function getShopHistoryById($id, $anyRecord = null)
    {
        $shopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $id));
        if (empty($shopHistory) && $anyRecord) {
            $shopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
        }

        return $shopHistory;
    }
    protected function getNetworkById($id, $anyRecord = null)
    {

        $fromNetwork = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Networks')->findOneBy(array('id' => $id));
        $network = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => $fromNetwork->getName()));

        if (empty($network) && $anyRecord) {
            $network = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
        }

        return $network;
    }

    protected function migrateNetwork($letsbonusNetwork)
    {
        /*if ($networkExist = $this->getNetworkById($letsbonusNetwork->getId(), 0)) {
            $network = $networkExist;
        } else {
            $network = new Network();
            $network->setId($letsbonusNetwork->getId());
        }*/

        $network = new Network();
        $network->setName($letsbonusNetwork->getName());
        $network->setUrl($letsbonusNetwork->getUrl());

        if (!empty($letsbonusNetwork->getCreated())):
            $network->setCreated(new \DateTime(date('Y-m-d H:i:s', $letsbonusNetwork->getCreated()->getTimestamp()))); else:
            $network->setCreated(new \DateTime());
        endif;

        if (!empty($letsbonusNetwork->getModified())):
            $network->setModified(new \DateTime(date('Y-m-d H:i:s', $letsbonusNetwork->getModified()->getTimestamp()))); else:
            $network->setModified(new \DateTime());
        endif;

        $this->em->persist($network);
        $metadata = $this->em->getClassMetaData(get_class($network));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();
        return $network->getId();
    }
    protected function removeNetworkAndUpdateId($letsbonusNetwork)
    {
        $network = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => $letsbonusNetwork->getName()));

        if ($network->getId() != $letsbonusNetwork->getId()) {
            $networkCredentials = $this->em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findBy(array('network' => $network->getId()));

            foreach ($networkCredentials as $networkCredential) {
                $this->em->remove($networkCredential);
                $this->em->flush();
            }

            $this->em->remove($network);
            $this->em->flush();

            return $this->migrateNetwork($letsbonusNetwork);
        } else {
            return $this->migrateNetwork($letsbonusNetwork);
        }
    }
    protected function checkIFNetworkExistsByName($letsbonusNetwork)
    {
        return $network = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => $letsbonusNetwork->getName()));
    }
    protected function migrateShops($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusShops = $this->queryBuilder
                                ->select('s')
                                ->from('iFlairLetsBonusMigrationBundle:Shops',  's')
                                ->where('s.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();                                

        $migrationComleted = 0;
        $iterableResultOfShops = $letsbonusShops->iterate();
        while (($letsbonusShop = $iterableResultOfShops->next()) !== false) {
            $id = $this->migrateShop($letsbonusShop[0]);
            $this->progress->advance();
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
            $this->em->detach($letsbonusShop[0]);
        }

        $this->em->persist($currentMigration);
        $this->em->flush();
        
        return $migrationComleted;
    }
    protected function migrateShop($letsbonusShop)
    {

        $shopExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $letsbonusShop->getId()));

        if ($shopExist) {
            $shop = $shopExist;
        } else {
            $shop = new Shop();
            $shop->setId($letsbonusShop->getId());
        }

        $fromCompany = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Companies')->findOneBy(array('id' => $letsbonusShop->getCompanyId()));
        $companies = "";
        if(!empty($fromCompany)) {
            $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('name' => $fromCompany->getName()));
        } else {
            $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
        }

        $shop->setCompanies($companies);
        $network ="";
        if (!empty($letsbonusShop->getNetworkId())):
            $fromNetwork = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Networks')->findOneBy(array('id' => $letsbonusShop->getNetworkId()));
            $network = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => $fromNetwork->getName()));
            $networkCredential = $this->em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findOneBy(array('network' => $network->getId()),array('id' => 'DESC'));
            if(!empty($networkCredential)) {
                $shop->setNetworkCredentials($networkCredential);
            }
        endif;

        $imgLogo = 'https://lbcashback.s3.amazonaws.com'.$letsbonusShop->getImgLogo();
        $imgLogo2 = 'https://lbcashback.s3.amazonaws.com'.$letsbonusShop->getImgLogo2();
        $imgLogoId = $this->migrateMedia($letsbonusShop->getImgLogo());
        $imgLogo2Id = $this->migrateMedia($letsbonusShop->getImgLogo2());
        $shop->setImage($imgLogoId);

        $administrator = $this->em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(
            array(),
            array('id' => 'ASC'),
            0,
            0
        );
        if (!empty($administrator)) {
            $shop->setAdministrator($administrator->getId());
        } else {
            $this->migrateAdministrators();
            $administrator = $this->em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(
                array(),
                array('id' => 'ASC'),
                0,
                0
            );
            if(empty($administrator)) {
                echo "\n\nThere is no administrator in system for shop creation\n\n";
                exit;
            }
            $shop->setAdministrator($administrator->getId());
        }

        $letsbonusShopHistory = $this->em
            ->getRepository('iFlairLetsBonusMigrationBundle:Shopshistories', 'letbonus')
            ->findOneBy(array('shopId' => $letsbonusShop->getId()));

        if (!empty($letsbonusShopHistory)) {
            $shop->setTitle($letsbonusShopHistory->getTitle());
            $shop->setUrl($letsbonusShopHistory->getUrl());
            $shop->setIntroduction($letsbonusShopHistory->getIntro());
            $shop->setDescription($letsbonusShopHistory->getDescription());
            $shop->setTearms($letsbonusShopHistory->getConditions());
            if(!empty($letsbonusShopHistory->getPrice())) {
                $shop->setCashbackPrice($letsbonusShopHistory->getPrice());
            }
            if(!empty($letsbonusShopHistory->getPercentage())){
                $shop->setCashbackPercentage($letsbonusShopHistory->getPercentage());
            }
            if(!empty($letsbonusShopHistory->getLbpercentage())) {
                $shop->setLetsBonusPercentage($letsbonusShopHistory->getLbpercentage());
            }

            /*if (empty($letsbonusShopHistory->getLabelId())) {
                $tags = $this->em->getRepository('iFlairLetsBonusAdminBundle:Tags')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
                if (!empty($tags)) {
                    $shop->setTag($tags);
                }
            }

            $shop->setTag("");*/
        } /*else {
            $letsbonusShopHistory = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Shopshistories', 'letbonus')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );

            if (!empty($letsbonusShopHistory)) {
                $shop->setTitle($letsbonusShopHistory->getTitle());
                $shop->setUrl($letsbonusShopHistory->getUrl());
                $shop->setIntroduction($letsbonusShopHistory->getIntro());
                $shop->setDescription($letsbonusShopHistory->getDescription());
                $shop->setTearms($letsbonusShopHistory->getConditions());
                $shop->setCashbackPrice($letsbonusShopHistory->getPrice());
                $shop->setCashbackPercentage($letsbonusShopHistory->getPercentage());
                $shop->setLetsBonusPercentage($letsbonusShopHistory->getLbpercentage());

                if (empty($letsbonusShopHistory->getLabelId())) {
                    $tags = $this->em->getRepository('iFlairLetsBonusAdminBundle:Tags')->findOneBy(
                        array(),
                        array('id' => 'ASC'),
                        0,
                        0
                    );
                    if (!empty($tags)) {
                        $shop->setTag($tags);
                    }
                } else {
                    $shop->setTag($tags);
                }
            } else {
                echo 'There is no shop history first load some shop history to continue';
                exit;
            }
        }*/

        $shop->setNetwork($network);
        $shop->setKeywords($letsbonusShop->getKeywords());
        // $shop->setBrand($letsbonusShop->getBrand());
        $VoucherPrograms = $this->em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')->findOneBy(array('nprogramId' => $letsbonusShop->getProgramId()));

        if (!empty($VoucherPrograms)) {
            $shop->setVprogram($VoucherPrograms);
            /* WAS COMMENTED ON ALKESH CODE :: NEED TO UNCOMMENT ON MY CODE :: YOGESH */
            $shop->setProgramId($letsbonusShop->getProgramId());
            /* WAS COMMENTED ON ALKESH CODE :: NEED TO UNCOMMENT ON MY CODE :: YOGESH */
        } else {
            $VoucherPrograms = $this->em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')->findOneBy(
                    array('network' => $network->getId()),
                    array('id' => 'ASC'),
                    0,
                    0
                );

            if (count($VoucherPrograms) > 1) {
                $shop->setVprogram($VoucherPrograms);
            }
        }


        $shop->setUrlAffiliate($letsbonusShop->getUrlAfiliacion());
        $shop->setDaysValidateConfirmation($letsbonusShop->getDaystoconfirm());
        $shop->setHighlightedHome(1);//iFlair::Please make it dynamic as per client Database, its very small and internal with shop not an external.
        $shop->setShopStatus(Shop::SHOP_DEACTIVATED);
        $shop->setInternalNotes($letsbonusShop->getInternalComments());
        $shop->setOffers('cashback');//iFlair::Its not availabel with client Database.
        /*$voucher = $this->em->getRepository('iFlairLetsBonusAdminBundle:Voucher')->findOneBy(array('id'=>1));//iFlair::currently set first voucher only as this is not available with client database.
        $shop->setVoucher($voucher);*/
        $shop->setExclusive(0);
        $shop->setHighlightedOffer(0);
        $shop->setStartDate($letsbonusShop->getStartDate());
        $shop->setEndDate($letsbonusShop->getEndDate());
        $this->em->persist($shop);
        $metadata = $this->em->getClassMetaData(get_class($shop));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $shop->getId();
    }
    protected function setVprogramFromShop()
    {
        $voucherDummy = new VoucherPrograms();

        $network = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );

        $voucherDummy->setNetwork($network);

        $media = $this->em->getRepository('ApplicationSonataMediaBundle:Media')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
        $voucherDummy->setImage($media);

        $voucherPrograms = $this->em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );

        if (count($voucherPrograms) > 0) {
            $voucherDummy->setNprogramId($voucherPrograms);
        } else {
            $voucherDummy->setNprogramId(0);
        }

        $voucherDummy->setProgramName('Test V Progam Id');
        $voucherDummy->setLogoPath('Test Path');
        $voucherDummy->setCreated(new \DateTime());
        $voucherDummy->setModified(new \DateTime());
        $this->em->persist($voucherDummy);
        $this->em->flush();

        return $voucherDummy;
    }
    protected function migrateMedia($name)
    {
        $mediaManager = $this->getContainer()->get('sonata.media.manager.media');
        $media = new Media();
        if ($name) {
            $media->setName($name);
            $media->setProviderReference($name);
        } else {
            $media->setName(0);
            $media->setProviderReference(0);
        }
        $media->setDescription(null);
        $media->setEnabled(true);
        $media->setProviderName('sonata.media.provider.image');
        $media->setProviderStatus(1);
        $media->setProviderMetadata(array());
        $media->setWidth(1);
        $media->setHeight(1);
        $media->setLength(null);
        $media->setContentType('image/jpeg');
        $media->setAuthorName(null);
        $media->setContext('default');
        $media->setCdnIsFlushable(null);
        $media->setCdnFlushAt(null);
        $media->setCdnStatus(null);
        $media->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $media->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $mediaManager->save($media);

        return $media;
    }
    protected function migrateTags($currentMigration)
    {
        $letsbonusLabels = $this->em
            ->getRepository('iFlairLetsBonusMigrationBundle:Labels', 'letbonus')
            ->findAll()
        ;
        foreach ($letsbonusLabels as $letsbonusLabel) {
            if (!$this->checkIFLabelsExistsByName($letsbonusLabel)) {
                $this->migrateTag($letsbonusLabel);
            } else {
                $this->removeTagAndUpdateId($letsbonusLabel);
            }
        }
    }
    protected function removeTagAndUpdateId($letsbonusLabel)
    {
        $label = $this->em->getRepository('iFlairLetsBonusAdminBundle:Tags')->findOneBy(array('name' => $letsbonusLabel->getTitle()));

        if ($label->getId() != $letsbonusLabel->getId()) {
            $this->em->remove($label);
            $this->em->flush();

            $this->migrateTag($letsbonusLabel);
        } else {
            $this->migrateTag($letsbonusLabel);
        }
    }
    protected function migrateTag($letsbonusLabel)
    {
        $tagsExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Tags')->findOneBy(array('id' => $letsbonusLabel->getId()));

        if ($tagsExist) {
            $tags = $tagsExist;
        } else {
            $tags = new Tags();
            $tags->setId($letsbonusLabel->getId());
        }

        $tags->setName($letsbonusLabel->getTitle());
        $tags->setCreated(new \DateTime(date('Y-m-d H:i:s', $letsbonusLabel->getCreated()->getTimestamp())));
        $tags->setModified(new \DateTime(date('Y-m-d H:i:s', $letsbonusLabel->getModified()->getTimestamp())));
        $this->em->persist($tags);

        $metadata = $this->em->getClassMetaData(get_class($tags));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $this->em->flush();
    }
    protected function checkIFLabelsExistsByName($letsbonusLabel)
    {
        return $label = $this->em->getRepository('iFlairLetsBonusAdminBundle:Tags')->findOneBy(array('name' => $letsbonusLabel->getTitle()));
    }
    protected function migrateGroups($currentMigration)
    {
        $letsbonusGroups = $this->em
            ->getRepository('iFlairLetsBonusMigrationBundle:Groups', 'letbonus')
            ->findAll()
        ;
        foreach ($letsbonusGroups as $letsbonusGroup) {
            if (!$this->checkIFGroupsExistsByName($letsbonusGroup, $this->em)) {
                $this->migrateGroup($letsbonusGroup, $this->em);
            } else {
                $this->removeGroupAndUpdateId($letsbonusGroup, $this->em);
            }
        }
    }

    protected function removeGroupAndUpdateId($letsbonusGroup)
    {
        $group = $this->em->getRepository('iFlairLetsBonusAdminBundle:Groups')->findOneBy(array('name' => $letsbonusGroup->getName()));

        if ($group->getId() != $letsbonusGroup->getId()) {
            $this->em->remove($group);
            $this->em->flush();

            $this->migrateGroup($letsbonusGroup, $this->em);
        } else {
            $this->migrateGroup($letsbonusGroup, $this->em);
        }
    }

    protected function migrateGroup($letsbonusGroup)
    {
        $groupExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Groups')->findOneBy(array('id' => $letsbonusGroup->getId()));

        if ($groupExist) {
            $groups = $groupExist;
        } else {
            $groups = new Groups();
            $groups->setId($letsbonusGroup->getId());
        }

        $groups->setName($letsbonusGroup->getName());
        $this->em->persist($groups);
        $metadata = $this->em->getClassMetaData(get_class($groups));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();
    }
    protected function checkIFGroupsExistsByName($letsbonusGroup)
    {
        return $group = $this->em->getRepository('iFlairLetsBonusAdminBundle:Groups')->findOneBy(array('name' => $letsbonusGroup->getName()));
    }
    protected function migrateAdministrators($currentMigration = null)
    {
        $letsbonusAdministrators = $this->em
            ->getRepository('iFlairLetsBonusMigrationBundle:Administrators', 'letbonus')
            ->findAll()
        ;
        if (!empty($letsbonusAdministrators)) {
            foreach ($letsbonusAdministrators as $letsbonusAdministrator) {
                if (!$this->checkIFAdministratorsExistsByEmail($letsbonusAdministrator)) {
                    $this->migrateAdministrator($letsbonusAdministrator);
                } else {
                    $this->removeAdministratorAndUpdateId($letsbonusAdministrator);
                }
            }
        } else {
            $administrator = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(array('email' => 'admin@gmail.com'));
            if (!$administrator) {
                $administrators = new Administrator();
                $administrators->setUsername('admin');
                $pass = md5('admin');
                $administrators->setPassword($pass);
                $administrators->setStatus(1);

                $group = $this->em->getRepository('iFlairLetsBonusAdminBundle:Groups')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );

                if (!empty($group)):
                    $administrators->setGroups($group); else:
                    $groups = new Groups();
                $groups->setName('letsbonus');
                $groups->setCreated(new \DateTime());
                $groups->setModified(new \DateTime());
                $this->em->persist($groups);
                $this->em->flush();
                $administrators->setGroups($groups);
                endif;
                $administrators->setEmail('admin@gmail.com');
                $administrators->setLastLogin(new \DateTime(date('Y-m-d H:i:s')));
                $administrators->setLastCompanyId(1);
                $this->em->persist($administrators);
                $this->em->flush();
            }
        }
    }
    protected function removeAdministratorAndUpdateId($letsbonusAdministrator)
    {
        $administrator = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(array('email' => $letsbonusAdministrator->getEmail()));

        if ($administrator->getId() != $letsbonusAdministrator->getId()) {
            $this->em->remove($administrator);
            $this->em->flush();
            $this->migrateAdministrator($letsbonusAdministrator);
        } else {
            $this->migrateAdministrator($letsbonusAdministrator);
        }
    }
    protected function migrateAdministrator($letsbonusAdministrator)
    {
        $administratorExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(array('id' => $letsbonusAdministrator->getId()));

        if ($administratorExist) {
            $administrators = $administratorExist;
        } else {
            $administrators = new Administrator();
            $administrators->setId($letsbonusAdministrator->getId());
        }

        if (!empty($letsbonusAdministrator->getUsername())):
            $administrators->setUsername($letsbonusAdministrator->getUsername()); else:
            $administrators->setUsername('');
        endif;

        $administrators->setPassword($letsbonusAdministrator->getPassword());

        $administrators->setStatus($letsbonusAdministrator->getEnabled());

        if (!empty($letsbonusAdministrator->getGroupId())):
            $administrators->setGroups($letsbonusAdministrator->getGroupId()); else:
            $administrators->setGroups(0);
        endif;

        if (!empty($letsbonusAdministrator->getEmail())) {
            $administrators->setEmail($letsbonusAdministrator->getEmail());
        } else {
            $administrators->setEmail('');
        }

        if (!empty($letsbonusAdministrator->getLastLogin())) {
            $administrators->setLastLogin($letsbonusAdministrator->getLastLogin());
        } else {
            $administrators->setLastLogin(new \DateTime());
        }

        if (!empty($letsbonusAdministrator->getLastcompanyid())) {
            $administrators->setLastCompanyId($letsbonusAdministrator->getLastcompanyid());
        } else {
            $administrators->setLastCompanyId(0);
        }

        $this->em->persist($administrators);
        $metadata = $this->em->getClassMetaData(get_class($administrators));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();
    }
    protected function checkIFAdministratorsExistsByEmail($letsbonusAdministrator)
    {
        return $administrator = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(array('email' => $letsbonusAdministrator->getEmail()));
    }

    protected function migrateShopHistories($currentMigration)
    {   
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusShopHistories = $this->queryBuilder
                                ->select('sh')
                                ->from('iFlairLetsBonusMigrationBundle:Shopshistories',  'sh')
                                ->where('sh.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();                                

        $migrationComleted = 0;
        $iterableResultOfShopHistories = $letsbonusShopHistories->iterate();
        while (($letsbonusShopHistory = $iterableResultOfShopHistories->next()) !== false) {
            $id = $this->migrateShopHistory($letsbonusShopHistory[0]);
                $this->progress->advance();            
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
            $this->em->detach($letsbonusShopHistory[0]);
        }

        $this->em->persist($currentMigration);
        $this->em->flush();

        
        return $migrationComleted;
    }
    protected function removeShopHistoryAndUpdateId($letsbonusShopHistory)
    {
        $shopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('title' => $letsbonusShopHistory->getTitle()));

        if ($shopHistory->getId() != $letsbonusShopHistory->getId()) {
            $reviews = $this->em->getRepository('iFlairLetsBonusFrontBundle:Review')->findBy(array('shopHistoryId' => $shopHistory->getId()));

            foreach ($reviews as $review) {
                $this->em->remove($review);
                $this->em->flush();
            }

            $transactions = $this->em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findBy(array('shopHistory' => $shopHistory->getId()));

            foreach ($transactions as $transaction) {
                $this->em->remove($transaction);
                $this->em->flush();
            }

            $this->em->remove($shopHistory);
            $this->em->flush();

            return $this->migrateShopHistory($letsbonusShopHistory);
        } else {
            return $this->migrateShopHistory($letsbonusShopHistory);
        }
    }
    protected function migrateShopHistory($letsbonusShopHistory)
    {
        $shopHistoryExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $letsbonusShopHistory->getId()));

        if ($shopHistoryExist) {
            $shopHistory = $shopHistoryExist;
        } else {
            $shopHistory = new shopHistory();
            $shopHistory->setId($letsbonusShopHistory->getId());
        }

        $shop = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $letsbonusShopHistory->getShopId()));

        if ($shop) {
            $shopHistory->setShop($shop);
        } else {
            $shop = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
            $shopHistory->setShop($shop);
        }

        $administrator = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(array('id' => $letsbonusShopHistory->getAdministratorId()));

        if ($administrator) {
            $shopHistory->setAdministrator($administrator);
        } else {
            $administrator = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(array('email' => 'admin@gmail.com'));
            if (empty($administrator)) {
                $shopHistory->setAdministrator($this->setShophistoryAdministrator());
            } else {
                $shopHistory->setAdministrator($administrator);
            }
        }
        if (!empty($letsbonusShopHistory->getTitle())) {
            $shopHistory->setTitle($letsbonusShopHistory->getTitle());
        } else {
            $shopHistory->setTitle('');
        }

        if (!empty($letsbonusShopHistory->getUrl())) {
            $shopHistory->setUrl($letsbonusShopHistory->getUrl());
        } else {
            $shopHistory->setUrl('');
        }
        if (!empty($letsbonusShopHistory->getIntro())) {
            $shopHistory->setIntroduction($letsbonusShopHistory->getIntro());
        } else {
            $shopHistory->setIntroduction('');
        }
        if (!empty($letsbonusShopHistory->getDescription())) {
            $shopHistory->setDescription($letsbonusShopHistory->getDescription());
        } else {
            $shopHistory->setDescription('');
        }
        if (!empty($letsbonusShopHistory->getConditions())) {
            $shopHistory->setTearms($letsbonusShopHistory->getConditions());
        } else {
            $shopHistory->setTearms('');
        }

        if (!empty($letsbonusShopHistory->getPrice())) {
            $shopHistory->setCashbackPrice($letsbonusShopHistory->getPrice());
        } else {
            $shopHistory->setCashbackPrice(0);
        }

        if (!empty($letsbonusShopHistory->getPercentage())) {
            $shopHistory->setCashbackPercentage($letsbonusShopHistory->getPercentage());
        } else {
            $shopHistory->setCashbackPercentage(0);
        }

        if (!empty($letsbonusShopHistory->getLbpercentage())) {
            $shopHistory->setLetsBonusPercentage($letsbonusShopHistory->getLbpercentage());
        } else {
            $shopHistory->setLetsBonusPercentage(0);
        }

        if (!empty($letsbonusShopHistory->getUrlAfiliacion())) {
            $shopHistory->setUrlAffiliate($letsbonusShopHistory->getUrlAfiliacion());
        } else {
            $shopHistory->setUrlAffiliate('');
        }

        if (!empty($letsbonusShopHistory->getStartDate())) {
            $shopHistory->setStartDate($letsbonusShopHistory->getStartDate());
        } else {
            $shopHistory->setStartDate(new \DateTime());
        }

      /*  if (!empty($letsbonusShopHistory->getEndDate())) {
            $shopHistory->setEndDate($letsbonusShopHistory->getEndDate());
        } else {
            $shopHistory->setEndDate(new \DateTime());
        }*/

        /*if (!$letsbonusShopHistory->getLabelId() || $letsbonusShopHistory->getLabelId() == null) {
            $tag = $this->em->getRepository('iFlairLetsBonusAdminBundle:Tags')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
            $shopHistory->setTag($tag);
        } else {
            $tag = $this->em->getRepository('iFlairLetsBonusAdminBundle:Tags')->findOneBy(array('id' => $letsbonusShopHistory->getLabelId()));
            $shopHistory->setTag($tag);
        }
        $shopHistory->setTag("");*/

        $shopHistory->getPrevLabelCrossedOut($letsbonusShopHistory->getStrikelabel());
        $shopHistory->setShippingInfo($letsbonusShopHistory->getDeliveryinfo());
        $this->em->persist($shopHistory);
        $metadata = $this->em->getClassMetaData(get_class($shopHistory));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $shopHistory->getId();
    }

    protected function setShophistoryAdministrator()
    {
        $administrator = new Administrator();
        $administrator->setUsername('admin');
        $administrator->setPassword(md5('admin'));
        $administrator->setStatus(1);
        //$administrator->setGroups(1);
        $administrator->setEmail('admin@gmail.com');
        $administrator->setLastLogin(new \DateTime());
        $administrator->setLastCompanyId(1);
        $administrator->setCreated(new \DateTime());
        $administrator->setModified(new \DateTime());
        $this->em->persist($administrator);
        $this->em->flush();

        return $administrator;
    }

    protected function checkIFGroupsExistsByTitle($letsbonusShopHistory)
    {
        return $ShopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('title' => $letsbonusShopHistory->getTitle()));
    }
    protected function migrateVariations($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusVariations = $this->queryBuilder
                                ->select('v')
                                ->from('iFlairLetsBonusMigrationBundle:Variations',  'v')
                                ->where('v.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();

        $migrationComleted = 0;
        $iterableResultOfVariations = $letsbonusVariations->iterate();
        while (($letsbonusVariation = $iterableResultOfVariations->next()) !== false) {
            $id = $this->migrateVariation($letsbonusVariation[0]);
            $this->progress->advance();

            if($lastId == $id) {
                $migrationComleted = 1;
            } 
            $currentMigration->setLastProcessedId($id);
            unset($id);
            $this->em->detach($letsbonusVariation[0]);
        }        

        $this->em->persist($currentMigration);
        $this->em->flush();

        return $migrationComleted;
    }
    protected function removeVariationAndUpdateId($letsbonusVariation)
    {
        $variation = $this->em->getRepository('iFlairLetsBonusAdminBundle:Variation')->findOneBy(array('title' => $letsbonusVariation->getLabel()));
        $shopVariation = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopVariation')->findOneBy(array('title' => $letsbonusVariation->getLabel()));

        if ($variation->getId() != $letsbonusVariation->getId()) {
            $this->em->remove($variation);
            $this->em->flush();
            $this->em->remove($shopVariation);
            $this->em->flush();
            $id = $this->migrateVariation($letsbonusVariation);
        } else {
            $id = $this->migrateVariation($letsbonusVariation);
        }

        return $id;
    }
    protected function migrateVariation($letsbonusVariation)
    {
        $variationExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Variation')->findOneBy(array('id' => $letsbonusVariation->getId()));

        if ($variationExist) {
            $variation = $variationExist;
        } else {
            $variation = new Variation();
            $variation->setId($letsbonusVariation->getId());
        }

        $shopVariationExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopVariation')->findOneBy(array('id' => $letsbonusVariation->getId()));

        if ($shopVariationExist) {
            $shopVariation = $shopVariationExist;
        } else {
            $shopVariation = new shopVariation();
            $shopVariation->setId($letsbonusVariation->getId());
        }

        $shopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $letsbonusVariation->getShopshistoryId()));

        $shopId = '';
        if (!empty($shopHistory)) {
            $shopId = $shopHistory->getShop();
            if (!empty($shopId)) {
                $shopVariation->setShop($shopId);
            } else {
                $shopVariation->setShop('');
            }
            $variation->setShopHistory($shopHistory);
        } else {
            $shopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
            $shopId = $shopHistory->getShop();
            if (!empty($shopId)) {
                $shopVariation->setShop($shopId);
            } else {
                $shopVariation->setShop('');
            }
            $variation->setShopHistory($shopHistory);
        }

        if (!empty($letsbonusVariation->getPercentage())) {
            $variation->setNumber($letsbonusVariation->getPercentage());
            $shopVariation->setNumber($letsbonusVariation->getPercentage());
        } else {
            $variation->setNumber(0);
            $shopVariation->setNumber(0);
        }

        if (!empty($letsbonusVariation->getLabel())) {
            $variation->setTitle($letsbonusVariation->getLabel());
            $shopVariation->setTitle($letsbonusVariation->getLabel());
        } else {
            $variation->setTitle('');
            $shopVariation->setTitle('');
        }



        $date = str_replace('/', '.', $letsbonusVariation->getDate());
        $dateArray = explode('.', $letsbonusVariation->getDate());
        $countOfDate = count($dateArray);
        
        $isValidDate = false;
        if($countOfDate == 3) {
            $isValidDate = true;
        } else {
            $isValidDate = false;
        }

        if (!empty($letsbonusVariation->getDate()) && $isValidDate):
            $variation->setDate(new \DateTime($letsbonusVariation->getDate()));
            $shopVariation->setDate(new \DateTime($letsbonusVariation->getDate())); 
        else:
            $variation->setDate(new \DateTime());
            $shopVariation->setDate(new \DateTime());
        endif;

        $this->em->persist($shopVariation);
        $metadata = $this->em->getClassMetaData(get_class($shopVariation));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        $variation->setShopVariationId($shopVariation->getId());

        $this->em->persist($variation);
        $metadata = $this->em->getClassMetaData(get_class($variation));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $variation->getId();
    }
    protected function checkIFVariationsExistsByTitle($letsbonusVariation)
    {
        return $Variations = $this->em->getRepository('iFlairLetsBonusAdminBundle:Variation')->findOneBy(array('title' => $letsbonusVariation->getLabel()));
    }
    protected function migrateFrontendUsers($currentMigration)
    {

        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();        

        $letsbonusUsers = $this->queryBuilder
                                ->select('u')
                                ->from('iFlairLetsBonusMigrationBundle:Users',  'u')
                                ->where('u.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();
        $migrationComleted = 0;
        $iterableResultOfUsers = $letsbonusUsers->iterate();
        while (($letsbonusUser = $iterableResultOfUsers->next()) !== false) {
            $id = $this->migrateFrontendUser($letsbonusUser[0]);
            $this->progress->advance();
            if($lastId == $id) {
                $migrationComleted = 1;
            }

            $currentMigration->setLastProcessedId($id);
            $this->em->detach($letsbonusUser[0]);
        }


        $this->em->persist($currentMigration);        
        $this->em->flush();
        $this->em->clear();
        return $migrationComleted;        
    }
    protected function removeFrontendUserAndUpdateId($letsbonusUser)
    {
        $frontUser = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('email' => $letsbonusUser->getEmail()));

        if ($frontUser->getId() != $letsbonusUser->getId()) {
            $this->em->remove($frontUser);
            $this->em->flush();
            $id = $this->migrateFrontendUser($letsbonusUser);
        } else {
            $id = $this->migrateFrontendUser($letsbonusUser);
        }
    }
    protected function checkIFUsersExistsByEmail($letsbonusUser)
    {
        return $ShopUsers = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('email' => $letsbonusUser->getEmail()));
    }

    protected function migrateFrontendUser($letsbonusUser)
    {
        $frontUserExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('email' => $letsbonusUser->getEmail()));
        $frontUser="";


        if ($frontUserExist) {
            if ($frontUserExist->getIsShoppiday() == 0) {
                $frontUser = $frontUserExist;
            } else {
                return $letsbonusUser->getId();
            }
            if($frontUserExist->getId() != $letsbonusUser->getId()) {
                $frontUserWithSameIdExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => $letsbonusUser->getId()));
                if($frontUserWithSameIdExist) {
                    $connection = $this->em->getConnection();
                    $query = $connection->prepare('SELECT MAX(id) as id FROM lb_front_user');
                    $query->execute();
                    $lastIndexid = $query->fetchAll();
                    $idToUpdateWith = $lastIndexid[0]['id'] + 1;
                    $frontUserWithSameIdExist->setId($idToUpdateWith);
                    $this->em->persist($frontUserWithSameIdExist);
                    $metadata = $this->em->getClassMetaData(get_class($frontUserWithSameIdExist));
                    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
                    $this->em->flush();
                    $frontUser = new FrontUser();
                }
            }
        } else {
            $frontUserWithSameIdExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => $letsbonusUser->getId()));
                if($frontUserWithSameIdExist) {
                    $connection = $this->em->getConnection();
                    $query = $connection->prepare('SELECT MAX(id) as id FROM lb_front_user');
                    $query->execute();
                    $lastIndexid = $query->fetchAll();
                    $idToUpdateWith = $lastIndexid[0]['id'] + 1;
                    $frontUserWithSameIdExist->setId($idToUpdateWith);
                    $this->em->persist($frontUserWithSameIdExist);
                    $metadata = $this->em->getClassMetaData(get_class($frontUserWithSameIdExist));
                    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
                    $this->em->flush();
                    $frontUser = new FrontUser();
                }
            
            $frontUser = new FrontUser();
        }
        $frontUser->setId($letsbonusUser->getId());
        $frontUser->setName($letsbonusUser->getName());
        $frontUser->setSurname($letsbonusUser->getSurname());
        $frontUser->setEmail($letsbonusUser->getEmail());
        $frontUser->setEnabled($letsbonusUser->getEnabled());
        $frontUser->setIsShoppiday(0);
        $frontUser->setApiFlag(1);
        $frontUser->setLoginType("1");

        $fromCompany = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Companies')->findOneBy(array('id' => $letsbonusUser->getCompanyId()));

        $frontUser->setCompanyId(0);
        if(!empty($fromCompany)) {
            $company = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('name' => $fromCompany->getName()));
            if (!empty($company)) {
                $frontUser->setCompanyId($company);
            }
        }


        $frontUser->setUserCreateDate($letsbonusUser->getUsercreatedate());
        $frontUser->setUserType($letsbonusUser->getUsertype());
        if(!empty($letsbonusUser->getUsergender())) {
            $frontUser->setUserGender($letsbonusUser->getUsergender());
        } else {
            $frontUser->setUserGender(0);
        }
        $frontUser->setUserBirthDate($letsbonusUser->getUserbirthdate());

        $this->em->persist($frontUser);
        $metadata = $this->em->getClassMetaData(get_class($frontUser));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();
        return $letsbonusUser->getId();
    }

    protected function migrateFirstLevelCatgories($currentMigration)
    {
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusCategories = $this->queryBuilder
                                ->select('c')
                                ->from('iFlairLetsBonusMigrationBundle:Categories',  'c')
                                ->where('c.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();                                

        $lastId = $currentMigration->getLastId();
        
        $migrationComleted = 0;
        $iterableResultOfCategories = $letsbonusCategories->iterate();
        while (($letsbonusCategory = $iterableResultOfCategories->next()) !== false) {
            if (!$this->checkIFCategoryExistsByTitle($letsbonusCategory[0])) {
                $id = $this->migrateFirstLevelCatgory($letsbonusCategory[0]);
            } else {
                $id = $this->removeFirstLevelCatgoryAndUpdateId($letsbonusCategory[0]);
            }
            $this->progress->advance();
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
            $this->em->detach($letsbonusCategory[0]);
        }        

        $this->em->persist($currentMigration);
        $this->em->flush();

        return $migrationComleted; 
        
    }
    protected function removeFirstLevelCatgoryAndUpdateId($letsbonusCategory)
    {
        $ShopCategory = $this->em->getRepository('iFlairLetsBonusAdminBundle:parentCategory')->findOneBy(array('name' => $letsbonusCategory->getName()));

        if ($ShopCategory->getId() != $letsbonusCategory->getId()) {
            $this->em->remove($ShopCategory);
            $this->em->flush();
            $id = $this->migrateFirstLevelCatgory($letsbonusCategory);
        } else {
            $id = $this->migrateFirstLevelCatgory($letsbonusCategory);
        }

        return $id;
    }
    protected function checkIFCategoryExistsByTitle($letsbonusCategory)
    {
        return $ShopCategory = $this->em->getRepository('iFlairLetsBonusAdminBundle:parentCategory')->findOneBy(array('name' => $letsbonusCategory->getName()));
    }
    protected function migrateFirstLevelCatgory($letsbonusCategory)
    {
        if (!empty($letsbonusCategory->getHeaderimage())):
            $headerImageLink = $letsbonusCategory->getHeaderimage();
        $headerImage = explode('http://lbcashback.s3.amazonaws.com', $headerImageLink);
        $headerImageURL = $headerImage[1]; else:
            $headerImageURL = '/categories/2015/06/viajes.jpg';
        endif;

        $parentCategoryExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:parentCategory')->findOneBy(array('id' => $letsbonusCategory->getId()));

        if ($parentCategoryExist) {
            $parentCategory = $parentCategoryExist;
        } else {
            $parentCategory = new parentCategory();
            $parentCategory->setId($letsbonusCategory->getId());
        }

        $parentCategory->setName($letsbonusCategory->getName());
        $imageId = $this->setCategoryMedia($headerImageURL);
        $parentCategory->setnImage($imageId);

        /*$parentCategory->setLink($letsbonusCategory->getHeaderimagelink());*/
        $parentCategory->setStatus($letsbonusCategory->getStatus());
        $parentCategory->setUrl(' ');
        $parentCategory->setBannerDescription(' ');
        $parentCategory->setBannerTitle(' ');
        $this->em->persist($parentCategory);
        $metadata = $this->em->getClassMetaData(get_class($parentCategory));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $parentCategory->getId();
    }
    protected function setCategoryMedia($categoryImagePath)
    {
        $media = new Media();
        $media->setName($categoryImagePath);
        $media->setProviderReference($categoryImagePath);
        $media->setDescription(null);
        $media->setEnabled(true);
        $media->setProviderName('sonata.media.provider.image');
        $media->setProviderStatus(1);
        $media->setProviderMetadata(array());
        $media->setWidth(1);
        $media->setHeight(1);
        $media->setLength(null);
        $media->setContentType('image/jpeg');
        $media->setAuthorName(null);
        $media->setContext('default');
        $media->setCdnIsFlushable(null);
        $media->setCdnFlushAt(null);
        $media->setCdnStatus(null);
        $media->setUpdatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $media->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $this->em->persist($media);
        $this->em->flush();

        return $media;
    }
    protected function migrateCollections($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusCollections = $this->queryBuilder
                                ->select('cl')
                                ->from('iFlairLetsBonusMigrationBundle:Collections',  'cl')
                                ->where('cl.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery()
                                ->getResult();

        
        $migrationComleted = 0;
        foreach ($letsbonusCollections as $letsbonusCollection) {
            if (!$this->checkIFCollectionExistsByName($letsbonusCollection)) {
                $id = $this->migrateCollection($letsbonusCollection);
            } else {
                $id = $this->removeCollectionAndUpdateId($letsbonusCollection);
            }
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
        }

        $this->em->persist($currentMigration);
        $this->em->flush();

        return $migrationComleted;
    }
    protected function removeCollectionAndUpdateId($letsbonusCollection)
    {
        $ShopCollection = $this->em->getRepository('iFlairLetsBonusAdminBundle:Collection')->findOneBy(array('name' => $letsbonusCollection->getName()));

        if ($ShopCollection->getId() != $letsbonusCollection->getId()) {
            $this->em->remove($ShopCollection);
            $this->em->flush();
            $id = $this->migrateCollection($letsbonusCollection);
        } else {
            $id = $this->migrateCollection($letsbonusCollection);
        }
        return $id;
    }
    protected function migrateCollection($letsbonusCollection)
    {
        $collectionExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Collection')->findOneBy(array('id' => $letsbonusCollection->getId()));

        if ($collectionExist) {
            $collection = $collectionExist;
        } else {
            $collection = new Collection();
            $collection->setId($letsbonusCollection->getId());
        }

        $collection->setName($letsbonusCollection->getName());
        $collection->setMarkSpecial(' ');
        $collection->setStatus($letsbonusCollection->getStatus());
        $collection->setShowInFront(1);
        $this->em->persist($collection);
        $metadata = $this->em->getClassMetaData(get_class($collection));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $collection->getId();
    }
    protected function checkIFCollectionExistsByName($letsbonusCollection)
    {
        return $ShopCollection = $this->em->getRepository('iFlairLetsBonusAdminBundle:Collection')->findOneBy(array('name' => $letsbonusCollection->getName()));
    }

    protected function migrateClicks($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusClicks = $this->queryBuilder
                                ->select('clk')
                                ->from('iFlairLetsBonusMigrationBundle:Clicks',  'clk')
                                ->where('clk.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery()
                                ->getResult();

        
        $migrationComleted = 0;
        foreach ($letsbonusClicks as $letsbonusClick) {
            $id = $this->migrateClick($letsbonusClick);
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
        }

        $this->em->persist($currentMigration);
        $this->em->flush();
        $this->em->clear();

        return $migrationComleted;
    }
    protected function migrateClick($letsbonusClick)
    {
        $clickExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Clicks')->findOneBy(array('id' => $letsbonusClick->getId()));

        if ($clickExist) {
            $click = $clickExist;
        } else {
            $click = new Clicks();
            $click->setId($letsbonusClick->getId());
        }

        if (!empty($this->letsBonusClickShop($letsbonusClick->getShopId()))) {
            $click->setShopId($this->letsBonusClickShop($letsbonusClick->getShopId()));
        } else {
            $click->setShopId($this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneById(8));
        }

        if (!empty($this->letsBonusClickUser($letsbonusClick->getUserId()))) {
            $click->setUserId($this->letsBonusClickUser($letsbonusClick->getUserId()));
        } else {
            $click->setUserId($this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneById(146611));
        }

        if (!empty($this->letsBonusClickShopHistory($letsbonusClick->getShopshistoryId()))) {
            $click->setShopshistoryId($this->letsBonusClickShopHistory($letsbonusClick->getShopshistoryId()));
        } else {
            $click->setShopshistoryId($this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneById(11107)->getId());
        }

        $click->setType($letsbonusClick->getType());
        $click->setTabType($letsbonusClick->getTabType());
        $click->setTabId($letsbonusClick->getTabId());
        $click->setTabPosition($letsbonusClick->getTabPosition());
        $click->setIp($letsbonusClick->getIp());
        $click->setUserAgent($letsbonusClick->getUserAgent());
        $click->setCreated($letsbonusClick->getCreated());
        $click->setModified($letsbonusClick->getModified());
        $fromCompany = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Companies')->findOneBy(array('id' => $letsbonusClick->getCompanyId()));
        $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('name' => $fromCompany->getName()));
        $click->setCompanyId($companies->getId());
        $this->em->persist($click);
        $metadata = $this->em->getClassMetaData(get_class($click));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $click->getId();
    }

    protected function letsBonusClickShop($id)
    {
        if (!empty($id)):
            return $this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneById(trim($id)); else:
            return $this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneById(1);
        endif;
    }
    protected function letsBonusClickUser($id)
    {
        if (!empty($id)):
            return $this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneById($id); else:
            return $this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneById(1);
        endif;
    }
    protected function letsBonusClickShopHistory($id)
    {
        if (!empty($id)):
            return $this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneById($id); else:
            return $this->getContainer()->get('doctrine')->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneById(1);
        endif;
    }

    protected function migrateSearchlogs($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusSearchlogs = $this->queryBuilder
                                ->select('sl')
                                ->from('iFlairLetsBonusMigrationBundle:Searchlogs',  'sl')
                                ->where('sl.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery()
                                ->getResult();

        
        $migrationComleted = 0;
        foreach ($letsbonusSearchlogs as $letsbonusSearchlog) {
            $id = $this->migrateSearchlog($letsbonusSearchlog);
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
        }

        $this->em->persist($currentMigration);
        $this->em->flush();

        return $migrationComleted;
    }

    protected function migrateSearchlog($letsbonusSearchlog)
    {
        $searchlogExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:Searchlogs')->findOneBy(array('id' => $letsbonusSearchlog->getId()));

        if ($searchlogExist) {
            $searchlog = $searchlogExist;
        } else {
            $searchlog = new Searchlogs();
            $searchlog->setId($letsbonusSearchlog->getId());
        }
        $searchlog->setIdClient($letsbonusSearchlog->getIdclient());
        $searchlog->getIdCity($letsbonusSearchlog->getIdcity());
        $searchlog->setLatitude($letsbonusSearchlog->getLatitude());
        $searchlog->setLongitude($letsbonusSearchlog->getLongitude());
        $searchlog->setTerm($letsbonusSearchlog->getTerm());
        $searchlog->setCleanedTerm($letsbonusSearchlog->getCleanedterm());
        $searchlog->setSearchFrom($letsbonusSearchlog->getSearchfrom());
        $searchlog->setVertical($letsbonusSearchlog->getVertical());
        $searchlog->setBreadcrumb($letsbonusSearchlog->getBreadcrumb());
        $searchlog->setSearchApp($letsbonusSearchlog->getSearchapp());
        $searchlog->setIpAddress($letsbonusSearchlog->getIpaddress());
        $searchlog->setInternalSearch($letsbonusSearchlog->getInternalsearch());
        $searchlog->setSearchedDate($letsbonusSearchlog->getSearcheddate());

        $this->queryBuilder->resetDQLParts();
        $this->queryBuilder->select('COUNT(a)')->from('iFlairLetsBonusAdminBundle:Searchlogs','a');
        $this->queryBuilder->where('a.term = :term');
        $this->queryBuilder->setParameter('term', $letsbonusSearchlog->getTerm());
        $count = $this->queryBuilder->getQuery()->getSingleScalarResult();
        
        /*$searchlog->setSearchedDate(new \DateTime($row['searchedDate']));*/
        $searchlog->setNumSearch($count);
        $searchlog->setNumResults($letsbonusSearchlog->getResults());
        $this->em->persist($searchlog);
        $metadata = $this->em->getClassMetaData(get_class($searchlog));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $searchlog->getId();
    }

    protected function migrateCashbackSettingsShops($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusCashbackSettingsShops = $this->queryBuilder
                                ->select('cs')
                                ->from('iFlairLetsBonusMigrationBundle:CashbacksettingsShops',  'cs')
                                ->where('cs.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();

        $migrationComleted = 0;
        $iterableResultOfCashbackSettingsShops = $letsbonusCashbackSettingsShops->iterate();
        while (($letsbonusCashbackSettingsShop = $iterableResultOfCashbackSettingsShops->next()) !== false) {
            $this->migrateCashbackSettingsShop($letsbonusCashbackSettingsShop[0]);
            $this->progress->advance();
            $id = $letsbonusCashbackSettingsShop[0]->getId();
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
            $this->em->detach($letsbonusCashbackSettingsShop[0]);
        }        

        $this->em->persist($currentMigration);
        $this->em->flush();

        return $migrationComleted;
    }

    protected function migrateCashbackSettingsShop($letsbonusCashbackSettingsShop)
    {
        $connection = $this->em->getConnection();
        $query = $connection->prepare('SELECT css.* FROM lb_cachback_settings_shop as css 
                                        WHERE css.cashback_settings_id = :cashbackSettingsId 
                                        AND css.shop_id = :shopId'
                                    );

        $query->bindValue('cashbackSettingsId', $letsbonusCashbackSettingsShop->getCashbacksettingId());
        $query->bindValue('shopId', $letsbonusCashbackSettingsShop->getShopId());
        $query->execute();
        $cashbackSettingsShop = $query->fetchAll();
        
        if(empty($cashbackSettingsShop)) {
            $shop = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $letsbonusCashbackSettingsShop->getShopId()
                ));
            if(!empty($shop)) {
                $query = $connection->prepare('INSERT INTO lb_cachback_settings_shop (cashback_settings_id,shop_id)
                                                VALUES(:cashbackSettingsId,:shopId);'
                                            );

                $query->bindValue('cashbackSettingsId', $letsbonusCashbackSettingsShop->getCashbacksettingId());
                $query->bindValue('shopId', $letsbonusCashbackSettingsShop->getShopId());
                $query->execute();
            }

        }

    }
    protected function migrateCashbackSettings($currentMigration)
    {

        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusCashbackSettings = $this->queryBuilder
                                ->select('cs')
                                ->from('iFlairLetsBonusMigrationBundle:Cashbacksettings',  'cs')
                                ->where('cs.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();

        
        $migrationComleted = 0;
        $iterableResultOfCashbackSettings = $letsbonusCashbackSettings->iterate();
        while (($letsbonusCashbackSetting = $iterableResultOfCashbackSettings->next()) !== false) {
            $id = $this->migrateCashbackSetting($letsbonusCashbackSetting[0]);
            $this->progress->advance();
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
            $this->em->detach($letsbonusCashbackSetting[0]);
        }        

        $this->em->persist($currentMigration);
        $this->em->flush();

        return $migrationComleted;
    }
    protected function removeCashbackSettingAndUpdateId($letsbonusCashbackSetting)
    {
        $CashbackSetting = $this->em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings')->findOneBy(array('name' => $letsbonusCashbackSetting->getName()));

        if ($CashbackSetting->getId() != $letsbonusCashbackSetting->getId()) {
            $this->em->remove($CashbackSetting);
            $this->em->flush();
            $id = $this->migrateCollection($letsbonusCashbackSetting);
        } else {
            $id = $this->migrateCollection($letsbonusCashbackSetting);
        }
        return $id;
    }
    protected function checkIFCashbackSettingsExistsByName($letsbonusCashbackSetting)
    {
        return $CashbackSetting = $this->em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings')->findOneBy(array('name' => $letsbonusCashbackSetting->getName()));
    }

    protected function migrateCashbackSetting($letsbonusCashbackSetting)
    {
        $cashbackSettingExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings')->findOneBy(array('id' => $letsbonusCashbackSetting->getId()));

        if ($cashbackSettingExist) {
            $cashbackSetting = $cashbackSettingExist;
        } else {
            $cashbackSetting = new cashbackSettings();
            $cashbackSetting->setId($letsbonusCashbackSetting->getId());
        }

        $fromCompany = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Companies')->findOneBy(array('id' => $letsbonusCashbackSetting->getCompanyId()));
        $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('name' => $fromCompany->getName()));

        if ($companies) {
            $cashbackSetting->setCompanies($companies);
        } else {
            $companies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );
            $cashbackSetting->setCompanies($companies);
        }
        $cashbackSetting->setName($letsbonusCashbackSetting->getName());
        $cashbackSetting->setType($letsbonusCashbackSetting->getType());
        $cashbackSetting->setStartDate($letsbonusCashbackSetting->getStartDate());
        $cashbackSetting->setEndDate($letsbonusCashbackSetting->getEndDate());
        $status = 0;
        if (!(is_null($letsbonusCashbackSetting->getStatus()))) {
            $status = $letsbonusCashbackSetting->getStatus();
        }
        $cashbackSetting->setStatus($status);
        $administrator = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(array('id' => $letsbonusCashbackSetting->getAdministratorId()));
        if ($administrator) {
            $cashbackSetting->setAdministrator($administrator);
        } else {
            $administrator = $this->em->getRepository('iFlairLetsBonusAdminBundle:Administrator')->findOneBy(
                    array(),
                    array('id' => 'ASC'),
                    0,
                    0
                );

            $cashbackSetting->setAdministrator($administrator);
        }
        $this->em->persist($cashbackSetting);

        $metadata = $this->em->getClassMetaData(get_class($cashbackSetting));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $this->em->flush();

        return $cashbackSetting->getId();
    }
    protected function migrateCashbackTransactions($currentMigration)
    {
        $lastId = $currentMigration->getLastId();
        $lastProcessedID = ($currentMigration->getLastProcessedId())?$currentMigration->getLastProcessedId():0;
        $this->queryBuilder->resetDQLParts();
        $letsbonusCashbacktransactions = $this->queryBuilder
                                ->select('ct')
                                ->from('iFlairLetsBonusMigrationBundle:Cashbacktransactions',  'ct')
                                ->where('ct.id > :nid')
                                ->setParameter('nid', $lastProcessedID)
                                ->setMaxResults($this->limit)
                                ->getQuery();

        
        $migrationComleted = 0;
        $iterableResultOfCashbacktransactions = $letsbonusCashbacktransactions->iterate();
        while (($letsbonusCashbacktransaction = $iterableResultOfCashbacktransactions->next()) !== false) {
            $id = $this->migrateCashbacktransaction($letsbonusCashbacktransaction[0]);
            $this->progress->advance();
            if($lastId == $id) {
                $migrationComleted = 1;
            } 

            $currentMigration->setLastProcessedId($id);
            $this->em->detach($letsbonusCashbacktransaction[0]);
        }        

        $this->em->persist($currentMigration);
        $this->em->flush();
        $this->em->clear();

        return $migrationComleted;
    }
    protected function checkIFCashbackTransactionExistsById($letsbonusCashbacktransaction)
    {
        return $ShopCategory = $this->em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => $letsbonusCashbacktransaction->getId()));
    }
    protected function migrateCashbackTransaction($letsbonusCashbacktransaction)
    {
        $cashbacktransactionExist = $this->em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => $letsbonusCashbacktransaction->getId()));


        if ($cashbacktransactionExist) {
            $cashbackTransaction = $cashbacktransactionExist;
        } else {
            $cashbackTransaction = new cashbackTransactions();
            $cashbackTransaction->setId($letsbonusCashbacktransaction->getId());
        }

        $shop = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $letsbonusCashbacktransaction->getShopId()));
        if (empty($shop)) {
            $shop = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(
                        array(),
                        array('id' => 'ASC'),
                        0,
                        0
                    );
        }
        $cashbackTransaction->setShopId($shop);

        $shoppidayShopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id'=>$letsbonusCashbacktransaction->getShopshistoryId()));
        if($shoppidayShopHistory){
            $cashbackTransaction->setShopHistory($shoppidayShopHistory);
        }else{
            $cashbackTransaction->setShopHistory(NULL);
        }

        $frontUser = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => $letsbonusCashbacktransaction->getUserId()));
        if (empty($frontUser)) {
            $frontUser = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(
                        array(),
                        array('id' => 'ASC'),
                        0,
                        0
                    );
        }
        $cashbackTransaction->setUserId($frontUser);

        if (!empty($letsbonusCashbacktransaction->getTransactionId())) {
            $cashbackTransaction->setTransactionId($letsbonusCashbacktransaction->getTransactionId());
        } else {
            $cashbackTransaction->setTransactionId('');
        }

        
        $fromNetwork = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Networks')->findOneBy(array('id' => $letsbonusCashbacktransaction->getNetworkId()));

        if(!empty($fromNetwork)) {
            $network = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => $fromNetwork->getName()));
            $cashbackTransaction->setNetworkId($network);
        }

        if (!empty($letsbonusCashbacktransaction->getAmount())) {
            $cashbackTransaction->setAmount($letsbonusCashbacktransaction->getAmount());
        } else {
            $cashbackTransaction->setAmount(0);
        }

        if (!empty($letsbonusCashbacktransaction->getAffiliateAmount())) {
            $cashbackTransaction->setAffiliateAmount($letsbonusCashbacktransaction->getAffiliateAmount());
        } else {
            $cashbackTransaction->setAffiliateAmount(0);
        }

        if (!empty($letsbonusCashbacktransaction->getTotalAffiliateAmount())) {
            $cashbackTransaction->setTotalAffiliateAmount($letsbonusCashbacktransaction->getTotalAffiliateAmount());
        } else {
            $cashbackTransaction->setTotalAffiliateAmount(0);
        }

        if (!empty($letsbonusCashbacktransaction->getLetsbonusPct())) {
            $cashbackTransaction->setLetsbonusPct($letsbonusCashbacktransaction->getLetsbonusPct());
        } else {
            $cashbackTransaction->setLetsbonusPct(0);
        }

        if (!empty($letsbonusCashbacktransaction->getExtraAmount())) {
            $cashbackTransaction->setExtraAmount($letsbonusCashbacktransaction->getExtraAmount());
        } else {
            $cashbackTransaction->setExtraAmount(0);
        }

        if (!empty($letsbonusCashbacktransaction->getExtraPct())) {
            $cashbackTransaction->setExtraPct($letsbonusCashbacktransaction->getExtraPct());
        } else {
            $cashbackTransaction->setExtraPct(NULL);
        }

        $currency = $this->em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => $letsbonusCashbacktransaction->getCurrency()));
        $cashbackTransaction->setCurrency($currency);

        if (!empty($letsbonusCashbacktransaction->getStatus())) {
            $cashbackTransaction->setStatus($letsbonusCashbacktransaction->getStatus());
        } else {
            $cashbackTransaction->setStatus(0);
        }

        if (!empty($letsbonusCashbacktransaction->getType())) {
            $cashbackTransaction->setType($letsbonusCashbacktransaction->getType());
        } else {
            $cashbackTransaction->setType('');
        }

        if (!empty($letsbonusCashbacktransaction->getNetworkStatus())) {
            $cashbackTransaction->setNetworkStatus($letsbonusCashbacktransaction->getNetworkStatus());
        } else {
            $cashbackTransaction->setNetworkStatus('');
        }

        if (!empty($letsbonusCashbacktransaction->getOrderReference())) {
            $cashbackTransaction->setOrderReference($letsbonusCashbacktransaction->getOrderReference());
        } else {
            $cashbackTransaction->setOrderReference('');
        }

        if (!empty($letsbonusCashbacktransaction->getAffiliateAproveddate()) && $letsbonusCashbacktransaction->getAffiliateAproveddate() != '0000-00-00 00:00:00' && $letsbonusCashbacktransaction->getAffiliateAproveddate()->format('Y-m-d') != '-0001-11-30') {
            $cashbackTransaction->setAffiliateAproveddate(new \DateTime(date('Y-m-d H:i:s', $letsbonusCashbacktransaction->getAffiliateAproveddate()->getTimestamp())));
        }

        if (!empty($letsbonusCashbacktransaction->getAffiliateCanceldate()) && $letsbonusCashbacktransaction->getAffiliateCanceldate() != '0000-00-00 00:00:00' && $letsbonusCashbacktransaction->getAffiliateCanceldate()->format('Y-m-d') != '-0001-11-30') {
            $cashbackTransaction->setAffiliateCanceldate(new \DateTime(date('Y-m-d H:i:s', $letsbonusCashbacktransaction->getAffiliateCanceldate()->getTimestamp())));
        }

        if (!empty($letsbonusCashbacktransaction->getAprovalDate()) && $letsbonusCashbacktransaction->getAprovalDate() != '0000-00-00 00:00:00' && $letsbonusCashbacktransaction->getAprovalDate()->format('Y-m-d') != '-0001-11-30') {
            $cashbackTransaction->setAprovalDate(new \DateTime(date('Y-m-d H:i:s', $letsbonusCashbacktransaction->getAprovalDate()->getTimestamp())));
        }

        if (!empty($letsbonusCashbacktransaction->getDate()) && $letsbonusCashbacktransaction->getDate() != '0000-00-00 00:00:00') {
            $cashbackTransaction->setDate(new \DateTime(date('Y-m-d H:i:s', $letsbonusCashbacktransaction->getDate()->getTimestamp())));
        }

        if (!empty($letsbonusCashbacktransaction->getUserName())) {
            $cashbackTransaction->setUserName($letsbonusCashbacktransaction->getUserName());
        } else {
            $cashbackTransaction->setUserName('');
        }

        if (!empty($letsbonusCashbacktransaction->getUserAddress())) {
            $cashbackTransaction->setUserAddress($letsbonusCashbacktransaction->getUserAddress());
        } else {
            $cashbackTransaction->setUserAddress('');
        }

        if (!empty($letsbonusCashbacktransaction->getUserDni())) {
            $cashbackTransaction->setUserDni($letsbonusCashbacktransaction->getUserDni());
        } else {
            $cashbackTransaction->setUserDni('');
        }

        if (!empty($letsbonusCashbacktransaction->getUserPhone())) {
            $cashbackTransaction->setUserPhone($letsbonusCashbacktransaction->getUserPhone());
        } else {
            $cashbackTransaction->setUserPhone('');
        }

        if (!empty($letsbonusCashbacktransaction->getUserBankAccountNumber())) {
            $cashbackTransaction->setUserBankAccountNumber($letsbonusCashbacktransaction->getUserBankAccountNumber());
        } else {
            $cashbackTransaction->setUserBankAccountNumber('');
        }

        if (!empty($letsbonusCashbacktransaction->getBic())) {
            $cashbackTransaction->setBic($letsbonusCashbacktransaction->getBic());
        } else {
            $cashbackTransaction->setBic('');
        }

        $fromCompany = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Companies')->findOneBy(array('id' => $letsbonusCashbacktransaction->getCompanyId()));
        $company = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('name' => $fromCompany->getName()));

        $cashbackTransaction->setCompanyId($company);

        if (!empty($letsbonusCashbacktransaction->getCashbacktransactionsChilds())) {
            $cashbackTransaction->setCashbacktransactionsChilds($letsbonusCashbacktransaction->getCashbacktransactionsChilds());
        } else {
            $cashbackTransaction->setCashbacktransactionsChilds('');
        }

        if (!empty($letsbonusCashbacktransaction->getAdminuserId())) {
            $cashbackTransaction->setAdminuserId($letsbonusCashbacktransaction->getAdminuserId());
        } else {
            $cashbackTransaction->setAdminuserId(NULL);
        }

        if (!empty($letsbonusCashbacktransaction->getManualNumdaystoapprove())) {
            $cashbackTransaction->setManualNumdaystoapprove($letsbonusCashbacktransaction->getManualNumdaystoapprove());
        } else {
            $cashbackTransaction->setManualNumdaystoapprove(NULL);
        }

        if (!empty($letsbonusCashbacktransaction->getComments())) {
            $cashbackTransaction->setComments($letsbonusCashbacktransaction->getComments());
        } else {
            $cashbackTransaction->setComments(NULL);
        }

        if (!empty($letsbonusCashbacktransaction->getParentTransactionId())) {
            $cashbackTransaction->setParentTransactionId($letsbonusCashbacktransaction->getParentTransactionId());
        } else {
            $cashbackTransaction->setParentTransactionId(NULL);
        }

        $cashbackSetting = $this->em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings')->findOneBy(array('id' => $letsbonusCashbacktransaction->getCashbacksettingId()));
        $cashbackTransaction->setCashbacksettingId($cashbackSetting);

        if (!empty($letsbonusCashbacktransaction->getSepageneratedbyUserId())) {
            $cashbackTransaction->setSepageneratedbyUserId($letsbonusCashbacktransaction->getSepageneratedbyUserId());
        } else {
            $cashbackTransaction->setSepageneratedbyUserId(NULL);
        }

        if (!empty($letsbonusCashbacktransaction->getSepageneratedDate()) && $letsbonusCashbacktransaction->getSepageneratedDate() != '0000-00-00 00:00:00') {
            $cashbackTransaction->setSepageneratedDate(new \DateTime(date('Y-m-d H:i:s', $letsbonusCashbacktransaction->getSepageneratedDate()->getTimestamp())));
        } else {
            $cashbackTransaction->setSepageneratedDate(new \DateTime());
        }

        if (!empty($letsbonusCashbacktransaction->getDeviceType())) {
            $cashbackTransaction->setDeviceType($letsbonusCashbacktransaction->getDeviceType());
        } else {
            $cashbackTransaction->setDeviceType(NULL);
        }

        $cashbackTransaction->setCreated(new \DateTime(date('Y-m-d H:i:s', $letsbonusCashbacktransaction->getCreated()->getTimestamp())));

        $cashbackTransaction->setModified(new \DateTime(date('Y-m-d H:i:s', $letsbonusCashbacktransaction->getModified()->getTimestamp())));

        $this->em->persist($cashbackTransaction);
        $metadata = $this->em->getClassMetaData(get_class($cashbackTransaction));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $cashbackTransaction->getId();
    }
}
