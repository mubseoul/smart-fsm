<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class InvoiceController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage invoice')) {
            $invoices = Invoice::where('parent_id', parentId())->get();
            return view('invoice.index', compact('invoices'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function create()
    {
        $clients = User::where('parent_id', parentId())->where('type', 'client')->get()->pluck('name', 'id');
        $clients->prepend(__('Select Client'), '');
        $invoiceNumber=$this->invoiceNumber();
        $status=Invoice::$status;
        return view('invoice.create', compact('clients','invoiceNumber','status'));
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create invoice')) {
            $validator = \Validator::make(
                $request->all(), [
                    'client' => 'required',
                    'workorder' => 'required',
                    'invoice_date' => 'required',
                    'due_date' => 'required',
                    'total' => 'required',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $invoice = new Invoice();
            $invoice->client = $request->client;
            $invoice->wo_id = $request->workorder;
            $invoice->invoice_id = $request->invoice_id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->due_date = $request->due_date;
            $invoice->total = $request->total;
            $invoice->discount = !empty($request->discount)?$request->discount:0;
            $invoice->status = $request->status;
            $invoice->notes = !empty($request->notes) ? $request->notes : null;
            $invoice->parent_id = parentId();
            $invoice->save();

            $module = 'invoice_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $setting = settings();
            $errorMessage = '';

            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $invoice->id);


                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $invoice->clients->email;

                $response = commonEmailSend($to, $data);

                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }

            return redirect()->back()->with('success', __('Invoice successfully created.').'</br>'.$errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show($ids)
    {
        if (\Auth::user()->can('show work order')) {
            $id=Crypt::decrypt($ids);
            $invoice=Invoice::find($id);
            $workorder=WorkOrder::find($invoice->wo_id);
            return view('invoice.show', compact('invoice','workorder'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function edit($id)
    {
        $invoice=Invoice::find($id);
        $clients = User::where('parent_id', parentId())->where('type', 'client')->get()->pluck('name', 'id');
        $clients->prepend(__('Select Client'), '');
        $status=Invoice::$status;
        return view('invoice.edit', compact('clients','invoice','status'));
    }


    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit invoice')) {
            $validator = \Validator::make(
                $request->all(), [
                    'invoice_date' => 'required',
                    'due_date' => 'required',
                    'total' => 'required',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $invoice=Invoice::find($id);
            $invoice->invoice_id = $request->invoice_id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->due_date = $request->due_date;
            $invoice->total = $request->total;
            $invoice->discount = !empty($request->discount)?$request->discount:0;
            $invoice->status = $request->status;
            $invoice->notes = !empty($request->notes) ? $request->notes : null;
            $invoice->save();

            return redirect()->back()->with('success', __('Invoice successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete invoice')) {
            $invoice=Invoice::find($id);
            $invoice->delete();
            return redirect()->back()->with('success', __('Invoice successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoiceNumber()
    {
        $lastInvoice = Invoice::where('parent_id', parentId())->latest()->first();
        if ($lastInvoice == null) {
            return 1;
        } else {
            return $lastInvoice->invoice_id + 1;
        }
    }

    public function getWorkorder(Request $request)
    {
        $invoice=Invoice::where('client',$request->client)->get()->pluck('wo_id')->toArray();
        $workorders = WorkOrder::where('client', $request->client)->whereNotIn('id',$invoice)->get();
        $woData=[];
        foreach ($workorders as $workorder){
            $woData[$workorder->id]=workOrderPrefix().$workorder->wo_id;
        }
        return response()->json($woData);
    }

    public function getWorkorderDetails(Request $request)
    {
        $workorder = WorkOrder::find($request->workorder);
        $getWorkorderTotalAmount=$workorder->getWorkorderTotalAmount();
        return response()->json($getWorkorderTotalAmount);
    }
}
