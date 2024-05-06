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
                'message' => 'No contact list',
            ],404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'contacts' => $contacts,
            ]
        ],200);
    }
    public function getContactDetail(Request $request)
    {
        $id = $request->id;
        $contact = Contact::with('user')->find($id);
        if ($contact) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => [
                    'contact'=>$contact,
                ],
            ],200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ],404);
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
                    'message' => 'Email sent successfully',
                    'data' => [
                        'email' => $user_mail,
                        'subject' => $subject,
                        'body' => $body,
                    ],
                ],200
            );
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage(),
            ],500);
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
                'message' => 'Contact status updated successfully',
            ],200);
        } else {
            // Handle the case where the contact with the given ID is not found
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ],404);
        }
    }
    public function deleteContact(Request $request){
        $id = $request->id;
        $contact = Contact::find($id);
        if ($contact) {
            $contact->delete();
            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully',
            ],200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ],404);
        }
    }
}
