<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Notification;
use App\Models\ServicePart;
use Illuminate\Http\Request;

class AssetController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage asset')) {
            $assets = Asset::where('parent_id', parentId())->get();
            return view('asset.index', compact('assets'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function create()
    {
        $parts=ServicePart::where('parent_id',parentId())->where('type','part')->get()->pluck('title','id');
        $parts->prepend(__('Select Part'),'');

        $assets=Asset::where('parent_id',parentId())->get()->pluck('name','id');
        $assets->prepend(__('Select Parent Asset'),'');
        return view('asset.create',compact('parts','assets'));
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create asset')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'asset_number' => 'required',
                    'part' => 'required',
                    'giai' => 'required',
                    'order_date' => 'required',
                    'installation_date' => 'required',
                    'purchase_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $asset = new Asset();
            $asset->name = $request->name;
            $asset->asset_number = $request->asset_number;
            $asset->part = $request->part;
            $asset->parent_asset = !empty($request->parent_asset)?$request->parent_asset:0;
            $asset->giai = $request->giai;
            $asset->order_date = $request->order_date;
            $asset->installation_date = $request->installation_date;
            $asset->purchase_date = $request->purchase_date;
            $asset->warranty_expiration = !empty($request->warranty_expiration)?$request->warranty_expiration:null;
            $asset->warranty_notes = !empty($request->warranty_notes)?$request->warranty_notes:null;
            $asset->description = !empty($request->description)?$request->description:null;
            $asset->parent_id = parentId();
            $asset->save();


            return redirect()->route('asset.index')->with('success', __('Asset successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show(Asset $asset)
    {
        return view('asset.show', compact('asset'));
    }


    public function edit(Asset $asset)
    {
        $parts=ServicePart::where('parent_id',parentId())->where('type','part')->get()->pluck('title','id');
        $parts->prepend(__('Select Part'),'');

        $assets=Asset::where('parent_id',parentId())->where('id','!=',$asset->id)->get()->pluck('name','id');
        $assets->prepend(__('Select Parent Asset'),'');
        return view('asset.edit',compact('asset','parts','assets'));
    }


    public function update(Request $request, Asset $asset)
    {
        if (\Auth::user()->can('edit asset')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'asset_number' => 'required',
                    'part' => 'required',
                    'giai' => 'required',
                    'order_date' => 'required',
                    'installation_date' => 'required',
                    'purchase_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }


            $asset->name = $request->name;
            $asset->asset_number = $request->asset_number;
            $asset->part = $request->part;
            $asset->parent_asset = !empty($request->parent_asset)?$request->parent_asset:0;
            $asset->giai = $request->giai;
            $asset->order_date = $request->order_date;
            $asset->installation_date = $request->installation_date;
            $asset->purchase_date = $request->purchase_date;
            $asset->warranty_expiration = !empty($request->warranty_expiration)?$request->warranty_expiration:null;
            $asset->warranty_notes = !empty($request->warranty_notes)?$request->warranty_notes:null;
            $asset->description = !empty($request->description)?$request->description:null;
            $asset->save();

            return redirect()->route('asset.index')->with('success', __('Asset successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function destroy(Asset $asset)
    {
        if (\Auth::user()->can('delete asset')) {
            $asset->delete();
            return redirect()->route('asset.index')->with('success', __('Asset successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
