<?php
/**
 * EtixPay - A Payment Module for PrestaShop 1.7
 *
 * Order Validation Controller
 *
 * @author Team Atrium <team@atrium.com>
 * @license http://opensource.org/licenses/afl-3.0.php
 */

class EtixPayValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {

        $cart = $this->context->cart;
        $authorized = false;


        if (!$this->module->active || $cart->id_customer == 0 || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0) {
            Tools::redirect('index.php?controller=order&step=1');
        }


        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'etixpay') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->l('This payment method is not available.'));
        }

        /** @var CustomerCore $customer */
        $customer = new Customer($cart->id_customer);


        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        /**
         * Confirmo Orden
         */

        $this->module->validateOrder(
            (int) $this->context->cart->id,
            Etixpay_OrderState::getInitialState(),
            (float) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            $this->module->displayName,
            null,
            null,
            (int) $this->context->currency->id,
            false,
            $customer->secure_key
        );
        /*
         * Ordeno pago
         * */
        $client = new GuzzleHttp\Client(['base_url' => 'https://api.youetix.com']);
        $data =  [
            'pay_pasarelaUSER' => Configuration::get('pay_pasarelaUSER'),
            'pay_pasarelaAPISECRET' => Configuration::get('pay_pasarelaAPISECRET'),
            'pay_pasarelaPAGERESPONSE' => _PS_BASE_URL_.'/index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key,
            'pay_pasarelaTIPO' => 'PAGO',
            'pay_pasarelaMETODOPAGO' => 'ATM',
            'pay_pasarelaCANTIDATICKET' => (string) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            'walletCode' => Configuration::get('walletCode'),
        ];

        $response = $client->post('/api/v2/etixpay/payments/order',[
            'body' => $data
        ]);

        $jsonResponse = json_decode($response->getBody()->getContents());
        EtixpayRecoverPay::create($this->module->currentOrder,$jsonResponse->data->code, $data['pay_pasarelaPAGERESPONSE']);



        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }
}
