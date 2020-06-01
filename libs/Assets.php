<?php

namespace Signa\Libs;

use Phalcon\Mvc\User\Component;

class Assets extends Component
{
    private $fileHeaderJS = 'header.js';
    private $fileFooterJS = 'footer.js';

    public function installAssets()
    {
        $cssHeader = $this->assets;
        // function creating assets in view
        if (checkEnv() != 'development') {
            $cssHeader
                ->addCss('css/main.css?v=' . SCRIPT_VERSION);
        } else {

            $this->assets->collection('dev')
                ->addJs('js/app/dev.js')
                ->addJs('bower_components/less/dist/less.js');
        }

        $this->createCollections();
        $this->addSeparatedFiles();

        if (!$this->config->asset->compileAlways && $this->checkJsFilesExist()) {
            $this->assets->collection('header')
                ->addJs($this->config->asset->outputDir . '/' . $this->fileHeaderJS);
            $this->assets->collection('footer')
                ->addJs($this->config->asset->outputDir . '/' . $this->fileFooterJS);
        } else {
            $this->setCollectionsFiles();
            $this->buildAssets();
        }
    }

    private function buildAssets()
    {
        if (checkEnv() != 'development') {
            $this->assets->collection('header')
                ->setTargetPath($this->config->asset->outputDir . '/' . $this->fileHeaderJS)// its imporant, it will be created under /public/js
                ->setTargetUri($this->config->asset->outputDir . '/' . $this->fileHeaderJS)// its imporant, its saying in view output there will be that link
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Jsmin());

            $this->assets->collection('footer')
                ->setTargetPath($this->config->asset->outputDir . '/' . $this->fileFooterJS)// its imporant, it will be created under /public/js
                ->setTargetUri($this->config->asset->outputDir . '/' . $this->fileFooterJS)// its imporant, its saying in view output there will be that link
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
        }
    }

    private function addSeparatedFiles()
    {
        $this->assets->collection('footerNotCompile')
            ->addJs('bower_components/datatables.net/js/jquery.dataTables.min.js')
            ->addJs('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')
            ->addJs('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')
            ->addJs('bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.nl.min.js')
            ->addJs('bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js')
            ->addJs('js/app/toastr/toastr.min.js')
            ->addJs("bower_components/datatables.net-buttons/js/dataTables.buttons.min.js")
            ->addJs("bower_components/datatables.net-buttons/js/buttons.flash.min.js")
            ->addJs("bower_components/datatables.net-buttons/js/buttons.html5.min.js")
            ->addJs("bower_components/datatables.net-buttons/js/buttons.print.min.js")
            ->addJs("bower_components/jszip/dist/jszip.min.js")
            ->addJs("bower_components/pdfmake/build/pdfmake.min.js")
            ->addJs("bower_components/select2/dist/js/select2.full.min.js")
            ->addJs("js/jquery/jquery.numeric.min.js");
    }

    private function setCollectionsFiles()
    {
        $this->assets->collection('header')
            ->addJs('bower_components/jquery/jquery.js');

        $this->assets->collection('footer')
            ->addJs('bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js')
            ->addJs('js/app/toastr/setting.js')
            ->addJs('js/bootstrap/dropdown.js')
            ->addJs("js/bootstrap/modal.js")
            ->addJs("js/bootstrap/button.js")
            ->addJs("js/bootstrap/collapse.js")
            ->addJs("js/bootstrap/transition.js")
            ->addJs("bower_components/datatables-colvis/js/dataTables.colVis.js")
            ->addJs("bower_components/pdfmake/build/vfs_fonts.js")
            ->addJs("bower_components/select2/dist/js/i18n/nl.js")
            ->addJs("bower_components/jquery.cookie/jquery.cookie.js")
            ->addJs('js/app/app.js');
    }

    private function checkJsFilesExist()
    {
        return (
            file_exists($this->config->application->publicDir . $this->config->asset->outputDir . '/' . $this->fileFooterJS)
            &&
            file_exists($this->config->application->publicDir . $this->config->asset->outputDir . '/' . $this->fileHeaderJS)
        );
    }

    private function createCollections()
    {
        $this->assets->collection('header');
        $this->assets->collection('footer');
        $this->assets->collection('additional');
        $this->assets->collection('footerNotCompile');
    }
}