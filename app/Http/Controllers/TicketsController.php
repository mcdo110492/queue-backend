<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use \Tymon\JWTAuth\Exceptions\UserNotDefinedException;

use App\Tickets;
use App\TicketsUsers;
use App\UsersCounters;


class TicketsController extends Controller
{


    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['generate']]);
        
    }

    public function getAuthUser() {
        $user = auth()->user();
        return $user->id;
    }

    public function getAuthUserDepartment() {
        $user = auth()->user();
        return $user->department_id;
    }

    /**
     * This will issue a new ticket and will be given to the customer
     * This method is open to anyone
     */
    public function generate(Request $request)
    {
        $request->validate([
            'priority' => 'required|integer',
            'department_id' => 'required|integer'
        ]);

        $now = Carbon::now()->toDateString();

        $getMaxTicket = Tickets::where(['date_issued' => $now])->count();

        $ticket_number = $getMaxTicket + 1;

        $department_id = $request->input('department_id');

        $data = [
            'ticket_number' => $ticket_number,
            'priority' => $request->input('priority'),
            'date_issued' => $now,
            'department_id' => $department_id
        ];


        $ticket = Tickets::create($data);

        $getPeopleinWating = Tickets::where([
            ['status', '=', 0],
            ['id', '!=', $ticket->id]
        ])->count();

        $estimatedWaitingTime = 10;

        $payload = [
            'id' => $ticket->id,
            'date_issed' => $ticket->date_issued,
            'created_at' => $ticket->created_at->toDateTimeString(),
            'priority' => $ticket->priority,
            'ticket_number' => $ticket->ticket_number,
            'people_in_waiting' => $getPeopleinWating,
            'estimated_waiting_time' => $estimatedWaitingTime
        ];

        event(new \App\Events\ProcessIssueToken($ticket, $department_id));

        return response()->json(compact('payload'),201);
    }

    /**
     * This will get the currently pending tickets 
     * This will depend to the current system date
     */
    public function getNowPending()
    {

        $now = Carbon::now()->toDateString();

        $department_id = $this->getAuthUserDepartment();

        $q = Tickets::where(['status' => 0, 'department_id' => $department_id]);

        $data = $q->get();

        return response()->json(['payload' => compact('data')]);

    }

    public function getList($status)
    {

        $now = Carbon::now()->toDateString();

        $q = Tickets::with(['latestUser.user','department.counter'])->where(['status' => $status,'date_issued' => $now])->get();

        $payload = ['data' => $q];

        return response()->json(compact('payload'), 200);

    }


    /**
     * This will call a ticket from the queue list
     * The status of the ticket must be 0 - pending
     * It can be called by anyone but it will restrict if the other user is currently calling this ticket
     */
    public function call(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
            'served_time' => 'required'
        ]);

        $ticket_id = $request->input('ticket_id');
        $served_time = $request->input('served_time');

        $tickets = Tickets::findOrFail($ticket_id);
        
        $ticketStatus = $tickets->status;

        $user_id = $this->getAuthUser();

        if($ticketStatus === 0)
        {
            //Get the latest current user transaction
            $checkUserCurrentTransaction = TicketsUsers::where(['user_id' => $user_id])->latest()->first();
            $userCurrentTicketStatus = isset($checkUserCurrentTransaction->status) ? $checkUserCurrentTransaction->status  : 0;
            if($userCurrentTicketStatus === 1 || $userCurrentTicketStatus === 2)
            {
                return response()->json(['payload' => 'Unable to call this token. You still have other token to process or complete'], 403);
            }

            $now = Carbon::now();

            $ticketUserData = [
                'user_id' => $user_id,
                'ticket_id' => $ticket_id,
                'status' => 1,
                'served_time' => $served_time,
                'complete_time' => $now
            ];

            $tickets->update(['status' => 1]);

            $ticketUser = TicketsUsers::create($ticketUserData);
            
            $message = 'You called this token';

            $payload = compact('message');

            broadcast(new \App\Events\ProcessTicketCall($tickets->id, $tickets->priority, $tickets->department_id))->toOthers();

            event(new \App\Events\DisplayNowServing($tickets->id));

            return response()->json(compact('payload'), 200);
            
        }

        $payload = ['message' => 'This token already been called'];
        
        return response()->json(compact('payload'), 403);
    }
    
    /*
     * This method will call again the current ticket 
     * And check if this ticket belongs to the right user
    */
    public function callAgain(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
            'served_time' => 'required'
        ]);

        $ticket_id = $request->input('ticket_id');
        $served_time = $request->input('served_time');

        $tickets = Tickets::findOrFail($ticket_id);
        $user_id = $this->getAuthUser();


        $checkTicketBelongToUser = TicketsUsers::where(['ticket_id' => $tickets->id, 'user_id' => $user_id])->count();

        if($checkTicketBelongToUser > 0)
        {

            $tickets->update(['status' => 1]);

            event(new \App\Events\DisplayNowServing($tickets->id));
            
            $message = 'You recall this token';

            $payload = compact('message');

            return response()->json(compact('payload'), 200);
        }

        $payload = ['message' => 'This token does not belongs to you'];

        return response()->json(compact('payload'),403);
    }

    /**
     * This will process/serve a ticket after you have been called
     * This will restrict and validate if you are not the user that previously called this ticket
     */
    public function serving(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
            'served_time' => 'required'
        ]);
        
        $ticket_id = $request->input('ticket_id');
        $served_time = $request->input('served_time');

        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        $user_id = $this->getAuthUser();

        if($ticketStatus === 1)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'status' => 1, 'user_id' => $user_id])->count();

            if($checkTicketOwner > 0)
            {
                $now = Carbon::now();
                $ticketUserData = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $user_id,
                    'served_time' => $served_time,
                    'complete_time' => $now,
                    'status' => 2
                ];

                $tickets->update(['status' => 2]);

                $ticketUser = TicketsUsers::create($ticketUserData);
                
                $message = 'You currently serving/process this token';
    
                $payload = compact('message');
    
                return response()->json(compact('payload'), 200);

            }

            $message = 'This token has been called by other user';
    
            $payload = compact('message');

            return response()->json(compact('payload'), 403);
        }

        $message = 'This token already been served/process';
    
        $payload = compact('message');

        return response()->json(compact('payload'), 403);

    }

    /**
     * This will complete the ticket transaction
     * This will validate and restrict if you are not the user that previously process/serving this ticket
     */
    public function complete(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
            'served_time' => 'required'
        ]);

        $ticket_id = $request->input('ticket_id');
        $served_time = $request->input('served_time');

        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        $user_id = $this->getAuthUser();

        if($ticketStatus === 2)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'status' => 2, 'user_id' => $user_id])->count();

            if($checkTicketOwner > 0)
            {
                $now = Carbon::now();
                $ticketUserData = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $user_id,
                    'served_time' => $served_time,
                    'complete_time' => $now,
                    'status' => 3
                ];

                $tickets->update(['status' => 3]);

                $ticketUser = TicketsUsers::create($ticketUserData);
                
                $message = 'This token is finish/complete';
    
                $payload = compact('message');
    
                return response()->json(compact('payload'), 200);

            }

            $message = 'This token already been finished/completed by other user';
    
            $payload = compact('message');

            return response()->json(compact('payload'), 403);
        }

        $message = 'This token already been finished/completed';
    
        $payload = compact('message');

        return response()->json(compact('payload'), 403);
    }

     /**
     * This will stop the ticket
     * This will validate and restrict if you are not the user who previously called or process this ticket
     */
    public function stop(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
            'served_time' => 'required'
        ]);

        $ticket_id = $request->input('ticket_id');
        $served_time = $request->input('served_time');

        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        $user_id = $this->getAuthUser();

        if($ticketStatus > 0)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'user_id' => $user_id])
            ->whereBetween('status', [1,2])
            ->count();

            if($checkTicketOwner > 0)
            {
                $now = Carbon::now();
                $ticketUserData = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $user_id,
                    'complete_time' => $now,
                    'served_time' => $served_time,
                    'status' => 4
                ];

                $tickets->update(['status' => 4]);

                $ticketUser = TicketsUsers::create($ticketUserData);
                
                $message = 'You stopped/cancelled this token';
    
                $payload = compact('message');
    
                return response()->json(compact('payload'), 200);

            }

            $message = 'This token has been called or process by other user';
    
            $payload = compact('message');

            return response()->json(compact('payload'), 403);
        }

        $message = 'Unable to stop this token. You need to call or process it first';
    
        $payload = compact('message');

        return response()->json(compact('payload'), 403);
    }

    /**
     * This will back the ticket to queue list
     * This will validate and restrict if you are not the user who previously called this ticket
     */
    public function backToQueue(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer',
            'served_time' => 'required'
        ]);

        $ticket_id = $request->input('ticket_id');
        $served_time = $request->input('served_time');

        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        $user_id = $this->getAuthUser();

        if($ticketStatus === 1)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'status' => 1, 'user_id' => $user_id])->count();

            if($checkTicketOwner > 0)
            {

                $now = Carbon::now();
                $ticketUserData = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $user_id,
                    'complete_time' => $now,
                    'served_time' => $served_time,
                    'status' => 0
                ];

                $tickets->update(['status' => 4]);

                $ticketUser = TicketsUsers::create($ticketUserData);

                $tickets->update(['status' => 0]);

                broadcast(new \App\Events\ProcessTicketBackToQueue($tickets->id, $tickets->priority, $tickets->department_id))->toOthers();
                
                $message = 'This token has been back to Queue List';
    
                $payload = compact('message');
    
                return response()->json(compact('payload'), 200);
            }

            $message = 'This token has been called by other user';
    
            $payload = compact('message');

            return response()->json(compact('payload'), 403);
        }

        $message = 'Unable to back this token to Queue List';
    
        $payload = compact('message');

        return response()->json(compact('payload'), 403);
    }

    public function getUserLastTransaction(){
        $user_id = $this->getAuthUser();

        $token = TicketsUsers::with('ticket')->where(['user_id' => $user_id])->latest()->first();

        $payload = ['token' => $token];

        return response()->json($payload, 200);

    }

   

    public function getUserCurrentLogs()
    {
        $user_id = $this->getAuthUser();

        $get = TicketsUsers::with('ticket')->byUser($user_id)->latest()->get();
        
        $payload = ['data' => $get];

        return response()->json(compact('payload'), 200);
    }
}