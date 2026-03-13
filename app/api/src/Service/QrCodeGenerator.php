<?php

namespace App\Service;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Logo\Logo;

class QrCodeGenerator
{
    public function generateUri(array $visiteurData): string
    {
        $baseUrl = "http://localhost:3000/inscription-immersion"; 
        $queryString = http_build_query([
            'nom' => $visiteurData['nom'] ?? '',
            'prenom' => $visiteurData['prenom'] ?? '',
            'email' => $visiteurData['email'] ?? '',
            'vId' => $visiteurData['id'] ?? ''
        ]);

        $url = $baseUrl . '?' . $queryString;


        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10
        );

        $writer = new SvgWriter();
        $result = $writer->write($qrCode);


        return $result->getDataUri();


    }
}