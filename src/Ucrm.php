<?php
namespace Jrbarna\UcrmLaravel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Ucrm {
    
    private string $url, $token;
    // private string $order = 'createdAt';
    protected $order;
    protected $direction;
    protected $dateFrom;
    protected $dateTo;

    public function __construct()
    {
        $this->url = config('ucrm-laravel.url_base').config('ucrm-laravel.url_path');
        $this->token = config('ucrm-laravel.token');
        $this->ucrmPaymentMethod = config('ucrm-laravel.payment_method');
        $this->ucrmPaymentUserId = config('ucrm-laravel.payment_user_id');
    }

    public function test()
    {
        return $this->url.' : '.$this->token;
    }

    public function sendRequest($verb, $endpoint, $data = [])
    {
        // return Http::withHeader('X-Auth-App-Key',$this->token)
        // ->$verb($this->url.$endpoint);
        $request = Http::withHeaders([
            'X-Auth-App-Key' => $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);
        if (!empty($data)) {
            $request->withBody(json_encode($data), 'application/json');
        }
        return $request->$verb($this->url . $endpoint);
    
    }

    public function addData($key = '', $value = '')
    {
        $this->parameters[$key] = $value;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }
    public function direction($direction)
    {
        $this->direction = $direction;
        return $this;
    }
    public function dateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }
    public function dateTo($dateTo)
    {
        $this->dateTo = $dateTo;
        return $this;
    }
    public function getUcrmTickets()
    {
        $apiParameters = '';
        $queryArray = array(
            "dateFrom" => $this->dateFrom,
            "dateTo" => $this->dateTo,
            "direction" => $this->direction,
            "order" => $this->order,
        );
        foreach ($queryArray as $key => $value) {
            if ($value == '' || $value == null) {
                continue;
            }
            $apiParameters .= $key . '=' . urlencode($value) . (next($queryArray) ? '&' : '');
        }
        return $this->sendRequest('get','ticketing/tickets?'.$apiParameters)->json();
    }

    public function getClientName($clientId)
    {
        return $this->sendRequest('get','clients/'.$clientId.'')->json();
    }
    public function getClient($clientId)
    {
        return $this->sendRequest('get','clients/'.$clientId.'')->json();
    }
    public function getInvoiceByNumber($invoiceNumber)
    {
        return $this->sendRequest('get','invoices?number='.$invoiceNumber.'&status%5B%5D=1&status%5B%5D=2')->json();
    }
    public function getInvoicesById($invoiceId)
    {
        return $this->sendRequest('get','invoices/'.$invoiceId)->json();
    }
    public function getInvoicesByClient($clientId)
    {
        return $this->sendRequest('get','invoices?clientId='.$clientId.'&statuses%5B%5D=1&statuses%5B%5D=2&order=createdDate&direction=DESC')->json();
    }
    public function getAllInvoicesByClient($clientId)
    {
        return $this->sendRequest('get','invoices?clientId='.$clientId.'&statuses%5B%5D=1&statuses%5B%5D=2&statuses%5B%5D=3&order=createdDate&direction=DESC')->json();
    }
    public function getUnpayedInvoices()
    {
        return $this->sendRequest('get','invoices?statuses%5B%5D=1&statuses%5B%5D=2')->json();
    }
    public function postPayment($invoiceId,$paymentAmount,$clientId)
    {
        $requestData = [
            'currencyCode' => 'HUF',
            'method' => intval($this->ucrmPaymentMethod),
            'clientId' => intval($clientId),
            'amount' => intval($paymentAmount),
            'note' => 'Paid from Laravel.',
            'providerName' => 'SimplePay',
            'providerPaymentId' => 'SP001',
            'userId'=> intval($this->ucrmPaymentUserId),
            'invoiceId' => intval($invoiceId)
        ];
        return $this->sendRequest('post','payments',$requestData)->json();
    }
    public function getVersion()
    {
        return $this->sendRequest('get','version')->json();
    }
}
