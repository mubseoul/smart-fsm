<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Notification;
use App\Models\ServicePart;
use App\Models\ServiceTask;
use App\Models\User;
use App\Models\WORequest;
use App\Models\WorkOrder;
use App\Models\WOServiceAppointment;
use App\Models\WOServicePart;
use App\Models\WOServiceTask;
use App\Models\WOType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WorkOrderController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage work order')) {
            $workorders = WorkOrder::where('parent_id', parentId())->get();
            return view('workorder.index', compact('workorders'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create work order')) {
            $woTypes = WOType::where('parent_id', parentId())->get()->pluck('type', 'id');
            $woTypes->prepend(__('Select Type'), '');

            $clients = User::where('parent_id', parentId())->where('type', 'client')->get()->pluck('name', 'id');
            $clients->prepend(__('Select Client'), '');

            $users = User::where('parent_id', parentId())->where('type', '!=', 'client')->get()->pluck('name', 'id');
            $users->prepend(__('Select User'), '');

            $assets = Asset::where('parent_id', parentId())->get()->pluck('name', 'id');
            $assets->prepend(__('Select Asset'), '');

            $priority = WORequest::$priority;
            $status = WORequest::$status;
            $workOrderNumber = $this->workOrderNumber();

            $services = ServicePart::where('parent_id', parentId())->where('type', 'service')->get()->pluck('title', 'id');
            $services->prepend(__('Select Service'), '');

            $parts = ServicePart::where('parent_id', parentId())->where('type', 'part')->get()->pluck('title', 'id');
            $parts->prepend(__('Select Part'), '');
            $time = WORequest::$time;
            return view('workorder.create', compact('users', 'assets', 'clients', 'priority', 'status', 'woTypes', 'workOrderNumber', 'services', 'parts', 'time'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create work order')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'wo_detail' => 'required',
                    'type' => 'required',
                    'client' => 'required',
                    'asset' => 'required',
                    'priority' => 'required',
                    'due_date' => 'required',
                    'assign' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $workOrder = new WorkOrder();
            $workOrder->wo_id = $request->wo_id;
            $workOrder->wo_detail = $request->wo_detail;
            $workOrder->type = $request->type;
            $workOrder->client = $request->client;
            $workOrder->asset = $request->asset;
            $workOrder->priority = $request->priority;
            $workOrder->due_date = $request->due_date;
            $workOrder->status = 'pending';
            $workOrder->assign = $request->assign;
            $workOrder->notes = !empty($request->notes) ? $request->notes : null;
            $workOrder->preferred_date = !empty($request->preferred_date) ? $request->preferred_date : null;
            $workOrder->preferred_time = !empty($request->preferred_time) ? $request->preferred_time : null;
            $workOrder->preferred_note = !empty($request->preferred_note) ? $request->preferred_note : null;
            $workOrder->parent_id = parentId();
            $workOrder->save();

            $services = !empty($request->services) ? $request->services : [];
            $parts = !empty($request->parts) ? $request->parts : [];

            if (!empty($services)) {
                for ($i = 0; $i < count($services); $i++) {
                    $service = ServicePart::find($services[$i]['service_part_id']);

                    $woService = new WOServicePart();
                    $woService->wo_id = $workOrder->id;
                    $woService->service_part_id = $services[$i]['service_part_id'];
                    $woService->quantity = $services[$i]['quantity'];
                    $woService->amount = $services[$i]['amount'];
                    $woService->description = $services[$i]['description'];
                    $woService->type = 'service';
                    $woService->save();


                    foreach ($service->serviceTasks as $task) {
                        $WOServiceTask = new WOServiceTask();
                        $WOServiceTask->wo_id = $workOrder->id;
                        $WOServiceTask->service_part_id = $task->service_id;
                        $WOServiceTask->service_task = $task->task;
                        $WOServiceTask->duration = $task->duration;
                        $WOServiceTask->description = $task->description;
                        $WOServiceTask->status = 'pending';
                        $WOServiceTask->save();
                    }
                }
            }


            if (!empty($parts)) {
                for ($i = 0; $i < count($parts); $i++) {
                    $woPart = new WOServicePart();
                    $woPart->wo_id = $workOrder->id;
                    $woPart->service_part_id = $parts[$i]['service_part_id'];
                    $woPart->quantity = $parts[$i]['quantity'];
                    $woPart->amount = $parts[$i]['amount'];
                    $woPart->description = $parts[$i]['description'];
                    $woPart->type = 'part';
                    $woPart->save();
                }
            }

            // Prepare services
            $servicesData = [];
            if (!empty($services)) {
                foreach ($services as $service) {
                    $servicesData[] = [
                        'estimation_id' => $workOrder->id,
                        'service_part_id' => $service['service_part_id'],
                        'quantity' => $service['quantity'],
                        'amount' => $service['amount'],
                    ];
                }
            }

            // Prepare parts
            $partsData = [];
            if (!empty($parts)) {
                foreach ($parts as $part) {
                    $partsData[] = [
                        'estimation_id' => $workOrder->id,
                        'service_part_id' => $part['service_part_id'],
                        'quantity' => $part['quantity'],
                        'amount' => $part['amount'],
                    ];
                }
            }

            $serviceDetails = "";
            foreach ($servicesData as $services) {
                $serviceName = ServicePart::find($services['service_part_id']);
                $serviceDetails .= "Service Name:" . $serviceName->title . ", Services Quantity:" . $services['quantity'] . ", Services Amount:" . $services['amount'] . "<br>";
            }

            $partsDetails = "";
            foreach ($partsData as $parts) {
                $partsName = ServicePart::find($parts['service_part_id']);
                $partsDetails .= "Part Name:" . $partsName->title . ", Parts Quantity:" . $parts['quantity'] . ", Parts Amount:" . $parts['amount'] . "<br>";
            }

            $module = 'work_order_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $notification->serviceDetail = $serviceDetails;
            $notification->partsDetail = $partsDetails;
            $setting = settings();
            $errorMessage = '';
            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $workOrder->id);
                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $workOrder->clients->email;

                $response = commonEmailSend($to, $data);

                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }

            $module = 'work_order_assign';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $notification->serviceDetail = $serviceDetails;
            $notification->partsDetail = $partsDetails;
            $setting = settings();
            $errorMessage = '';
            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $workOrder->id);
                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $workOrder->assigned->email;

                $response = commonEmailSend($to, $data);

                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }

            return redirect()->route('workorder.index')->with('success', __('Work Order successfully created.').'</>'.$errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show($id)
    {
        if (\Auth::user()->can('show work order')) {
            $id = Crypt::decrypt($id);
            $workorder = WorkOrder::find($id);
            $status = WorkOrder::$status;
            return view('workorder.show', compact('workorder', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('create work order')) {

            $id = Crypt::decrypt($id);
            $workOrder = WorkOrder::find($id);
            $woTypes = WOType::where('parent_id', parentId())->get()->pluck('type', 'id');
            $woTypes->prepend(__('Select Type'), '');

            $clients = User::where('parent_id', parentId())->where('type', 'client')->get()->pluck('name', 'id');
            $clients->prepend(__('Select Client'), '');

            $users = User::where('parent_id', parentId())->where('type', '!=', 'client')->get()->pluck('name', 'id');
            $users->prepend(__('Select User'), '');

            $assets = Asset::where('parent_id', parentId())->get()->pluck('name', 'id');
            $assets->prepend(__('Select Asset'), '');

            $priority = WORequest::$priority;
            $status = WORequest::$status;
            $workOrderNumber = $this->workOrderNumber();

            $services = ServicePart::where('parent_id', parentId())->where('type', 'service')->get()->pluck('title', 'id');
            $services->prepend(__('Select Service'), '');

            $parts = ServicePart::where('parent_id', parentId())->where('type', 'part')->get()->pluck('title', 'id');
            $parts->prepend(__('Select Part'), '');
            $time = WORequest::$time;

            $workOrderServiceData = $workOrder->services;

            $workOrderServices = [];
            foreach ($workOrderServiceData as $workOrderService) {
                $workorderService['id'] = $workOrderService->id;
                $workorderService['wo_id'] = $workOrderService->wo_id;
                $workorderService['service_part_id'] = $workOrderService->service_part_id;
                $workorderService['quantity'] = $workOrderService->quantity;
                $workorderService['amount'] = $workOrderService->amount;
                $workorderService['unit'] = !empty($workOrderService->serviceParts) ? $workOrderService->serviceParts->unit : '';
                $workOrderServices[] = $workorderService;
            }

            $workOrderPartData = $workOrder->parts;
            $workOrderParts = [];
            foreach ($workOrderPartData as $workOrderPart) {
                $workorderPart['id'] = $workOrderPart->id;
                $workorderPart['wo_id'] = $workOrderPart->wo_id;
                $workorderPart['service_part_id'] = $workOrderPart->service_part_id;
                $workorderPart['quantity'] = $workOrderPart->quantity;
                $workorderPart['amount'] = $workOrderPart->amount;
                $workorderPart['unit'] = !empty($workOrderPart->serviceParts) ? $workOrderPart->serviceParts->unit : '';
                $workOrderParts[] = $workorderPart;
            }

            return view('workorder.edit', compact('users', 'assets', 'clients', 'priority', 'status', 'woTypes', 'workOrderNumber', 'services', 'parts', 'time', 'workOrder', 'workOrderServices', 'workOrderParts'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit work order')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'wo_detail' => 'required',
                    'type' => 'required',
                    'client' => 'required',
                    'asset' => 'required',
                    'priority' => 'required',
                    'due_date' => 'required',
                    'assign' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $id = Crypt::decrypt($id);
            $workOrder = WorkOrder::find($id);

            $workOrder->wo_id = $request->wo_id;
            $workOrder->wo_detail = $request->wo_detail;
            $workOrder->type = $request->type;
            $workOrder->client = $request->client;
            $workOrder->asset = $request->asset;
            $workOrder->priority = $request->priority;
            $workOrder->due_date = $request->due_date;
            $workOrder->assign = $request->assign;
            $workOrder->notes = !empty($request->notes) ? $request->notes : null;
            $workOrder->preferred_date = !empty($request->preferred_date) ? $request->preferred_date : null;
            $workOrder->preferred_time = !empty($request->preferred_time) ? $request->preferred_time : null;
            $workOrder->preferred_note = !empty($request->preferred_note) ? $request->preferred_note : null;
            $workOrder->save();

            $services = !empty($request->services) ? $request->services : [];
            $parts = !empty($request->parts) ? $request->parts : [];

            if (!empty($services)) {
                for ($i = 0; $i < count($services); $i++) {
                    $serviceId = isset($services[$i]['id']) ? $services[$i]['id'] : 0;
                    $woService = WOServicePart::find($serviceId);

                    if ($woService == null) {
                        $woService = new WOServicePart();
                        $woService->wo_id = $workOrder->id;
                    }
                    $woService->service_part_id = $services[$i]['service_part_id'];
                    $woService->quantity = $services[$i]['quantity'];
                    $woService->amount = $services[$i]['amount'];
                    $woService->description = $services[$i]['description'];
                    $woService->type = 'service';
                    $woService->save();
                }
            }

            if (!empty($parts)) {
                for ($i = 0; $i < count($parts); $i++) {
                    $woPart = WOServicePart::find(isset($parts[$i]['id']) ? $parts[$i]['id'] : 0);
                    if ($woPart == null) {
                        $woPart = new WOServicePart();
                        $woPart->wo_id = $workOrder->id;
                    }
                    $woPart->service_part_id = $parts[$i]['service_part_id'];
                    $woPart->quantity = $parts[$i]['quantity'];
                    $woPart->amount = $parts[$i]['amount'];
                    $woPart->description = $parts[$i]['description'];
                    $woPart->type = 'part';
                    $woPart->save();
                }
            }
            return redirect()->route('workorder.index')->with('success', __('Work Order successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete work order')) {
            $workOrder = WorkOrder::find($id);
            WOServicePart::where('wo_id', $workOrder->id)->delete();
            $workOrder->delete();
            return redirect()->route('workorder.index')->with('success', __('Work Order successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function workOrderNumber()
    {
        $lastWorkorder = WorkOrder::where('parent_id', parentId())->latest()->first();
        if ($lastWorkorder == null) {
            return 1;
        } else {
            return $lastWorkorder->wo_id + 1;
        }
    }


    public function getServicePart(Request $request)
    {
        $servicePart = ServicePart::find($request->id);
        return response()->json($servicePart);
    }

    public function servicePartDestroy(Request $request)
    {
        if (\Auth::user()->can('delete workorder service & part')) {
            if (isset($request->id) && !empty($request->id)) {
                $servicePart = WOServicePart::find($request->id);
                $servicePart->delete();
            }

            return 1;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function workorderStatus(Request $request, $workorderId)
    {
        $workorder = WorkOrder::find($workorderId);
        $workorder->status = $request->status;
        $workorder->save();
        return redirect()->back()->with('success', __('Workorder status successfully changed.'));
    }

    public function serviceTaskCreate($id)
    {
        $workorder = WorkOrder::find($id);
        $woServices = $workorder->services;
        $status = WOServiceTask::$status;
        return view('workorder.service_task_create', compact('workorder', 'woServices', 'status'));
    }

    public function serviceTaskStore(Request $request, $id)
    {
        if (\Auth::user()->can('create workorder service task')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'service' => 'required',
                    'service_task' => 'required',
                    'duration' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->with('active_tab', 'service_task')->withInput();
            }

            $task = new WOServiceTask();
            $task->wo_id = $id;
            $task->service_part_id = $request->service;
            $task->service_task = $request->service_task;
            $task->duration = $request->duration;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->save();

            return redirect()->back()->with('success', __('Service task successfully created.'))->with('active_tab', 'service_task');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function serviceTaskEdit($woId, $taskId)
    {
        $workorder = WorkOrder::find($woId);
        $woServices = $workorder->services;
        $status = WOServiceTask::$status;
        $task = WOServiceTask::find($taskId);
        return view('workorder.service_task_edit', compact('workorder', 'woServices', 'status', 'task'));
    }

    public function serviceTaskUpdate(Request $request, $woId, $taskId)
    {
        if (\Auth::user()->can('edit workorder service task')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'service' => 'required',
                    'service_task' => 'required',
                    'duration' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $task = WOServiceTask::find($taskId);
            $task->service_part_id = $request->service;
            $task->service_task = $request->service_task;
            $task->duration = $request->duration;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->save();

            return redirect()->back()->with('success', __('Service task successfully updated.'))->with('active_tab', 'service_task');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function serviceTaskDestroy($woId, $taskId)
    {
        if (\Auth::user()->can('delete workorder service task')) {
            $task = WOServiceTask::find($taskId);
            $task->delete();
            return redirect()->back()->with('success', __('Service task successfully deleted.'))->with('active_tab', 'service_task');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function serviceAppointment($wo_id)
    {
        $status = WOServiceAppointment::$status;
        $serviceAppointment = WOServiceAppointment::where('wo_id', $wo_id)->first();
        return view('workorder.service_appointment', compact('wo_id', 'status', 'serviceAppointment'));
    }

    public function serviceAppointmentStore(Request $request, $id)
    {

        if (\Auth::user()->can('create service appointment')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'start_date' => 'required',
                    'start_time' => 'required',
                    'end_date' => 'required',
                    'end_time' => 'required',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->with('active_tab', 'service_appointment')->withInput();
            }

            $appointment = WOServiceAppointment::where('wo_id', $id)->first();
            if (empty($appointment)) {
                $appointment = new WOServiceAppointment();
            }
            $appointment->wo_id = $id;
            $appointment->start_date = $request->start_date;
            $appointment->start_time = $request->start_time;
            $appointment->end_date = $request->end_date;
            $appointment->end_time = $request->end_time;
            $appointment->notes = $request->notes;
            $appointment->status = $request->status;
            $appointment->parent_id = parentId();
            $appointment->save();

            return redirect()->back()->with('success', __('Service appointment successfully created.'))->with('active_tab', 'service_appointment');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function serviceAppointmentDestroy($woId)
    {
        if (\Auth::user()->can('delete service appointment')) {
            $appointment = WOServiceAppointment::where('wo_id', $woId)->first();
            $appointment->delete();
            return redirect()->back()->with('success', __('Service appointment successfully deleted.'))->with('active_tab', 'service_appointment');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
