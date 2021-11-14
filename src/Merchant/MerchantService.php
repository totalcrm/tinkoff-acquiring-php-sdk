<?php

namespace TotalCRM\TinkoffAcquiring\Merchant;

class MerchantService
{
    public const API_URL = 'https://securepay.tinkoff.ru/v2/';
    public const INIT_URL = 'https://securepay.tinkoff.ru/v2/Init/';
    public const CHARGE_URL = 'https://securepay.tinkoff.ru/v2/Charge/';
    public const RESEND_URL = 'https://securepay.tinkoff.ru/v2/Resend';

    protected ?string $terminalKey = '';
    protected ?string $password = '';
    protected ?array $params = [];
    protected ?array $order = [];
    protected ?array $response = [];


    /**
     * MerchantService constructor.
     * @param string $terminalKey
     * @param string $password
     */
    public function __construct(string $terminalKey = '', string $password = '')
    {
        $this->terminalKey = $terminalKey;
        $this->password = $password;

        $this->params = [
            'TerminalKey' => $this->terminalKey,
            'Password' => $this->password,
        ];
    }

    /**
     * @param string|null $api
     * @param array|null $params
     * @return array|bool
     */
    private function sendRequest(?string $api = '', ?array $params = [])
    {

        if (empty($api) || empty($params)) {
            return false;
        }
        if (is_array($params)) {
            $params = json_encode($params);
        }

        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $api);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));

            $httpResponse = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!empty($httpResponse) && $httpCode === 200) {
                $response = json_decode($httpResponse, true, 512, JSON_THROW_ON_ERROR);
            } else {
                $response = null;
            }

            $this->response = $response;

            return [
                'httpResponse' => $httpResponse,
                'httpCode' => $httpCode,
                'response' => $response,
                'params' => $params,
            ];
        }

        return false;
    }

    /**
     * @return bool|string|null
     */
    public function getRedirectURL()
    {
        if (!empty($this->response) && isset($this->response['PaymentURL'])) {
            return $this->response['PaymentURL'];
        }

        return false;
    }

    /**
     * @param array $params
     * @return bool|string
     */
    public function auth($params = array())
    {
        if (isset($params['DATA'])) {
            unset($params['DATA']);
        }

        if (isset($params['Receipt'])) {
            unset($params['Receipt']);
        }

        if (isset($params['Items'])) {
            unset($params['Items']);
        }

        if (!empty($this->password) && !empty($this->terminalKey)) {
            $params['Password'] = $this->password;
            $params['TerminalKey'] = $this->terminalKey;
            ksort($params);
            $x = implode('', $params);

            return hash('sha256', $x);
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getOrderDescription(): ?string
    {
        if (isset($this->order['Description']) && !empty($this->order['Description'])) {
            return $this->order['Description'];
        }

        return '';
    }

    /**
     * @return string|null
     */
    public function getOrderEmail(): ?string
    {
        if (isset($this->order['DATA']['Email']) && !empty($this->order['DATA']['Email'])) {
            return $this->order['DATA']['Email'];
        }

        return '';
    }

    /**
     * @return string|null
     */
    public function getOrderPhone(): ?string
    {
        if (isset($this->order['DATA']['Phone']) && !empty($this->order['DATA']['Phone'])) {
            return $this->order['DATA']['Phone'];
        }

        return '';
    }

    /**
     * @param int|null $id
     */
    public function setOrderId(?int $id = 0): void
    {
        $this->order['OrderId'] = $id;
    }

    /**
     * @param array|null $params
     * @return mixed
     */
    public function init(?array $params = [])
    {
        if (empty($params)) {
            $params = $this->order;
        }

        $params['TerminalKey'] = $this->terminalKey;
        $params['Token'] = $this->auth($params);

        if (isset($params['Receipt']) && !is_object($params['Receipt'])) {
            $params['Receipt'] = (object)$params['Receipt'];
        }

        if (isset($params['DATA']) && !is_object($params['DATA'])) {
            $params['DATA'] = (object)$params['DATA'];
        }

        if (!isset($params['Recurrent'])) {
            $params['Recurrent'] = 'N';
        }

        if (isset($params['TerminalKey'], $params['Amount'], $params['OrderId'], $params['DATA'], $params['Receipt'])) {

            return $this->sendRequest(self::INIT_URL, $params);
        }

        return false;
    }

    public function setRecurrent(): void
    {
        $this->order['Recurrent'] = 'Y';
    }

    /**
     * @param array|null $data
     */
    public function setMainInfo(?array $data = []): void
    {
        $this->order = $data;
    }

    /**
     * @param array|null $data
     */
    public function addData(?array $data = []): void
    {
        $this->order['DATA'] = $data;
    }

    /**
     * @param array|null $data
     */
    public function addReceipt(?array $data = []): void
    {
        $this->order['Receipt'] = $data;
    }

    /**
     * @param array|null $data
     */
    public function setOrderItem(?array $data = []): void
    {
        if (!is_array($data)) {
            return;
        }

        if (strlen($data['Name']) > 128) {
            $data['Name'] = substr($data['Name'], 0, 127);
        }

        if (!isset($data['Name'], $data['Price'], $data['Quantity'], $data['Tax'])) {

            return;
        }

        if (!isset($this->order['Receipt']['Items'])) {
            $this->order['Receipt']['Items'] = array();
        }

        $data['Amount'] = (int)($data['Price'] * $data['Quantity']);
        $this->order['Receipt']['Items'][] = (object)$data;

        $this->calcAmount();
    }

    public function calcAmount(): void
    {
        if (!isset($this->order['Receipt']['Items'])) {
            $this->order['Amount'] = 0;
            return;
        }

        $amount = 0;
        if (is_array($this->order['Receipt']['Items'])) {
            foreach ($this->order['Receipt']['Items'] as $k => $item) {
                if (isset($item->Amount)) {
                    $amount += (int)$item->Amount;
                }
            }
        }

        if (!isset($this->order['Amount'])) {
            $this->order = array('Amount' => $amount) + $this->order;
        } else {
            $this->order['Amount'] = (int)$amount;
        }
    }

    /**
     * @param string|null $phone
     */
    public function setOrderPhone(?string $phone = ''): void
    {
        if (empty($phone)) {
            return;
        }

        if (!isset($this->order['Receipt'])) {
            $this->order['Receipt'] = [];
        }

        if (!isset($this->order['DATA'])) {
            $this->order['DATA'] = [];
        }

        $this->order['DATA']['Phone'] = $phone;
        $this->order['Receipt']['Phone'] = $phone;
    }

    /**
     * @param string|null $email
     */
    public function setOrderEmail(?string $email = ''): void
    {
        if (empty($email)) {
            return;
        }

        if (!isset($this->order['Receipt'])) {
            $this->order['Receipt'] = array();
        }

        if (!isset($this->order['DATA'])) {
            $this->order['DATA'] = array();
        }

        $this->order['DATA']['Email'] = $email;
        $this->order['Receipt']['Email'] = $email;
    }

    /**
     * @param int|null $index
     */
    public function removeOrderItem(?int $index = 0): void
    {
        if (!isset($this->order['Receipt']['Items'])) {
            return;
        }

        if (isset($this->order['Receipt']['Items'][$index])) {
            unset($this->order['Receipt']['Items'][$index]);
            array_multisort($this->order['Receipt']['Items'], SORT_DESC);
            $this->calcAmount();
        }
    }

    /**
     * @return array
     */
    public function getAvailableTaxation(): array
    {
        return [
            'osn',
            'usn_income',
            'usn_income_outcome',
            'envd',
            'esn',
            'patent',
        ];
    }

    /**
     * @return array
     */
    public function getAvailableTax(): array
    {
        return [
            'none',
            'vat0',
            'vat10',
            'vat18',
            'vat110',
            'vat118',
        ];
    }

    /**
     * @param  $tax
     * @return bool
     */
    public function isTaxation($tax = ''): bool
    {
        if (empty($tax)) {
            return false;
        }

        if (in_array($tax, $this->getAvailableTaxation(), true)) {
            return true;
        }

        return false;
    }

    /**
     * @param string|null $tax
     * @return bool
     */
    public function isTax(?string $tax = ''): bool
    {
        if (empty($tax)) {

            return false;
        }

        if (in_array($tax, $this->getAvailableTax(), true)) {

            return true;
        }

        return false;
    }

    /**
     * @param $tax
     */
    public function setTax(?string $tax = ''): void
    {
        if (!$this->isTax($tax)) {
            return;
        }

        if (!isset($this->order['Receipt']['Items'])) {
            return;
        }

        if (!empty($this->order['Receipt']['Items']) && is_array($this->order['Receipt']['Items'])) {
            foreach ($this->order['Receipt']['Items'] as $k => $item) {
                if (is_object($item)) {
                    $item = (array)$item;
                }

                $item['Tax'] = $tax;
                $this->order['Receipt']['Items'][$k] = (object)$item;
            }
        }
    }

    /**
     * @param string|null $tax
     */
    public function setTaxation(?string $tax = ''): void
    {
        if (!isset($this->order['Receipt'])) {
            return;
        }

        if (!$this->isTaxation($tax)) {
            return;
        }

        $this->order['Receipt']['Taxation'] = $tax;
    }

    /**
     * @param array $params
     * @return bool|null
     */
    public function checkResultResponse(?array $params = []): ?bool
    {
        if (!is_array($params)) {
            $params = (array)$params;
        }

        $prev_token = $params['Token'];

        $params['Success'] = (int)$params['Success'];
        if ($params['Success'] > 0) {
            $params['Success'] = 'true';
        } else {
            $params['Success'] = 'false';
        }

        unset($params['Token']);

        $params['Password'] = $this->password;
        $params['TerminalKey'] = $this->terminalKey;

        ksort($params);
        $x = implode('', $params);

        return strcmp(strtolower($prev_token), strtolower(hash('sha256', $x))) === 0;
    }

    public function clearOrder(): void
    {
        $this->order = array();
    }

    /**
     * @return array|bool
     */
    public function resendPayment()
    {
        $params['TerminalKey'] = $this->terminalKey;
        $params['Token'] = $this->auth($params);

        return $this->sendRequest(self::RESEND_URL, $params);
    }
}