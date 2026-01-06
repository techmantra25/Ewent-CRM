<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ICICI Payment Response</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f8fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .box {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .box h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .label {
            font-weight: 600;
            color: #555;
        }
        .value {
            font-weight: 500;
            color: #2818d4;
        }
        .alert-custom {
            max-width: 500px;
            margin: 80px auto;
        }
        .btn-back {
            display: block;
            max-width: 500px;
            margin: 20px auto;
        }
        .btn-primary {
            background-color: #282a42;
            border-color: #282a42;
        }
        .redirect-text {
            text-align: center;
            margin-top: 10px;
            color: #666;
        }
    </style>
</head>
<body>

    @if(!empty($message))
        <div class="alert alert-danger text-center alert-custom">
            {{ $message }}
        </div>
    @elseif(!empty($response))
        {{-- <div class="alert alert-success text-center alert-custom">
            {{ $success_message }}
        </div> --}}
        <div class="box">
            <h3>Payment Summary</h3>
            <div class="info-row">
                <div class="label">Amount</div>
                <div class="value 
                    @if(strtolower($response['respDescription'] ?? '') === 'transaction successful') text-success
                    @elseif(strtolower($response['respDescription'] ?? '') === 'transaction rejected') text-danger
                    @endif">
                    {{ $response['amount'] ?? 'N/A' }}
                </div>
            </div>
            <div class="info-row">
                <div class="label">Response</div>
                <div class=" 
                    @if(($response['respDescription'] ?? '') === 'Transaction successful') text-success 
                    @elseif(($response['respDescription'] ?? '') === 'Transaction Rejected') text-danger 
                    @endif">
                    {{ $response['respDescription'] ?? 'N/A' }}
                </div>
            </div>

            <div class="info-row">
                <div class="label">Transaction ID</div>
                <div class="value 
                    @if(strtolower($response['respDescription'] ?? '') === 'transaction successful') text-success
                    @elseif(strtolower($response['respDescription'] ?? '') === 'transaction rejected') text-danger
                    @endif">
                    {{ $response['txnID'] ?? 'N/A' }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center alert-custom">
            No response received.
        </div>
    @endif
    <!-- Back button with countdown -->
    <a href="{{ route('organization.dashboard', ['type' => 'deposit_history']) }}" class="btn btn-primary btn-back" id="backBtn">
        Back to Dashboard
    </a>
    <div class="redirect-text">
        Redirecting in <span id="countdown">5</span> seconds...
    </div>

    <script>
        let counter = 5;
        const countdownEl = document.getElementById('countdown');
        const backBtn = document.getElementById('backBtn');
        const redirectUrl = backBtn.getAttribute('href');

        const interval = setInterval(() => {
            counter--;
            countdownEl.textContent = counter;

            if (counter <= 0) {
                clearInterval(interval);
                window.location.href = redirectUrl;
            }
        }, 1000);
    </script>

</body>
</html>
