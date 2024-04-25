<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;

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
    public function getEmailById(Request $request){
        $id = $request->id;
        $user = User::find($id);
        if($user){
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'Success!',
                'content'=>[
                    'email'=>$user->email,
                ],
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'status'=>404,
                'message'=>'User not found',
            ]);
        }
    }


}
