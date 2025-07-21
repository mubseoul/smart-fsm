<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Estimation;
use App\Models\EstimationServicePart;
use App\Models\Notification;
use App\Models\ServicePart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EstimationController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage estimation')) {
            $estimations = Estimation::where('parent_id', parentId())->get();
            return view('estimation.index', compact('estimations'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create estimation')) {
            $clients = User::where('parent_id', parentId())->where('type', 'client')->get()->pluck('name', 'id');
            $clients->prepend(__('Select Client'), '');

            $assets = Asset::where('parent_id', parentId())->get()->pluck('name', 'id');
            $assets->prepend(__('Select Parent Asset'), '');

            $services = ServicePart::where('parent_id', parentId())->where('type', 'service')->get()->pluck('title', 'id');
            $services->prepend(__('Select Service'), '');

            $parts = ServicePart::where('parent_id', parentId())->where('type', 'part')->get()->pluck('title', 'id');
            $parts->prepend(__('Select Part'), '');

            $estimationNumber = $this->estimationNumber();
            return view('estimation.create', compact('clients', 'assets', 'estimationNumber', 'services', 'parts'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create estimation')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'estimation_id' => 'required',
                    'client' => 'required',
                    'asset' => 'required',
                    'due_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $estimation = new Estimation();
            $estimation->estimation_id = $request->estimation_id;
            $estimation->title = $request->title;
            $estimation->client = $request->client;
            $estimation->asset = $request->asset;
            $estimation->due_date = $request->due_date;
            $estimation->notes = $request->notes;
            $estimation->status = 'pending';
            $estimation->parent_id = parentId();
            $estimation->save();
            $services = !empty($request->services) ? $request->services : [];
            $parts = !empty($request->parts) ? $request->parts : [];

            if (!empty($services)) {
                for ($i = 0; $i < count($services); $i++) {
                    $estimationService = new EstimationServicePart();
                    $estimationService->estimation_id = $estimation->id;
                    $estimationService->service_part_id = $services[$i]['service_part_id'];
                    $estimationService->quantity = $services[$i]['quantity'];
                    $estimationService->amount = $services[$i]['amount'];
                    $estimationService->description = $services[$i]['description'];
                    $estimationService->type = 'service';
                    $estimationService->save();
                }
            }


            if (!empty($parts)) {
                for ($i = 0; $i < count($parts); $i++) {
                    $estimationPart = new EstimationServicePart();
                    $estimationPart->estimation_id = $estimation->id;
                    $estimationPart->service_part_id = $parts[$i]['service_part_id'];
                    $estimationPart->quantity = $parts[$i]['quantity'];
                    $estimationPart->amount = $parts[$i]['amount'];
                    $estimationPart->description = $parts[$i]['description'];
                    $estimationPart->type = 'part';
                    $estimationPart->save();
                }
            }

            // Prepare services
            $servicesData = [];
            if (!empty($services)) {
                foreach ($services as $service) {
                    $servicesData[] = [
                        'estimation_id' => $estimation->id,
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
                        'estimation_id' => $estimation->id,
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

            $module = 'estimate_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $notification->serviceDetail=$serviceDetails;
            $notification->partsDetail=$partsDetails;
            $setting = settings();
            $errorMessage = '';
            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $estimation->id);
                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $estimation->clients->email;

                $response = commonEmailSend($to, $data);

                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }
            return redirect()->route('estimation.index')->with('success', __('Estimation successfully created.') . '</>' . $errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function show($id)
    {
        if (\Auth::user()->can('show estimation')) {
            $id = Crypt::decrypt($id);
            $estimation = Estimation::find($id);
            $status = Estimation::$status;
            return view('estimation.show', compact('estimation', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit estimation')) {
            $id = Crypt::decrypt($id);
            $estimation = Estimation::find($id);

            $clients = User::where('parent_id', parentId())->where('type', 'client')->get()->pluck('name', 'id');
            $clients->prepend(__('Select Client'), '');

            $assets = Asset::where('parent_id', parentId())->get()->pluck('name', 'id');
            $assets->prepend(__('Select Parent Asset'), '');

            $services = ServicePart::where('parent_id', parentId())->where('type', 'service')->get()->pluck('title', 'id');
            $services->prepend(__('Select Service'), '');

            $parts = ServicePart::where('parent_id', parentId())->where('type', 'part')->get()->pluck('title', 'id');
            $parts->prepend(__('Select Part'), '');

            $estimationServiceData = $estimation->services;
            $estimationServices = [];
            foreach ($estimationServiceData as $estimationService) {
                $estimationService['id'] = $estimationService->id;
                $estimationService['estimation_id'] = $estimationService->estimation_id;
                $estimationService['service_part_id'] = $estimationService->service_part_id;
                $estimationService['quantity'] = $estimationService->quantity;
                $estimationService['amount'] = $estimationService->amount;
                $estimationService['unit'] = !empty($estimationService->serviceParts) ? $estimationService->serviceParts->unit : '';
                $estimationServices[] = $estimationService;
            }

            $estimationPartData = $estimation->parts;
            $estimationParts = [];
            foreach ($estimationPartData as $estimationPart) {
                $estimationPart['id'] = $estimationPart->id;
                $estimationPart['estimation_id'] = $estimationPart->estimation_id;
                $estimationPart['service_part_id'] = $estimationPart->service_part_id;
                $estimationPart['quantity'] = $estimationPart->quantity;
                $estimationPart['amount'] = $estimationPart->amount;
                $estimationPart['unit'] = !empty($estimationPart->serviceParts) ? $estimationPart->serviceParts->unit : '';
                $estimationParts[] = $estimationPart;
            }

            return view('estimation.edit', compact('clients', 'assets', 'estimation', 'services', 'parts', 'estimationServices', 'estimationParts'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit estimation')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'estimation_id' => 'required',
                    'client' => 'required',
                    'asset' => 'required',
                    'due_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $id = Crypt::decrypt($id);
            $estimation = Estimation::find($id);

            $estimation->estimation_id = $request->estimation_id;
            $estimation->title = $request->title;
            $estimation->client = $request->client;
            $estimation->asset = $request->asset;
            $estimation->due_date = $request->due_date;
            $estimation->notes = $request->notes;
            $estimation->save();
            $services = !empty($request->services) ? $request->services : [];
            $parts = !empty($request->parts) ? $request->parts : [];

            for ($i = 0; $i < count($services); $i++) {
                $estimationService = EstimationServicePart::find(isset($services[$i]['id']) ? $services[$i]['id'] : 0);
                if ($estimationService == null) {
                    $estimationService = new EstimationServicePart();
                    $estimationService->estimation_id = $estimation->id;
                }
                $estimationService->service_part_id = $services[$i]['service_part_id'];
                $estimationService->quantity = $services[$i]['quantity'];
                $estimationService->amount = $services[$i]['amount'];
                $estimationService->description = $services[$i]['description'];
                $estimationService->type = 'service';
                $estimationService->save();
            }


            for ($i = 0; $i < count($parts); $i++) {
                $estimationPart = EstimationServicePart::find(isset($parts[$i]['id']) ? $parts[$i]['id'] : 0);
                if ($estimationPart == null) {
                    $estimationPart = new EstimationServicePart();
                    $estimationPart->estimation_id = $estimation->id;
                }
                $estimationPart->service_part_id = $parts[$i]['service_part_id'];
                $estimationPart->quantity = $parts[$i]['quantity'];
                $estimationPart->amount = $parts[$i]['amount'];
                $estimationPart->description = $parts[$i]['description'];
                $estimationPart->type = 'part';
                $estimationPart->save();
            }
            return redirect()->route('estimation.index')->with('success', __('Estimation successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function destroy(Estimation $estimation)
    {
        if (\Auth::user()->can('delete estimation')) {
            EstimationServicePart::where('estimation_id', $estimation->id)->delete();
            $estimation->delete();
            return redirect()->route('estimation.index')->with('success', __('Estimation successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

    public function estimationNumber()
    {
        $lastEstimation = Estimation::where('parent_id', parentId())->latest()->first();
        if ($lastEstimation == null) {
            return 1;
        } else {
            return $lastEstimation->estimation_id + 1;
        }
    }

    public function getServicePart(Request $request)
    {
        $servicePart = ServicePart::find($request->id);
        return response()->json($servicePart);
    }

    public function servicePartDestroy(Request $request)
    {
        if (\Auth::user()->can('delete estimation service & part')) {
            if (isset($request->id) && !empty($request->id)) {
                $servicePart = EstimationServicePart::find($request->id);
                $servicePart->delete();
            }

            return 1;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function estimationStatus(Request $request, $estimationId)
    {
        $estimation = Estimation::find($estimationId);
        $estimation->status = $request->status;
        $estimation->save();
        return redirect()->back()->with('success', __('Estimation status successfully changed.'));
    }
}
