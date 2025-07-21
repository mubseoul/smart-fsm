<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use App\Models\Custom;
use App\Models\FAQ;
use App\Models\HomePage;
use App\Models\Invoice;
use App\Models\NoticeBoard;
use App\Models\PackageTransaction;
use App\Models\Page;
use App\Models\Subscription;
use App\Models\Support;
use App\Models\User;
use App\Models\WORequest;
use App\Models\WorkOrder;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        if (\Auth::check()) {
            if (\Auth::user()->type == 'super admin') {
                $result['totalOrganization'] = User::where('type', 'owner')->count();
                $result['totalSubscription'] = Subscription::count();
                $result['totalTransaction'] = PackageTransaction::count();
                $result['totalIncome'] = PackageTransaction::sum('amount');
                $result['totalNote'] = NoticeBoard::where('parent_id', parentId())->count();
                $result['totalContact'] = Contact::where('parent_id', parentId())->count();

                $result['organizationByMonth'] = $this->organizationByMonth();
                $result['paymentByMonth'] = $this->paymentByMonth();

                return view('dashboard.super_admin', compact('result'));
            } else {
                $result['totalClient'] = User::where('parent_id', parentId())->where('type','client')->count();
                $result['totalWORequest'] = WORequest::where('parent_id', parentId())->count();
                $result['totalWorkorder'] = WorkOrder::where('parent_id', parentId())->count();
                $result['totalInvoice'] = Invoice::where('parent_id', parentId())->count();

                $result['incomeByMonth'] = $this->incomeByMonth();
                $result['settings']=settings();

                return view('dashboard.index', compact('result'));
            }
        } else {
            if (!file_exists(setup())) {
                header('location:install');
                die;
            } else {

                $landingPage=getSettingsValByName('landing_page');
                if($landingPage=='on'){
                    $subscriptions=Subscription::get();
                    $menus = Page::where('enabled',1)->get();
                    $FAQs = FAQ::where('enabled',1)->get();
                    return view('layouts.landing',compact('subscriptions', 'menus', 'FAQs'));
                }else{
                    return redirect()->route('login');
                }
            }

        }

    }

    public function organizationByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $organization = [];
        while ($currentdate <= $end) {
            $organization['label'][] = date('M-Y', $currentdate);

            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $organization['data'][] = User::where('type', 'owner')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $currentdate = strtotime('+1 month', $currentdate);
        }


        return $organization;

    }

    public function paymentByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $payment = [];
        while ($currentdate <= $end) {
            $payment['label'][] = date('M-Y', $currentdate);

            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $payment['data'][] = PackageTransaction::whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('amount');
            $currentdate = strtotime('+1 month', $currentdate);
        }
        return $payment;

    }


    public function incomeByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $payment = [];
        while ($currentdate <= $end) {
            $payment['label'][] = date('M-Y', $currentdate);
            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $payment['income'][] = Invoice::where('parent_id', parentId())->whereMonth('invoice_date', $month)->whereYear('invoice_date', $year)->sum('total');
            $currentdate = strtotime('+1 month', $currentdate);
        }

        return $payment;

    }


}
