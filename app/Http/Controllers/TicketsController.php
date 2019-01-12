<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Tickets;
use App\TicketsUsers;

class TicketsController extends Controller
{

    protected $user_id;

    public function __construct()
    {

        $this->middleware('auth:api');
        $id = 2;
        $this->user_id = $id;
    }

    /**
     * This will issue a new ticket and will be given to the customer
     * This method is only available to the user ticket issuer
     */
    public function issue(Request $request)
    {
        $request->validate([
            'priority' => 'required|integer'
        ]);

        $now = Carbon::now()->toDateString();

        $getMaxTicket = Tickets::where(['date_issued' => $now])->count();

        $ticket_number = $getMaxTicket + 1;

        $data = [
            'ticket_number' => $ticket_number,
            'name' => $request->input('name'),
            'priority' => $request->input('priority'),
            'date_issued' => $now
        ];

        $ticket = Tickets::create($data);

        return response()->json(['payload' => $ticket],201);
    }

    /**
     * This will get the currently pending tickets 
     * This will depend to the current system date
     */
    public function getNowPending()
    {
        $now = Carbon::now()->toDateString();

        $q = Tickets::where(['status' => 0, 'date_issued' => $now]);

        $count = $q->count();
        $data = $q->get();

        return response()->json(['payload' => compact('count','data')]);

    }


    /**
     * This will call a ticket from the queue list
     * The status of the ticket must be 0 - pending
     * It can be called by anyone but it will restrict if the other user is currently calling this ticket
     */
    public function call(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer'
        ]);

        $tickets = Tickets::findOrFail($request->input('ticket_id'));
        
        $ticketStatus = $tickets->status;

        if($ticketStatus === 0)
        {
            //Get the latest current user transaction
            $checkUserCurrentTransaction = TicketsUsers::where(['user_id' => $this->user_id])->latest()->first();
            $userCurrentTicketStatus = $checkUserCurrentTransaction->status;
            if($userCurrentTicketStatus === 1 || $userCurrentTicketStatus === 2)
            {
                return response()->json(['payload' => 'Unable to call this ticket. You still have other ticket to process or complete'], 403);
            }

            $now = Carbon::now();

            $ticketUserData = [
                'user_id' => $this->user_id,
                'ticket_id' => $request->input('ticket_id'),
                'status' => 1,
                'complete_time' => $now
            ];

            $tickets->update(['status' => 1]);

            TicketsUsers::create($ticketUserData);

            return response()->json(['payload' => 'You called this ticket'], 200);
            
        }

        
        return response()->json(['payload'=> 'This ticket already been called'], 403);
    }

    /**
     * This will process/serve a ticket after you have been called
     * This will restrict and validate if you are not the user that previously called this ticket
     * In other words you need to call the ticket first before serving or process this ticket
     */
    public function serving(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer'
        ]);
        
        $ticket_id = $request->input('ticket_id');
        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        if($ticketStatus === 1)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'status' => 1, 'user_id' => $this->user_id])->count();

            if($checkTicketOwner > 0)
            {
                $now = Carbon::now();
                $data = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $this->user_id,
                    'complete_time' => $now,
                    'status' => 2
                ];

                $tickets->update(['status' => 2]);

                TicketsUsers::create($data);

                return response()->json(['payload' => 'You currently serving/process this ticket'], 200);
            }

            return response()->json(['payload'=> 'This ticket has been called by other user'], 403);
        }

        return response()->json(['payload'=> 'This ticket already been served/process'], 403);
    }

    /**
     * This will complete the ticket transaction
     * This will validate and restrict if you are not the user that previously process/serving this ticket
     * In other words you need to serve/process this ticket 
     */
    public function complete(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer'
        ]);

        $ticket_id = $request->input('ticket_id');

        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        if($ticketStatus === 2)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'status' => 2, 'user_id' => $this->user_id])->count();

            if($checkTicketOwner > 0)
            {
                $now = Carbon::now();
                $data = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $this->user_id,
                    'complete_time' => $now,
                    'status' => 3
                ];

                $tickets->update(['status' => 3]);

                TicketsUsers::create($data);

                return response()->json(['payload' => 'This ticket is finish/complete'], 200);
            }

            return response()->json(['payload'=> 'This ticket already been finished/completed by other user'], 403);
        }

        return response()->json(['payload'=> 'This ticket already been finished/completed'], 403);
    }

    /**
     * This will back the ticket to queue list
     * This will validate and restrict if you are not the user who previously called this ticket
     * In other words you need to first call this ticket
     */
    public function backToQueue(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer'
        ]);

        $ticket_id = $request->input('ticket_id');
        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        if($ticketStatus === 1)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'status' => 1, 'user_id' => $this->user_id])->count();

            if($checkTicketOwner > 0)
            {
                $now = Carbon::now();
                $data = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $this->user_id,
                    'complete_time' => $now,
                    'status' => 0
                ];

                $tickets->update(['status' => 0]);

                TicketsUsers::create($data);

                return response()->json(['payload' => 'This ticket has been back to Queue List'], 200);
            }

            return response()->json(['payload'=> 'This ticket has been called by other user'], 403);
        }

        return response()->json(['payload'=> 'Unable to back this ticket to Queue List'], 403);
    }

    /**
     * This will stop the ticket
     * This will validate and restrict if you are not the user who previously called or process this ticket
     * In other words you need to call or process this ticket
     */
    public function stop(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer'
        ]);

        $ticket_id = $request->input('ticket_id');
        $tickets = Tickets::findOrFail($ticket_id);

        $ticketStatus = $tickets->status;

        if($ticketStatus > 0)
        {
            $checkTicketOwner = TicketsUsers::where(['ticket_id' => $ticket_id, 'user_id' => $this->user_id])
            ->whereBetween('status', [1,2])
            ->count();

            if($checkTicketOwner > 0)
            {
                $now = Carbon::now();
                $data = [
                    'ticket_id' => $ticket_id,
                    'user_id' => $this->user_id,
                    'complete_time' => $now,
                    'status' => 4
                ];

                $tickets->update(['status' => 4]);

                TicketsUsers::create($data);

                return response()->json(['payload' => 'You stopped/cancelled this ticket'], 200);
            }

            return response()->json(['payload'=> 'This ticket has been called or process by other user'], 403);
        }

        return response()->json(['payload'=> 'Unable to stop this ticket. You need to call or process it first'], 403);
    }
}