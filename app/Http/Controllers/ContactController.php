<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;


use function PHPUnit\Framework\isEmpty;

class ContactController extends Controller
{
    //
    public function getAllContacts()
    {
        $contacts = Contact::all();

        if ($contacts->isEmpty()) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'No contact list',
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'contacts' => $contacts,
            ],
        ]);
    }
    public function getContactDetail(Request $request)
    {
        $id = $request->id;
        $contact = Contact::with('user')->find($id);
        if ($contact) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Success',
                'data' => [
                    'contact'=>$contact,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Contact not found',
            ]);
        }
    }
    public function replyEmail(Request $request)
    {
        $user_mail = $request->email;
        $subject = 'Email reply your contact';
        $body = $request->message;
        try{
            Mail::to($user_mail)->send(new SendMail($subject, $body));
            return response()->json(
                [
                    'success' => true,
                    'status' => 200,
                    'message' => 'Email sent successfully',
                    'data' => [
                        'email' => $user_mail,
                        'subject' => $subject,
                        'body' => $body,
                    ],
                ]
            );
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Error sending email: ' . $e->getMessage(),
            ]);
        }
    }
    public function updateContactStatus(Request $request){
        $id = $request->id;
        $status = $request->status;
        $contact = Contact::find($id);
        if ($contact) {
            $contact->status = $status;
            $contact->save();
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Contact status updated successfully',
            ]);
        } else {
            // Handle the case where the contact with the given ID is not found
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Contact not found',
            ]);
        }
    }
}
