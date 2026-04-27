<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OTPController extends Controller
{
    // Send OTP via email or phone
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string'
        ]);

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => explode('@', $request->email)[0],
                'phone' => $request->phone,
                'password' => Hash::make(Str::random(16))
            ]
        );

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP (expires in 10 minutes)
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Send OTP via email with HTML template
        $this->sendOTPEmail($user->email, $otp, $user->name);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email',
            'email' => $user->email
        ]);
    }

    // Verify OTP and login/register
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check OTP validity
        if ($user->otp !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 401);
        }

        if (now()->gt($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired'
            ], 401);
        }

        // Clear OTP after successful verification
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->is_verified = true;
        $user->verified_at = now();
        $user->save();

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone
            ]
        ]);
    }

    // Resend OTP
    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        $this->sendOTPEmail($user->email, $otp, $user->name);

        return response()->json([
            'success' => true,
            'message' => 'New OTP sent'
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // Get authenticated user
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    // Send HTML OTP email
    private function sendOTPEmail($email, $otp, $name)
    {
        try {
            $htmlContent = $this->generateHTMLEmail($otp, $name);

            Mail::send([], [], function ($message) use ($email, $htmlContent) {
                $message->to($email)
                        ->subject('🔐 Your OTP Code - AI Content Platform')
                        ->html($htmlContent);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }

    // Generate beautiful HTML email template
    private function generateHTMLEmail($otp, $name)
    {
        $appName = config('app.name', 'AI Content Platform');
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - {$appName}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7ff;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
            padding: 32px 24px;
            text-align: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo span {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 8px;
            border-radius: 12px;
        }

        .content {
            padding: 40px 32px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e1b4b;
            margin: 0 0 8px 0;
        }

        .greeting {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .otp-box {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(139, 92, 246, 0.05));
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            margin: 24px 0;
        }

        .otp-code {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 12px;
            color: #6366f1;
            background: white;
            padding: 16px 24px;
            border-radius: 12px;
            display: inline-block;
            font-family: 'Courier New', monospace;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .expiry {
            font-size: 13px;
            color: #f59e0b;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .info-box {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 16px;
            border-radius: 12px;
            margin: 24px 0;
        }

        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #166534;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            margin-top: 8px;
            transition: all 0.3s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }

        .features {
            display: flex;
            gap: 16px;
            margin: 32px 0;
            padding: 0;
            list-style: none;
        }

        .features li {
            flex: 1;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .features li i {
            display: block;
            font-size: 20px;
            margin-bottom: 6px;
        }

        .footer {
            background: #f9fafb;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin: 0 0 8px;
            font-size: 12px;
            color: #9ca3af;
        }

        .footer a {
            color: #6366f1;
            text-decoration: none;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 24px 0;
        }

        .warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 16px;
        }

        .warning p {
            margin: 0;
            font-size: 12px;
            color: #92400e;
        }

        @media (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            .content {
                padding: 24px 20px;
            }
            .otp-code {
                font-size: 32px;
                letter-spacing: 8px;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 20px; background-color: #f5f7ff;">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <span>✨ {$appName}</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h1>Verify Your Identity</h1>
            <p class="greeting">Hello <strong>{$name}</strong>,</p>
            <p style="color: #4b5563; line-height: 1.5;">
                Thank you for signing up! Please use the verification code below to complete your login process.
            </p>

            <!-- OTP Box -->
            <div class="otp-box">
                <div class="otp-code">{$otp}</div>
                <div class="expiry">
                    <span>⏰</span> This code will expire in 10 minutes
                </div>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p>✓ <strong>Didn't request this code?</strong> You can safely ignore this email. Someone might have entered your email by mistake.</p>
            </div>

            <!-- Features -->
            <div class="divider"></div>
            <h3 style="font-size: 14px; color: #1e1b4b; text-align: center; margin-bottom: 16px;">What you get with {$appName}</h3>
            <ul class="features">
                <li>
                    <span>🚀</span>
                    <span>10 Free<br>Generations/day</span>
                </li>
                <li>
                    <span>🎨</span>
                    <span>6 Content<br>Types</span>
                </li>
                <li>
                    <span>⚡</span>
                    <span>Fast AI<br>Processing</span>
                </li>
                <li>
                    <span>🔒</span>
                    <span>Secure &<br>Private</span>
                </li>
            </ul>
            <div class="divider"></div>

            <!-- Warning -->
            <div class="warning">
                <p>⚠️ <strong>Security Alert:</strong> Never share this OTP with anyone. Our support team will never ask for your verification code.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {$year} {$appName}. All rights reserved.</p>
            <p>
                <a href="#">Privacy Policy</a> &nbsp;|&nbsp;
                <a href="#">Terms of Service</a> &nbsp;|&nbsp;
                <a href="#">Contact Support</a>
            </p>
            <p style="font-size: 11px;">
                This is an automated message, please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
