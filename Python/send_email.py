from __future__ import print_function
import pickle
import os.path
from googleapiclient.discovery import build
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request

from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.base import MIMEBase

from email import encoders
import mimetypes
import os
import base64
from base64 import urlsafe_b64encode
from httplib2 import Http
from googleapiclient.discovery import build

from format_body import format_body

#End to end process of formatting the body --> creating the message --> sending
def create_and_send(to, vendor, customer, is_punchout, account_no, ccs, is_consignment, is_ltc, existing_account, contact_name, contact_email, go_live, isa=None, attachment_file=None, warning_message=None):
    message_body = format_body(vendor, customer, is_punchout, account_no, is_consignment, is_ltc, existing_account, contact_name, contact_email, go_live, isa)
    message = create_message_with_attachment('email@domain.com', to, message_body, ccs, vendor, customer, is_punchout, attachment_file, warning_message)
    gmail_auth_creds = authenticate()
    service = build('gmail', 'v1', credentials=gmail_auth_creds)
    send_message(service, 'me', message)


def authenticate():
    SCOPES = ['https://mail.google.com/']
    creds = None
    # The file token.pickle stores the user's access and refresh tokens, and is
    # created automatically when the authorization flow completes for the first
    # time.
    if os.path.exists('creds/token.pickle'):
        with open('creds/token.pickle', 'rb') as token:
            creds = pickle.load(token)
            # If there are no (valid) credentials available, let the user log in.
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            print(creds.expired)
            print(creds.refresh_token)
            creds.refresh(Request())
        else:
            flow = InstalledAppFlow.from_client_secrets_file(
                'creds/credentials.json', SCOPES)
            creds = flow.run_local_server(port=0)
        # Save the credentials for the next run
        with open('creds/token.pickle', 'wb') as token:
            pickle.dump(creds, token)
    return creds

#2. Create message
def create_message_with_attachment(sender, to, message_text, ccs, vendor, customer, is_punchout, file=None, warning_message=None):
  """Create a message for an email.

  Args:
    sender: Email address of the sender.
    to: Email address of the receiver.
    message_text: The text of the email message.
    file: The path to the file to be attached.

  Returns:
    An object containing a base64url encoded email object.
  """
  message = MIMEMultipart()
  message['to'] = to
  message['from'] = sender
  #message['subject'] = "EDI request for Company_Name customer"
  message['CC'] = ccs

  if warning_message is not None:
      msg = MIMEText(warning_message)
      message['subject'] = "EDI REQUEST COULD NOT BE SENT"
  else:
      msg = MIMEText(message_text)
      if is_punchout == 'yes':
          if vendor.lower() == 'medline':
              message['subject'] = "Punch-out request for {customer}".format(customer=customer)
          else:
              message['subject'] = "EDI / CXML request for {customer}".format(customer=customer)
      else:
          message['subject'] = "EDI request for {customer}".format(customer=customer)
  message.attach(msg)

  if file is not None:
      part = MIMEBase('application', "vnd.ms-excel")
      part.set_payload(open(file, "rb").read())
      encoders.encode_base64(part)
      part.add_header('Content-Disposition', 'attachment', filename=file)
      message.attach(part)

  raw = base64.urlsafe_b64encode(message.as_bytes())
  raw = raw.decode()
  return {'raw': raw}

#3. Send Message
def send_message(service, user_id, message):
  """
  Args:
    service: Authorized Gmail API service instance.
    user_id: User's email address. The special value "me"
    can be used to indicate the authenticated user.
    message: Message to be sent.
  """
  try:
    #message = (service.users().messages().send(userId=user_id, body=message, cc=cc_address)
    message = (service.users().messages().send(userId=user_id, body=message)
               .execute())
    print('Message Id: %s' % message['id'])
    return message
  #except errors.HttpError, error:
  except Exception as e:
    print('An error occurred: %s' % e)
    print('error')
