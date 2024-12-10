<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            padding: 32px;
            text-align: center;
            color: #374151;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 24px;
        }

        .container h3 {
            font-size: 1.5rem;
            margin-bottom: 16px;
        }

        .icon-wrapper {
            display: flex;
            justify-content: center;
            margin: 16px 0;
        }

        .icon {
            width: 96px;
            height: 96px;
            color: #34d399;
        }

        .container p {
            margin: 16px 0;
            font-size: 1rem;
            line-height: 1.5;
        }

        .button {
            display: inline-block;
            padding: 8px 16px;
            background-color: #2563eb;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            color: white!important;
        }

        .link {
            color: #2563eb;
            text-decoration: underline;
            font-size: 0.875rem;
            word-wrap: break-word;
        }

        .note {
            margin-top: 16px;
            font-size: 0.875rem;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Thanks for signing up for SnapAura!</h3>
        <div class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
            </svg>
        </div>
        <p>We're happy you're here. Let's get your email address verified:</p>
        <div>
            <a href="{{ $data['link'] }}" class="button">
                Click to Verify Email
            </a>
            <p class="note">
                If you’re having trouble clicking the "Verify Email Address"
                button, copy and paste the URL below into your web browser:
                <a href="{{ $data['link'] }}" class="link">{{ $data['link'] }}</a>
            </p>
        </div>
    </div>
</body>

</html>