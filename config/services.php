<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID', 'lini-dev'),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID', '99db87d6571eb61631ca3872ada881fe957cfd48'),
        'private_key' => env('PRIVATE_KEY', "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCvQA0zLfCxomXi\n5qf1IzyLAynb/mtEAzncgxcMoeJYSLkULFgWQhFmXlS9IRk8iF/7tNeL/UuNALgp\n8spKRkrxbeXg1njyr2pNT1pAq+Yz0XahDndzPR3lCuCBJUQrGrR0mkr7MxSmw76t\no+RJSGnlTGji5fYq8rkG0bIs7DTBFzMGYB80jUYgPpnbbjTs4yeBtcIZWkcmWNMk\nWd/q6yZlJ2SLDLWAx3q/bvf7B+0BoQB2YEc/Q6MNb8RqEneLGYwyuO6so5GQI9ZB\nmv4QfxE8ybJaXmCMRhjkQ5mRWT9eUNeWUxPU/zV8pqYGB9CiE2rCdXUGNPqA/YbC\n9sVY5YdXAgMBAAECggEAAhX1EkLdtn+ETusH3LcKpWzoNqIUjL6tFZ7Jy3CxzPMw\noLjWY41tv5Yn1Ahs0HefDNPeLOBa4da8WmN1XOTxR2ZTchWbYugLj9f97v8cwkkf\n7FLLwSddD7jNazwST6EFvM2JTuwQtttljZOByA2AlSYPqYOrJl+1LAua30YAarvi\n5gZwkVRv/HFgdMz67+iH/rM8TfPk2/OoXfdEvX03TArBl/Wh1TBr4pnZL74zSbOE\nSaZ33IdxeU7Z1mIMkSQRXTJLGBgwxiY/gNynuLek7TCDQuD9acWxVrxksKpZquET\nyrAxxXD+Gdk+ZAdAZRQJeRizWNRJ9CkREH69+MdnwQKBgQDiTvqf7CejXBNF+Mhq\n9JqYst5xq9Qw55Oa3EtdPD7celgSiOGpyya1rs96pKFv6snC0LZv5bBK9HZmMX0t\nKcYTXc8eUD9YW/wPZjgJXoWzHkuk0L/HCrpFI6lY6R5xZu2Ydvw+yQBh8D2uwcXt\nZmaKWEE2sGsnN9TE7Cz/SVNxmQKBgQDGPi2B++xsptcDFrBgpxAjaLGRrcluQTn0\ny3VVzDnQuY/EpTZF3EXVI78RKfc3isLlThzfjlELtRfX6InR3dXukhJqfXJhIhjL\nFmlUFKTOv383v1zc3byxenRegSj44stlRWpfq0CeGmjjKCYt9KQpj4/+0VAh6FT9\nt1gPvGk2bwKBgFjrG0I2saRYCyKVC3oqvYt9zDTxhJ/qfW59XAc4IqLSV0SBD9l8\n/qqoEtFnWh71K20mQGha4ReyUcr5auuR3EfDtht9AEahbbpHOYFE6FdmceZCNvfn\n2SRNTL6oadO402Xyak3pAAN7N2Ewtan/cA6veoAYdSGlqC3vdgagMu8RAoGAeMsY\nNZTIiafbaYFgtXP2bGz1jbuj+i96XgO/GilXrSv0QzEb8dN3JZkjahT1Ev3VJZ3E\nECAKmCSfQ6rol10hZr3QxNxtXmxFSTH9ugLeLTAYF2Ld9aKSwF3KHG99BLJElw+F\n8odJ10xmTst/AA+KG9zjM7RcrFyBtRsLaIM4ZLcCgYEA0owc0Aadw4m3ksityDxk\ndzFeMmK5DvswljZME0T8n5r4tQXQXY/tEn8TBzDeiAqlxxYptFWaTg3rbs7zyObn\nzOs7LaQvgX4NCrAdZTsg1rh2KTfeejYMNro9UIFPMZoWEndfgzNKZ5h2JwgHB40f\nG1MItRzUK1DTM7jsoketTl8=\n-----END PRIVATE KEY-----\n"),
        'client_email' => env('FIREBASE_CLIENT_EMAIL', 'firebase-adminsdk-pit7f@lini-dev.iam.gserviceaccount.com'),
        'client_id' => env('FIREBASE_CLIENT_ID', '106895721179336811515'),
        'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL', 'https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-pit7f%40lini-dev.iam.gserviceaccount.com'),
        'database_url' => env('FIREBASE_DATABASE_URL', 'https://lini-dev.firebaseio.com')
    ],

];
