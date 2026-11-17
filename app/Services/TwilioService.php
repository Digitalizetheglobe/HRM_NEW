<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->client = new Client($sid, $token);
    }


    public function sendWhatsAppMessage($to, $message)
    {
        if (empty($to)) {
            \Log::error('Twilio Error: Recipient number is null');
            return false;
        }

        try {
            // Format phone number to E.164 standard
            $formattedTo = $this->formatPhoneNumber($to);
            \Log::info('Attempting to send message to: ' . $formattedTo);
            \Log::info('Message content: ' . $message);
            \Log::info('From number: ' . env('TWILIO_WHATSAPP_FROM'));
            
            // Check if we're in test mode (no valid Twilio numbers)
            if (env('TWILIO_TEST_MODE', false)) {
                \Log::info('TEST MODE: Message would be sent to ' . $formattedTo . ': ' . $message);
                return (object)['sid' => 'test_' . uniqid()];
            }
            
            // Try regular WhatsApp message (no template)
            try {
                $result = $this->client->messages->create(
                    'whatsapp:' . $formattedTo,
                    [
                        'from' => env('TWILIO_WHATSAPP_FROM'),
                        'body' => $message
                    ]
                );

                \Log::info('WhatsApp message sent successfully. SID: ' . $result->sid);
                return $result;

            } catch (\Exception $whatsappError) {
                \Log::error('WhatsApp send failed: ' . $whatsappError->getMessage());
                
                // Fallback to SMS
                try {
                    \Log::info('Attempting SMS fallback');
                    $result = $this->client->messages->create(
                        $formattedTo,
                        [
                            'from' => env('TWILIO_SMS_FROM'),
                            'body' => $message
                        ]
                    );
                    \Log::info('SMS sent successfully. SID: ' . $result->sid);
                    return $result;
                } catch (\Exception $smsError) {
                    \Log::error('SMS fallback also failed: ' . $smsError->getMessage());
                    return false;
                }
            }

            
        } catch (\Exception $e) {
            \Log::error('Twilio Error: ' . $e->getMessage());
            \Log::error('Error details: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Format phone number to E.164 standard
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // If number doesn't start with '+', add country code
        // Assuming India (+91) for numbers starting with 9 and having 10 digits
        if (strlen($phone) == 10 && substr($phone, 0, 1) == '9') {
            $phone = '91' . $phone;
        }
        
        // If number doesn't start with '+', add it
        if (substr($phone, 0, 1) != '+') {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

public function sendWhatsAppTemplate($to, $variables)
{
    try {
        $formattedTo = $this->formatPhoneNumber($to);
        
        // Log the variables for debugging
        \Log::info('WhatsApp Template - Variables being sent:', [
            'to' => $formattedTo,
            'contentSid' => env('TWILIO_WHATSAPP_CONTENT_SID'),
            'variables' => $variables,
            'json_encoded_variables' => json_encode($variables)
        ]);

        // Create the content variables JSON string exactly as Twilio expects
        $contentVariablesJson = json_encode($variables);
        
        \Log::info('Attempting to send with contentVariables: ' . $contentVariablesJson);
        
        try {
            // Send WhatsApp template message
            $result = $this->client->messages->create(
                'whatsapp:' . $formattedTo,
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'),
                    'contentSid' => env('TWILIO_WHATSAPP_CONTENT_SID'),
                    'contentVariables' => $contentVariablesJson
                ]
            );

            \Log::info('WhatsApp template sent successfully. SID: ' . $result->sid);
            return $result;

        } catch (\Exception $e) {
            \Log::error('WhatsApp template failed: ' . $e->getMessage());
            
            // Try without variables as a fallback
            try {
                \Log::info('Attempting to send template without variables');
                $result = $this->client->messages->create(
                    'whatsapp:' . $formattedTo,
                    [
                        'from' => env('TWILIO_WHATSAPP_FROM'),
                        'contentSid' => env('TWILIO_WHATSAPP_CONTENT_SID')
                    ]
                );
                \Log::info('Template sent without variables. SID: ' . $result->sid);
                return $result;
            } catch (\Exception $e2) {
                \Log::error('Template without variables also failed: ' . $e2->getMessage());
                return false;
            }
        }

    } catch (\Exception $e) {
        \Log::error('WhatsApp Template Error: ' . $e->getMessage());
        return false;
    }
}

}