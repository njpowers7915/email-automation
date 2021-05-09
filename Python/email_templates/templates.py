standard_edi = '''
Hello {vendor} Team:

Our Customer "{customer}" is requesting to be brought live on EDI.  We have multiple EDI integrated clients with {vendor} at this time so please use our existing AS2 connection. {punch_out}

Account Number(s): {account_no}{isa}
EDI Documents to Transfer: 810, 850, 855, 856{consignment}{ltc}

Please let us know if you need any additional information.

Thank you!

'''

#HENRY SCHEIN
henry_schein = '''
Hello {vendor} Team:

Company_Name Customer "{customer}" is requesting to be brought live on EDI.  Please setup accounts for EDI orders, EDI invoices and punch-out.

Account Number(s): {account_no}{contact_name}{contact_email}

Please let us know if you need any additional information.

Thank you!

'''

#MCKESSON
mckesson = '''
Hello McKesson Team:

Company_Name Customer "{customer}" is requesting to be brought live on EDI.  We have multiple EDI integrated clients with McKesson at this time so please use the existing Company_Name AS2 connection. {punch_out}

Account Number(s): {account_no}{isa}
EDI Documents to Transfer: 810, 850, 855, 856{consignment}{ltc}

Please let us know if you need any additional information.

Thank you!

'''
mckesson_existing_customer = '''
Hello {vendor} Team:

Company_Name Customer "{customer}" is requesting add another account number to their existing EDI configuration.

Account Number(s): {account_no}{isa}

Please let us know if you need any additional information.

Thank you!

'''

#MEDLINE
medline_edi = '''
Hello Medline Team:

Company_Name Customer "{customer}" is requesting to be brought live on EDI.  We have multiple EDI integrated clients with Medline at this time so please use the existing Company_Name AS2 connection.

Account Number(s): {account_no}{isa}{go_live}
EDI Documents to Transfer: 810, 850, 855, 856{consignment}

Please let us know if you need any additional information.

Thank you!

'''
medline_cxml = '''
Hello Medline Team:

Company_Name Customer "{customer}" is requesting CXML / punch-out be configured in addition to EDI which we've already requested. Company_Name has many EDI / CXML integrated clients with Medline at this time so please configure this customer like the other Company_Name associated accounts.

Account Number(s): {account_no}{go_live}{consignment}

Please let us know if you need any additional information.

Thank you!
'''
