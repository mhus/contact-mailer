Purpose
-------

The project implements a simple contact or mail formular. 

Overview
--------

The central php script receives the messages. Check the content and send mails to the
webmaster and a confirm mail to the user. The JS file contains the ajax call function
and the mailform.html contains a sample form.

Configuration is separated in different files for different pages.

Configuration
-------------

Configuration is stored in the page_*.php files. The name of the page is given in the
request. The configuration file itself is a php code file. It fills an array with data.

Example:

<?php

$config['to']='mike@mhus.de';
$config['from']='webmaster@mhus.de';
$config['subject']='Testseite';
$config['fields']= array('name','email','problem');
$config['replyToField'] = 'email';
$config['success_href']='success.html';

$config['email_required']='Bitte geben Sie eine EMail an';
$config['email_regex']='/@.+\./';
$config['name_required']='Bitte geben Sie einen Namen an';

$config['confirm_subject']='Ihre Nachricht auf MHUS wurde empfangen';
$config['confirm']='<p>Sehr geerter Kunde von MHUS,</p>
<p>wir haben Ihre Nachricht erhalten und werden uns darum k&uuml;mmern.</p>
<p> Wir werden uns bei Ihnen unter der EMail %email% melden.</p>
<p>Mit freundlichen Grüßen,</p><p>Mike</p>';


?>

As you can see you need the php enclosing tags and to set the parameters like 
$config['']='';

The Parameters
--------------

to: Mail address of the web admin receiver
from: Mail address of the mail sender (to webadmin and user)
subject: Subject of the webadmin mail
fields: List of allowed fields in the web formular
success_href: Redirect link to a url in case of success (not mandatory!)

confirm_subject: Subject of the confirm mail
confirm: Text for the confirm mail (if not set, no automatic confirm mail is send)

To send a confirm mail the field 'email' must be set and the configuration parameter
'confirm' must be set. The 'confirm' text can contain placeholders for form content. The
format '%name of the field%' will be replaced with the field content. Be aware of sending
not format checked (like email) fields back to the user. This could be used to send spam
mails via your mail form.

DO NOT USE:

<p>Your Message:</p><p>%problem%</p>

BECAUSE IT COULD BE USED AS SPAM MAIL RELAY.

Field validation
----------------

Additional you can add configurations to validate fields. Use the syntax
'field name_parameter name' like 'email_required' following described as '*_required'.

*_required: Define the field as required, define the error message if not set (this is the
            initial definition to check a field content)
*_regex: If not set the content will be checked to be not empty. Otherwise the content
         will be chacked using this regular expression.

