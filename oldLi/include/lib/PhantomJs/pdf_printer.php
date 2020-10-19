<?php

    define('ENVIRONMENT', 'development');
    require "include/lib/PhantomJs/vendor/autoload.php";
    use JonnyW\PhantomJs\Client;
    // require_once 'vendor_phantomjs/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Client.php';

    $client = Client::getInstance();
    $client->getEngine()->setPath('E:/web/dwsj_git/include/lib/PhantomJs/bin/phantomjs.exe');

    /** 
     * @see JonnyW\PhantomJs\Http\PdfRequest
     **/
    $request = $client->getMessageFactory()->createPdfRequest('https://www.baidu.com', 'GET');
    $request->setOutputFile('E:/document.pdf');
    $request->setFormat('A4');
    $request->setOrientation('landscape');
    $request->setMargin('1cm');

    /** 
     * @see JonnyW\PhantomJs\Http\Response 
     **/
    $response = $client->getMessageFactory()->createResponse();

    // Send the request
    $client->send($request, $response);