<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

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
                'message' => 'No contact list!',
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Success!',
            'content' => [
                'contacts' => $contacts,
            ],
        ]);
    }


}
