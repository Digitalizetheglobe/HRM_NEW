<?php
namespace Modules\LandingPage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FormController extends Controller
{
    public function submitForm(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'message' => 'required|string',
            'agree' => 'accepted'
        ]);

        // Prepare the email details
        $to = 'enquire@connect360.in';
        $subject = 'New Form Submission';
        $message = "
            <h1>New Contact Form Submission</h1>
            <p><strong>Name:</strong> {$request->input('name')}</p>
            <p><strong>Phone:</strong> {$request->input('phone')}</p>
            <p><strong>Email:</strong> {$request->input('email')}</p>
            <p><strong>Message:</strong> {$request->input('message')}</p>
        ";
        $headers = "From: " . $request->input('email') . "\r\n";
        $headers .= "Reply-To: " . $request->input('email') . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";

        // Send the email using PHP's mail function
        if (mail($to, $subject, $message, $headers)) {
            return redirect()->back()->with('success', 'Form submitted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to send email. Please try again.');
        }
    }
}
