<?php

namespace App\Services\Document;

use App\Services\Document\DocumentFactory;
use App\Services\DomService;
use App\Services\NotificationService;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use PhpOffice\PhpWord\{
    PhpWord, IOFactory, Shared\Html
};

class DocumentService
{
    /**
     * Type of output file depending on extention
     */
    const FORMATS = [
        'docx' => 'Word2007',
        'rtf' => 'RTF',
        'odt' => 'ODText',
    ];

    /**
     * \App\Services\Document\Elements\Document
     */
    protected $document;
    protected $path;
    protected $fileName;
    protected $barcodeImagePath;
    /**
     * extention of output file
     * @var string
     */
    protected $format;

    public static function getFormats()
    {
        return implode(',', array_keys(self::FORMATS));
    }

    public function __construct(array $data, string $format)
    {
        $this->format = $format;
        $this->document = (new DocumentFactory($data))->build();
    }

    public function getHtml()
    {
        return $this->document->toHtml();
    }

    public function download($html = null, $title = null, $barcode = null)
    {
        $this->fileName = bin2hex(openssl_random_pseudo_bytes(20));

        $phpWord = new PhpWord;
        $section = $phpWord->addSection();
        if (!is_null($barcode)) {
            $barcodeImageFileName = bin2hex(openssl_random_pseudo_bytes(20));

            $directory = '/public/barcode/';
            $this->_makeDirectory($directory);
            $generatorPNG = new BarcodeGeneratorPNG;
            $barcode = $generatorPNG->getBarcode($barcode, $generatorPNG::TYPE_UPC_A);
            $this->barcodeImagePath = storage_path("/app$directory" . "$barcodeImageFileName.png");
            $ifp = fopen($this->barcodeImagePath, 'wb');
            fwrite($ifp, $barcode);
            fclose($ifp);
            $section->addImage($this->barcodeImagePath);
        }

        $phpWord->addNumberingStyle('listStyle_0', [
            'type' => 'multilevel',
            'levels' => $this->_getLevels(6),
        ]);
        $directory = '/public/docx/';
        $this->_makeDirectory($directory);
        $this->path = storage_path("/app$directory{$this->fileName}.{$this->format}");

        $htmlAdd = $html ?: $this->document->toHtml();
        $domService = new DomService($htmlAdd);
        $domService->removeTags(['a', 'br']);
        $domService->removeNestedTag('p');
        // Returns full html
        $htmlAdd = $domService->getHtml();
        Html::addHtml($section, $htmlAdd, false, false);

        $objWriter = IOFactory::createWriter($phpWord, self::FORMATS[$this->format]);
        $objWriter->save($this->path);

        $fileName = $title ?? $this->fileName;

        (new NotificationService)->sendDocumentToEmail($this->path, $fileName);

        app()->terminating(function() {
            unlink($this->path);
            if (!is_null($this->barcodeImagePath)) {
                unlink($this->barcodeImagePath);
            }
        });

        return response()->download($this->path, $fileName, [
            'Content-Type' => 'application/msword',
            'Filename' => rawurlencode($fileName),
        ]);
    }

    /**
     * Generates array of levels for multilevel list
     * @param integer $number
     * @return array
     */
    private function _getLevels($number)
    {
        $levels = [];
        for ($i=1; $i<=$number; $i++) {
            $levels[] = $this->_getLevel($i);
        }
        return $levels;
    }

    /**
     * Generates level array for multilevel list
     * @param integer $level
     * @return array
     */
    private function _getLevel($level)
    {
        $text = '';
        $size = $step = 150;
        for ($i=1; $i<=$level; $i++) {
            $size += $step;
            $text .= "%$i.";
        }
        return [
            'format' => 'decimal',
            'text' => $text,
            'left' => $size,
            'hanging' => $size,
            'tabPos' => $size,
        ];
    }

    /**
     * @param string $directory
     * @return void
     */
    private function _makeDirectory($directory)
    {
        if (!Storage::has($directory)) {
            Storage::makeDirectory($directory);
        }
    }
}
