<?php

namespace App\Http\Controllers;

use App\Models\ClientDetail;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage client')) {
            $clients = User::where('parent_id', parentId())->where('type','client')->get();
            return view('client.index', compact('clients'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function create()
    {
        return view('client.create');
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create client')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'service_address' => 'required',
                    'service_city' => 'required',
                    'service_state' => 'required',
                    'service_country' => 'required',
                    'service_zip_code' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $ids = parentId();
            $authUser = \App\Models\User::find($ids);
            $totalClient = $authUser->totalClient();
            $subscription = Subscription::find($authUser->subscription);
            if ($totalClient >= $subscription->client_limit && $subscription->client_limit != 0) {
                return redirect()->back()->with('error', __('Your client limit is over, please upgrade your subscription.'));
            }
            $userRole = Role::where('parent_id',parentId())->where('name','client')->first();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->password = \Hash::make(123456);
            $user->type = $userRole->name;
            $user->profile = 'avatar.png';
            $user->lang = 'english';
            $user->parent_id = parentId();
            $user->save();
            $user->assignRole($userRole);

            if(!empty($user)){
                $client=new ClientDetail();
                $client->client_id=$this->clientNumber();
                $client->user_id=$user->id;
                $client->company=$request->company;
                $client->service_address=$request->service_address;
                $client->service_city=$request->service_city;
                $client->service_state=$request->service_state;
                $client->service_country=$request->service_country;
                $client->service_zip_code=$request->service_zip_code;
                if(isset($request->billing_info)){
                    $client->billing_address=$request->billing_address;
                    $client->billing_city=$request->billing_city;
                    $client->billing_state=$request->billing_state;
                    $client->billing_country=$request->billing_country;
                    $client->billing_zip_code=$request->billing_zip_code;
                }else{
                    $client->billing_address=$request->service_address;
                    $client->billing_city=$request->service_city;
                    $client->billing_state=$request->service_state;
                    $client->billing_country=$request->service_country;
                    $client->billing_zip_code=$request->service_zip_code;
                }
                $client->parent_id=parentId();
                $client->save();
            }

            $module = 'client_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $setting = settings();
            $errorMessage = '';

            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $user->id);


                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $request->email;

                $response = commonEmailSend($to, $data);

                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }

            return redirect()->route('client.index')->with('success', __('Client successfully created.'.'</br>'.$errorMessage));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show($ids)
    {
        $id=Crypt::decrypt($ids);
        $client=User::find($id);
        return view('client.show',compact('client'));
    }


    public function edit($id)
    {
        $user=User::find($id);
        return view('client.edit',compact('user'));
    }


    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit client')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'service_address' => 'required',
                    'service_city' => 'required',
                    'service_state' => 'required',
                    'service_country' => 'required',
                    'service_zip_code' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->save();
            if(!empty($user)){
                $client=ClientDetail::where('user_id',$user->id)->first();
                $client->company=$request->company;
                $client->service_address=$request->service_address;
                $client->service_city=$request->service_city;
                $client->service_state=$request->service_state;
                $client->service_country=$request->service_country;
                $client->service_zip_code=$request->service_zip_code;
                $client->billing_address=$request->billing_address;
                $client->billing_city=$request->billing_city;
                $client->billing_state=$request->billing_state;
                $client->billing_country=$request->billing_country;
                $client->billing_zip_code=$request->billing_zip_code;
                $client->save();
            }
            return redirect()->route('client.index')->with('success', __('Client successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete client')) {
            $user = User::find($id);
            $user->delete();
            ClientDetail::where('user_id',$id)->delete();
            return redirect()->route('client.index')->with('success', __('Client successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function clientNumber()
    {
        $lastClient = ClientDetail::where('parent_id', parentId())->latest()->first();
        if ($lastClient == null) {
            return 1;
        } else {
            return $lastClient->client_id + 1;
        }
    }

}
