<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


use function PHPUnit\Framework\isEmpty;

class ContactController extends Controller
{   
    /**
     * @OA\Delete(
     *     path="/api/admin/contacts",
     *     summary="Display all contacts from database",
     *      tags={"Show Contacts"},
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getAllContacts()
    {
        $contacts = Contact::with('user')->get();
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
    // Get contact details
    /**
     * @OA\Get(
     *     path="/api/admin/contacts/{id}",
     *     summary="Get one contact detail ",
     *     tags={"Contact Details"},
     *          @OA\Parameter(
     *              name="id",
     *               in="path",
     *              description="Contact ID",
     *              required=true,
     *              @OA\Schema(type="integer")
     *          ),
     *     @OA\Response(response=200, description="Success"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
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
    /**
 * @OA\Post(
 *     path="/api/admin/replyEmail",
 *     summary="Reply to email",
 *     tags={"Reply Email"},
 *     description="Reply to an email with a message",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Request body",
 *         @OA\JsonContent(
 *             required={"email", "message"},
 *             @OA\Property(property="email", type="string", format="email", example="nguyenmaioc0@gmail.com"),
 *             @OA\Property(property="message", type="string", example="Your message goes here"),
 *         )
 *     ),
 *     @OA\Response(response=200, description="Email sent successfully"),
 *     @OA\Response(response=400, description="Bad request"),
 *     @OA\Response(response=500, description="Internal server error"),
 *     security={{"bearerAuth":{}}}
 * )
 */
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
    /**
     * @OA\Post(
     *     path="/api/admin/contacts",
     *     summary="Update contact status",
     *     tags={"update Contact Status"},
     *     description="Update the status of a contact",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body",
     *         @OA\JsonContent(
     *             required={"id", "status"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="boolean", example="1")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contact status updated successfully"),
     *     @OA\Response(response=404, description="Contact not found"),
     *     security={{"bearerAuth":{}}},
     *     @OA\SecurityScheme(
     *         securityScheme="X-CSRF-TOKEN",
     *         type="apiKey",
     *         in="header",
     *         name="X-CSRF-TOKEN",
     *         description="CSRF Token"
     *     )
     * )
     */
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
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ],404);
        }
    }
    /**
 * @OA\Delete(
 *     path="/api/admin/contacts/{id}",
 *     summary="Delete one contact detail",
 *     tags={"Delete Contact"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Contact ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Success"),
 *     @OA\Response(response=400, description="Bad request"),
 *     @OA\Response(response=404, description="Not Found"),
 *     security={{"bearerAuth":{}}}
 * )
 */
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
