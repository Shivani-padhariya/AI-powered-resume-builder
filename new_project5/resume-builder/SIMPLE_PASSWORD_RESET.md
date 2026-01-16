# Simple Password Reset - ResumeAI

## What This Does
A simplified forgot password system that allows users to change their password by just entering their registered email address - **NO OTP required**.

## How It Works

### Step 1: Enter Email
1. Go to `/auth/forgot_password_simple.php`
2. Enter your registered email address
3. Click "Verify Email"

### Step 2: Change Password
1. If email is found, you'll see a password change form
2. Enter your new password (minimum 6 characters)
3. Confirm your new password
4. Click "Change Password"

### Step 3: Done!
- Password is updated in the database
- You can now login with your new password

## Features
- ✅ **No OTP** - Simple and direct
- ✅ **Email verification** - Only registered emails can change passwords
- ✅ **Password validation** - Minimum 6 characters, confirmation required
- ✅ **Secure** - Passwords are properly hashed
- ✅ **User-friendly** - Clear step-by-step process

## Files Created
- `forgot_password_simple.php` - Main simple password reset page
- Updated `login.php` - Links to simple password reset
- Updated `forgot_password.php` - Links to simple mode

## Security Notes
- This method is simpler but less secure than OTP
- Anyone with access to a registered email can change the password
- Suitable for development/testing or low-security applications
- For production use, consider the OTP version for better security

## Usage
1. **Direct access**: `/auth/forgot_password_simple.php`
2. **From login page**: Click "Forgot your password?" link
3. **From forgot password page**: Click "Simple Mode (No OTP)" link

## Testing
1. Register a new account with an email
2. Go to the simple forgot password page
3. Enter your registered email
4. Change your password
5. Try logging in with the new password

This gives you exactly what you wanted - a simple way to change passwords by just entering your registered email, no OTP complications!
