<?php

namespace App\Controllers;
use App\Libraries\Fiky_encryption;
use App\Libraries\Fiky_excel;
use App\Libraries\Fiky_pdf;
use App\Libraries\Fiky_qrcode;
use App\Libraries\Fiky_report;
use App\Libraries\Fiky_version;
use App\Libraries\Important_class;
use App\Libraries\Template;
use App\Libraries\Fiky_menu;

use App\Models\Assmgmt\M_assets_management;
use App\Models\Capital\M_capital_administration;
use App\Models\Capital\M_capital_budget;
use App\Models\Capital\M_capital_masters;
use App\Models\Contacts\M_Supplier;
use App\Models\Dashboard\M_Dashboard;
use App\Models\Dashboard\M_DashboardUser;
use App\Models\Form\M_Form;

use App\Models\Globals\M_Gobals;
use App\Models\Globals\M_Trxerror;

use App\Models\Import\M_Winacc;
use App\Models\Intern\M_Mobile;
use App\Models\Master\M_Item;
use App\Models\Master\M_Location;
use App\Models\Master\M_Menu;
use App\Models\Master\M_Role;
use App\Models\Master\M_User;
use App\Models\Master\M_Suppliers;
use App\Models\Master\M_Customer;
use App\Models\Master\M_Currency;
use App\Models\Master\M_Coa;
use App\Models\Master\M_Job;

use App\Models\Purchase\M_Purchaseorder;
use App\Models\Stock\M_Balance;
use App\Models\Stock\M_Bbk;
use App\Models\Stock\M_Bbm;
use App\Models\Stock\M_StockReport;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Mimes;
use Psr\Log\LoggerInterface;


/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    public $request;
    public $session;
    public $myconfig;
    protected $template;


    /** Model **/
    public $m_global;
    public $m_dashboard;
    public $m_user;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['captcha'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->uri = \Config\Services::uri();
        $this->image = \Config\Services::image();
        $this->email = \Config\Services::email();



        /* Helper DB */
        $this->db = \Config\Database::connect('default');
        /* Libraries */
        $this->template = new Template();
        $this->fiky_encryption = new Fiky_encryption();
        $this->fiky_report = new Fiky_report();
        $this->fiky_menu = new Fiky_menu();
        $this->fiky_version = new Fiky_version();
        $this->fiky_excel = new Fiky_excel();
        $this->fiky_qrcode = new Fiky_qrcode();
        $this->fiky_pdf = new Fiky_pdf();
        $this->important_class = new Important_class();
        $this->mimes = new Mimes();

        /*  Load Model */
        $this->m_global = new M_Gobals();
        $this->m_dashboard = new M_Dashboard();
        $this->m_dashboarduser = new M_DashboardUser();
        $this->m_user = new M_User();
        $this->m_menu = new M_Menu();
        $this->m_trxerror = new M_Trxerror();
        $this->m_role = new M_Role();
        $this->m_location = new M_Location();
        $this->m_supplier = new M_Supplier();
        $this->m_suppliers = new M_Suppliers();
        $this->m_item = new M_Item();
        $this->m_balance = new M_Balance();
        $this->m_bbm = new M_Bbm();
        $this->m_bbk = new M_Bbk();
        $this->m_stockreport = new M_StockReport();
        $this->m_Purchaseorder = new M_Purchaseorder();
        $this->m_mobile = new M_Mobile();
        $this->m_assets_management = new M_assets_management();
        $this->m_capital_masters = new M_capital_masters();
        $this->m_capital_administration = new M_capital_administration();
        $this->m_capital_budget = new M_capital_budget();
        $this->m_winacc = new M_Winacc();
        $this->m_form = new M_Form();
        $this->m_customer = new M_Customer();
        $this->m_currency = new M_Currency();
        $this->m_coa = new M_Coa();
        $this->m_job = new M_Job();
    }
}
