import requests
import json
import smtplib
import re
import sys
from email.utils import formataddr
from email.message import EmailMessage
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

hfrom = '«FROM» '
hsubject = '«SUBJECT» '
hmessage = '«MESSAGE» '
hrecipient = '«RECIPIENT» '
heom = '«EOM»'

try:
    fargs = 'chats/' +  sys.argv[1]

    file1 = open(fargs)
    args = file1.read()
    file1.close()

    mail_content = args

    #The mail addresses and password
    sender_address = 'levibernistoakihero1993@gmail.com'
    sender_pass = 'ryydgwbcflowqkcg'
    receiver_address = (((str(re.findall(r"[a-z0-9\.\-+_]+@[a-z0-9\.\-+_]+\.[a-z]+", mail_content))).replace("[", "")).replace("]", "")).replace("'", "")

    subject = args[args.index(hsubject) + len(hsubject) : args.index(hmessage)] 
    content = args[args.index(hmessage) + len(hmessage) : args.index(heom)] 
    sfrom  = args[args.index(hfrom) + len(hfrom) : args.index(hrecipient)] 

    #print(receiver_address)
    #Setup the MIME
    message = MIMEMultipart()
    message['From'] = formataddr((sfrom, sender_address))
    message['Reply-to'] = formataddr((sfrom, sender_address))
    message['To'] = receiver_address
    message['Subject'] = subject  #The subject line
    #The body and the attachments for the mail
    message.attach(MIMEText(content +  "\n" + "\n" +  'TG: ' + sfrom, 'plain'))
    #Create SMTP session for sending the mail
    session = smtplib.SMTP('smtp.gmail.com', 587) #use gmail with port
    session.starttls() #enable security
    session.login(sender_address, sender_pass) #login with mail_id and password
    text = message.as_string()
    session.sendmail(sender_address, receiver_address, text)
    session.quit()
    print('Mail Sent to', receiver_address)
except Exception as e:
    print(e)
