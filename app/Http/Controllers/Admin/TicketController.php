<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\HostingCustomer;
use Spatie\Permission\Models\Role;
use App\Mail\TicketMail;
use App\Models\TicketDocument;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Mail;
use DB;
use Hash;
use Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new Ticket;
        // $this->middleware('permission:Ticket-System', ['only' => ['index','store','create','edit','destroy','update']]);

        $this->columns = [
            "id",
            "ticket_code",
            "subject",
            "department",
            "priority",
            "status",
            "created_at",
        ];
    }

    public static function generateTicketCode($ticket_id) {

		// Generate 4 random digits
        $code = '#' . mt_rand(1000, 9999);

        // Append the ticket_id
        $code .= $ticket_id;
    
        return $code;
	}
    public function index()
    {
        $ticket = Ticket::all();
       
        return view('admin.tickets.index',compact('ticket'));
    }

    public function ticketAjax(Request $request)
    {
        // dd($request->start);
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchTicket($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $categories = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $categories = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        $i =  $request->start;
        foreach ($categories as $value) {
            
            $data = [];
            $data['id'] = ++$i;
            $data['ticket_code'] = $value->ticket_code;
            $data['subject'] = substr($value->subject, 0, 50);
            $data['department'] = ucfirst($value->department) ?? 'N/A';

            // $data['type'] = $value->type == 2 ? 'Customer' : ($value->type == 1 ? 'Vendor' : 'Unknown');

            $data['priority'] =   ucfirst($value->priority);
            $data['created_at'] = date('Y-m-d', strtotime($value->created_at));


            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer ticketStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";

            // $view = "<a href='" . route('admin.ticket-system.show', $value->id) . "' data-status='1' class='badge badge-secondary userStatus'>View</a>";

            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.ticket-system.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';

            $action .= '<a href="' . route('admin.ticket-system.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            $action .= '<a href="javascript:void(0)" onclick="deleteTickets(this)" data-url="' . route('admin.ticketdestory') . '" class="toolTip deleteTickets" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';

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
        $ticket = null;
        // $users = User::where("status","1")->where('id', '!=', 1)->where('role', '=', 2)->get(['id',"name"]);
        $customerlist = HostingCustomer::get(['id',"name"]);
        
        return view('admin.tickets.create',compact('ticket','customerlist'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request) {
        $input = $request->all();
        $validate = Validator($request->all(), [
            'subject' => 'required',
            'department' => 'required',
        ]);
    
        $attr = [
            'subject' => 'Subject',
            'department' => 'Department',
        ];
        $validate->setAttributeNames($attr);
    
        if ($validate->fails()) {
            return redirect()->route('admin.ticket-system.create')
                             ->withInput($request->all())
                             ->withErrors($validate);
        } else {
            try {
                $ticket = new Ticket;
                $ticketcode = $this->generateTicketCode(1);
    
                $ticket->ticket_code = $ticketcode;
                $ticket->customer_id = $request->customer_id;
                $ticket->customer_email = $request->customer_email;
                $ticket->subject = $request->subject;
                $ticket->department = $request->department;
                $ticket->user_id = $request->user_id;
                $ticket->priority = $request->priority;
                $ticket->message = $request->message;
                $ticket->save();
    
                // Process attachments
                $getattachments = [];
                if ($request->hasfile('attachment')) {
                    foreach ($request->file('attachment') as $file) {
                        $filename = time() . '_' . str_replace(' ', '', $file->getClientOriginalName());
                        $file->move(public_path('ticketfile'), $filename);
    
                        // Save file information to ticket_document table
                        TicketDocument::create([
                            'ticket_id' => $ticket->id,
                            'attachment' => $filename,
                            'file_path' => 'ticketfile/' . $filename,
                        ]);
    
                        // Collect file paths for email attachment
                        $getattachments[] = public_path('ticketfile/' . $filename);
                    }
                }
                // $type1 = 1;
                // $type2 = 0;
                // Send emails with attachments
                if (Auth::user()->role == 2) {
                    $types = Auth::user()->name;
                    Mail::to($ticket->customer_email)->send(new TicketMail($ticket, $ticketcode,$types, $getattachments)); 
                }
                $adminEmail = User::select('users.email','users.name')->where('id', '1')->first();
                $types =$adminEmail->name;

                Mail::to($adminEmail->email)->send(new TicketMail($ticket, $ticketcode,$types, $getattachments));

                if (Auth::user()->role == 1) {
                    $customer = HostingCustomer::select('hosting_customers.email','hosting_customers.name')->where('id', $request->user_id)->first();
                    $ticket->user_id = $customer->id;
                    // $type1 = 0;
                    // $type2 = 1;
                    $types = $customer->name;
               
                    Mail::to($customer->email)->send(new TicketMail($ticket, $ticketcode,$types, $getattachments));
                }
    
                $request->session()->flash('success', 'Ticket added successfully');
                return redirect()->route('admin.ticket-system.index');
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.ticket-system.index');
            }
        }
    }
    


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket=Ticket::find($id);
      
        return view('admin.tickets.view',compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            $ticket = Ticket::find($id);
            
            if (isset($ticket->id)) {

                $type = 'edit';
           
                $customerlist = HostingCustomer::get(['id',"name"]);

                // Retrieve existing documents for the ticket
                $existingDocuments = $ticket->documents; // Get related documents

                return view('admin.tickets.create', compact('ticket', 'type','customerlist','existingDocuments'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.ticket-system.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.ticket-system.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        if (isset($id) && $id != null) {

            $ticket = Ticket::find($id);
            if (!$ticket) {
                $request->session()->flash('error', 'Ticket not found');
                return redirect()->route('admin.ticket-system.edit', ['id' => $id]);
            }

            $request->validate([
                'subject' => 'required',
                'department' => 'required',
            ]);

            $attr = [
                'subject' => 'Subject',
                'department' => 'Department',
            ];


            try {

                $ticket->customer_id = $request->customer_id;
                $ticket->customer_email = $request->customer_email;
                $ticket->subject = $request->subject;
                $ticket->department = $request->department;
                $ticket->user_id = $request->user_id;
                $ticket->priority = $request->priority;
                $ticket->message = $request->message;
                $ticket->updated_at = now();

                if ($ticket->save()) {

                    // Handle the removal of old files
                    if ($request->has('remove_document')) {
                        foreach ($request->remove_document as $documentId) {
                            $document = TicketDocument::find($documentId);
                            if ($document) {
                                // Unlink (delete) the old file from the folder
                                $filePath = $document->attachment;
                                if (file_exists($filePath)) {
                                    unlink($filePath);
                                }
                                // Delete the document record from the database
                                $document->delete();
                            }
                        }
                    }
                   
                    // Handle new file uploads
                    if ($request->hasfile('attachment')) {
                        foreach ($request->file('attachment') as $file) {
                            // Store each new file
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $filename = str_replace(' ', '', $filename);
                            $file->move(public_path('ticketfile'), $filename);

                            // Save file information to the ticket_document table
                            TicketDocument::create([
                                'ticket_id' => $ticket->id,
                                'attachment' => $filename,
                                'file_path' => 'ticketfile/' . $filename,
                            ]);
                        }
                    }
    
                      
                    $request->session()->flash('success', 'Ticket updated successfully');
                    return redirect()->route('admin.ticket-system.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.ticket-system.edit', ['id' => $id]);
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.ticket-system.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.ticket-system.edit', ['id' => $id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function ticketdestory(Request $request)
    {
        $id = $request->id;
        $record = Ticket::findOrFail($id);
        $record->delete();

        // BankInformation::where('user_id', $ticket->id)->delete();
        return redirect()->route('admin.ticket-system.index')->with('success', 'Ticket deleted Successfully.');;

    }

    public function ChangeTicketStatus(Request $request)
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
