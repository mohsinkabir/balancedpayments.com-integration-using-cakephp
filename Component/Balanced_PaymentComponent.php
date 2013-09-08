<?php
/************************************************************/
/* Balanced Payment api integration for ACH & credit card transactions
/*@package Balanced_PaymentComponent
/*@Author Mohsin Kabir
 */
require('/home/sbaker/vendor/autoload.php');

class Balanced_PaymentComponent extends Component {

    /* Api key - need to get it from balancedpayment.com */
    private $api_key = 'api_key_here';

    /**
     * Intialize RESTFUL & HTTPful
     */
    function __construct() {
        Httpful\Bootstrap::init();
        RESTful\Bootstrap::init();
        Balanced\Bootstrap::init();
        Balanced\Settings::$api_key = $this->api_key;
    }

    /**
     * Create New Customer or Modify existing customer
     * @param array $data - data contains customer name, city, state, zip , country code, address etc.
     *
     * @return int - customer id
     */
    function create_customer($data) {
        if (!empty($data['User']['balanced_id'])) {
            $customer = Balanced\Customer::get("/v1/customers/" . $data['User']['balanced_id']);
            $customer->phone = $data['User']['phone'];
            $customer->address = array('line2' => $data['User']['address2'],
                'line1' => $data['User']['address1'],
                'state' => $data['User']['state'],
                'postal_code' => $data['User']['zip'],
                'city' => $data['User']['city'],
                'country_code' => 'US');
            $customer->business_name = $data['User']['title'];
            $customer->name = $data['User']['first_name'];
            $customer->ssn_last4 = $data['User']['ssn'];
            $customer->save();
        } else {
            $customer_info = array(
                'name' => $data['User']['first_name'],
                'business_name' => $data['User']['title'],
                'ssn_last4' => $data['User']['ssn'],
                'email' => $data['User']['email'],
                'phone' => $data['User']['phone'],
                'address' => array('line1' => $data['User']['address1'],
                    'line2' => $data['User']['address2'],
                    'city' => $data['User']['city'],
                    'state' => $data['User']['state'],
                    'postal_code' => $data['User']['zip'], 'country_code' => 'US')
            );

            $customer = new Balanced\Customer($customer_info);
            $r = $customer->save();
            return $r->id;
        }

        return $customer->id;
    }

    /**
     * Get Cutomer details
     * @param int $customer_id
     * @return array
     */
    function get_customer($customer_id) {
        $customer = Balanced\Customer::get("/v1/customers/" . $customer_id);
        $customer_array = array(
            'id' => $customer_id,
            'twitter' => $customer->twitter,
            'phone' => $customer->phone,
            'facebook' => $customer->facebook,
            'city' => $customer->address->city,
            'address2' => $customer->address->line2,
            'address1' => $customer->address->line1,
            'state' => $customer->address->state,
            'zip' => $customer->address->postal_code,
            'country_code' => $customer->address->country_code,
            'business_name' => $customer->business_name,
            'name' => $customer->name,
            'email' => $customer->email,
            'ssn_last4' => $customer->ssn_last4);
        return $customer_array;
    }

    /**
     * Create Bank account
     * @param array $data
     * @return bank account id
     */
    function create_bank_account($data) {
        $account_info = array(
            "account_number" => $data['account_number'],
            "name" => $data['name'],
            "routing_number" => $data['routing_number'],
            "type" => $data['account_type'],
        );
        $bank_account = new \Balanced\BankAccount($account_info);
        $account = $bank_account->save();
        return $account->id;
    }

    /**
     * Add bank account nuber to any cusomer
     * @param <type> $customer_id
     * @param <type> $bank_account_no
     */
    function add_bank_account_to_customer($customer_id, $bank_account_no) {
        $customer = \Balanced\Customer::get("/v1/customers/" . $customer_id);
        return $customer->addBankAccount("/v1/bank_accounts/" . $bank_account_no);
    }

    /**
     * Get Customer all bank accounts
     * @param int $customer_id
     * @return array
     */
    function get_all_bank_accounts($customer_id) {
        $customer = Balanced\Customer::get("/v1/customers/" . $customer_id . '/bank_accounts');
        $items = $customer->items;
        foreach ($items as $i => $item) {
            $all_items[$i] = array(
                'id' => $item->id,
                'bank_name' => $item->bank_name,
                'name' => $item->name,
                'account_number' => $item->account_number,
                'routing_number' => $item->routing_number,
                'type' => $item->type,
                'created' => $item->created_at,
                'verification_uri' => $item->verification_uri,
                'customer' => $item->customer,
                'can_debit' => $item->can_debit
            );
        }
        return $all_items;
    }

    /**
     * bank account verify
     * @param int $bank_account_id
     */
    function bank_account_verity($bank_account_id) {
        $bank_account = Balanced\BankAccount::get("/v1/bank_accounts/" . $bank_account_id);
        $verification = $bank_account->verify();
    }

    /**
     * Delete any bank account
     * @param int $bank_account_id
     */
    function bank_account_delete($bank_account_id) {
        $bank_account = Balanced\BankAccount::get("/v1/bank_accounts/" . $bank_account_id);
        $bank_account->unstore();
    }

    /**
     * bank account verification confirmation
     * @param int $bank_account_id
     */
    function bank_account_confirm_verity($bank_account_id) {
        $bank_account = Balanced\BankAccount::get("/v1/bank_accounts/" . $bank_account_id);
        $verification = Balanced\BankAccountVerification::get($bank_account->verification_uri);
        $verification->amount_1 = 1;
        $verification->amount_2 = 1;
        $verification->save();
    }

    /**
     * Make deposite on any bank account
     * @param customer id -- $balanced_id
     * @param bank account id - $bank_account_id
     * @param amount deposit (in pennies) -  $amount
     * @param deposite title - $title
     * @param deposite description -  $description
     */
    function debit_on_bank_account($balanced_id,$bank_account_id, $amount, $title=null, $description=null) {
        $buyer = Balanced\Account::get("/v1/marketplaces/TEST-MP1gpQaoozApjnAQvGEh4eXQ/accounts/".$balanced_id);
        $r = $buyer->debit(($amount*100),null,null,null, "/v1/bank_accounts/" . $bank_account_id);
    }

}