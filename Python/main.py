from flask import Flask, request, Response

from send_email import create_and_send
from update_excel import update_excel
import json
import datetime
import re

app = Flask(__name__)

# @app.route('/')
# def hello_world():
#     return 'Hello, World!'

@app.route('/webhook', methods=['POST'])
#POST request made to /webhook will trigger respond()
def respond():
    request.get_data()
    data = request.json['data']
    print(data)

    date = data['request_date']
    imp_email = data['record_owner']
    site = data['site']
    vendor = data['vendor']
    contact_name = data['customer_contact_name']
    contact_email = data['customer_contact_email']
    account_no = data['account_no']
    is_edi = data['is_edi']
    is_punchout = data['is_punchout']
    is_ltc = data['is_ltc'] if vendor.lower() == 'mckesson' else None
    is_consignment = data['is_consignment']
    go_live_date = data['go_live_date']
    isa_requirement = data['isa_requirement']
    is_810 = data['810']
    is_855 = data['855']
    is_856 = data['856']

    #Create list of EDI docs to request to be set up
    edi_docs = ('850' if is_edi == 'yes' else None)
    if edi_docs == '850':
        for doc in ['810', '855', '856']:
            if data[doc] == 'yes':
                edi_docs = edi_docs + ', ' + doc
    print(edi_docs)

    #This logic replaces site-specific ISA with vendor requirements
    if isa_requirement == 'confirm with vendor':
        isa = None
    elif isa_requirement != '':
        isa = isa_requirement
    else:
        if data['isa'] == '':
            isa = data['site_slug'].upper()
        else:
            isa = data['isa'].split(',')[0]

    #employee to be cc'd on all requests
    resource = data['resource']
    if resource is None:
        resource = 'email@domain.com'

    #Verify Vendor Emails are valid
    warning_message = None
    def check(email):
        regex = '^[a-z0-9]+[\._]?[a-z0-9]+[@]\w+[.]\w{2,3}$'
        return email if (re.search(regex, email)) else None
    vendor_email = check(data['vendor_email'].lower().strip())
    vendor_email_2 = check(data['vendor_email_2'].lower().strip())
    vendor_rep_email = check(data['vendor_rep_email'].lower().strip())
    if ((vendor_email is None) and (vendor_rep_email is not None)):
        vendor_email = vendor_rep_email
        vendor_rep_email = None
    if ((vendor_email is None) and (vendor_rep_email is None)):
        vendor_email = imp_email
        warning_message = "Your EDI request for {site} - {vendor} did not go out because a vendor email was not provided".format(vendor=vendor, site=site)

    #Add secondary vendor emails to list of ccs
    ccs = imp_email + ',' + resource
    for email in [vendor_email_2, vendor_rep_email]:
        if email is not None:
            ccs = ccs + ',' + email

    # Specify whether EDI request is for an exisiting configuration
    existing_account = False
    if data['request_type'] == "Add Account # to Existing Config":
        existing_account = True
    if existing_account == True:
        if vendor.lower() == 'mckesson':
            vendor_email = 'email@vendor.com'
            ccs = imp_email + ',' + resource

    #Only attach excel file for McKesson + Medline
    if vendor.lower() == 'mckesson' or vendor.lower() == 'medline':
        excel_file_path = update_excel(vendor, site, account_no, is_punchout, date, isa, contact_name, contact_email)
    else:
        excel_file_path = None

    #Code to generate and send emails
    if vendor.lower() == 'medline':
        if is_punchout == 'yes':
            is_punchout = False
            vendor_email = 'EDI@vendor.com'
            ccs = imp_email + ',' + resource
            create_and_send(vendor_email, vendor, site, is_punchout, account_no, ccs, is_consignment, is_ltc, existing_account, contact_name, contact_email, go_live_date, isa, excel_file_path, warning_message)

            is_punchout = 'yes'
            vendor_email = 'email@vendor.com'
            ccs = imp_email + ',' + resource
            create_and_send(vendor_email, vendor, site, is_punchout, account_no, ccs, is_consignment, is_ltc, existing_account, contact_name, contact_email, go_live_date, isa, excel_file_path, warning_message)
        else:
            create_and_send(vendor_email, vendor, site, is_punchout, account_no, ccs, is_consignment, is_ltc, existing_account, contact_name, contact_email, go_live_date, isa, excel_file_path, warning_message)

    if vendor.lower() in ['vendor1', 'vendor2', 'vendor3']:
        create_and_send(vendor_email, vendor, site, is_punchout, account_no, ccs, is_consignment, is_ltc, existing_account, contact_name, contact_email, go_live_date, isa, excel_file_path, warning_message)
    return '200'

if __name__ == "__main__":
    app.run()
    #app.run(host='0.0.0.0', port=5000, ssl_context=('server.crt', 'server.key'))
