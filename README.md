# Email Automation for EDI Config Requests

## Overview
My employer is an e-commerce platform for physician offices. Our customers place orders through our website which then get sent to a medical supply vendor.  Some vendors provide the option of electronic ordering (EDI) to increase the speed and security with which the orders are received and processed.

The problem my company faced was that every unique customer/vendor EDI connection needed to be requested via email.  And all vendor EDI communication was limited to two members of the technical support team. Not only was this process very unscalable but did not provide any visibility into the status of EDI requests for the customer support and onboarding teams who lodged these requests internally.

I solved this problem by building an email automation app.  I first wrote the app in Python within my first few weeks of working at this company but learned afterwards that I'd need to change it to PHP which is the preferred coding language of our developers.  This repo contains both the Python and PHP versions of the application. The PHP version is more complete as it is the one that was ultimately rolled out for usage. Below is the README for the PHP version of the application.


# PHP - README.md

The purpose of this app is to trigger an EDI request email from **edi@Company_Name.com** whenever a new EDI Request record is created in QuickBase (content management system).  The app is written in PHP and runs on an Apache server to listen for an incoming POST request from QuickBase. Upon receiving the request, the app utilizes the Gmail API to send an email from **edi@Company_Name.com**

The main component of the app is in the **index.php** file which listens for a POST request.  When the app receives this request it triggers the function in the file **process_request_and_send_email.php**.  This function uses helper functions to parse the request for all important info, (for 2 vendors only) generates an Excel file, and then sends the email. The function for creating an excel file is imported from **update_excel.php** and the function for sending the email is imported from **send_message.php**.  If the whole process is successful, we will receive a 200 message as well as the message ID. If the process fails, we will get an error message.

## Using the Gmail API
In order to automate emails with Gmail API, I first needed to enable the Gmail API for **edi@Company_Name.com**. I went to the following URL:

https://developers.google.com/gmail/api/quickstart/

scrolled to "Turn on the Gmail API", clicked through the dialogue screens until the end and then downloaded the **credentials.json** file. I saved these in the file **/creds/edi_crednetials.json**. If you see other credentials.json files in this folder, they were used for testing and can be ignored.  In order to send emails from a new email address, a **token.pickle** file is needed.  This is auto-generated when running through the automation script for the first time.

**Troubleshooting Tip:** I've occasionally encountered errors regarding expired credentials. I fixed this by deleting the **token.pickle** file and having the app regenerate this file.  Usually this file updates automatically when creds have expired.

## PHP Requirements
For managing the dependencies of this application, I used composer. The following commands were run to download composer on my local machine:

`php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"`
`php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"`
`php composer-setup.php`
`php -r "unlink('composer-setup.php');"`

I then ran the following to start composer:

`php composer.phar`

To install any existing dependencies I ran:

`php composer.phar install`

I then needed to install the below dependencies specific to this application:

`composer require google/apiclient:"^2.7"`
`composer require "swiftmailer/swiftmailer:^6.0"`
`composer require phpoffice/phpspreadsheet --ignore-platform-reqs`
`composer require maknz/slack`

## process_request_and_send_email.php

The function **create_and_send()** is the main function used by the application and consists of 4 steps:
1. The function checks to see if a **token.pickle** file exists providing authorization credentials. This file should be generated when the authorization flow completes for the first time. If there are no valid credentials available, then the user must login to their Gmail account in order to create these.
2. The body of the message is created with the function **format_body()** imported from **format_body.php**. This step is self-explanatory. It takes data from the Quick Base POST request and parses it into a message template imported from a file in the **/excel** directory . The function returns the message body.
3. The body is then used as part of the following step which uses the function **create_message_with_attachment()** to create the message object which also includes a subject, sender, receiver, ccs and attachment file.  The function uses Python’s **email.MIME** library to encode the message into **MIME standard (Multipurpose Internet Mail Exchange)** which is necessary for sending emails through Gmail.
  * Excel files are created by using PHP's **PHPSpreadsheet** library to edit an existing template.
4. An “authorized Gmail API service instance” is created with the **build()** function from the **googleapiclient.discovery** library
The message is sent using the **send_message()** function.

## Posting to Slack

In order to keep track of requests and create email failure alerts, I created a Slack channel to store this information.  I used the dependency Maknz/Slack to simplify the POST requests to Slack. There are various scenarios which trigger a POST to the #edi-request-automation channel in Slack. They are sent to a webhook URL which **is updated everytime the corresponding Slack app is updated / reinstalled**.  The POST to slack is executed by the function in **post_to_slack.php**.
