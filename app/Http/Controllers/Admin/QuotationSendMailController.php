<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuotationSendMail;
use App\Models\QuotationDetail;
use App\Models\QuotationMoreDetail;
use App\Models\User;
use DB;
use Hash;

class QuotationSendMailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new QuotationSendMail;

        $this->columns = [
            "id",
            "client_id",
            "quatation_id",
            "status",
        ];
    }

    public function index()
    {
        $quotationmail = QuotationSendMail::all();
    
        return view('admin.quotationmail.index',compact('quotationmail'));
    }

    public function quotationmailAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchmailQuotation($request, $this->columns);
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
            $data['client_id'] = $value->getClient->name;
            $data['quotation_id'] = $value->getQuotation->quotation_subject;
          
            // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer quotationStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
            $data['status'] = '<select class="form-control status-select" data-id="' . $value->id . '" data-select2-selector="status">';
            $data['status'] .= '<option value="new" data-bg="bg-success"' . ($value->status == 'new' ? ' selected' : '') . '>New</option>';
            $data['status'] .= '<option value="accepted" data-bg="bg-warning"' . ($value->status == 'accepted' ? ' selected' : '') . '>Accepted</option>';
            $data['status'] .= '<option value="declined" data-bg="bg-danger"' . ($value->status == 'declined' ? ' selected' : '') . '>Declined</option>';
            $data['status'] .= '<option value="revised" data-bg="bg-danger"' . ($value->status == 'revised' ? ' selected' : '') . '>Revised</option>';

            $data['status'] .= '</select>';

            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.quotationmail.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            // $action .= '<a href="' . route('admin.quotationmail.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            //    $action .= '<a href="javascript:void(0)" onclick="deleteQuotation(this)" data-url="' . route('admin.quotationmaildestory') . '" class="toolTip deleteQuotation" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
 
            $action.="</div>";

            $data['view'] = $action;
            // $data['status'] = $status;
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
        $quotationmail = null;
        $clientlist = User::where("status","1")->where('id', '!=', 1)->get(['id',"name"]);
        $quotationlist = QuotationDetail::where("status","1")->get(['id',"quotation_subject"]);
        
        return view('admin.quotationmail.create',compact('quotationmail','clientlist','quotationlist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        // dd($request->all());
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'quotation_id' => 'required',
        ]);
        $attr = [
            'quotation_id' => 'Quotation Name',
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.quotationmail.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                

                $quotationmail = new QuotationSendMail;
       
                $quotationmail->quotation_id = $request->quotation_id;
                $quotationmail->client_id = $request->client_id;
                $quotationmail->created_at = date('Y-m-d H:i:s');
                $quotationmail->updated_at = date('Y-m-d H:i:s');
                // dd($quotationmail);
                if ($quotationmail->save()) {

                    $request->session()->flash('success', 'Quotation added successfully');
                    return redirect()->route('admin.quotationmail.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.quotationmail.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.quotationmail.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quotationmail=QuotationSendMail::find($id);

        // Retrieve the related more details for the quotation
        // $quotationMoreDetails = QuotationMoreDetail::where('quotation_details_id', $id)->get();

        return view('admin.quotationmail.view',compact('quotationmail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {

            $quotationmail = QuotationSendMail::with('getQotation')->find($id);
            // dd($quotation);
            
            if (isset($quotationmail->id)) {
            
                $type = 'edit';
                $categorylist = Category::where("status","1")->get(['id',"name"]);
               
               
                return view('admin.quotationmail.create', compact('quotationmail', 'type','categorylist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.quotationmail.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.quotationmail.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $quotationmail = QuotationSendMail::where('id', $id)->first();

            if (isset($quotationmail->id)) {
                $validate = Validator($request->all(),  [
                    'quotation_name' => 'required',
            
                ]);
                $attr = [
                    'quotation_name' => 'Quotation Name',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editProjects', ['id' => $quotationmail->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $item1 = $request->input('quotation_name', []); // Assuming 'items' is an array of item data
                        $item2 = $request->input('short_description', []); // Assuming 'items' is an array of item data
                        
                        // $quotation_code = "Q-" .rand(100000,999999);
                     
                        $quotationmail =  QuotationSendMail::find($id);
                       
                        $quotationmail->quotation_code = $quotation->quotation_code;
                        $quotationmail->description = $request->description;
                        $quotationmail->category_id = $request->category_id;
                        $quotationmail->updated_at = date('Y-m-d H:i:s');
                        if ($quotationmail->save()) {

                            $request->session()->flash('success', 'Quotationupdate successfully');
                            return redirect()->route('admin.quotationmail.index');
                        } else {
                            
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.quotationmail.index');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.quotationmail.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.quotationmail.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.quotationmail.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    // public function quotationdestory(Request $request)
    // {
    //     $id = $request->id;
    //     $record = QuotationSendMail::findOrFail($id);
    //     $record->status = 2; 
    //     $record->save();
    //     return redirect()->route('admin.quotationmail.index')->with('success', 'Quotation deleted Successfully.');
    // }

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
