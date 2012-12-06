Lagger - Lightweight and flexible errors/exceptions/debugs handler for PHP

Features:
Lightweight and flexible errors/exceptions/debugs handler for PHP

There are 3 event handlers classes:

  * Lagger_Handler_Errors - to handle PHP-system errors (including FATAL)
  * Lagger_Handler_Exceptions - to handle exceptions
  * Lagger_Handler_Debug - to handle custom debug messages

There are 7 classes of actions that can be maked on handling some event:

  * Lagger_Action_Print - send messages to STDOUT
  * Lagger_Action_Email - send Email
  * Lagger_Action_Sms - send SMS
  * Lagger_Action_FileLog - write to log-file
  * Lagger_Action_Exception - throw Exception
  * Lagger_Action_ChromeConsole - send messages to Google Chrome extension PHP Console http://goo.gl/b10YF
  * Lagger_Action_WinSpeak - speak message (just for fun, work on Windows servers)

And some other important features:

  * Ignoring handling of repeated(same) events (by Lagger_Skiper)
  * Using templates to define actions messages
  * Defining tags for events and configuring handlers actions to catch events of specific tags
  * Reconfiguring handlers dynamicaly by specific GET request (using Lagger_Tagger)
  * Handling internal errors
  * Just 20kb of 100% OOP source code

Project site:
http://code.google.com/p/lagger
https://github.com/barbushin/lagger

SVN:
http://lagger.googlecode.com/svn/trunk

GIT:
https://github.com/barbushin/lagger.git

Recommended:
 * Google Chrome extension PHP Console - http://goo.gl/b10YF
 * Google Chrome extension JavaScript Errors Notifier - http://goo.gl/kNix9