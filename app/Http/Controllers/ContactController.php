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
        /**
     * @OA\Get(
     *      path="/contacts",
     *      operationId="getAllContacts",
     *      tags={"Contacts"},
     *      summary="Get all contacts",
     *      description="Retrieve a list of all contacts.",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="contacts",
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/Contact")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="No contact list",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="No contact list")
     *          )
     *      )
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/api/contact-detail",
     *     summary="Get contact detail",
     *     description="Retrieve details of a contact by ID.",
     *     tags={"Contact"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="contact",
     *                     ref="#/components/schemas/Contact"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Contact not found")
     *         )
     *     )
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
     *     path="/api/reply-email",
     *     summary="Reply to an email",
     *     description="Reply to an email sent by a user.",
     *     tags={"Email"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Email data",
     *         @OA\JsonContent(
     *             required={"email", "message"},
     *             @OA\Property(property="email", type="string", format="email", example="example@example.com"),
     *             @OA\Property(property="message", type="string", example="This is the reply message.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email sent successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="email", type="string", format="email", example="example@example.com"),
     *                 @OA\Property(property="subject", type="string", example="Email reply your contact"),
     *                 @OA\Property(property="body", type="string", example="This is the reply message.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error sending email",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error sending email: [error message]")
     *         )
     *     )
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
     * @OA\Put(
     *     path="/api/update-contact-status",
     *     summary="Update contact status",
     *     description="Update the status of a contact by ID.",
     *     tags={"Contact"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="New status for the contact",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"active", "inactive"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact status updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contact status updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Contact not found")
     *         )
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
     *     path="/api/delete-contact",
     *     summary="Delete contact",
     *     description="Delete a contact by ID.",
     *     tags={"Contact"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID of the contact",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Contact deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Contact not found")
     *         )
     *     )
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
