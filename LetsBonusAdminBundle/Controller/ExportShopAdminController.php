<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use Symfony\Component\HttpFoundation\Response;
use PHPExcel;
use PHPExcel_IOFactory;

class ExportShopAdminController extends Controller
{
    public function indexAction()
    {
        $response = new Response();

        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT
                        b.id, b.brand, b.keywords, b.shopStatus, b.highlightedHome, b.startDate, b.endDate,
                        h.title,
                        c.company_id,
                        n.name as network_name,
                        s.shop_id ,t.name as subcategory_name,
                        p.title as parent_title
                        FROM lb_shop b
                        JOIN lb_shop_category s ON b.id = s.shop_id
                        JOIN lb_category t ON s.category_id = t.id
                        JOIN lb_parent_category p ON p.id = t.parent_category_id
                        JOIN lb_shop_history h ON h.shop = b.id
                        JOIN lb_cashback_transactions c ON c.shop_id = b.id
                        JOIN lb_network n ON n.id = c.network_id');
        $statement->execute();
        $shop_data = $statement->fetchAll();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('Admin')
            ->setLastModifiedBy('Admin')
            ->setTitle('letsbonus_cashback_shops')
            ->setSubject('letsbonus_cashback_shops');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'title')
            ->setCellValue('C1', 'brand')
            ->setCellValue('D1', 'company')
            ->setCellValue('E1', 'keywords')
            ->setCellValue('F1', 'status')
            ->setCellValue('G1', 'categories')
            ->setCellValue('H1', 'subcategories')
            ->setCellValue('I1', 'network')
            ->setCellValue('J1', 'home')
            ->setCellValue('K1', 'start_date')
            ->setCellValue('L1', 'end_date');

        $y = 2;
        for ($i = 0; $i < count($shop_data); ++$i) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$y, $shop_data[$i]['id'])
                ->setCellValue('B'.$y, $shop_data[$i]['title'])
                ->setCellValue('C'.$y, $shop_data[$i]['brand'])
                ->setCellValue('D'.$y, $shop_data[$i]['company_id'])
                ->setCellValue('E'.$y, $shop_data[$i]['keywords'])
                ->setCellValue('F'.$y, $shop_data[$i]['shop_status'])
                ->setCellValue('G'.$y, $shop_data[$i]['parent_title'])
                ->setCellValue('H'.$y, $shop_data[$i]['subcategory_name'])
                ->setCellValue('I'.$y, $shop_data[$i]['network_name'])
                ->setCellValue('J'.$y, $shop_data[$i]['highlighted_home'])
                ->setCellValue('K'.$y, $shop_data[$i]['start_date'])
                ->setCellValue('L'.$y, $shop_data[$i]['end_date']);
            ++$y;
        }

        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="letsbonus_cashback_shops.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');

        //$response->prepare();
        $response->sendHeaders();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    }
    public function userreportAction()
    {
        $response = new Response();

        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare('SELECT b.id, b.transaction_id, b.reference_id, b.commission, b.amount, b.param_0, b.param_1, b.param_2, n.network_id, n.created, n.modified, n.status, h.id as shophistory_id, c.code as currency_code FROM lb_transactions b JOIN lb_cashback_transactions n ON n.transaction_id = b.transaction_id JOIN lb_shop_history h ON h.shop = b.shop_id JOIN lb_currency c ON c.id = b.currency_id');
        $statement->execute();
        $shop_data = $statement->fetchAll();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('Admin')
            ->setLastModifiedBy('Admin')
            ->setTitle('letsbonus_cashback_transactions_without_users')
            ->setSubject('letsbonus_cashback_transactions_without_users');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'transactionid')
            ->setCellValue('C1', 'reference_id')
            ->setCellValue('D1', 'network_id')
            ->setCellValue('E1', 'shopshistory_id')
            ->setCellValue('F1', 'commission')
            ->setCellValue('G1', 'amount')
            ->setCellValue('H1', 'currency')
            ->setCellValue('I1', 'status')
            ->setCellValue('J1', 'status_name')
            ->setCellValue('K1', 'trackingDate')
            ->setCellValue('L1', 'modifiedDate')
            ->setCellValue('M1', 'clickDate')
            ->setCellValue('N1', 'clickId')
            ->setCellValue('O1', 'program_id')
            ->setCellValue('P1', 'program_name')
            ->setCellValue('Q1', 'param0')
            ->setCellValue('R1', 'param1')
            ->setCellValue('S1', 'param2')
            ->setCellValue('T1', 'orderNumber')
            ->setCellValue('U1', 'orderValue')
            ->setCellValue('V1', 'trackingUrl')
            ->setCellValue('W1', 'productName')
            ->setCellValue('X1', 'daysToAutoApprove')
            ->setCellValue('Y1', 'processed')
            ->setCellValue('Z1', 'created')
            ->setCellValue('AA1', 'modified');

        $y = 2;
        for ($i = 0; $i < count($shop_data); ++$i) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$y, $shop_data[$i]['id'])
                ->setCellValue('B'.$y, $shop_data[$i]['transaction_id'])
                ->setCellValue('C'.$y, $shop_data[$i]['reference_id'])
                ->setCellValue('D'.$y, $shop_data[$i]['network_id'])
                ->setCellValue('E'.$y, $shop_data[$i]['shophistory_id'])
                ->setCellValue('F'.$y, $shop_data[$i]['commission'])
                ->setCellValue('G'.$y, $shop_data[$i]['amount'])
                ->setCellValue('H'.$y, $shop_data[$i]['currency_code'])
                ->setCellValue('I'.$y, $shop_data[$i]['status'])
                ->setCellValue('J'.$y, '-')
                ->setCellValue('K'.$y, '-')
                ->setCellValue('L'.$y, '-')
                ->setCellValue('M'.$y, '-')
                ->setCellValue('N'.$y, '-')
                ->setCellValue('O'.$y, '-')
                ->setCellValue('P'.$y, '-')
                ->setCellValue('Q'.$y, $shop_data[$i]['param_0'])
                ->setCellValue('R'.$y, $shop_data[$i]['param_1'])
                ->setCellValue('S'.$y, $shop_data[$i]['param_2'])
                ->setCellValue('T'.$y, '-')
                ->setCellValue('U'.$y, '-')
                ->setCellValue('V'.$y, '-')
                ->setCellValue('W'.$y, '-')
                ->setCellValue('X'.$y, '-')
                ->setCellValue('Y'.$y, '-')
                ->setCellValue('Z'.$y, $shop_data[$i]['created'])
                ->setCellValue('AA'.$y, $shop_data[$i]['modified']);
            ++$y;
        }

        $objPHPExcel->setActiveSheetIndex(0);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="letsbonus_cashback_transactions_without_users.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');

        $response->sendHeaders();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    }
}
