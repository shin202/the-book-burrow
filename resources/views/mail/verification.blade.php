<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to your Book Burrow! ☕️</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff6f1;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #e1e1e1;
            border-radius: 10px;
            background-color: #fff6f1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            text-align: center;
        }

        .button {
            background-color: #D19F71;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            display: inline-block;
        }

        .button:hover {
            background-color: #DEBA98;
        }

        p {
            line-height: 1.6;
            margin-bottom: 15px;
            color: #666666; /* Dark gray text */
        }
    </style>
</head>
<body>
<div class="container">
    <div style="text-align: center;">
        <img src="{{ $image }}" alt="Cozy Reading Corner"
             style="width: 100%; max-width: 250px; height: auto; object-fit: cover; border-radius: 10px;">
    </div>

    <h1>Welcome to The Book Burrow, {{ $username }}!</h1>

    <p>Thank you for joining The Book Burrow, your gateway to a world of captivating stories and literary
        adventures.</p>

    <p>Prepare to immerse yourself in the magic of storytelling. But first, let's get started:</p>

    <div style="text-align: center;">
        <a href="{{ $url }}" class="button">Verify & Start Exploring</a>
    </div>

    <p>Picture yourself nestled in a cozy nook, a captivating book in hand, as you embark on unforgettable journeys
        through the power of literature.</p>

    <p>If you ever need assistance or recommendations, our dedicated team is here to support you every step of the
        way.</p>

    <p>Wishing you countless hours of joyous reading,</p>

    <p>The Book Burrow Team</p>
</div>
</body>
</html>
