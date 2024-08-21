const express = require('express');
const axios = require('axios');
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.json());

// Environment Variables for MPESA Credentials
const consumer_key = 'qo5ANduNV44KHgLPGRF7IDveXIc0AXFORejy0bXhcGqOwFb8';
const consumer_secret = 'd2bAp31GuBw0TuCt7xBzgv9wafFEah1KdjhdYnfkgaRfgF5wakfBVbmFuKAtGyZs';
const shortcode = 'YOUR_SHORTCODE';
const security_credential = 'YOUR_SECURITY_CREDENTIAL';
const initiator_name = 'mpurse';

// MPESA API URLs
const oauth_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
const b2c_url = 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';

let token;

// Get OAuth Token
const getToken = async () => {
    const auth = Buffer.from(`${consumer_key}:${consumer_secret}`).toString('base64');
    try {
        const response = await axios.get(oauth_url, {
            headers: {
                Authorization: `Basic ${auth}`,
            },
        });
        token = response.data.access_token;
    } catch (error) {
        console.log('Error fetching OAuth token:', error);
    }
};

// B2C Payment Request
app.post('/mpesa/b2c', async (req, res) => {
    await getToken();
    
    const requestData = {
        InitiatorName: initiator_name,
        SecurityCredential: security_credential,
        CommandID: 'BusinessPayment',
        Amount: req.body.amount,
        PartyA: shortcode,
        PartyB: req.body.phone,
        Remarks: 'Payment',
        QueueTimeOutURL: 'https://yourdomain.com/timeout', 
        ResultURL: 'https://yourdomain.com/result',
        Occasion: 'M-Purse Payment',
    };

    try {
        const response = await axios.post(b2c_url, requestData, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        res.status(200).send(response.data);
    } catch (error) {
        console.log('Error making B2C payment request:', error);
        res.status(500).send('Error processing request');
    }
});

// Handle Result
app.post('/result', (req, res) => {
    const result = req.body;
    // Process and store the result in your database
    console.log('B2C Result:', result);

    // Update user dashboard with payment summary
    // Your logic to store payment summary
    res.status(200).send('Result received');
});

// Handle Timeout
app.post('/timeout', (req, res) => {
    const timeout = req.body;
    // Handle timeout scenarios
    console.log('B2C Timeout:', timeout);
    res.status(200).send('Timeout received');
});

// Start the Server
app.listen(3000, () => {
    console.log('Server running on port 3000');
});
