from email_templates.templates import *
#1. Format body of the email
def format_body(vendor, customer, is_punchout, account_no, is_consignment, is_ltc, existing_account, contact_name, contact_email, go_live, isa=None):
    isa = (('\nISA: ' + isa) if isa != None else '')

    punch_out = ('Please configure punch out as well.' if is_punchout == 'yes' else '')
    consignment = ('\nConsignment Customer: TRUE' if is_consignment == 'yes' else '')
    ltc = ('\nLong Term Care Customer: TRUE' if is_ltc == 'yes' else '')
    go_live = ('\nGo Live Date: {go_live_date}' if go_live != '' else '')
    contact_name = ('\nCustomer Contact: {customer_contact_name}'.format(customer_contact_name=contact_name) if contact_name != '' else '')
    contact_email = ('\nContact Email: {customer_contact_email}'.format(customer_contact_email=contact_email) if contact_email != '' else '')
    ##Working on customizing "Documents to Transfer" section by vendor
    #edi_docs = (('\nDocuments to Transfer: ' + edi_docs) if edi_docs != None else '')

    email = standard_edi.format(vendor=vendor, customer=customer, isa=isa, punch_out=punch_out, account_no=account_no, consignment=consignment, ltc=ltc)
    if vendor.lower() == 'mckesson':
        if existing_account == True:
            email = mckesson_existing_customer.format(vendor=vendor, customer=customer, isa=isa, account_no=account_no)
        else:
            email = mckesson.format(customer=customer, isa=isa, punch_out=punch_out, account_no=account_no, consignment=consignment, ltc=ltc)
    if vendor.lower() == 'medline':
        if is_punchout == 'yes':
            email = medline_cxml.format(customer=customer, account_no=account_no, go_live=go_live, consignment=consignment)
        else:
            email = medline_edi.format(customer=customer, isa=isa, account_no=account_no, go_live=go_live, consignment=consignment)
    if 'henry schein' in vendor.lower():
        email = henry_schein.format(vendor=vendor, customer=customer, account_no=account_no, contact_name=contact_name, contact_email=contact_email)

    return(email)
