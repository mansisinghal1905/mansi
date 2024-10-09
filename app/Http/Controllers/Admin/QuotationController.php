<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\QuotationDetail;
use App\Models\QuotationMoreDetail;


use App\Models\Category;
use DB;
use Hash;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new QuotationDetail;

        $this->columns = [
            "id",
            "name",
            "status",
        ];
    }

    public function index()
    {
        $quotation = QuotationDetail::all();
    
        return view('admin.quotation.index',compact('quotation'));
    }

    public function quotationAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchQuotation($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $categories = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $categories = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        $i = $request->start;
        foreach ($categories as $value) {
            $data = [];
            $data['id'] = ++$i;
            $data['quotation_subject'] = isset($value->quotation_subject) ? ucfirst($value->quotation_subject) : '-';
            $data['quotation_code'] = isset($value->quotation_code) ? ucfirst($value->quotation_code) : '-';
            // $data['short_description'] = $value->short_description;
            $data['category_id'] = $value->getCategory->name;
            $data['start_date'] = date('Y-m-d', strtotime($value->start_date));
            $data['end_date'] = date('Y-m-d', strtotime($value->end_date));

            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer quotationStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.quotation.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.quotation.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

           $action .= '<a href="javascript:void(0)" onclick="deleteQuotation(this)" data-url="' . route('admin.quotationdestory') . '" class="toolTip deleteQuotation" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
 
            $action.="</div>";

            $data['view'] = $action;
            $data['status'] = $status;
            $result[] = $data;

        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $quotation = null;
        $categorylist = Category::where("status","1")->get(['id',"name"]);

        return view('admin.quotation.create',compact('quotation','categorylist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        // dd($request->all());
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'quotation_name' => 'required',
            'quotation_subject' => 'required'
        ]);
        $attr = [
            'quotation_name' => 'Quotation Name',
            'quotation_subject' => 'Quotation Subject',

        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.quotation.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $item1 = $request->input('quotation_name', []); // Assuming 'items' is an array of item data
                $item2 = $request->input('short_description', []); // Assuming 'items' is an array of item data
                
               $quotation_code = "Q-" .rand(100000,999999);

                $quotation = new QuotationDetail;
                $quotation->quotation_subject = $request->quotation_subject;
                $quotation->quotation_code = $quotation_code;
                $quotation->description = $request->description;
                $quotation->category_id = $request->category_id;
                $quotation->start_date = $request->start_date;
                $quotation->end_date = $request->end_date;
                $quotation->created_at = date('Y-m-d H:i:s');
                $quotation->updated_at = date('Y-m-d H:i:s');
                // dd($quotation);
                if ($quotation->save()) {

                    foreach ($item1 as $key => $item) {
    
                            $quotationItem = new QuotationMoreDetail;
                            $quotationItem->quotation_details_id = $quotation->id;
                            $quotationItem->quotation_name = $item1[$key];
                            $quotationItem->short_description = $item2[$key];
                            $quotationItem->save();
                        
                    }
                    $request->session()->flash('success', 'Quotation added successfully');
                    return redirect()->route('admin.quotation.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.quotation.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.quotation.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quotation=QuotationDetail::find($id);

        // Retrieve the related more details for the quotation
        $quotationMoreDetails = QuotationMoreDetail::where('quotation_details_id', $id)->get();

        return view('admin.quotation.view',compact('quotation','quotationMoreDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {

            $quotation = QuotationDetail::with('getQotation')->find($id);
            // dd($quotation);
            
            if (isset($quotation->id)) {
            
                $type = 'edit';
                $categorylist = Category::where("status","1")->get(['id',"name"]);
               
               
                return view('admin.quotation.create', compact('quotation', 'type','categorylist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.quotation.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.quotation.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $quotation = QuotationDetail::where('id', $id)->first();

            if (isset($quotation->id)) {
                $validate = Validator($request->all(),  [
                    'quotation_name' => 'required',
            
                ]);
                $attr = [
                    'quotation_name' => 'Quotation Name',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editProjects', ['id' => $quotation->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $item1 = $request->input('quotation_name', []); // Assuming 'items' is an array of item data
                        $item2 = $request->input('short_description', []); // Assuming 'items' is an array of item data
                        
                        // $quotation_code = "Q-" .rand(100000,999999);
                     
                        $quotation =  QuotationDetail::find($id);
                        $quotation->quotation_subject = $request->quotation_subject;
                        $quotation->quotation_code = $quotation->quotation_code;
                        $quotation->description = $request->description;
                        $quotation->category_id = $request->category_id;
                        $quotation->start_date = $request->start_date;
                        $quotation->end_date = $request->end_date;
                    
                        $quotation->updated_at = date('Y-m-d H:i:s');
                        if ($quotation->save()) {

                        QuotationMoreDetail::where('quotation_details_id', $quotation->id)->delete();

                            foreach ($item1 as $key => $item) {
                                $quotationItem = new QuotationMoreDetail;
                                $quotationItem->quotation_details_id = $quotation->id;
                                $quotationItem->quotation_name = $item1[$key];
                                $quotationItem->short_description = $item2[$key];
                                $quotationItem->save();  
                            }
                            $request->session()->flash('success', 'Quotationupdate successfully');
                            return redirect()->route('admin.quotation.index');
                        } else {
                            
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.quotation.index');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.quotation.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.quotation.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.quotation.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function quotationdestory(Request $request)
    {
        $id = $request->id;
        $record = QuotationDetail::findOrFail($id);
        $record->status = 2; 
        $record->save();
        return redirect()->route('admin.quotation.index')->with('success', 'Quotation deleted Successfully.');
    }

    public function changeQuotationStatus(Request $request)
    {
        $response = $this->Model->where('id', $request->id)->update(['status' => $request->status]);
        if ($response) {
            return json_encode([
                'status' => true,
                "message" => "Status Changes Successfully"
            ]);
        } else {
            return json_encode([
                'status' => false,
                "message" => "Status Changes Fails"
            ]);
        }
    }
}
