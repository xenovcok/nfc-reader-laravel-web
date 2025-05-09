<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NFC Wristband Registration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f7f9fa; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        h2 { text-align: center; }
        label { display: block; margin-top: 12px; }
        input, button { width: 100%; padding: 8px; margin-top: 6px; border-radius: 4px; border: 1px solid #ccc; }
        button { background: #007bff; color: white; border: none; margin-top: 18px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { margin-top: 18px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>NFC Wristband Registration</h2>
        <form action="#" id="wrisbandForm">
            <label for="code">Code:</label>
            <input type="text" id="code" name="code" required readonly>
            <label for="tag_name">Tag Name:</label>
            <input type="text" id="tag_name" name="tag_name" required readonly>
            <button type="button" id="scanNfcBtn" style="background:#28a745;margin-bottom:8px;">Scan NFC Tag</button>
            <button type="submit">Register Wristband</button>
        </form>
        <div class="message" id="message"></div>
    </div>
    <script>
        // NFC Scan button handler
        document.getElementById('scanNfcBtn').addEventListener('click', async function() {
            const codeInput = document.getElementById('code');
            const nameInput = document.getElementById('tag_name');
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = '';
            if ('NDEFReader' in window) {
                try {
                    const ndef = new NDEFReader();
                    await ndef.scan();
                    messageDiv.textContent = 'Hold your NFC tag near your device...';
                    ndef.onreading = async event => {
                        messageDiv.textContent = 'NFC tag read!';
                        let tagValue = '';
                        if (event.serialNumber) {
                            tagValue = event.serialNumber;
                        } else if (event.message && event.message.records.length > 0) {
                            const record = event.message.records[0];
                            if (record.recordType === 'text' && record.data) {
                                tagValue = new TextDecoder().decode(record.data);
                            }
                        }
                        codeInput.value = tagValue;
                        // Fetch next CBS name from backend
                        try {
                            const resp = await fetch('https://2233-36-85-61-143.ngrok-free.app/api/wrisband/next-name');
                            const data = await resp.json();
                            nameInput.value = data.next_name;
                        } catch (e) {
                            nameInput.value = '';
                            messageDiv.textContent = 'Failed to get next CBS name.';
                            messageDiv.style.color = 'red';
                        }
                    };

                    ndef.onerror = () => {
                        messageDiv.textContent = 'Failed to read NFC tag.';
                        messageDiv.style.color = 'red';
                    };
                } catch (error) {
                    messageDiv.textContent = 'NFC scan failed: ' + error;
                    messageDiv.style.color = 'red';
                }
            } else {
                messageDiv.textContent = 'Web NFC is not supported on this device/browser.';
                messageDiv.style.color = 'red';
            }
        });

        document.getElementById('wrisbandForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const tag_id = document.getElementById('code').value;
            const tag_name = document.getElementById('tag_name').value;
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = '';
            try {
                const response = await fetch('https://2233-36-85-61-143.ngrok-free.app/api/wrisband', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tag_id, tag_name })
                });
                if (response.ok) {  
                    messageDiv.textContent = 'Wristband registered successfully!';
                    messageDiv.style.color = 'green';
                    document.getElementById('wrisbandForm').reset();
                } else if (response.status === 409) {
                    const data = await response.json();
                    messageDiv.textContent = data.message || 'Tag already scanned.';
                    messageDiv.style.color = 'red';
                    document.getElementById('wrisbandForm').reset();
                } else {
                    const data = await response.json();
                    messageDiv.textContent = data.message || 'Failed to register wristband.';
                    messageDiv.style.color = 'red';
                }
            } catch (error) {
                messageDiv.textContent = 'Network error.';
                messageDiv.style.color = 'red';
            }
        });
    </script>
</body>
</html>
