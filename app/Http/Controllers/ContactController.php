<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class ContactController extends Controller
{
    // Định nghĩa quy tắc validation
    public $rules = [
        'email' => 'required|email',
        'message' => 'required|string',
        'status' => 'required',
    ];

    // Định nghĩa thông điệp validation
    public $messages = [
        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'message.required' => 'The message field is required.',
        'message.string' => 'The message must be a string.',
        'status.required' => 'The status field is required.',
    ];
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
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'contacts' => $contacts,
            ]
        ], 200);
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
                    'contact' => $contact,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
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
        $validator = Validator::make($request->only('email', 'message'), [
            'email' => 'required|email',
            'message' => 'required|string',
        ], $this->messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        } else {
            $user_mail = $request->email;
            $subject = 'Email reply your contact';
            $body = $request->message;
            try {
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
                    ],
                    200
                );
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending email: ' . $e->getMessage(),
                ], 500);
            }
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
    public function updateContactStatus(Request $request, $id)
    {
        $status = $request->status;
        $contact = Contact::find($id);
        if ($contact) {
            $contact->status = $status;
            $contact->save();
            return response()->json([
                'success' => true,
                'message' => 'Contact status updated successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
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
    public function deleteContact(Request $request, $id)
    {
        $contact = Contact::find($id);
        if ($contact) {
            $contact->delete();
            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/contactUs",
     *     summary="Contact Us",
     *     description="Saves contact information",
     *     tags={"Contact"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "message"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="message", type="string", example="This is a message from a user."),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact information saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Contact information saved successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Please log in to send the email"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Error saving contact information: Internal Server Error"),
     *         ),
     *     ),
     * )
     */
    public function contactUs(Request $request)
    {
        $validator = Validator::make($request->only('email', 'message'), [
            'email' => 'required|email',
            'message' => 'required|string',
        ], $this->messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        } else {
            $user = Auth::user();
            if ($user) {
                try {
                    $contact = new Contact();
                    $contact->user_id = $user->id;
                    $contact->email = $request->email;
                    $contact->content = $request->message;
                    $contact->save();
                    return response()->json([
                        'success' => true,
                        'message' => 'Your message has been sent successfully. We will respond as soon as possible.',
                    ], 200);
                } catch (Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error saving contact information: ' . $e->getMessage(),
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to send the email',
                ], 401);
            }
        }
    }
    // update


}
