import openpyxl
from datetime import datetime
from datetime import date
import os.path

def update_excel(vendor, site, account_no, is_punchout, date, isa, customer_contact_name, customer_contact_email):
    if vendor.lower() in ['medline', 'mckesson']:
        excel_path = "excel/template.xlsx"
        book = openpyxl.load_workbook(excel_path)
        sheet = book.active
        sheet['A3'] = site
        sheet['B3'] = isa
        sheet['C3'] = isa

        account_list = account_no.split(',')
        for i in range(len(account_list)):
            cell = 'D' + str(3 + i)
            sheet[cell] = account_list[i].strip()
        try:
            req_date = datetime.strptime(date, '%m-%d-%Y %H:%M %p')
        except:
            try:
                req_date = datetime.strptime(date, '%m-%d-%Y')
            except:
                req_date = datetime.now()
                req_date = req_date.strftime("%d/%m/%Y %H:%M:%S")
        file_name = site + str(req_date.year) + '_' + str(req_date.month) + '_' + str(req_date.day) + '.xlsx'
        book.save('excel/' + file_name)
        return 'excel/' + file_name
