<?php
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

if(! function_exists('settings')) {
    /**
     * Settings class helper
     *
     * @return App\Herramientas\Settings
     */
    function settings()
    {
        return new App\Herramientas\Settings;
    }
}

if(! function_exists('numeroaletras')) {
    /**
     * Settings class helper
     *
     * @return App\Herramientas\NumeroALetras
     */
    function numeroaletras()
    {
        return new App\Herramientas\NumeroALetras;
    }
}

if(! function_exists('currency')) {
    /**
     * Currency class helper
     *
     * @return \App\Herramientas\CurrencyWrapper
     */
    function currency()
    {
        return new App\Herramientas\CurrencyWrapper;
    }
}

if(! function_exists('counter')) {
    /**
     * Counter class helper
     *
     * @return App\Herramientas\Counter
     */
    function counter()
    {
        return new App\Herramientas\Counter;
    }
}

if(! function_exists('pdf')) {
    /**
     * PDF output helper
     *
     * @param string $file
     * @param array $model
     * @return
     */
    function pdf($file, $model,$identificador)
    {

        $pdf = pdfRaw($file, $model,$identificador);

        $file = $identificador.'-'.time().'.pdf';

        if(request()->has('mode') && request()->mode == 'download') {
            return $pdf->Output($file, Destination::DOWNLOAD);
        }

        return $pdf->Output($file, Destination::INLINE);
    }
}

function pdfRaw($file, $model,$identificador) {
    $options = [
        'foo' => 'bar'
    ];

    // dd($options['header-html']);
    $html = view($file, ['model' => $model, 'options' =>  $options,'identificador'=> $identificador]);
    $pdf = new Mpdf(config('pdf'));
    $pdf->SetTitle($identificador);
    $pdf->WriteHTML($html);

    return $pdf;
}

function moneyFormat($value, $currency, $code = true)
{
    $amount = number_format($value, $currency->decimales);

    return $code? $currency->codigo.' '.$amount : $amount;
}
